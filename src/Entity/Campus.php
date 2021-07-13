<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 * 
 * @author Marin Taverniers
 */
class Campus {

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
}
