<?php

namespace App\Entity;

use App\Repository\OutingStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass = OutingStateRepository::class)
 * 
 * @author Marin Taverniers
 */
class OutingState {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 50)
     * @Assert\NotBlank(message = "The label is required.")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "The label is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern = "/^[- a-zA-Z]*$/", message = "The label contains illegal characters.")
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity = Outing::class, mappedBy = "state")
     */
    private $outings;

    public function __construct() {
        $this->outings = new ArrayCollection();
    }

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

    public function getOutings(): Collection {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setState($this);
        }
        return $this;
    }

    public function removeOuting(Outing $outing): self {
        if ($this->outings->removeElement($outing)) {
            if ($outing->getState() === $this) {
                $outing->setState(null);
            }
        }
        return $this;
    }

    public function getFriendlyName(): string {
        switch ($this->label) {
            case 'DRAFT':
                $friendlyName = 'Brouillon';
                break;
            case 'OPEN':
                $friendlyName = 'Ouverte';
                break;
            case 'PENDING':
                $friendlyName = 'En attente';
                break;
            case 'ONGOING':
                $friendlyName = 'En cours';
                break;
            case 'FINISHED':
                $friendlyName = 'Terminée';
                break;
            case 'CANCELED':
                $friendlyName = 'Annulée';
                break;
            case 'ARCHIVED':
                $friendlyName = 'Archivée';
                break;
            default:
                $friendlyName = null;
        }
        return $friendlyName;
    }
}
