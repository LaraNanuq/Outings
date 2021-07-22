<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass = OutingRepository::class)
 * 
 * @author Marin Taverniers
 */
class Outing {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 50)
     * @Assert\NotBlank(message = "The name is required.")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "The name is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern = "/^[-' \w\p{L}]*$/u", message = "The name contains illegal characters.")
     */
    private $name;

    /**
     * @ORM\Column(type = "string", length = 1000)
     * @Assert\NotBlank(message = "The description is required.")
     * @Assert\Length(
     *      min = 5,
     *      max = 1000,
     *      minMessage = "The description is too short (minimum {{ limit }} characters).",
     *      maxMessage = "The description is too long (maximum {{ limit }} characters)."
     * )
     */
    private $description;

    // TODO: @Assert\DateTime(message = "The date is not a valid date.")
    /**
     * @ORM\Column(type = "datetime")
     * @Assert\NotBlank(message = "The date is required.")
     * @Assert\Range(
     *      invalidDateTimeMessage = "The date is not a valid date.",
     *      min = "now + 1 day",
     *      max = "now + 2 years",
     *      notInRangeMessage = "The date is not between tomorrow and the next 2 years.",
     * )
     */
    private $date;

    /**
     * @ORM\Column(type = "integer")
     * @Assert\NotBlank(message = "The duration is required.")
     * @Assert\Type(type = "int", message = "The duration is not a valid number.")
     * @Assert\Range(
     *      min = 15,
     *      max = 10080,
     *      notInRangeMessage = "The duration is not between {{ min }} and {{ max }}."
     * )
     */
    private $duration;

    // TODO: @Assert\Date(message = "The registration closing date is not a valid date.")
    /**
     * @ORM\Column(type = "date")
     * @Assert\NotBlank(message = "The registration closing date is required.")
     * @Assert\Range(
     *      invalidDateTimeMessage = "The registration closing date is not a valid date.",
     *      min = "now + 1 day",
     *      maxPropertyPath = "date",
     *      notInRangeMessage = "The registration closing date is not between tomorrow and the outing date.",
     * )
     */
    private $registrationClosingDate;

    /**
     * @ORM\Column(type = "integer")
     * @Assert\NotBlank(message = "The maximum registrants number is required.")
     * @Assert\Type(type = "int", message = "The maximum registrants number is not a valid number.")
     * @Assert\Range(
     *      min = 1,
     *      max = 10000,
     *      notInRangeMessage = "The maximum registrants number is not between {{ min }} and {{ max }}."
     * )
     */
    private $maxRegistrants;

    /**
     * @ORM\Column(type = "string", length = 1000, nullable = true)
     * @Assert\Length(
     *      min = 5,
     *      max = 1000,
     *      minMessage = "The cancellation reason is too short (minimum {{ limit }} characters).",
     *      maxMessage = "The cancellation reason is too long (maximum {{ limit }} characters)."
     * )
     */
    private $cancellationReason;

    /**
     * @ORM\ManyToOne(targetEntity = User::class, inversedBy = "organizedOutings")
     * @ORM\JoinColumn(nullable = false)
     */
    private $organizer;

    /**
     * @ORM\ManyToOne(targetEntity = Campus::class, inversedBy = "outings")
     * @ORM\JoinColumn(nullable = false)
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity = Location::class, inversedBy = "outings")
     * @ORM\JoinColumn(nullable = false)
     * // Validates the embed location form in the outing creation form
     * @Assert\Valid()
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity = OutingState::class, inversedBy = "outings")
     * @ORM\JoinColumn(nullable = false)
     */
    private $state;

    /**
     * @ORM\ManyToMany(targetEntity = User::class, inversedBy = "outings")
     * @JoinTable(name = "outings_registrants")
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

    public function setDate(?\DateTimeInterface $date): self {
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

    public function setRegistrationClosingDate(?\DateTimeInterface $registrationClosingDate): self {
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
