<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
#[ORM\Table(name: 'opf')]
class Opf implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $abbreviation;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fullName;

    #[Ignore]
    #[ORM\OneToMany(
        targetEntity: Company::class,
        mappedBy: 'opf',
        cascade: ['persist', 'remove'],
    )]
    private Collection $companies;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
          'id' => $this->id,
          'abbreviation' => $this->abbreviation,
          'fullName' => $this->fullName,
        ];
    }
}
