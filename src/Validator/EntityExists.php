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
     * @var ?bool $shouldExist raise error if entity does not exist; default is true
     */
    public ?bool $shouldExist;

    /**
     * @var ?string $identifier Product identifier
     */
    public ?string $identifier;

    /**
     * @var ?string $entityClass Entity class
     */
    public ?string $entityClass;

    #[HasNamedArguments]
    public function __construct(?string $identifier, ?string $entityClass, ?bool $shouldExist = true, array $groups = null, mixed $payload = null)
    {

        if(!$shouldExist){
            $this->message = 'entity.already-exists';
        }

        parent::__construct([], $groups, $payload);
        $this->identifier = $identifier;
        $this->entityClass = $entityClass;
        $this->shouldExist = $shouldExist;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}