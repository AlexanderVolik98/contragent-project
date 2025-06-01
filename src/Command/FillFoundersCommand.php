<?php

namespace App\Command;

use App\Entity\Company;
use App\Mapper\CompanyDataMapper;
use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use App\Service\FounderService;
use App\Service\IndividualService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fill-founders',
    description: 'Создает для компаний основателей, если значение $foundersProcessed = false',
)]
class FillFoundersCommand extends Command
{
    private const int BATCH_SIZE = 1000;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository      $companyRepository,
        private readonly FounderService         $founderService,
        private readonly CompanyDataMapper      $companyDataMapper,
        private readonly IndividualService      $individualService,
        private readonly CompanyService         $companyService,
        private readonly LoggerInterface        $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Максимальное число компаний для обработки за запуск',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = $input->getOption('limit');
        $processed = 0;

        $io->title('Начало обработки основателей компаний');

        do {
            $batchLimit = min(self::BATCH_SIZE, $limit ? ($limit - $processed) : self::BATCH_SIZE);

            $companies = $this->companyRepository->findBy(
                ['foundersProcessed' => false, 'isMain' => true],
                ['id' => 'ASC'],
                $batchLimit
            );

            if (empty($companies)) {
                break;
            }

            foreach ($companies as $company) {
                /** @var Company $company */
                try {
                    $io->text(sprintf('Обрабатываем основателей для компании ID %d (INN: %s)', $company->getId(), $company->getInn()));

                    $dataModel = $this->companyDataMapper::map($company->getData());
                    $this->founderService->createFounders($dataModel, $company);

                    $company->setFoundersProcessed(true);
                    $this->entityManager->persist($company);

                    $processed++;

                } catch (\Throwable $e) {
                    $this->logger->error(sprintf(
                        'Ошибка при обработке компании ID %d: %s',
                        $company->getId(),
                        $e->getMessage()
                    ), ['command' => self::$defaultName]);
                    // Продолжаем процесс дальше
                    continue;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            $this->individualService->resetCache();
            $this->companyService->resetCache();

            $io->text(sprintf('Обработано компаний на текущем шаге: %d', count($companies)));

            if ($limit && $processed >= $limit) {
                break;
            }

        } while (count($companies) === $batchLimit);

        $io->success(sprintf('Всего обработано компаний: %d', $processed));
        return Command::SUCCESS;
    }
}
