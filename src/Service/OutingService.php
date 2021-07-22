<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\User;
use App\Repository\OutingStateRepository;

class OutingService {
    private OutingStateRepository $outingStateRepository;

    public function __construct(OutingStateRepository $outingStateRepository) {
        $this->outingStateRepository = $outingStateRepository;
    }

    /* Service */

    public function setOutingState(Outing &$outing, string $label) {
        $state = $this->outingStateRepository->findOneBy(['label' => $label]);
        $outing->setState($state);
    }

    public function isOutingDraft(Outing $outing): bool {
        return (strtoupper($outing->getState()->getLabel()) === 'DRAFT');
    }

    public function isOutingPublic(Outing $outing): bool {
        return (!in_array(strtoupper($outing->getState()->getLabel()), ['DRAFT', 'ARCHIVED']));
    }

    public function isOutingPending(Outing $outing): bool {
        return (in_array(strtoupper($outing->getState()->getLabel()), ['OPEN', 'PENDING']));
    }

    public function isUserOrganizer(User $user, Outing $outing): bool {
        return ($user === $outing->getOrganizer());
    }
}
