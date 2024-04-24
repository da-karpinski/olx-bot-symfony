<?php

namespace App\Integration\Email\Validator;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailArrayValidator
{

    public function __construct(
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function validate(?array $addresses, string $propertyPath): true
    {
        if ($addresses === null) {
            return true;
        }

        $constraintViolations = new ConstraintViolationList();
        $index = 0;
        foreach ($addresses as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $propertyPathWithIndex = $propertyPath . '[' . $index . ']';
                $constraintViolations->add(
                    new ConstraintViolation(
                        $this->translator->trans('This value is not a valid email address.', [], 'validators'),
                        '', [], null, $propertyPathWithIndex, '', null
                    )
                );
            }
            $index++;
        }

        if ($constraintViolations->count() > 0) {
            throw new ValidationException($constraintViolations);
        }

        return true;
    }

}