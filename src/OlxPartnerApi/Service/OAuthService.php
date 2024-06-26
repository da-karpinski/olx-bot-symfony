<?php

namespace App\OlxPartnerApi\Service;

use App\OlxPartnerApi\Adapter\OAuthAdapter;
use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class OAuthService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly string $olxPartnerClientId,
        private readonly string $olxPartnerClientSecret,
        private readonly TranslatorInterface $translator
    )
    {
        $this->model = OlxPartnerApi::OAuth;
    }

    public function __invoke(): string
    {

        $response = $this->apiClient->request(
            $this->model->method(),
            $this->olxPartnerApiUrl . $this->model->uri(),
            ['json' => OAuthAdapter::adapt($this->olxPartnerClientId, $this->olxPartnerClientSecret)]
        );

        if (!isset($response[$this->model->dataKey()])) {
            throw new AccessDeniedHttpException($this->translator->trans('error.olx-partner-api.oauth-error', [], 'error'));
        }

        return $response[$this->model->dataKey()];
    }

}