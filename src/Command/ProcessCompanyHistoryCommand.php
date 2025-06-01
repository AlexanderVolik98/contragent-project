<?php

namespace App\Command;

use App\Entity\Company;
use App\Mapper\CompanyDataMapper;
use App\Service\CompanyHistoryService;
use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessCompanyHistoryCommand extends Command
{
    protected static $defaultName = 'app:process-company-history';
    protected static $defaultDescription = 'Обрабатывает историю компаний: predecessors и successors';

    public function __construct(
        private CompanyRepository       $companyRepository,
        private CompanyHistoryService   $companyHistoryService,
        private EntityManagerInterface  $entityManager,
        private LoggerInterface         $logger,
        private readonly CompanyService $companyService
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Максимальное число компаний для обработки за запуск (по умолчанию все)',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $chunkSize = 1000; // Размер чанка

        $io->title('Начало обработки истории компаний');

        $processed = 0;
        $offset = 0;

        while (true) {
            $batchLimit = $chunkSize;

            // Если установлен общий лимит, подгоняем лимит чанка
            if ($limit !== null && ($limit - $processed) < $chunkSize) {
                $batchLimit = $limit - $processed;
            }

            $companies = $this->companyRepository->findBy(
                ['historyProcessed' => false, 'isMain' => true],
                ['id' => 'ASC'],
                $batchLimit,
                $offset
            );

            if (empty($companies)) {
                break;
            }

            foreach ($companies as $company) {
                try {
                    /** @var Company $company */
                    $io->text(sprintf('Обрабатываем компанию ID %d (INN: %s)', $company->getId(), $company->getInn()));

                    $rawData = $company->getData();
                    $dataModel = CompanyDataMapper::map($rawData);

                    $this->companyHistoryService->createCompanyHistory($company, $dataModel);

                    $company->setHistoryProcessed(true);
                    $this->entityManager->persist($company);

                    $processed++;

                    if ($limit !== null && $processed >= $limit) {
                        break 2; // Выйти из обоих циклов
                    }

                } catch (\Throwable $e) {
                    $this->logger->error('Ошибка при обработке компании ID '.$company->getId().': '.$e->getMessage(), [
                        'exception' => $e, 'command' => self::$defaultName
                    ]);

                    // Продолжаем обработку
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            $this->companyService->resetCache();

            $offset += $batchLimit;
        }

        $io->success(sprintf('Обработано %d компаний.', $processed));
        return Command::SUCCESS;
    }
}
