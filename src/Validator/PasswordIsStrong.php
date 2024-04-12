<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordIsStrong extends Constraint
{
    public $message = 'user.password.policy';

    public $mode = 'strict';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}