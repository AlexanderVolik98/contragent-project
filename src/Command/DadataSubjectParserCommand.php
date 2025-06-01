<?php

namespace App\Command;

use App\Enum\SubjectTypeEnum;
use App\Mapper\CompanyDataMapper;
use App\Mapper\IndividualDataMapper;
use App\Service\CompanyService;
use App\Service\IndividualService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use FilesystemIterator;

#[AsCommand(
    name: 'app:parser-from-files',
    description: 'Parse JSON files from the given directory and store data in the database',
)]
class DadataSubjectParserCommand extends Command
{
    private SymfonyStyle $io;
    private int $parsed = 0;
    private string $fullPath;
    private int $chunkSize;
    private int $workersCount;
    private int $workerId;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CompanyService $companyService,
        private readonly IndividualService $individualService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Путь к директории с JSON файлами')
            ->addOption('chunk-size', null, InputOption::VALUE_OPTIONAL, 'Сколько файлов обработать за запуск', 50000)
            ->addOption('workers-count', null, InputOption::VALUE_OPTIONAL, 'Количество параллельных воркеров', 1)
            ->addOption('worker-id', null, InputOption::VALUE_OPTIONAL, 'ID текущего воркера (начиная с 0)', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io           = new SymfonyStyle($input, $output);
        $this->fullPath     = rtrim((string) $input->getOption('path'), '/');
        $this->chunkSize    = (int) $input->getOption('chunk-size');
        $this->workersCount = (int) $input->getOption('workers-count');
        $this->workerId     = (int) $input->getOption('worker-id');

        if (empty($this->fullPath) || !is_dir($this->fullPath)) {
            $this->io->error("Директория не найдена или не указана: {$this->fullPath}");
            return Command::FAILURE;
        }
        if ($this->workerId < 0 || $this->workerId >= $this->workersCount) {
            $this->io->error('Неверный worker-id');
            return Command::FAILURE;
        }

        $this->io->text("Будем обрабатывать до {$this->chunkSize} файлов из {$this->fullPath}...");

        $processedThisRun = 0;
        $iterator = new FilesystemIterator($this->fullPath, FilesystemIterator::SKIP_DOTS);

        /** @var \SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            if ($processedThisRun >= $this->chunkSize) {
                break;
            }

            if ($fileInfo->getExtension() !== 'json') {
                continue;
            }

            // Простая реализация разделения по воркерам
            if (crc32($fileInfo->getFilename()) % $this->workersCount !== $this->workerId) {
                continue;
            }

            $originalPath   = $fileInfo->getPathname();
            $processingPath = $originalPath . '.p';

            if (!@rename($originalPath, $processingPath)) {
                continue;
            }

            try {
                $content    = file_get_contents($processingPath);
                $data       = json_decode($content, true);
                $suggestion = $data['suggestions'][0] ?? null;

                if ($suggestion) {
                    $dadataId = $suggestion['data']['hid'] ?? null;
                    if (!$dadataId) {
                        throw new \RuntimeException('Отсутствует dadataId в данных.');
                    }

                    if ($suggestion['data']['type'] === SubjectTypeEnum::LEGAL->name) {
                        $model = CompanyDataMapper::map($suggestion['data']);
                        $this->companyService->createCompany($model);
                    } else {
                        $model = IndividualDataMapper::map($suggestion['data']);
                        $this->individualService->createIndividual($model);
                    }
                }
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage(), ['file' => $processingPath]);
            }

            $this->parsed++;
            $processedThisRun++;

            if ($this->parsed % 1000 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $this->companyService->resetCache();
                $this->individualService->resetCache();
            }

            $donePath = substr($processingPath, 0, -strlen('.p')) . '.d';
            @rename($processingPath, $donePath);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->io->success("Обработано файлов за запуск: {$processedThisRun}");
        return Command::SUCCESS;
    }
}
