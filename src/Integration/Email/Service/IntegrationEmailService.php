<?php

namespace App\Integration\Email\Service;

use App\Entity\Notification;
use App\Entity\Integration;
use App\Entity\Worker;
use App\Exception\IntegrationException;
use App\Integration\Email\Entity\EmailIntegration;
use App\Integration\IntegrationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class IntegrationEmailService implements IntegrationInterface
{

    public const INTEGRATION_CODE = 'INTEGRATION_EMAIL';

    public function __construct(
        private readonly Environment $twig,
        private readonly string $dashboardUrl,
        private readonly string $contactHelpEmail,
        private readonly string $mailerSendFrom,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $integrationLogger,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function prepareNotifications(array $offers, Worker $worker, Integration $integration): Notification|array
    {

        $notification = new Notification();
        $notification->setWorker($worker);
        $notification->setIntegration($integration);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setOffer($offers[0]);

        $this->translator->setLocale($integration->getLocaleCode());

        $notification->setTitle($this->translator->trans('notification.title', [], 'integration-email-message'));

        $notification->setMessage($this->twig->render('@Integration/Email/Template/email-notification.html.twig', [
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

        if(!$integrationType->isEnabled()){
            $this->integrationLogger->info('[Email integration] Notification was not sent because integration was disabled.');
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

        try{
            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new IntegrationException(sprintf('[Email integration] Notification was not sent because of an error: %s', $e->getMessage()), Level::Error, $this->integrationLogger);
        }
    }
}