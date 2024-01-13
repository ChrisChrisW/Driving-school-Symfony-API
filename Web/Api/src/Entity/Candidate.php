<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PostCandidateController;
use App\PersistProcessor\CandidatePersistProcessor;
use App\Repository\CandidateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
#[UniqueEntity(fields: "email", message: "Tu ne peux pas créer le même email")]
#[GetCollection(normalizationContext: ['groups' => ['read:candidate']])]
#[Get(
    uriTemplate: '/candidate/{email}',
    openapiContext: [
       // 'summary' => 'Create a resource',
       'description' => "Get candidate information's with email",
       'requestBody' => [
           'content' => [
               'application/json' => [
                   'schema' => [
                       'type' => 'object',
                       'properties' => ['email' => ['type' => 'string']]
                   ],
                   'example' => ['email' => 'project@ensiie.fr']
               ]
           ]
       ]
    ],
    normalizationContext: ['groups' => ['read:candidate']]
)]
#[Post(
    uriTemplate: '/candidate',
    controller: PostCandidateController::class,
    openapiContext: [
        'description' => 'Post candidate informations',
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'type' => 'object',
                    'schema' => [
                        'properties' => [
                            'email' => ['type' => 'string'],
                            'address' => ['type' => 'string'],
                            'age' => ['type' => 'int'],
                            'lastName' => ['type' => 'string'],
                            'firstName' => ['type' => 'string'],
                        ]
                    ],
                    'example' => [
                        'email' => 'project@ensiie.fr',
                        'address' => 'adresse',
                        'age' => 23,
                        'lastName' => 'Nom de famille',
                        'firstName' => 'Prénom'
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:candidate']],
    itemUriTemplate: '/candidate'
)]
#[Put(uriTemplate: '/candidate/{email}', processor : CandidatePersistProcessor::class)]
#[Patch(uriTemplate: '/candidate/formula/{email}', openapiContext: [
    'description' => 'Post candidate formula code or driving',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'formuleCode' => ['type' => 'boolean'],
                        'formuleCodeIllimite' => ['type' => 'boolean'],
                        'startDate' => ['type' => 'string', "format" => "date"],
                        'formuleConduite' => ['type' => "string"]
                    ]
                ],
                'example' => [
                    'formuleCode' => true,
                    'formuleCodeIllimite' => true,
                    'formuleConduite' => "slug",
                    'message' => "soit formuleCode ou formuleCodeIllimite ou formuleConduite si vous prenez les formules code, il faut renseigner la date de début"
                ]
            ]
        ]
    ]
], processor : CandidatePersistProcessor::class)]
// #[Delete(uriTemplate: '/candidate/{email}')]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:candidate', 'write:candidate', 'read:courseDate:all', 'read:formula:all'])]
    #[Assert\NotBlank(NULL, message: "L'email est requis.")]
    #[Assert\Email(NULL, "L'email est incorrect.")]
    #[ApiProperty(identifier: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:candidate', 'write:candidate'])]
    #[Assert\NotBlank(NULL, message: "L'adresse est requis.")]
    private ?string $address = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:candidate', 'write:candidate'])]
    #[Assert\NotBlank(NULL, message: "L'age est requis.")]
    #[Assert\GreaterThanOrEqual(value: 16)]
    private ?int $age = null;

    #[ORM\OneToOne(mappedBy: 'candidate', cascade: ['persist', 'remove'])]
    #[Groups(['read:candidate', 'write:candidate', 'read:courseDate:all'])]
    private ?User $identity = null;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: FormulaCodeDate::class)]
    #[Groups(['read:candidate', 'write:candidate'])]
    private Collection $formulaCodeDates;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: CourseDates::class)]
    private Collection $courseDates;

    #[ORM\ManyToMany(targetEntity: DrivingFormula::class, inversedBy: 'candidates')]
    #[Groups(['read:candidate', 'write:candidate'])]
    private Collection $drivingFormulas;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->formulaCodeDates = new ArrayCollection();
        $this->courseDates = new ArrayCollection();
        $this->drivingFormulas = new ArrayCollection();
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return $this
     */
    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getIdentity(): ?User
    {
        return $this->identity;
    }

    /**
     * @param User|null $identity
     * @return $this
     */
    public function setIdentity(?User $identity): self
    {
        // unset the owning side of the relation if necessary
        if ($identity === null && $this->identity !== null) {
            $this->identity->setCandidate(null);
        }

        // set the owning side of the relation if necessary
        if ($identity !== null && $identity->getCandidate() !== $this) {
            $identity->setCandidate($this);
        }

        $this->identity = $identity;

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
            $formulaCodeDate->setCandidate($this);
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
            if ($formulaCodeDate->getCandidate() === $this) {
                $formulaCodeDate->setCandidate(null);
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
            $courseDate->setCandidate($this);
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
            if ($courseDate->getCandidate() === $this) {
                $courseDate->setCandidate(null);
            }
        }

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
        }

        return $this;
    }

    /**
     * @param DrivingFormula $drivingFormula
     * @return $this
     */
    public function removeDrivingFormula(DrivingFormula $drivingFormula): self
    {
        $this->drivingFormulas->removeElement($drivingFormula);

        return $this;
    }
}
