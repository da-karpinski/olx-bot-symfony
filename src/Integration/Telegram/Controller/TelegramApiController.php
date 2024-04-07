<?php

namespace App\Integration\Telegram\Controller;

use App\Integration\Telegram\Service\TelegramApiWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TelegramApiController extends AbstractController
{

    public function __construct(
        private readonly TelegramApiWebhookService $telegramApiWebhookService
    )
    {
    }

    #[Route(path: '/integration/telegram/webhook', methods: ['POST'])]
    public function integrationTelegramWebhook(Request $request): JsonResponse
    {
        try{
            $this->telegramApiWebhookService->handleUpdate($request);
            return new JsonResponse(['status' => 'ok']);
        }catch (\Throwable $e){
            return new JsonResponse(['error' => $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
    }

}