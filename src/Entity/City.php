<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * 
 * @author Marin Taverniers
 */
class City {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank(message="The postal code is required.")
     * @Assert\Length(
     *      max=15,
     *      maxMessage="The postal code is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^\d*$/", message="The postal code contains illegal characters.")
     */
    private $postalCode;

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
     * @ORM\OneToMany(targetEntity=Location::class, mappedBy="city")
     */
    private $locations;

    public function __construct() {
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getPostalCode(): ?string {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getLocations(): Collection {
        return $this->locations;
    }

    public function addLocation(Location $location): self {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCity($this);
        }
        return $this;
    }

    public function removeLocation(Location $location): self {
        if ($this->locations->removeElement($location)) {
            if ($location->getCity() === $this) {
                $location->setCity(null);
            }
        }
        return $this;
    }
}
