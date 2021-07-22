<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass = CampusRepository::class)
 * 
 * @author Marin Taverniers
 */
class Campus {

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
     * @ORM\OneToMany(targetEntity = User::class, mappedBy = "campus")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity = Outing::class, mappedBy = "campus")
     */
    private $outings;

    public function __construct() {
        $this->users = new ArrayCollection();
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

    public function getUsers(): Collection {
        return $this->users;
    }

    public function addUser(User $user): self {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCampus($this);
        }
        return $this;
    }

    public function removeUser(User $user): self {
        if ($this->users->removeElement($user)) {
            if ($user->getCampus() === $this) {
                $user->setCampus(null);
            }
        }
        return $this;
    }

    public function getOutings(): Collection {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setCampus($this);
        }
        return $this;
    }

    public function removeOuting(Outing $outing): self {
        if ($this->outings->removeElement($outing)) {
            if ($outing->getCampus() === $this) {
                $outing->setCampus(null);
            }
        }
        return $this;
    }
}
