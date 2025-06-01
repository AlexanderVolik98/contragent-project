<?php

namespace App\Model;

readonly class IndividualDataModel
{
    private ?int $id;
    private ?string $inn;
    private ?string $name;
    private ?string $surname;
    private ?string $patronymic;
    private ?string $gender;
    private ?array $state;   // Массив с данными о состоянии
    private ?array $address;  // Массив с данными об адресе
    private ?array $finance;  // Массив с данными об адресе
    private ?array $documents;
    private ?string $okved;  // Код основного ОКВЭДа
    private ?array $okveds;   // Массив с данными о дополнительных ОКВЭДах
    private ?string $okogu;
    private ?string $oktmo;
    private ?string $okfs;
    private ?string $okato;
    private ?string $okpo;
    private ?string $ogrnip;
    private ?string $hid;
    private ?array $authorities;
    private ?array $phones;
    private ?array $emails;
    private array $rawData;  // Исходный массив

    public function __construct(
        ?int $id,
        ?string $inn,
        ?string $name,
        ?string $surname,
        ?string $patronymic,
        ?string $gender,
        ?string $okpo,
        ?string $okato,
        ?string $ogrnip,
        ?string $oktmo,
        ?string $okogu,
        ?string $okfs,
        ?array $state,
        ?array $address,
        ?string $okved,
        ?array $finance,
        ?array $documents,
        ?array $okveds,
        ?array $authorities,
        ?array $phones,
        ?array $emails,
        array $rawData,
        ?string $hid,
    ) {
        $this->id = $id;
        $this->inn = $inn;
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->gender = $gender;
        $this->state = $state;
        $this->address = $address;
        $this->okved = $okved;
        $this->okogu = $okogu;
        $this->oktmo = $oktmo;
        $this->okfs = $okfs;
        $this->okato = $okato;
        $this->okpo = $okpo;
        $this->ogrnip = $ogrnip;
        $this->finance = $finance;
        $this->documents = $documents;
        $this->okveds = $okveds;
        $this->phones = $phones;
        $this->emails = $emails;
        $this->authorities = $authorities;
        $this->rawData = $rawData;
        $this->hid = $hid;
    }

    public function getId(): ?int { return $this->id; }
    public function getInn(): ?string { return $this->inn; }
    public function getName(): ?string { return $this->name; }
    public function getSurname(): ?string { return $this->surname; }
    public function getOgrnip(): ?string { return $this->ogrnip; }
    public function getPatronymic(): ?string { return $this->patronymic; }
    public function getGender(): ?string { return $this->gender; }
    public function getState(): ?array { return $this->state; }
    public function getAddress(): ?array { return $this->address; }
    public function getFinance(): ?array { return $this->finance; }
    public function getRawData(): array { return $this->rawData; }
    public function getDocuments(): ?array { return $this->documents; }
    public function getAuthorities(): ?array { return $this->authorities; }
    public function getOkved(): ?string { return $this->okved; }
    public function getOkveds(): ?array { return $this->okveds; }
    public function getPhones(): ?array { return $this->phones; }
    public function getEmails(): ?array { return $this->emails; }
    public function getOkato(): ?string { return $this->okato; }
    public function getOktmo(): ?string { return $this->oktmo; }
    public function getOkogu(): ?string { return $this->okogu; }
    public function getOkfs(): ?string { return $this->okfs; }
    public function getOkpo(): ?string { return $this->okpo; }
    public function getHid(): ?string { return $this->hid; }
}
