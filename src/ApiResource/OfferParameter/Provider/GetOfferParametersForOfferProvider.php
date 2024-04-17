<?php

namespace App\ApiResource\OfferParameter\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetOfferParametersForOfferProvider implements ProviderInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $offer = $this->em->getRepository(Offer::class)->find($uriVariables['id']);

        if(!$offer) {
            throw new AccessDeniedHttpException($this->translator->trans('entity.does-not-exist', [], 'validators'));
        }

        if(!$this->security->isGranted('ROLE_ADMIN') && $offer->getWorker()->getUser() !== $this->security->getUser()) {
            throw new AccessDeniedHttpException($this->translator->trans('access.denied', [], 'validators'));
        }

        return $offer->getOfferParameters();

    }
}