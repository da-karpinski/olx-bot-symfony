<?php

namespace App\Controller;

use App\Payload\Request\WorkerRequestPayload;
use App\Service\WorkerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/worker')]
class WorkerController extends AbstractController
{
    public function __construct(
        private readonly WorkerService $workerService,
        private readonly SerializerInterface $serializationService,
        private readonly ValidatorInterface $validator,
    )
    {
    }

    #[Route(path: '', name: 'api_worker_create', methods: ['POST'])]
    public function createWorker(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $payload = $this->serializationService->deserialize($request->getContent(), WorkerRequestPayload::class, 'json');

        $validator = $this->validator->validate($payload);
        if (count($validator) > 0) {
            return $this->json($validator, Response::HTTP_BAD_REQUEST);
        }

        $response = $this->serializationService->serialize($this->workerService->createWorker($payload), 'json', ['groups' => 'worker:write']);
        return new Response($response);
    }
}
