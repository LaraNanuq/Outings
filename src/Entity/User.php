<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields = {"alias, email"}, message="The {{ label }} is already used.")
 * 
 * @author Marin Taverniers
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(message="The username is required.")
     * @Assert\Length(
     *      min=5,
     *      max=50,
     *      minMessage="The username is too short (minimum {{ limit }} characters).",
     *      maxMessage="The username is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[-\w]*$/", message="The username contains illegal characters.")
     */
    private $alias;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The last name is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The last name is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[-' a-zA-Z]*$/", message="The last name contains illegal characters.")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The first name is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The first name is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^[-' a-zA-Z]*$/", message="The first name contains illegal characters.")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\NotBlank(message="The email is required.")
     * @Assert\Length(
     *      max=250,
     *      maxMessage="The email is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Email(message="The email is not valid.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The password is required.")
     * @Assert\Length(
     *      max=50,
     *      maxMessage="The password is too long (maximum {{ limit }} characters)."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Length(
     *      max=15,
     *      maxMessage="The phone number is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Regex(pattern="/^\d*$/", message="The phone number contains illegal characters.")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(
     *      max=1000,
     *      maxMessage="The picture url is too long (maximum {{ limit }} characters)."
     * )
     * @Assert\Url(message="The picture url is not valid.")
     */
    private $pictureUrl;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="bool", message="The disabled value is not valid.")
     */
    private $isDisabled;

    /**
     * @ORM\Column(type="json")
     * @Assert\Json(message = "The roles array is not valid.")
     */
    private $roles = [];

    public function getId(): ?int {
        return $this->id;
    }

    public function getAlias(): ?string {
        return $this->alias;
    }

    public function setAlias(string $alias): self {
        $this->alias = $alias;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    public function getPictureUrl(): ?string {
        return $this->pictureUrl;
    }

    public function setPictureUrl(?string $pictureUrl): self {
        $this->pictureUrl = $pictureUrl;
        return $this;
    }

    public function getIsDisabled(): ?bool {
        return $this->isDisabled;
    }

    public function setIsDisabled(bool $isDisabled): self {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    public function getRoles(): array {
        $this->roles[] = 'ROLE_USER';
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     */
    public function getUserIdentifier(): string {
        return (string) $this->alias;
    }

    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string {
        return $this->getUserIdentifier();
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     */
    public function getSalt(): ?string {
        return null;
    }
}
