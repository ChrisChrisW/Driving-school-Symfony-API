<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Odm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Odm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PostCourseDatesController;
use App\PersistProcessor\CourseDatesPersistProcessor;
use App\Repository\CourseDatesRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: CourseDatesRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['isConfirm' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['isConfirm' => 'exact', 'isAchieve' => 'exact', 'trainer.numSs' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['isConfirm' => 'exact', 'isAchieve' => 'exact', 'candidate.email' => 'exact'])]
#[GetCollection(uriTemplate: '/courseDates', normalizationContext: ['groups' => ['read:courseDate:all']])]
#[Get(uriTemplate: '/courseDate/{id}', normalizationContext: ['groups' => ['read:courseDate']])]
#[Post(
    uriTemplate: '/courseDate',
    controller: PostCourseDatesController::class,
    openapiContext: [
        'description' => 'Post courseDate informations',
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'startDate' => ['type' => 'string', "format" => "date-time"],
                            'endDate' => ['type' => 'string', "format" => "date-time"],
                            'trainerNumSs' => ['type' => 'string'],
                            'candidateEmail' => ['type' => 'string'],
                            'formulaSlug' => ['type' => 'string'],
                            'vehicleNumPlate' => ['type' => 'int']
                        ],
                        'example' => [
                            'startDate' => "Date de début de séance",
                            'endDate' => "Date de fin de séance",
                            'trainerNumSs' => 'Numéro Ss',
                            'candidateEmail' => 'email',
                            'formulaSlug' => 'formule en slug',
                            'vehicleNumPlate' => 'int de 13 à 15'
                        ]
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:courseDate']],
    itemUriTemplate: '/courseDate'
)]
#[Post(
    uriTemplate: '/courseDate/code',
    controller: PostCourseDatesController::class,
    openapiContext: [
        'description' => 'Post courseDate informations',
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'startDate' => ['type' => 'string', "format" => "date-time"],
                            'endDate' => ['type' => 'string', "format" => "date-time"],
                            'candidateEmail' => ['type' => 'string']
                        ],
                        'example' => [
                            'startDate' => "Date de début de séance",
                            'endDate' => "Date de fin de séance",
                            'candidateEmail' => 'email'
                        ]
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:courseDate']],
    itemUriTemplate: '/courseDate'
)]
#[Patch(uriTemplate: '/courseDate/achieve/{id}', openapiContext: [
    'description' => 'CourseDate achieves',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'isAchieve' => ['type' => 'boolean']
                    ],
                    'example' => [
                        'isAchieve' => true
                    ]
                ]
            ]
        ]
    ]
], processor : CourseDatesPersistProcessor::class)]
#[Patch(uriTemplate: '/courseDate/delete/{id}', openapiContext: [
    'description' => 'CourseDate confirms',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'isConfirm' => ['type' => 'boolean']
                    ],
                    'example' => [
                        'isConfirm' => false
                    ]
                ]
            ]
        ]
    ]
], processor : CourseDatesPersistProcessor::class)]
#[Patch(uriTemplate: '/courseDate/absenceJustification/{id}', openapiContext: [
    'description' => 'CourseDate absence justification',
    'requestBody' => [
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'absenceJustification' => ['type' => 'boolean']
                    ],
                    'example' => [
                        'absenceJustification' => false
                    ]
                ]
            ]
        ]
    ]
], processor : CourseDatesPersistProcessor::class)]
class CourseDates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    #[Groups(['read:courseDate:all', 'write:courseDate'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate', 'read:trainer:all', 'read:trainer', 'write:trainer'])]
    private ?bool $isAchieve = false;

    #[ORM\Column]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate', 'read:trainer:all', 'read:trainer', 'write:trainer'])]
    private ?bool $isConfirm = false;

    #[ORM\Column]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate', 'read:trainer:all', 'read:trainer', 'write:trainer'])]
    private ?bool $isRedirectedToAnotherTrainer = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate', 'read:trainer:all', 'read:trainer'])]
    #[Assert\Type(DateTimeInterface::class)]
    #[Assert\NotBlank(NULL, message: "La date de début est requise.")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate', 'read:trainer:all', 'read:trainer'])]
    #[Assert\Type(DateTimeInterface::class)]
    #[Assert\NotBlank(NULL, message: "La date de fin est requise.")]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'courseDates')]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate'])]
    #[Assert\NotBlank(NULL, message: "Le trainer est requis.")]
    private ?Trainer $trainer = null;

    #[ORM\ManyToOne(inversedBy: 'courseDates')]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate'])]
    #[Assert\NotBlank(NULL, message: "Le candidat est requis.")]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'courseDates')]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate'])]
    private ?Formula $formula = null;

    #[ORM\ManyToOne(inversedBy: 'courseDate')]
    #[Groups(['read:courseDate:all', 'read:courseDate', 'write:courseDate'])]
    // #[Assert\NotBlank(NULL, message: "Le vehicule est requis.")]
    private ?Vehicle $vehicle = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getIsAchieve(): ?bool
    {
        return $this->isAchieve;
    }

    /**
     * @param bool $isAchieve
     * @return $this
     */
    public function setIsAchieve(bool $isAchieve): self
    {
        $this->isAchieve = $isAchieve;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsConfirm(): ?bool
    {
        return $this->isConfirm;
    }

    /**
     * @param bool $isConfirm
     * @return $this
     */
    public function setIsConfirm(bool $isConfirm): self
    {
        $this->isConfirm = $isConfirm;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsRedirectedToAnotherTrainer(): ?bool
    {
        return $this->isRedirectedToAnotherTrainer;
    }

    /**
     * @param bool $isRedirectedToAnotherTrainer
     * @return $this
     */
    public function setIsRedirectedToAnotherTrainer(bool $isRedirectedToAnotherTrainer): self
    {
        $this->isRedirectedToAnotherTrainer = $isRedirectedToAnotherTrainer;

        return $this;
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
     * @return Trainer|null
     */
    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    /**
     * @param Trainer|null $trainer
     * @return $this
     */
    public function setTrainer(?Trainer $trainer): self
    {
        $this->trainer = $trainer;

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
     * @return Vehicle|null
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle|null $vehicle
     * @return $this
     */
    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
