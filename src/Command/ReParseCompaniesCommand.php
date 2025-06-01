<?php

namespace App\Command;

use App\Entity\Company;
use App\Mapper\CompanyDataMapper;
use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use App\Service\DadataService;
use App\Service\StateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 're-parse-companies',
    description: 'Add a short description for your command',
)]
class ReParseCompaniesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository      $companyRepository,
        private readonly DadataService          $dadataService,
        private readonly CompanyService         $companyService,
        private readonly StateService           $stateService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Файл с инн')
            ->addOption('recreateByNullStatusFile', 'r', InputOption::VALUE_OPTIONAL, 'Файл с инн')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $io = new SymfonyStyle($input, $output);

            if ($input->getOption('file')) {

                $inns = file_get_contents($input->getOption('file'));
                $inns = explode("\n", $inns);
                foreach ($inns as $inn) {
                    $inn = trim($inn);

                    if (!$inn) {
                        continue;
                    }
                    $company = $this->companyRepository->findOneBy(['inn' => $inn]);

                    if ($company) {
                        $this->entityManager->remove($company);
                        $this->entityManager->flush();
                    }

                    $data = $this->dadataService->findSubjectByIdAndKwards($inn, ['branch_type' => 'main']);

                    $companyModel = CompanyDataMapper::map($data[0]['data']);

                    $company = $this->companyService->createCompany($companyModel);

                    $this->entityManager->persist($company);
                    $this->entityManager->flush();
                }
            }

            if ($input->getOption('recreateByNullStatusFile')) {

                $inns = file_get_contents($input->getOption('recreateByNullStatusFile'));
                $inns = explode("\n", $inns);
                foreach ($inns as $k => $inn) {
                    $inn = trim($inn);

                    if (!$inn) {
                        continue;
                    }
                    /** @var Company $company */
                    $company = $this->companyRepository->findOneBy(['inn' => $inn]);


                    if ($company->getState() != null) {

                        if ($company->getState()->getStatus()) {
                            continue;
                        }

                        $state = $company->getState();

                        $this->entityManager->remove($state);
                        $this->entityManager->flush();
                    }


                    $companyDataModel = CompanyDataMapper::map($company->getData());
                    $this->stateService->createState($company, $companyDataModel);

                    $this->entityManager->persist($company);
                    $this->entityManager->flush();

                    $io->note("$k обработан");
                }
            }

            $io->success('Обработка закончена');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            dd($e);
        }
    }
}
