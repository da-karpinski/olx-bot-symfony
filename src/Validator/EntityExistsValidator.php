<?php

namespace App\Validator;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ){}

    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                $this->check($v, $constraint);
            }
        } else {
            $this->check($value, $constraint);
        }
    }

    private function check($value, Constraint $constraint) : void
    {

        try {
            $entity = $this->em->getRepository($constraint->entityClass)->findOneBy([$constraint->identifier => $value]);
        } catch(Exception $e) {
            $this->context
                ->buildViolation('entity.not-callable')
                ->addViolation();
            return;
        }

        if (!$entity instanceof $constraint->entityClass) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}