<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
     public  function getFooterCategories()
    {
        $footer_categories = DB::table('categories')

            ->orderBy('title', 'asc')
            ->limit(12)
            ->get();

        return [
            'footer_categories_col1' => $footer_categories->take(7),
            'footer_categories_col2' => $footer_categories->slice(7)
        ];
    }

}
