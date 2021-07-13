<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OutingRepository::class)
 * 
 * @author Marin Taverniers
 */
class Outing {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The name is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The name is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[-' \w]*$/", message="The name contains illegal characters.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank(message="The description is required.")
     * @Assert\Length(
     *      min=5,
     *      max=1000,
     *      minMessage="The description is too short (minimum {{ limit }} characters).",
     *      maxMessage="The description is too long (maximum {{ limit }} characters)."
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="The date is required.")
     * @Assert\DateTime(message="The date is not valid.")
     * @Assert\GreaterThanOrEqual(value="today", message="The date is not valid.")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="The duration is required.")
     * @Assert\Type(type="int", message="The duration is not valid.")
     * @Assert\Positive(message="The duration is not valid.")
     */
    private $duration;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="The registration closing date is required.")
     * @Assert\Date(message="The registration closing date is not valid.")
     * @Assert\GreaterThan(propertyPath="date", message="The registration closing date is not valid.")
     */
    private $registrationClosingDate;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="The maximum registrants number is required.")
     * @Assert\Type(type="int", message="The maximum registrants number is not valid.")
     * @Assert\Positive(message="The maximum registrants number is not valid.")
     */
    private $maxRegistrants;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(
     *      min=5,
     *      max=1000,
     *      minMessage="The cancellation reason is too short (minimum {{ limit }} characters).",
     *      maxMessage="The cancellation reason is too long (maximum {{ limit }} characters)."
     * )
     */
    private $cancellationReason;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="organizedOutings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=OutingState::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="registeredOutings")
     */
    private $registrants;

    public function __construct() {
        $this->registrants = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;
        return $this;
    }

    public function getDuration(): ?int {
        return $this->duration;
    }

    public function setDuration(int $duration): self {
        $this->duration = $duration;
        return $this;
    }

    public function getRegistrationClosingDate(): ?\DateTimeInterface {
        return $this->registrationClosingDate;
    }

    public function setRegistrationClosingDate(\DateTimeInterface $registrationClosingDate): self {
        $this->registrationClosingDate = $registrationClosingDate;
        return $this;
    }

    public function getMaxRegistrants(): ?int {
        return $this->maxRegistrants;
    }

    public function setMaxRegistrants(int $maxRegistrants): self {
        $this->maxRegistrants = $maxRegistrants;
        return $this;
    }

    public function getCancellationReason(): ?string {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): self {
        $this->cancellationReason = $cancellationReason;
        return $this;
    }

    public function getOrganizer(): ?User {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self {
        $this->organizer = $organizer;
        return $this;
    }

    public function getCampus(): ?Campus {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self {
        $this->campus = $campus;
        return $this;
    }

    public function getLocation(): ?Location {
        return $this->location;
    }

    public function setLocation(?Location $location): self {
        $this->location = $location;
        return $this;
    }

    public function getState(): ?OutingState {
        return $this->state;
    }

    public function setState(?OutingState $state): self {
        $this->state = $state;
        return $this;
    }

    public function getRegistrants(): Collection {
        return $this->registrants;
    }

    public function addRegistrant(User $registrant): self {
        if (!$this->registrants->contains($registrant)) {
            $this->registrants[] = $registrant;
        }
        return $this;
    }

    public function removeRegistrant(User $registrant): self {
        $this->registrants->removeElement($registrant);
        return $this;
    }
}
