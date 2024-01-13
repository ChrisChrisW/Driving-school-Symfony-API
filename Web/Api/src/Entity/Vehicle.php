<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\PersistProcessor\VehiclePersistProcessor;
use App\Repository\VehicleRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
#[UniqueEntity(fields: "numPlate", message: "Tu ne peux pas créer le même numPlate")]
#[ApiResource(order: ['numPlate' => 'ASC'])]
#[ApiFilter(SearchFilter::class, properties: ['drivingFormulas.formula.slug' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['numPlate' => 'exact'])]
#[GetCollection(uriTemplate: '/vehicles', normalizationContext: ['groups' => ['read:vehicle:all']])]
#[GetCollection(uriTemplate: '/vehicle', normalizationContext: ['groups' => ['read:vehicle']])]
#[Get(normalizationContext: ['groups' => ['read:vehicle']])]
#[Post(
    uriTemplate: '/vehicle',
    openapiContext: [
        'description' => 'Post vehicle informations',
    ],
    denormalizationContext: ['groups' => ['write:vehicle']],
    itemUriTemplate: '/vehicle'
)]
#[Put(uriTemplate: '/vehicle/{numPlate}', processor : VehiclePersistProcessor::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:vehicle:all', 'read:vehicle', 'write:vehicle', 'read:formula:all', 'read:formula'])]
    #[Assert\NotBlank(NULL, message: "Le numéro de ta plaque est requis.")]
    #[Assert\Length(NULL, 7, 9)]
    #[ApiProperty(identifier: true)]
    private ?string $numPlate = null;

    /**
     * @var DateTimeInterface|null A "Y-m-d H:i:s" formatted value
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:vehicle', 'write:vehicle'])]
    #[Assert\Type(DateTimeInterface::class)]
    #[Assert\NotBlank(NULL, message: "La date d'achat est requis.")]
    private ?DateTimeInterface $purchaseDate = null;

    #[ORM\Column]
    #[Groups(['read:vehicle', 'write:vehicle'])]
    #[Assert\NotBlank(NULL, message: "La puissance est requis.")]
    #[Assert\Positive]
    private ?int $power = null;

    #[ORM\ManyToMany(targetEntity: DrivingFormula::class, mappedBy: 'vehicles', cascade: ['persist', 'remove'])]
    #[Groups(['read:vehicle:all', 'read:vehicle'])]
    private Collection $drivingFormulas;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: CourseDates::class)]
    private Collection $courseDate;

    /**
     *  Constructor
     */
    public function __construct()
    {
        $this->drivingFormulas = new ArrayCollection();
        $this->courseDate = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNumPlate(): ?string
    {
        return $this->numPlate;
    }

    /**
     * @param string $numPlate
     * @return $this
     */
    public function setNumPlate(string $numPlate): self
    {
        $this->numPlate = $numPlate;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPurchaseDate(): ?DateTimeInterface
    {
        return $this->purchaseDate;
    }

    /**
     * @param DateTimeInterface $purchaseDate
     * @return $this
     */
    public function setPurchaseDate(DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPower(): ?int
    {
        return $this->power;
    }

    /**
     * @param int $power
     * @return $this
     */
    public function setPower(int $power): self
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @return Collection<int, DrivingFormula>
     */
    public function getDrivingFormulas(): Collection
    {
        return $this->drivingFormulas;
    }

    /**
     * @param DrivingFormula $drivingFormula
     * @return $this
     */
    public function addDrivingFormula(DrivingFormula $drivingFormula): self
    {
        if (!$this->drivingFormulas->contains($drivingFormula)) {
            $this->drivingFormulas->add($drivingFormula);
            $drivingFormula->addVehicle($this);
        }

        return $this;
    }

    /**
     * @param DrivingFormula $drivingFormula
     * @return $this
     */
    public function removeDrivingFormula(DrivingFormula $drivingFormula): self
    {
        if ($this->drivingFormulas->removeElement($drivingFormula)) {
            $drivingFormula->removeVehicle($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CourseDates>
     */
    public function getCourseDate(): Collection
    {
        return $this->courseDate;
    }

    /**
     * @param CourseDates $courseDate
     * @return $this
     */
    public function addCourseDate(CourseDates $courseDate): self
    {
        if (!$this->courseDate->contains($courseDate)) {
            $this->courseDate->add($courseDate);
            $courseDate->setVehicle($this);
        }

        return $this;
    }

    /**
     * @param CourseDates $courseDate
     * @return $this
     */
    public function removeCourseDate(CourseDates $courseDate): self
    {
        if ($this->courseDate->removeElement($courseDate)) {
            // set the owning side to null (unless already changed)
            if ($courseDate->getVehicle() === $this) {
                $courseDate->setVehicle(null);
            }
        }

        return $this;
    }
}
