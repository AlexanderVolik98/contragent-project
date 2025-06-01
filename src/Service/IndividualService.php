<?php

namespace App\Service;

use App\Entity\Individual;
use App\Model\IndividualDataModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class IndividualService
{
    private array $individualsCache = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private OkvedService $okvedService,
        private StateService $stateService,
        private AddressService $addressService,
        private FinanceService $financeService,
        private DocumentService $documentService,
        private AuthorityService $authorityService,
    ) {}

    public function createIndividual(IndividualDataModel $individualDataModel): Individual
    {
        $inn = $individualDataModel->getInn();

        if ($inn) {
            if (isset($this->individualsCache[$inn])) {
                return $this->individualsCache[$inn];
            }
        }

        $slugBase = sprintf(
            '%s-%s%s-%s',
            $individualDataModel->getSurname(),
            mb_strtolower(mb_substr($individualDataModel->getName(), 0, 1)),
            mb_strtolower(mb_substr($individualDataModel->getPatronymic(), 0, 1)),
            $inn
        );
        $slug = (new AsciiSlugger())->slug($slugBase)->lower()->toString();

        if (!$inn) {

            $slug .= $individualDataModel->getHid();
            if (isset($this->individualsCache[$slug])) {
                return $this->individualsCache[$slug];
            }
        }

        $individual = (new Individual())
            ->setInn($inn)
            ->setName($individualDataModel->getName())
            ->setSurname($individualDataModel->getSurname())
            ->setPatronymic($individualDataModel->getPatronymic())
            ->setData($individualDataModel->getRawData())
            ->setSlug($slug)
            ->setDadataId($individualDataModel->getHid())
            ->setOkfs($individualDataModel->getOkfs())
            ->setOktmo($individualDataModel->getOktmo())
            ->setOkato($individualDataModel->getOkato())
            ->setOkogu($individualDataModel->getOkogu())
            ->setOkpo($individualDataModel->getOkpo())
            ->setOgrnip($individualDataModel->getOgrnip())
            ->setPhones($individualDataModel->getPhones())
            ->setEmails($individualDataModel->getEmails())
            ->setGender($individualDataModel->getGender());

        // Связанные сущности
        $this->stateService->createState($individual, $individualDataModel);
        $this->addressService->createAddress($individual, $individualDataModel);
        $this->financeService->createFinance($individual, $individualDataModel);
        $this->okvedService->createOkvedsRelations($individual, $individualDataModel);
        $this->documentService->createDocuments($individual, $individualDataModel);
        $this->authorityService->createAuthorities($individual, $individualDataModel);

        $this->entityManager->persist($individual);

        if ($inn) {
            $this->individualsCache[$inn] = $individual;
        } else {
            $this->individualsCache[$individual->getSlug()] = $individual;
        }

        return $individual;
    }

    public function resetCache(): void
    {
        $this->individualsCache = [];
    }
}
