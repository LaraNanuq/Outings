<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 * 
 * @author Marin Taverniers
 */
class Location {

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
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="The latitude is required.")
     * @Assert\Type(type="float", message="The latitude is not valid.")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="The longitude is required.")
     * @Assert\Type(type="float", message="The longitude is not valid.")
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The street is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The street is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[-' \w]*$/", message="The street contains illegal characters.")
     */
    private $street;

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
}
