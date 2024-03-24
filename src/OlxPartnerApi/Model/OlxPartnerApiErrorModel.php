<?php

namespace App\OlxPartnerApi\Model;

/**
 * @see https://developer.olx.pl/api/doc#section/Troubleshooting
 */
class OlxPartnerApiErrorModel
{
    public const ERROR_KEY = 'error';
    public const TITLE_KEY = 'title';
    public const DETAIL_KEY = 'detail';
    public const ERROR_DESCRIPTION_KEY = 'error_description';

    public static function getErrorTitle(array $response) : string
    {
        if(is_array($response[self::ERROR_KEY]) and isset($response[self::ERROR_KEY][self::TITLE_KEY])) {
            return $response[self::ERROR_KEY][self::TITLE_KEY];
        }else{
            return $response[self::ERROR_KEY];
        }
    }

    public static function getErrorDetail(array $response) : string
    {
        if(is_array($response[self::ERROR_KEY]) and isset($response[self::ERROR_KEY][self::DETAIL_KEY])) {
            return $response[self::ERROR_KEY][self::DETAIL_KEY];
        }else{
            return $response[self::ERROR_DESCRIPTION_KEY];
        }
    }

}