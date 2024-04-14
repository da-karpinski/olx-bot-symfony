<?php

namespace App\Security\Voter;

use App\ApiResource\User\Dto\UserCreateInput;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const USER_GET = 'USER_GET';
    const USER_CREATE = 'USER_CREATE';
    const USER_EDIT = 'USER_EDIT';
    const USER_DELETE = 'USER_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [
            self::USER_GET,
            self::USER_CREATE,
            self::USER_EDIT,
            self::USER_DELETE
        ]);

        if(!is_null($subject)){
            $supportsSubject = in_array(get_class($subject), [
                User::class,
                UserCreateInput::class
            ]);
        }else{
            $supportsSubject = false;
        }

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::USER_GET, self::USER_CREATE, self::USER_EDIT, self::USER_DELETE => $this->hasAccess($subject),
            default => false,
        };
    }

    private function hasAccess($subject): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if($this->security->isGranted('ROLE_USER')) {
            return $subject === $this->security->getUser();
        }

        return false;
    }
}