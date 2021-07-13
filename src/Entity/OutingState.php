<?php

namespace App\Entity;

use App\Repository\OutingStateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OutingStateRepository::class)
 * 
 * @author Marin Taverniers
 */
class OutingState {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The label is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The label is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[- a-zA-Z]*$/", message="The label contains illegal characters.")
     */
    private $label;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function setLabel(string $label): self {
        $this->label = $label;
        return $this;
    }
}
