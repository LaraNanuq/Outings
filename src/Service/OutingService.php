<?php

namespace App\Service;

use App\Entity\Outing;
use App\Repository\OutingStateRepository;

/**
 * @author Marin Taverniers
 */
class OutingService {
    public const STATE_DRAFT = 'DRAFT';
    public const STATE_OPEN = 'OPEN';
    public const STATE_PENDING = 'PENDING';
    public const STATE_ONGOING = 'ONGOING';
    public const STATE_FINISHED = 'FINISHED';
    public const STATE_CANCELED = 'CANCELED';
    public const STATE_ARCHIVED = 'ARCHIVED';
    private OutingStateRepository $outingStateRepository;

    public function __construct(OutingStateRepository $outingStateRepository) {
        $this->outingStateRepository = $outingStateRepository;
    }

    public function setOutingState(Outing &$outing, string $label): void {
        $state = $this->outingStateRepository->findOneBy(['label' => $label]);
        if ($state) {
            $outing->setState($state);
        }
    }

    public function isOutingPrivate(Outing $outing): bool {
        return ($this->getOutingState($outing) === self::STATE_DRAFT);
    }

    public function isOutingPublic(Outing $outing): bool {
        return (!in_array($this->getOutingState($outing), [self::STATE_DRAFT, self::STATE_ARCHIVED]));
    }

    public function isOutingOpenForRegistration(Outing $outing): bool {
        return ($this->getOutingState($outing) === self::STATE_OPEN);
    }

    public function isOutingUpcoming(Outing $outing): bool {
        return (in_array($this->getOutingState($outing), [self::STATE_OPEN, self::STATE_PENDING]));
    }

    public function isOutingCanceled(Outing $outing): bool {
        return ($this->getOutingState($outing) === self::STATE_CANCELED);
    }

    private function getOutingState(Outing $outing): string {
        return (strtoupper($outing->getState()->getLabel()));
    }
}
