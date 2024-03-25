<?php

namespace App\OlxPartnerApi\Adapter;

use App\Entity\Category;

class CategoryToEntityAdapter
{

    public static function adapt(array $olxCategory, ?Category $parentCategory): Category
    {
        $category = new Category();
        $category->setOlxId($olxCategory['id']);
        $category->setName($olxCategory['name']);
        $category->setParent($parentCategory);

        return $category;
    }

}