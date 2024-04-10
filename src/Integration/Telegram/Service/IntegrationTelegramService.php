<?php

namespace App\Integration\Telegram\Service;

use App\Entity\Notification;
use App\Entity\Integration;
use App\Entity\Offer;
use App\Entity\Worker;
use App\Exception\IntegrationException;
use App\Integration\IntegrationInterface;
use App\Integration\Telegram\Entity\TelegramIntegration;
use App\Integration\Telegram\HttpClient;
use App\Integration\Telegram\Model\TelegramApi;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IntegrationTelegramService implements IntegrationInterface
{

    public const INTEGRATION_CODE = 'INTEGRATION_TELEGRAM';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpClient $apiClient,
        #[Autowire(env: 'TELEGRAM_BOT_TOKEN')]
        private readonly string $botToken,
        #[Autowire(env: 'TELEGRAM_BOT_API_URL')]
        private readonly string $apiUrl,
        private readonly LoggerInterface $integrationLogger
        private readonly LoggerInterface $integrationLogger,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function prepareNotifications(array $offers, Worker $worker, Integration $integration): Notification|array
    {
        $notifications = [];

        $this->translator->setLocale($integration->getLocaleCode());

        /** @var Offer $offer */
        foreach ($offers as $offer) {
            $notification = new Notification();
            $notification->setWorker($worker);
            $notification->setIntegration($integration);
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setOffer($offer);
            $notification->setMessage(sprintf(
                "New offer found by OLX Bot!\nTitle: %s\nDescription: %s\nPrice: %s %s\nLink: %s",
                $offer->getTitle(),
                strip_tags($offer->getDescription()),
                $offer->getPrice(),
                $offer->getPriceCurrency(),
                $offer->getUrl()
            ));

            $notifications[] = $notification;
        }

        return $notifications;
    }

    public function sendNotification(Notification $notification): void
    {
        $integrationType = $notification->getIntegration()->getIntegrationType();

        if(!$integrationType->isEnabled()){
            $this->integrationLogger->info('[Telegram Bot API] Notification was not sent because integration was disabled.');
            return;
        }

        $config = $this->em->getRepository(TelegramIntegration::class)->findOneBy(['integration' => $notification->getIntegration()]);

        $payload = [
            'chat_id' => $config->getChatId(),
            'text' => $notification->getMessage()
        ];

        $model = TelegramApi::sendMessage;

        $response = $this->apiClient->request(
            $model->method(),
            $this->apiUrl . str_replace('{token}', $this->botToken, $model->uri()),
            [
                'headers' => $model->headers(),
                'json' => $payload
            ]
        );

        if(!$response[$model->dataKey()]) {
            throw new IntegrationException(sprintf('[Telegram Bot API] Sending notification error. Response body: %s', $response), Level::Error, $this->integrationLogger);
        }

    }
}