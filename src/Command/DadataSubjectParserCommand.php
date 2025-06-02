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
use Symfony\Component\Console\Command\LockableTrait;

#[AsCommand(
    name: 'app:parser-from-files',
    description: 'Parse all JSON files from the given directory and store data in the database',
)]
class DadataSubjectParserCommand extends Command
{
    use LockableTrait;

    private SymfonyStyle $io;
    private string $fullPath;
    private int $parsed = 0;

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
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Путь к директории с JSON файлами');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('<comment>Команда уже запущена в другом процессе.</comment>');
            return Command::SUCCESS;
        }

        ini_set('memory_limit', '-1');
        $this->io = new SymfonyStyle($input, $output);
        $this->fullPath = rtrim((string) $input->getOption('path'), '/');

        if (empty($this->fullPath) || !is_dir($this->fullPath)) {
            $this->io->error("Директория не найдена или не указана: {$this->fullPath}");
            return Command::FAILURE;
        }

        $this->io->text("Обрабатываем файлы из директории: {$this->fullPath}");

        try {
            $iterator = new FilesystemIterator($this->fullPath, FilesystemIterator::SKIP_DOTS);

            /** @var \SplFileInfo $fileInfo */
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->getExtension() !== 'json') {
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

            $this->io->success("Всего обработано файлов: {$this->parsed}");
            return Command::SUCCESS;

        } finally {
            $this->release(); // Снимает lock даже при исключениях
        }
    }
}
