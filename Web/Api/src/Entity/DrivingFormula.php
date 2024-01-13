<?php

namespace App\Entity;

use App\Repository\DrivingFormulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: DrivingFormulaRepository::class)]
class DrivingFormula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:formula:all', 'read:formula', 'write:formula', 'read:vehicle:all', 'read:vehicle'])]
    #[Assert\NotBlank(NULL, message: "Le nombre d'heure est requis.")]
    #[Assert\GreaterThan(value: 0)]
    private ?int $nbHours = null;

    #[ORM\OneToOne(inversedBy: 'drivingFormula', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: "FormulaIsDrivingFormula")]
    #[Groups(['read:formula', 'write:formula', 'read:vehicle:all', 'read:vehicle', 'read:candidate', 'write:candidate'])]
    #[MaxDepth(1)]
    private ?Formula $formula = null;

    #[ORM\ManyToMany(targetEntity: Vehicle::class, inversedBy: 'drivingFormulas', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: "DrivingFormulaHasVehicles")]
    #[MaxDepth(1)]
    #[Groups(['read:formula:all', 'read:formula', 'write:formula'])]
    private Collection|ArrayCollection $vehicles;

    #[ORM\ManyToOne(inversedBy: 'drivingFormula')]
    private ?Candidate $candidate = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getNbHours(): ?int
    {
        return $this->nbHours;
    }

    /**
     * @param int $nbHours
     * @return $this
     */
    public function setNbHours(int $nbHours): self
    {
        $this->nbHours = $nbHours;

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
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    /**
     * @param Vehicle $vehicle
     * @return $this
     */
    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
        }

        return $this;
    }

    /**
     * @param Vehicle $vehicle
     * @return $this
     */
    public function removeVehicle(Vehicle $vehicle): self
    {
        $this->vehicles->removeElement($vehicle);

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
