<?php

namespace App\Security\Voter;

use App\ApiResource\Worker\Dto\WorkerCreateInput;
use App\ApiResource\Worker\Dto\WorkerUpdateInput;
use App\Entity\Worker;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkerVoter extends Voter
{

    const WORKER_GET = 'WORKER_GET';
    const WORKER_CREATE = 'WORKER_CREATE';
    const WORKER_EDIT = 'WORKER_EDIT';
    const WORKER_DELETE = 'WORKER_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [
            self::WORKER_GET,
            self::WORKER_CREATE,
            self::WORKER_EDIT,
            self::WORKER_DELETE
        ]);

        if(!is_null($subject)){
            $supportsSubject = in_array(get_class($subject), [
                Worker::class,
                WorkerCreateInput::class,
                WorkerUpdateInput::class
            ]);
        }else{
            $supportsSubject = false;
        }

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::WORKER_GET, self::WORKER_CREATE, self::WORKER_EDIT, self::WORKER_DELETE => $this->hasAccess($subject),
            default => false,
        };
    }

    private function hasAccess($subject): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if($this->security->isGranted('ROLE_USER')) {
            return $subject->getUser() === $this->security->getUser();
        }

        return false;
    }
}