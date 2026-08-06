<?php

namespace App\Repositories;

use App\Models\Brand;


class BrandRepository
{

    public function brand($id){

        $brand = Brand::find($id);
        return $brand;
    }

}
