<?php

namespace App\Integration\Email\Service;

use App\Entity\Notification;
use App\Entity\Integration;
use App\Entity\Worker;
use App\Entity\Offer;
use App\Integration\Email\Entity\EmailIntegration;
use App\Integration\IntegrationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class IntegrationEmailService implements IntegrationInterface
{

    public function __construct(
        private readonly Environment $twig,
        private readonly string $dashboardUrl,
        private readonly string $contactHelpEmail,
        private readonly string $mailerSendFrom,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer
    )
    {
    }

    public function getIntegrationCode(): string
    {
        return 'INTEGRATION_EMAIL';
    }

    public function prepareNotification(array $offers, Worker $worker, Integration $integration): ?Notification
    {

        if($integration->getIntegrationType()->getIntegrationCode() !== $this->getIntegrationCode()){
            return null;
        }

        $notification = new Notification();
        $notification->setWorker($worker);
        $notification->setIntegration($integration);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setOffer($offers[0]);
        $notification->setTitle('New offers found by OLX Bot!');

        $notification->setMessage($this->twig->render('@Integration/Email/Template/email-notification-en.html.twig', [
            'user' => $worker->getUser(),
            'dashboardUrl' => $this->dashboardUrl,
            'offers' => $offers,
            'contactEmail' => $this->contactHelpEmail,
        ]));

        return $notification;
    }

    public function sendNotification(Notification $notification): void
    {
        $integrationType = $notification->getIntegration()->getIntegrationType();

        if($integrationType->getIntegrationCode() !== $this->getIntegrationCode()){
            return;
        }

        if(!$integrationType->isEnabled()){
            //TODO: log send attempt
            return;
        }

        $config = $this->em->getRepository(EmailIntegration::class)->findOneBy(['integration' => $notification->getIntegration()]);

        $email = (new Email())
            ->from($this->mailerSendFrom)
            ->to($config->getRecipientAddress())
            ->subject($notification->getTitle())
            ->html($notification->getMessage());

        if(!empty($config->getCcAddresses())) {
            foreach ($config->getCcAddresses() as $cc) {
                $email->AddCc($cc);
            }
        }

        if(!empty($config->getBccAddresses())) {
            foreach ($config->getBccAddresses() as $bcc) {
                $email->AddBcc($bcc);
            }
        }

        //TODO: log exception
        $this->mailer->send($email);
    }
}