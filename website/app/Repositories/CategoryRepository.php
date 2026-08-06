<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryParent;

class CategoryRepository
{

    public function category(){

        $parentCategories = CategoryParent::join('categories', 'category_parents.parent_category_id', '=', 'categories.id')
    ->select('categories.title as category_name', 'categories.id as Category_id')
    ->limit(10)
    ->get();
        return $parentCategories;
    }

    public function childCategory($id){
        $childCategories= CategoryParent::join('categories', 'category_parents.parent_category_id', '=', 'categories.id')
                                        ->select('categories.title as category_name', 'categories.id as Category_id')
                                        ->where('category_parents.parent_category_id',$id)->get();

                                        return $childCategories;
                                    }

}
