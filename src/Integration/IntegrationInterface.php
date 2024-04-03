<?php

namespace App\Integration;

use App\Entity\Integration;
use App\Entity\Notification;
use App\Entity\Offer;
use App\Entity\Worker;

interface IntegrationInterface
{
    /**
     * @param Offer[] $offers
     * @param Worker $worker
     * @param Integration $integration
     * @return Notification|null
     */
    public function prepareNotification(array $offers, Worker $worker, Integration $integration): ?Notification;
    public function sendNotification(Notification $notification): void;
    public function getIntegrationCode(): string;
}