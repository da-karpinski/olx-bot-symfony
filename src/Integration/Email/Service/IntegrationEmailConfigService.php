<?php

namespace App\Integration\Email\Service;

use ApiPlatform\Validator\ValidatorInterface;
use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\Entity\Integration;
use App\Integration\Email\Dto\IntegrationEmailConfigCreate;
use App\Integration\Email\Dto\IntegrationEmailConfigUpdate;
use App\Integration\Email\Entity\EmailIntegration;
use App\Integration\Email\Validator\EmailArrayValidator;
use App\Integration\IntegrationConfigInterface;
use Doctrine\ORM\EntityManagerInterface;

class IntegrationEmailConfigService implements IntegrationConfigInterface
{

    public const INTEGRATION_CODE = 'INTEGRATION_EMAIL';

    public function __construct(
        private readonly ValidatorInterface  $validator,
        private readonly EmailArrayValidator $emailArrayValidator,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function getService(): string
    {
        return self::class;
    }

    public function onCreate(IntegrationCreateInput $input, Integration $integration): EmailIntegration
    {
        $inputDto = $this->adaptToDto(IntegrationEmailConfigCreate::class, $input->integrationConfig);

        $emailIntegration = new EmailIntegration();
        $emailIntegration->setIntegration($integration)
            ->setRecipientAddress($inputDto->recipientAddress)
            ->setCcAddresses($inputDto->ccAddresses)
            ->setBccAddresses($inputDto->bccAddresses);

        return $emailIntegration;
    }

    public function onUpdate(IntegrationUpdateInput $input, Integration $integration): EmailIntegration
    {
        $inputDto = $this->adaptToDto(IntegrationEmailConfigUpdate::class, $input->integrationConfig);
        $emailIntegration = $this->em->getRepository(EmailIntegration::class)->findOneBy(['integration' => $integration]);

        if(!empty($inputDto->recipientAddress)) {
            $emailIntegration->setRecipientAddress($inputDto->recipientAddress);
        }

        if(!is_null($inputDto->ccAddresses)) {
            $emailIntegration->setCcAddresses($inputDto->ccAddresses);
        }

        if(!is_null($inputDto->bccAddresses)) {
            $emailIntegration->setBccAddresses($inputDto->bccAddresses);
        }

        return $emailIntegration;
    }

    public function onDelete(Integration $integration): void
    {
        $this->em->remove($integration->getIntegrationConfig());
    }

    private function adaptToDto(string $dto, $input): IntegrationEmailConfigCreate|IntegrationEmailConfigUpdate
    {
        $configDto = new $dto;
        $configDto->recipientAddress = $input['recipientAddress'] ?? null;
        $configDto->ccAddresses = $input['ccAddresses'] ?? null;
        $configDto->bccAddresses = $input['bccAddresses'] ?? null;

        $this->validator->validate($configDto);

        $this->emailArrayValidator->validate($configDto->ccAddresses, 'integrationConfig.ccAddresses');
        $this->emailArrayValidator->validate($configDto->bccAddresses, 'integrationConfig.bccAddresses');

        return $configDto;
    }
}