<?php

namespace App\Command;

use App\Mapper\CompanyDataMapper;
use App\Model\CompanyDataModel;
use App\Service\CompanyService;
use App\Service\DadataService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-company-create',
    description: 'Тестовое создание компании с логами по шагам',
)]
class TestCompanyCreateCommand extends Command
{
    private CompanyService $companyCreateService;
    private DadataService $dadataService;

    public function __construct(CompanyService $companyCreateService, DadataService $dadataService)
    {
        parent::__construct();
        $this->companyCreateService = $companyCreateService;
        $this->dadataService = $dadataService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Начинаем создание компании...</info>');

        try {
            $data = $this->dadataService->findSubjectByIdAndKwards(7706092528, ['branch_type' => 'MAIN']);

            $output->writeln('⏳ Вызываем createCompany()...');
            $company = $this->companyCreateService->createCompany(CompanyDataMapper::map($data[0]['data']));

            dd($company);
            $output->writeln('<info>✅ Компания успешно создана!</info>');
            $output->writeln('ID: ' . $company->getId());

        } catch (\Throwable $e) {
            $output->writeln('<error>❌ Ошибка: ' . $e->getMessage() . '</error>');
            $output->writeln('<error>' . $e->getTraceAsString() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
