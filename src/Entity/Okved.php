<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'okved')]
class Okved
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $parentCode = '';

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private string $section;

    #[ORM\Column(type: 'text')]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $comment;

    #[ORM\OneToMany(targetEntity: SubjectOkved::class, mappedBy: 'okved')]
    private Collection $companyOkveds;

    public function __construct()
    {
        $this->companyOkveds = new ArrayCollection();
    }

    // Getters and Setters
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getParentCode(): string
    {
        return $this->parentCode;
    }

    public function setParentCode(?string $parentCode): self
    {
        $this->parentCode = $parentCode;
        return $this;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCompanyOkveds(): Collection
    {
        return $this->companyOkveds;
    }
}