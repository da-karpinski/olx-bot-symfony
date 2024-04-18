<?php

namespace App\Security\Voter;

use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\Entity\Integration;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IntegrationVoter extends Voter
{

    const INTEGRATION_GET = 'INTEGRATION_GET';
    const INTEGRATION_CREATE = 'INTEGRATION_CREATE';
    const INTEGRATION_EDIT = 'INTEGRATION_EDIT';
    const INTEGRATION_DELETE = 'INTEGRATION_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [
            self::INTEGRATION_GET,
            self::INTEGRATION_CREATE,
            self::INTEGRATION_EDIT,
            self::INTEGRATION_DELETE
        ]);

        if(!is_null($subject)){
            $supportsSubject = in_array(get_class($subject), [
                Integration::class,
                IntegrationCreateInput::class,
                IntegrationUpdateInput::class
            ]);
        }else{
            $supportsSubject = false;
        }

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::INTEGRATION_GET, self::INTEGRATION_CREATE, self::INTEGRATION_EDIT, self::INTEGRATION_DELETE => $this->hasAccess($subject),
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