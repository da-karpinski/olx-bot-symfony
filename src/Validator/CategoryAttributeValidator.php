<?php

namespace App\Validator;

use App\ApiResource\CategoryAttribute\Dto\CategoryAttributeCreateInput;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryAttributeValidator
{

    public static function validate(
        array $allowedCategoryAttributes,
        CategoryAttributeCreateInput $inputCategoryAttribute,
        TranslatorInterface $translator
    ): void
    {

        $originalAttributeCode = $inputCategoryAttribute->attributeCode;
        $inputCategoryAttribute->attributeCode = explode(":", $inputCategoryAttribute->attributeCode)[0];

        if(in_array($inputCategoryAttribute->attributeCode, array_column($allowedCategoryAttributes, 'code'))){

            $allowedCategoryAttributeIndex = array_search(
                $inputCategoryAttribute->attributeCode,
                array_column($allowedCategoryAttributes, 'code')
            );

            /** CASE: Attribute is numeric - check if it is in range */
            if($allowedCategoryAttributes[$allowedCategoryAttributeIndex]["validation"]["numeric"] === true)
            {
                self::validateNumericCategoryAttribute(
                    $allowedCategoryAttributes[$allowedCategoryAttributeIndex],
                    $inputCategoryAttribute,
                    $originalAttributeCode,
                    $translator
                );
            }

            /** CASE: Attribute has predefined values - check if it is (all) allowed */
            if(!empty($allowedCategoryAttributes[$allowedCategoryAttributeIndex]["values"]))
            {
                $attributeValuesToValidate = explode(",", $inputCategoryAttribute->attributeValue);

                foreach($attributeValuesToValidate as $value){
                    self::validatePredefinedCategoryAttribute(
                        $allowedCategoryAttributes[$allowedCategoryAttributeIndex],
                        $inputCategoryAttribute->attributeCode,
                        $value,
                        $translator
                    );
                }
            }

        }else{
            /** Attribute not allowed for this category */
            throw new UnprocessableEntityHttpException($translator->trans(
                'error.category-attribute.key-not-allowed',
                ['{{ key }}' => $originalAttributeCode],
                'error'
            ));
        }

    }

    private static function validateNumericCategoryAttribute(
        array $allowedCategoryAttribute,
        $categoryAttribute,
        $originalAttributeCode,
        TranslatorInterface $translator
    ): void
    {
        $maxRange = $allowedCategoryAttribute["validation"]["max"];
        $minRange = $allowedCategoryAttribute["validation"]["min"];
        if($categoryAttribute->attributeValue > $maxRange or $categoryAttribute->attributeValue < $minRange) {
            throw new UnprocessableEntityHttpException($translator->trans('error.category-attribute.value-out-of-range', [
                '{{ attribute }}' => $originalAttributeCode,
                '{{ min }}' => $minRange,
                '{{ max }}' => $maxRange
            ], 'error'));
        }
    }

    private static function validatePredefinedCategoryAttribute(
        array $allowedCategoryAttribute,
        $categoryAttributeCode,
        $categoryAttributeValue,
        TranslatorInterface $translator
    ): void
    {
        foreach ($allowedCategoryAttribute["values"] as $value) {
            if ($value["code"] === $categoryAttributeValue) {
                return;
            }
        }
        throw new UnprocessableEntityHttpException($translator->trans('error.category-attribute.value-not-allowed', [
            '{{ attribute }}' => $categoryAttributeCode,
            '{{ value }}' => $categoryAttributeValue
        ], 'error'));
    }

}