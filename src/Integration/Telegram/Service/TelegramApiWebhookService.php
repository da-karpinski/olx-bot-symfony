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
use Symfony\Contracts\Translation\TranslatorInterface;

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
        private readonly TranslatorInterface $translator
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

        $this->translator->setLocale($update['from']['language_code'] ?? 'en');

        if(!empty($update['entities'])){
            $command = substr($update['text'], $update['entities'][0]['offset'], $update['entities'][0]['length']);

            $telegramIntegration = $this->entityManager->getRepository(TelegramIntegration::class)->findOneBy(['chatId' => $update['chat']['id']]);

            if($telegramIntegration?->getOtp()){
                $message = $this->translator->trans('webhook.handle-update.not-fully-configured', ['{{ otpCode }}' => $telegramIntegration->getOtp()], 'integration-telegram-message');
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
                $message = $this->translator->trans('webhook.create-user.linked-enabled', [], 'integration-telegram-message');
            }else{
                $message = $this->translator->trans('webhook.create-user.linked-disabled', [], 'integration-telegram-message');
            }

            $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
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

            $message = $this->translator->trans('webhook.create-user.send-otp', ['{{ otpCode }}' => $telegramIntegration->getOtp()], 'integration-telegram-message');
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

                $message = $this->translator->trans('webhook.enable-user.enabled', [], 'integration-telegram-message');
            }else{
                $message = $this->translator->trans('webhook.enable-user.already-enabled', [], 'integration-telegram-message');
            }
            $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
        }
    }

    private function disableUser(array $update, ?TelegramIntegration $telegramIntegration): void
    {

        if($telegramIntegration) {
            if($telegramIntegration->isActive()) {
                $telegramIntegration->setActive(false);
                $this->entityManager->persist($telegramIntegration);
                $this->entityManager->flush();

                $message = $this->translator->trans('webhook.disable-user.disabled', [], 'integration-telegram-message');
            }else{
                $message = $this->translator->trans('webhook.disable-user.already-disabled', [], 'integration-telegram-message');
            }
            $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
        }
    }

    private function handleUnsupportedCommand(array $update): void
    {
        $this->integrationLogger->warning(sprintf('Unsupported command: %s. Request message: %s', $update['text'], json_encode($update)));
        $message = $this->translator->trans('webhook.unknown-command', [], 'integration-telegram-message');
        $this->sendMessage(['chat_id' => $update['chat']['id'], 'text' => $message]);
    }

    private function sendMessage(array $payload)
    {
        $model = TelegramApi::sendMessage;
        $response = $this->apiClient->request(
            TelegramApi::sendMessage->method(),
            $this->apiUrl . str_replace('{token}', $this->botToken, TelegramApi::sendMessage->uri()),
            [
                'headers' => $model->headers($this->botToken),
                'json' => $payload
            ]
        );

        if(!$response[$model->dataKey()]){
            throw new IntegrationException('[Telegram Webhook]: Error sending message. Response: ' . json_encode($response), Level::Critical, $this->integrationLogger);
        }
    }

}