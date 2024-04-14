<?php

namespace App\ApiResource\CategoryAttribute\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Worker;
use App\Security\Voter\CategoryAttributeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetCategoryAttributesForWorkerProvider implements ProviderInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategoryAttributeVoter $voter,
        private readonly Security $security,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $worker = $this->em->getRepository(Worker::class)->find($uriVariables['id']);

        if(!$worker) {
            throw new AccessDeniedHttpException($this->translator->trans('entity.does-not-exist', [], 'validators'));
        }

        $token = $this->security->getToken();

        if($this->voter->vote($token, $worker, [CategoryAttributeVoter::CATEGORY_ATTRIBUTE_GET]) !== VoterInterface::ACCESS_GRANTED){
            throw new AccessDeniedHttpException($this->translator->trans('access.denied', [], 'validators'));
        }

        return $worker->getCategoryAttributes();
    }
}