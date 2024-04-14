<?php

namespace App\Security\Voter;

use App\Entity\CategoryAttribute;
use App\Entity\Worker;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryAttributeVoter extends Voter
{

    const CATEGORY_ATTRIBUTE_GET = 'CATEGORY_ATTRIBUTE_GET';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [
            self::CATEGORY_ATTRIBUTE_GET,

        ]);

        if(!is_null($subject)){
            $supportsSubject = in_array(get_class($subject), [
                CategoryAttribute::class,
                Worker::class
            ]);
        }else{
            $supportsSubject = false;
        }

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::CATEGORY_ATTRIBUTE_GET => $this->canRead($subject),
            default => false,
        };
    }

    private function canRead($subject): bool
    {
        /* @var Worker $subject */

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if($this->security->isGranted('ROLE_USER')) {
            return $subject->getUser() === $this->security->getUser();
        }

        return false;
    }
}