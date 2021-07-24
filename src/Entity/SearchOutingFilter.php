<?php

namespace App\Entity;

use DateTimeInterface;

/**
 * @author Marin Taverniers
 */
class SearchOutingFilter {
    private $campus;
    private $name;
    private $minDate;
    private $maxDate;
    private $isUserOrganizer;
    private $isUserRegistrant;
    private $isUserNotRegistrant;
    private $isFinished;
    private $page;
    private $itemsPerPage;

    public function getCampus(): ?Campus {
        return $this->campus;
    }

    public function setCampus(Campus $campus): self {
        $this->campus = $campus;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getMinDate(): ?DateTimeInterface {
        return $this->minDate;
    }

    public function setMinDate(?DateTimeInterface $minDate): self {
        $this->minDate = $minDate;
        return $this;
    }

    public function getMaxDate(): ?DateTimeInterface {
        return $this->maxDate;
    }

    public function setMaxDate(?DateTimeInterface $maxDate): self {
        $this->maxDate = $maxDate;
        return $this;
    }

    public function isUserOrganizer(): ?bool {
        return $this->isUserOrganizer;
    }

    public function setIsUserOrganizer(bool $isUserOrganizer): self {
        $this->isUserOrganizer = $isUserOrganizer;
        return $this;
    }

    public function isUserRegistrant(): ?bool {
        return $this->isUserRegistrant;
    }

    public function setIsUserRegistrant(bool $isUserRegistrant): self {
        $this->isUserRegistrant = $isUserRegistrant;
        return $this;
    }

    public function isUserNotRegistrant(): ?bool {
        return $this->isUserNotRegistrant;
    }

    public function setIsUserNotRegistrant(bool $isUserNotRegistrant): self {
        $this->isUserNotRegistrant = $isUserNotRegistrant;
        return $this;
    }

    public function isFinished(): ?bool {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): self {
        $this->isFinished = $isFinished;
        return $this;
    }

    public function getPage(): ?int {
        return $this->page;
    }

    public function setPage(int $page): self {
        $this->page = $page;
        return $this;
    }

    public function getItemsPerPage(): ?int {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): self {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }
}
