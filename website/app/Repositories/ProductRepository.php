<?php

namespace App\Repositories;

use App\Models\Product;


class ProductRepository
{

    public function product($id){

       $product= Product::find($id);

        return $product;
    }

}
