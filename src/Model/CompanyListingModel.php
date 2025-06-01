<?php

namespace App\Model;

use App\Entity\Company;

class CompanyListingModel
{
    public ?string $name;
    public ?string $inn;
    public ?string $ogrn;
    public ?string $kpp;
    public ?string $phones;
    public ?string $emails;
    public ?string $slug;
    public ?string $entity_type;
    public ?string $state_status;
    public ?int $registration_date_int;
    public ?string $region_name;
    public ?string $opf_abbreviation;
    public ?string $okved_name;
    public ?int $state_status_order;

    public function __construct(Company $company)
    {
        $this->name = $company->getName();
        $this->inn = $company->getInn();
        $this->ogrn = $company->getOgrn();
        $this->kpp = $company->getKpp();
        $this->phones = json_encode($company->getPhones());
        $this->emails = json_encode($company->getEmails());
        $this->slug = $company->getSlug();
        $this->entity_type = $company->getType();
        $this->state_status = $company->getState()->getStatus()->value;
        $this->registration_date_int = $company->getState()->getRegistrationDate()->format('Ymd');
        $this->region_name = $company->getAddress()->getRegion()->getName();
        $this->opf_abbreviation = $company->getOpf()->getAbbreviation();
        $this->okved_name = $company->getPrimaryOkved()->getOkved()->getName();
        $this->state_status_order = 1;
    }
}
