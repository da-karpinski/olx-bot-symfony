<?php

namespace App\Integration\Telegram\Adapter;

use App\Entity\Offer;
use Symfony\Contracts\Translation\TranslatorInterface;

class OfferToNotificationMessageAdapter
{
    public static function adapt(Offer $offer, TranslatorInterface $translator): string
    {
        return
            "<strong><ins>" . $translator->trans('notification.message.title', [], 'integration-telegram-message') . "</ins></strong>\n\n" .
            "<strong>" . $translator->trans('notification.message.offer.title', [], 'integration-telegram-message') . ":</strong> " . $offer->getTitle() . "\n\n" .
            "<strong>" . $translator->trans('notification.message.offer.price', [], 'integration-telegram-message') . ":</strong> " . $offer->getPrice() . " " . $offer->getPriceCurrency() . "\n\n" .
            "<strong>" . $translator->trans('notification.message.offer.link', [], 'integration-telegram-message') . ":</strong> <a href=\"" . $offer->getUrl() . "\">" . $offer->getUrl() . "</a>\n\n" .
            "<strong>" . $translator->trans('notification.message.offer.description', [], 'integration-telegram-message') . ":</strong>\n" .
            "<pre>" . strip_tags($offer->getDescription()) . "</pre>"
            ;
    }

}