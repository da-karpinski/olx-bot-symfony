<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EntityExists extends Constraint
{
    public $message = 'entity.does-not-exist';

    public $mode = 'strict';

    /**
     * @var ?string $identifier Product indentifier
     */
    public ?string $identifier;

    /**
     * @var ?string $entityClass Entity class
     */
    public ?string $entityClass;

    #[HasNamedArguments]
    public function __construct(?string $identifier, ?string $entityClass, ?array $skippable =[], array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        $this->identifier = $identifier;
        $this->entityClass = $entityClass;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}