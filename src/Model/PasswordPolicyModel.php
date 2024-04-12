<?php

namespace App\Model;

class PasswordPolicyModel
{

    /** @var int Minimum length of the password */
    public const MIN_LENGTH = 12;

    /** @var int Maximum length of the password */
    public const MAX_LENGTH = 64;

    /** @var bool Must contain at least one digit */
    public const MUST_CONTAIN_DIGIT = true;

    /** @var bool Must contain at least one special character */
    public const MUST_CONTAIN_SPECIAL_CHARACTER = true;

    /** @var bool Must contain at least one uppercase letter */
    public const MUST_CONTAIN_UPPERCASE_LETTER = true;

}