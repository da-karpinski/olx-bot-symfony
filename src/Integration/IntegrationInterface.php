<?php

namespace App\Integration;

use App\Entity\Notification;
use App\Entity\Offer;
use App\Entity\Worker;

interface IntegrationInterface
{
    public function prepareNotification(Offer $offer, Worker $worker): Notification;
    public function sendNotification(Notification $notification): void;
}