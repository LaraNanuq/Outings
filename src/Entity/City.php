<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;

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
}
