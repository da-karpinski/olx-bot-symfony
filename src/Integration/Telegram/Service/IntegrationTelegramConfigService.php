<?php

namespace App\Integration\Telegram\Service;

use ApiPlatform\Validator\ValidatorInterface;
use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\Entity\Integration;
use App\Exception\IntegrationException;
use App\Integration\IntegrationConfigInterface;
use App\Integration\Telegram\Dto\IntegrationTelegramConfigCreate;
use App\Integration\Telegram\Entity\TelegramIntegration;
use App\Integration\Telegram\HttpClient;
use App\Integration\Telegram\Model\TelegramApi;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntegrationTelegramConfigService implements IntegrationConfigInterface
{

    public const INTEGRATION_CODE = 'INTEGRATION_TELEGRAM';

    public function __construct(
        private readonly ValidatorInterface  $validator,
        private readonly EntityManagerInterface $em,
        private readonly HttpClient $apiClient,
        #[Autowire(env: 'TELEGRAM_BOT_TOKEN')]
        private readonly string $botToken,
        #[Autowire(env: 'TELEGRAM_BOT_API_URL')]
        private readonly string $apiUrl,
        private readonly LoggerInterface $integrationLogger,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function getService(): string
    {
        return self::class;
    }

    public function onCreate(IntegrationCreateInput $input, Integration $integration): TelegramIntegration
    {
        $inputDto = $this->adaptToDto($input->integrationConfig);

        $telegramIntegration = $this->em->getRepository(TelegramIntegration::class)->findOneBy(['otp' => $inputDto->otpCode]);
        if(!$telegramIntegration) {
            throw new NotFoundHttpException(
                $this->translator->trans('configuration.otp-not-found', [], 'integration-telegram-message'),
            );
        }

        $telegramIntegration
            ->setIntegration($integration)
            ->setOtp(null)
            ->setActive(true);

        $this->translator->setLocale($integration->getLocaleCode());
        $message = $this->translator->trans('configuration.success', ['{{ integrationName }}' => $integration->getName()], 'integration-telegram-message');
        $this->sendConfirmationMessage($telegramIntegration, $message);

        return $telegramIntegration;
    }

    public function onUpdate(IntegrationUpdateInput $input, Integration $integration): TelegramIntegration
    {
        throw new UnprocessableEntityHttpException(
            $this->translator->trans('configuration.update-not-allowed', [], 'integration-telegram-message')
        );
    }

    public function onDelete(Integration $integration): void
    {
        $telegramIntegration = $integration->getIntegrationConfig();
        $this->em->remove($telegramIntegration);

        $this->translator->setLocale($integration->getLocaleCode());
        $message = $this->translator->trans('configuration.delete', ['{{ integrationName }}' => $integration->getName()], 'integration-telegram-message');

        try{
            $this->sendConfirmationMessage($telegramIntegration, $message);
        }catch (\Exception $e){
            $this->integrationLogger->error('[Telegram Configuration]: Error sending message. Error: ' . $e->getMessage());
        }
    }

    private function adaptToDto($input): IntegrationTelegramConfigCreate
    {
        $configDto = new IntegrationTelegramConfigCreate();
        $configDto->otpCode = $input["otpCode"] ?? null;

        $this->validator->validate($configDto);

        return $configDto;
    }

    private function sendConfirmationMessage(TelegramIntegration $telegramIntegration, string $message): void
    {
        $model = TelegramApi::sendMessage;

        $response = $this->apiClient->request(
            TelegramApi::sendMessage->method(),
            $this->apiUrl . str_replace('{token}', $this->botToken, TelegramApi::sendMessage->uri()),
            [
                'headers' => $model->headers(),
                'json' => ['chat_id' => $telegramIntegration->getChatId(), 'text' => $message]
            ]
        );

        if(!$response[$model->dataKey()]){
            throw new IntegrationException('[Telegram Configuration]: Error sending message. Response: ' . json_encode($response), Level::Critical, $this->integrationLogger);
        }
    }
}