<?php

namespace App\Model;

use App\Entity\Individual;

class IndividualListingModel
{
    public ?string $name;
    public ?string $inn;
    public ?string $ogrnip;
    public ?string $kpp;
    public ?string $phones;
    public ?string $emails;
    public ?string $slug;
    public ?string $entity_type;
    public ?string $state_status;
    public ?int $registration_date_int;
    public ?string $region_name;
    public ?string $okved_name;
    public ?int $state_status_order;

    public function __construct(Individual $individual)
    {
        $this->name = $individual->getSurname() . ' ' . $individual->getName() . ' ' . $individual->getPatronymic();
        $this->inn = $individual->getInn();
        $this->ogrnip = $individual->getOgrnip();
        $this->phones = json_encode($individual->getPhones());
        $this->emails = json_encode($individual->getEmails());
        $this->slug = $individual->getSlug();
        $this->entity_type = 'Individual';
        $this->state_status = $individual->getState()->getStatus()->value;
        $this->registration_date_int = $individual->getState()->getRegistrationDate()->format('Ymd');
        $this->region_name = $individual->getAddress()->getRegion()->getName();
        $this->okved_name = $individual->getOkved()->getName();
        $this->state_status_order = 1;
    }
}
