<?php

namespace App\Model;

class CompanyDataModel
{
    private string $nameFull;
    private string $shortName;
    private ?string $branchType;
    private ?string $hid;
    private ?int $inn;
    private ?int $kpp;
    private ?int $ogrn;
    private ?array $state;    // Массив с данными о состоянии
    private ?array $address;  // Массив с данными об адресе
    private ?array $managers;  // Массив с данными об адресе
    private ?string $okved;   // Код основного ОКВЭДа
    private ?array $okveds;   // Массив с данными о дополнительных ОКВЭДах
    private array $rawData;  // Исходный массив
    private ?array $founders;
    private ?array $finance;
    private ?array $capital;
    private ?string $opf;
    private ?array $documents;
    private ?array $authorities;
    private ?string $okpo;
    private ?string $okato;
    private ?string $oktmo;
    private ?string $okogu;
    private ?string $okfs;
    private ?array $successors;
    private ?array $predecessors;
    private ?array $phones;
    private ?array $emails;
    private ?array $licenses;

    public function __construct(
        string $nameFull,
        string $shortName,
        ?string $branchType,
        ?int $inn,
        ?string $hid,
        ?int $kpp,
        ?int $ogrn,
        ?array $state,
        ?array $address,
        ?array $founders,
        ?array $managers,
        ?string $okved,
        ?array $okveds,
        ?array $finance,
        ?array $capital,
        ?string $opf,
        ?array $documents,
        ?array $authorities,
        ?string $okpo,
        ?string $okato,
        ?string $oktmo,
        ?string $okogu,
        ?string $okfs,
        ?array $successors,
        ?array $predecessors,
        ?array $phones,
        ?array $emails,
        ?array $licenses,
        array $rawData = []
    ) {
        $this->nameFull   = $nameFull;
        $this->shortName   = $shortName;
        $this->branchType = $branchType;
        $this->inn = $inn;
        $this->hid = $hid;
        $this->kpp = $kpp;
        $this->ogrn = $ogrn;
        $this->finance = $finance;
        $this->founders = $founders;
        $this->state = $state;
        $this->address = $address;
        $this->managers = $managers;
        $this->okved = $okved;
        $this->okveds = $okveds;
        $this->rawData = $rawData;
        $this->capital = $capital;
        $this->documents = $documents;
        $this->opf = $opf;
        $this->authorities = $authorities;
        $this->okpo = $okpo;
        $this->okato = $okato;
        $this->oktmo = $oktmo;
        $this->okogu = $okogu;
        $this->okfs = $okfs;
        $this->successors = $successors;
        $this->predecessors = $predecessors;
        $this->phones = $phones;
        $this->licenses = $licenses;
        $this->emails = $emails;
    }

    public function getNameFull(): string { return $this->nameFull; }
    public function getBranchType(): ?string { return $this->branchType; }
    public function getInn(): ?int { return $this->inn; }
    public function getKpp(): ?int { return $this->kpp; }
    public function getOgrn(): ?int { return $this->ogrn; }
    public function getState(): ?array { return $this->state; }
    public function getAddress(): ?array { return $this->address; }
    public function getPrimaryOkvedCode(): ?string { return $this->okved; }
    public function getSecondaryOkveds(): ?array { return $this->okveds; }
    public function getFounders(): ?array { return $this->founders; }
    public function getManagers(): ?array { return $this->managers; }
    public function getFinance(): ?array { return $this->finance; }
    public function getRawData(): array { return $this->rawData; }
    public function getHid(): ?string { return $this->hid; }
    public function getOkved(): ?string { return $this->okved; }
    public function getOkveds(): ?array { return $this->okveds; }
    public function getCapital(): ?array { return $this->capital; }
    public function getShortName(): ?string { return $this->shortName; }
    public function getAuthorities(): ?array { return $this->authorities; }
    public function getDocuments(): ?array { return $this->documents; }
    public function getOpf(): ?string { return $this->opf; }
    public function getOkpo(): ?string { return $this->okpo; }
    public function getOkato(): ?string { return $this->okato; }
    public function getOktmo(): ?string { return $this->oktmo; }
    public function getOkogu(): ?string { return $this->okogu; }
    public function getOkfs(): ?string { return $this->okfs; }
    public function getSuccessors(): ?array { return $this->successors; }
    public function getPredecessors(): ?array { return $this->predecessors; }
    public function getPhones(): ?array { return $this->phones; }
    public function getEmails(): ?array { return $this->emails; }
    public function getLicenses(): ?array { return $this->licenses; }
}
