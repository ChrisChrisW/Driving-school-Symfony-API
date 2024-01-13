<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PostFormulaController;
use App\PersistProcessor\FormulaPersistProcessor;
use App\Repository\FormulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: FormulaRepository::class)]
#[UniqueEntity(fields: "slug", message: "Tu ne peux pas créer le même wording")]
#[ApiResource(order: ['slug' => 'ASC'])]
#[ApiFilter(RangeFilter::class, properties: ['minAge'])]
#[ApiFilter(SearchFilter::class, properties: ['slug' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['formulaCodeDates.candidate.email' => 'exact'])]
#[GetCollection(uriTemplate: '/formulas', normalizationContext: ['groups' => ['read:formula:all']])]
#[Get(normalizationContext: ['groups' => ['read:formula']])]
#[Post(
    uriTemplate: '/formula',
    controller: PostFormulaController::class,
    openapiContext: [
        'description' => 'Post formula informations',
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'wording' => ['type' => 'string'],
                            'price' => ['type' => 'integer'],
                            'minAge' => ['type' => 'integer'],
                            'nbHours' => ['type' => 'integer'],
                            'vehiculeNumPlate' => ['type' => 'string']
                        ]
                    ],
                    'example' => [
                        'wording' => "Formule X",
                        'price' => 23,
                        'minAge' => 16,
                        'nbHours' => 25,
                        'vehiculeNumPlate' => "Plaque"
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:formula']],
    itemUriTemplate: '/formula'
)]
#[Put(uriTemplate: '/formula/{slug}', processor : FormulaPersistProcessor::class)]
#[Put(uriTemplate: '/formula/vehicle/add/{slug}', openapiContext: [
    'description' => 'Post formula informations',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'wording' => ['type' => 'string'],
                        'vehiculeNumPlate' => ['type' => 'string']
                    ]
                ],
                'example' => [
                    'wording' => "Formule X",
                    'vehiculeNumPlate' => "Plaque"
                ]
            ]
        ]
    ]
], processor: FormulaPersistProcessor::class)]
#[Put(uriTemplate: '/formula/vehicle/remove/{slug}', openapiContext: [
    'description' => 'Post formula informations',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'wording' => ['type' => 'string'],
                        'removeVehicle' => ['type' => 'string']
                    ]
                ],
                'example' => [
                    'wording' => "Formule X",
                    'removeVehicle' => "Plaque"
                ]
            ]
        ]
    ]
], processor: FormulaPersistProcessor::class)]
class Formula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:trainer:all', 'read:trainer', 'read:formula:all', 'read:formula', 'write:formula', 'read:candidate', 'write:candidate',  'read:vehicle:all', 'read:vehicle'])]
    #[Assert\NotBlank(NULL, message: "La formule est requis.")]
    private ?string $wording = null;

    /**
     * @var string|null slug of wording
     */
    #[ORM\Column(length: 255)]
    #[ApiProperty(identifier: true)]
    #[Groups(['read:formula:all', 'read:formula', 'read:trainer:all', 'read:trainer', 'read:candidate', 'write:candidate',  'read:vehicle:all', 'read:vehicle'])]
    private ?string $slug = null;

    #[ORM\Column]
    #[Groups(['read:formula:all', 'read:formula', 'write:formula'])]
    #[Assert\NotBlank(NULL, message: "Le prix est requis.")]
    #[Assert\GreaterThan(value: 0)]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(['read:formula:all', 'read:formula', 'write:formula'])]
    #[Assert\NotBlank(NULL, message: "L'age est requis.")]
    #[Assert\GreaterThanOrEqual(value: 16)]
    private ?int $minAge = 16;

    #[ORM\ManyToMany(targetEntity: Trainer::class, inversedBy: 'formulas', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: "TrainerHasFormula")]
    private Collection $trainers;

    #[ORM\OneToMany(mappedBy: 'formula', targetEntity: FormulaCodeDate::class)]
    private Collection $formulaCodeDates;

    #[ORM\OneToMany(mappedBy: 'formula', targetEntity: CourseDates::class)]
    private Collection $courseDates;

    #[ORM\OneToOne(mappedBy: 'formula', targetEntity: DrivingFormula::class)]
    #[Groups(['read:formula:all', 'read:formula', 'write:formula'])]
    private ?DrivingFormula $drivingFormula = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trainers = new ArrayCollection();
        $this->formulaCodeDates = new ArrayCollection();
        $this->courseDates = new ArrayCollection();
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
    public function getWording(): ?string
    {
        return $this->wording;
    }

    /**
     * @param string $wording
     * @return $this
     */
    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $wording
     * @return $this
     */
    public function setSlug(string $wording): self
    {
        $divider = '-';

        // replace non letter or digits by divider
        $wording = preg_replace('~[^\pL\d]+~u', $divider, $wording);

        // transliterate
        $wording = iconv('utf-8', 'us-ascii//TRANSLIT', $wording);

        // remove unwanted characters
        $wording = preg_replace('~[^-\w]+~', '', $wording);

        // trim
        $wording = trim($wording, $divider);

        // remove duplicate divider
        $wording = preg_replace('~-+~', $divider, $wording);

        // lowercase
        $wording = strtolower($wording);

        $this->slug = $wording;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return $this
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    /**
     * @param int $minAge
     * @return $this
     */
    public function setMinAge(int $minAge): self
    {
        $this->minAge = $minAge;

        return $this;
    }

    /**
     * @return Collection<int, Trainer>
     */
    public function getTrainers(): Collection
    {
        return $this->trainers;
    }

    /**
     * @param Trainer $trainer
     * @return $this
     */
    public function addTrainer(Trainer $trainer): self
    {
        if (!$this->trainers->contains($trainer)) {
            $this->trainers->add($trainer);
        }

        return $this;
    }

    /**
     * @param Trainer $trainer
     * @return $this
     */
    public function removeTrainer(Trainer $trainer): self
    {
        $this->trainers->removeElement($trainer);

        return $this;
    }

    /**
     * @return Collection<int, FormulaCodeDate>
     */
    public function getFormulaCodeDates(): Collection
    {
        return $this->formulaCodeDates;
    }

    /**
     * @param FormulaCodeDate $formulaCodeDate
     * @return $this
     */
    public function addFormulaCodeDate(FormulaCodeDate $formulaCodeDate): self
    {
        if (!$this->formulaCodeDates->contains($formulaCodeDate)) {
            $this->formulaCodeDates->add($formulaCodeDate);
            $formulaCodeDate->setFormula($this);
        }

        return $this;
    }

    /**
     * @param FormulaCodeDate $formulaCodeDate
     * @return $this
     */
    public function removeFormulaCodeDate(FormulaCodeDate $formulaCodeDate): self
    {
        if ($this->formulaCodeDates->removeElement($formulaCodeDate)) {
            // set the owning side to null (unless already changed)
            if ($formulaCodeDate->getFormula() === $this) {
                $formulaCodeDate->setFormula(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CourseDates>
     */
    public function getCourseDates(): Collection
    {
        return $this->courseDates;
    }

    /**
     * @param CourseDates $courseDate
     * @return $this
     */
    public function addCourseDate(CourseDates $courseDate): self
    {
        if (!$this->courseDates->contains($courseDate)) {
            $this->courseDates->add($courseDate);
            $courseDate->setFormula($this);
        }

        return $this;
    }

    /**
     * @param CourseDates $courseDate
     * @return $this
     */
    public function removeCourseDate(CourseDates $courseDate): self
    {
        if ($this->courseDates->removeElement($courseDate)) {
            // set the owning side to null (unless already changed)
            if ($courseDate->getFormula() === $this) {
                $courseDate->setFormula(null);
            }
        }

        return $this;
    }

    /**
     * @return DrivingFormula|null
     */
    public function getDrivingFormula(): ?DrivingFormula
    {
        return $this->drivingFormula;
    }

    /**
     * @param DrivingFormula|null $drivingFormula
     * @return $this
     */
    public function setDrivingFormula(?DrivingFormula $drivingFormula): self
    {
        // unset the owning side of the relation if necessary
        if ($drivingFormula === null && $this->drivingFormula !== null) {
            $this->drivingFormula->setFormula(null);
        }

        // set the owning side of the relation if necessary
        if ($drivingFormula !== null && $drivingFormula->getFormula() !== $this) {
            $drivingFormula->setFormula($this);
        }

        $this->drivingFormula = $drivingFormula;

        return $this;
    }
}
