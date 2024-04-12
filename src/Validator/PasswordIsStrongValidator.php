<?php

namespace App\Validator;

use App\Model\PasswordPolicyModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PasswordIsStrongValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PasswordIsStrong) {
            throw new UnexpectedTypeException($constraint, PasswordIsStrong::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (strlen($value) <= PasswordPolicyModel::MIN_LENGTH) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ requirements }}', sprintf($this->translator->trans('user.password.too-short', [], 'validators'),PasswordPolicyModel::MIN_LENGTH))
                ->addViolation();
        }

        if (strlen($value) >= PasswordPolicyModel::MAX_LENGTH) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ requirements }}', sprintf($this->translator->trans('user.password.too-long', [], 'validators'),PasswordPolicyModel::MAX_LENGTH))
                ->addViolation();
        }

        if (PasswordPolicyModel::MUST_CONTAIN_DIGIT and !preg_match('/\d/', $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ requirements }}', $this->translator->trans('user.password.rule.digit', [], 'validators'))
                ->addViolation();
        }

        if (PasswordPolicyModel::MUST_CONTAIN_UPPERCASE_LETTER and !preg_match('/[A-Z]/', $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ requirements }}', $this->translator->trans('user.password.rule.uppercase', [], 'validators'))
                ->addViolation();
        }

        if (PasswordPolicyModel::MUST_CONTAIN_SPECIAL_CHARACTER and !preg_match('/[^a-zA-Z\d]/', $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ requirements }}', $this->translator->trans('user.password.rule.special-character', [], 'validators'))
                ->addViolation();
        }

    }
}