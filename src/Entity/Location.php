<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass = LocationRepository::class)
 * 
 * @author Marin Taverniers
 */
class Location {

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
     * @Assert\Regex(pattern = "/^[-' \w]*$/", message = "The name contains illegal characters.")
     */
    private $name;

    /**
     * @ORM\Column(type = "float")
     * @Assert\NotBlank(message = "The latitude is required.")
     * @Assert\Range(
     *      invalidMessage = "The latitude is not a valid number.",
     *      min = -90,
     *      max = 90,
     *      notInRangeMessage = "The latitude is not between {{ min }} and {{ max }}."
     * )
     */
    private $latitude;

    /**
     * @ORM\Column(type = "float")
     * @Assert\NotBlank(message = "The longitude is required.")
     * @Assert\Range(
     *      invalidMessage = "The longitude is not a valid number.",
     *      min = -180,
     *      max = 180,
     *      notInRangeMessage = "The longitude is not between {{ min }} and {{ max }}."
     * )
     */
    private $longitude;

    /**
     * @ORM\Column(type = "string", length = 50)
     * @Assert\NotBlank(message = "The street is required.")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "The street is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern = "/^[-' \w]*$/", message = "The street contains illegal characters.")
     */
    private $street;

    /**
     * @ORM\ManyToOne(targetEntity = City::class, inversedBy = "locations")
     * @ORM\JoinColumn(nullable = false)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity = Outing::class, mappedBy = "location")
     */
    private $outings;

    public function __construct() {
        $this->outings = new ArrayCollection();
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

    public function getLatitude(): ?float {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self {
        $this->longitude = $longitude;
        return $this;
    }

    public function getStreet(): ?string {
        return $this->street;
    }

    public function setStreet(string $street): self {
        $this->street = $street;
        return $this;
    }

    public function getCity(): ?City {
        return $this->city;
    }

    public function setCity(?City $city): self {
        $this->city = $city;
        return $this;
    }

    public function getOutings(): Collection {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setLocation($this);
        }
        return $this;
    }

    public function removeOuting(Outing $outing): self {
        if ($this->outings->removeElement($outing)) {
            if ($outing->getLocation() === $this) {
                $outing->setLocation(null);
            }
        }
        return $this;
    }
}
