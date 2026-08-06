<?php

namespace App\Repositories;

use App\Models\Banner;


class HomeRepository
{

    public function banners(){

        $banner = Banner::all();
        return $banner;
    }

}
