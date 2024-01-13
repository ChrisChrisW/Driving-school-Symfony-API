<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PostCandidateController;
use App\Controller\PostTrainerController;
use App\PersistProcessor\CandidatePersistProcessor;
use App\PersistProcessor\TrainerPersistProcessor;
use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: TrainerRepository::class)]
#[UniqueEntity(fields: "numSs", message: "Tu ne peux pas créer le même numSs")]
#[GetCollection(normalizationContext: ['groups' => ['read:trainer:all']])]
#[ApiFilter(SearchFilter::class, properties: ['courseDates.startDate' => 'ipartial'])]
#[ApiFilter(SearchFilter::class, properties: ['numSs' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['formulas.slug' => 'exact'])]
#[Get(
    uriTemplate: '/trainer/{numSs}',
    openapiContext: [
        'description' => "Get trainer information's with numSs",
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => ['numSs' => ['type' => 'integer']]
                    ],
                    'example' => ['numSs' => 'nuémro de sécurité social']
                ]
            ]
        ]
    ],
    normalizationContext: ['groups' => ['read:trainer']]
)]
#[Post(
    uriTemplate: '/trainer',
    controller: PostTrainerController::class,
    openapiContext: [
        'description' => 'Post trainer informations',
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'numSs' => ['type' => 'string'],
                            'lastName' => ['type' => 'string'],
                            'firstName' => ['type' => 'string'],
                            'formulaSlug' => ['type' => 'string'],
                        ]
                    ],
                    'example' => [
                        'numSs' => 'nuémro de sécurité social',
                        'lastName' => 'Nom de famille',
                        'firstName' => 'Prénom',
                        'formulaSlug' => 'slug',
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:trainer']],
    itemUriTemplate: '/trainer'
)]
#[Put(uriTemplate: '/trainer/{numSs}', processor : TrainerPersistProcessor::class)]
#[Put(uriTemplate: '/trainer/formula/add/{numSs}', openapiContext: [
    'description' => 'Post trainer informations',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'numSs' => ['type' => 'string'],
                        'formulaSlug' => ['type' => 'string'],
                    ]
                ],
                'example' => [
                    'numSs' => 'nuémro de sécurité social',
                    'formulaSlug' => 'slug',
                ]
            ]
        ]
    ]
], processor: TrainerPersistProcessor::class)]
#[Put(uriTemplate: '/trainer/formula/remove/{numSs}', openapiContext: [
    'description' => 'Post trainer informations',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'numSs' => ['type' => 'string'],
                        'removeFormula' => ['type' => 'string'],
                    ]
                ],
                'example' => [
                    'numSs' => 'nuémro de sécurité social',
                    'removeFormula' => 'slug',
                ]
            ]
        ]
    ]
], processor: TrainerPersistProcessor::class)]
// #[Delete(uriTemplate: '/trainer/{numSs}')]
class Trainer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['read:trainer:all', 'read:trainer', 'write:trainer'])]
    #[Assert\NotBlank(NULL, message: "Le numéro Ss est requis.")]
    #[Assert\Length(NULL, 13, 15)]
    #[ApiProperty(identifier: true)]
    private ?string $numSs = null;

    #[ORM\OneToOne(mappedBy: 'trainer', cascade: ['persist', 'remove'])]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'read:trainer:all', 'read:trainer', 'write:trainer'])]
    private ?User $identity = null;

    #[ORM\OneToMany(mappedBy: 'trainer', targetEntity: CourseDates::class)]
    #[Groups(['read:trainer:all', 'read:trainer', 'write:trainer'])]
    private Collection $courseDates;

    #[ORM\ManyToMany(targetEntity: Formula::class, mappedBy: 'trainers')]
    #[Groups(['read:trainer:all', 'read:trainer', 'write:trainer'])]
    // #[ORM\JoinColumn(nullable: false)]
    private Collection $formulas;

    /**
     *  Constructor
     */
    public function __construct()
    {
        $this->courseDates = new ArrayCollection();
        $this->formulas = new ArrayCollection();
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
    public function getNumSs(): ?string
    {
        return $this->numSs;
    }

    /**
     * @param string $numSs
     * @return $this
     */
    public function setNumSs(string $numSs): self
    {
        $this->numSs = $numSs;

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
            $this->identity->setTrainer(null);
        }

        // set the owning side of the relation if necessary
        if ($identity !== null && $identity->getTrainer() !== $this) {
            $identity->setTrainer($this);
        }

        $this->identity = $identity;

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
            $courseDate->setTrainer($this);
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
            if ($courseDate->getTrainer() === $this) {
                $courseDate->setTrainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formula>
     */
    public function getFormulas(): Collection
    {
        return $this->formulas;
    }

    /**
     * @param Formula $formula
     * @return $this
     */
    public function addFormula(Formula $formula): self
    {
        if (!$this->formulas->contains($formula)) {
            $this->formulas->add($formula);
            $formula->addTrainer($this);
        }

        return $this;
    }

    /**
     * @param Formula $formula
     * @return $this
     */
    public function removeFormula(Formula $formula): self
    {
        if ($this->formulas->removeElement($formula)) {
            $formula->removeTrainer($this);
        }

        return $this;
    }
}
