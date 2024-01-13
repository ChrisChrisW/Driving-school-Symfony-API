<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; // Symfony's built-in constraints

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
// #[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:trainer:all', 'read:candidate', 'read:trainer', 'read:courseDate:all'])]
    #[Assert\NotBlank(NULL, "Le nom de famille est requis.")]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:trainer:all', 'read:candidate', 'read:trainer', 'read:courseDate:all'])]
    #[Assert\NotBlank(NULL, "Le prénom est requis.")]
    private ?string $firstName = null;

    #[ORM\OneToOne(inversedBy: 'identity', cascade: ['persist', 'remove'])]
    private ?Candidate $candidate = null;

    #[ORM\OneToOne(inversedBy: 'identity', cascade: ['persist', 'remove'])]
    private ?Trainer $trainer = null;

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
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

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
}
