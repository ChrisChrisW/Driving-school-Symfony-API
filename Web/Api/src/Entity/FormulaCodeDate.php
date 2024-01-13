<?php

namespace App\Entity;

use App\Repository\FormulaCodeDateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FormulaCodeDateRepository::class)]
class FormulaCodeDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:candidate', 'write:candidate'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:candidate', 'write:candidate'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'formulaCodeDates')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:candidate', 'write:candidate'])]
    private ?Formula $formula = null;

    #[ORM\ManyToOne(inversedBy: 'formulaCodeDates')]
    private ?Candidate $candidate = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param \DateTimeInterface $startDate
     * @return $this
     */
    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeInterface $endDate
     * @return $this
     */
    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Formula|null
     */
    public function getFormula(): ?Formula
    {
        return $this->formula;
    }

    /**
     * @param Formula|null $formula
     * @return $this
     */
    public function setFormula(?Formula $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * @return Candidate|null
     */
    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    /**
     * @param Candidate|null $candidate
     * @return $this
     */
    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }
}
