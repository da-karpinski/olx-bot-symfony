<?php

namespace App\Integration\Telegram\Service;

use App\Exception\IntegrationException;
use App\Integration\Telegram\Entity\TelegramIntegration;
use App\Integration\Telegram\HttpClient;
use App\Integration\Telegram\Model\TelegramApi;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;

class TelegramApiWebhookService
{
    public function __construct(
        #[Autowire(env: 'TELEGRAM_BOT_TOKEN')]
        private readonly string $botToken,
        #[Autowire(env: 'TELEGRAM_BOT_API_URL')]
        private readonly string $apiUrl,
        #[Autowire(env: 'TELEGRAM_BOT_WEBHOOK_SECRET_TOKEN')]
        private readonly string $secretToken,
        private readonly LoggerInterface $integrationLogger,
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClient $apiClient,
    )
    {
    }

    public function handleUpdate(Request $request): void
    {
        $requestSecretToken = $request->headers->get('x-telegram-bot-api-secret-token');

        if(!$requestSecretToken or $requestSecretToken !== $this->secretToken){
            throw new IntegrationException('[Telegram Webhook]: Empty or invalid secret token.', Level::Error, $this->integrationLogger);
        }

        $update = json_decode($request->getContent(), true)["message"];

        if(empty($update)){
            throw new IntegrationException('[Telegram Webhook]: Empty update.', Level::Error, $this->integrationLogger);
        }

        if(!empty($update['entities'])){
            $command = substr($update['text'], $update['entities'][0]['offset'], $update['entities'][0]['length']);

            $telegramIntegration = $this->entityManager->getRepository(TelegramIntegration::class)->findOneBy(['chatId' => $update['chat']['id']]);

            if($telegramIntegration?->getOtp()){
                $message = 'Hold on! You are not completely integrated yet. First, please enter the OTP code "' . $telegramIntegration->getOtp() . '" in your integration settings. When you are done, I will send you a confirmation message here.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
                return;
            }

            match ($command){
                '/start' => $this->createUser($update, $telegramIntegration),
                '/enable' => $this->enableUser($update, $telegramIntegration),
                '/disable' => $this->disableUser($update, $telegramIntegration),
                default => $this->handleUnsupportedCommand($update),
            };
        }
    }

    private function createUser(array $update, ?TelegramIntegration $telegramIntegration): void
    {
        if($telegramIntegration){
            if($telegramIntegration->isActive()){
                $message = 'Hold on! Your account is already linked and active. If you want to disable notifications, use /disable command.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }else{
                $message = 'Hold on! Your account is already linked, but not active. If you want to enable notifications, use /enable command.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }
        }

        if(!$telegramIntegration){
            $telegramIntegration = new TelegramIntegration();
            $telegramIntegration->setChatId($update['chat']['id']);
            $telegramIntegration->setChatType($update['chat']['type']);
            $telegramIntegration->setOtp(rand(1000, 9999) . '-' . rand(1000, 9999));
            $telegramIntegration->setActive(false);
            $telegramIntegration->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($telegramIntegration);
            $this->entityManager->flush();

            $message = 'Hi, nice to meet you! To complete the integration, please enter the OTP code: "' . $telegramIntegration->getOtp() . '". When you are done, I will send you a confirmation message here.';
            $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
        }
    }

    private function enableUser(array $update, ?TelegramIntegration $telegramIntegration): void
    {
        if($telegramIntegration) {
            if(!$telegramIntegration->isActive()) {
                $telegramIntegration->setActive(true);
                $this->entityManager->persist($telegramIntegration);
                $this->entityManager->flush();

                $message = 'Notifications enabled successfully. You will receive notifications from now on. If you want to disable notifications, use /disable command.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }else{
                $message = 'Notifications are still enabled. You will continue to receive notifications, stay tuned!';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }
        }
    }

    private function disableUser(array $update, ?TelegramIntegration $telegramIntegration): void
    {

        if($telegramIntegration) {
            if($telegramIntegration->isActive()) {
                $telegramIntegration->setActive(false);
                $this->entityManager->persist($telegramIntegration);
                $this->entityManager->flush();

                $message = 'Notifications disabled successfully. You will not receive notifications from now on. If you want to enable notifications, use /enable command.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }else{
                $message = 'Notifications are already disabled. You will not receive notifications, but you can enable them anytime using /enable command.';
                $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
            }
        }
    }

    private function handleUnsupportedCommand(array $update): void
    {
        $this->integrationLogger->warning(sprintf('Unsupported command: %s. Request message: %s', $update['text'], json_encode($update)));
        $message = "Hmm, I don't understand this command. Please use: \n /start to link your account; \n /enable to enable notifications; \n /disable to disable notifications.";
        $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
    }

    private function sendMessage(array $payload)
    {
        $model = TelegramApi::sendMessage;
        $response = $this->apiClient->request(
            TelegramApi::sendMessage->method(),
            $this->apiUrl . str_replace('{token}', $this->botToken, TelegramApi::sendMessage->uri()),
            [
                'json' => $payload
            ]
        );

        if(!$response[$model->dataKey()]){
            throw new IntegrationException('[Telegram Webhook]: Error sending message. Response: ' . json_encode($response), Level::Critical, $this->integrationLogger);
        }
    }

}