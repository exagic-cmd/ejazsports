<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;

class Banner extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'web_image',
        'mobile_image',
        'serial_no',
        'web_heading',
        'web_sub_heading',
        'mobile_heading',
        'mobile_sub_heading',
        'url',
        'status',
    ];
    protected $dates = ['deleted_at'];


    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $banner = new Banner();
            $banner->serial_no = $request->serial_no;
            $banner->web_heading = $request->web_heading;
            $banner->web_sub_heading = $request->web_sub_heading;
            $banner->mobile_heading = $request->mobile_sub_heading;
            $banner->mobile_sub_heading = $request->mobile_sub_heading;
            $banner->serial_no = $request->serial_no ? $request->serial_no : 0;
            $banner->status = $request->status;
            $banner->url = $request->url;


            //insert the Banner image
            // if($request->web_image) {
            //     $name = time() . '-WebBannerImage-' . $request->file('web_image')->getClientOriginalName();
            //     $path = $request->file('web_image')->storeAs('images/banner',$name);

            //         $banner->web_image = $path;
            //     }
            //     if($request->mobile_image) {
            //         $name = time() . '-MobileBannerImage-' . $request->file('mobile_image')->getClientOriginalName();
            //     $path = $request->file('mobile_image')->storeAs('images/banner',$name);

            //     $banner->mobile_image = $path;
            // }
            if ($request->web_image) {
                $name = time() . '-WebBannerImage-' . $request->file('web_image')->getClientOriginalName();
                // store under storage/app/public/images/banner
                $path = $request->file('web_image')->storeAs('images/banner', $name, 'public');

                $banner->web_image = $path;
            }

            if ($request->mobile_image) {
                $name = time() . '-MobileBannerImage-' . $request->file('mobile_image')->getClientOriginalName();
                // store under storage/app/public/images/banner
                $path = $request->file('mobile_image')->storeAs('images/banner', $name, 'public');

                $banner->mobile_image = $path;
            }

            


            $banner->save();

            DB::commit();

            return $banner;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function updateBanner($request,$banner) {

        DB::beginTransaction();
        try {

            //update the basic information
            $banner->serial_no = $request->serial_no;
            $banner->web_heading = $request->web_heading;
            $banner->web_sub_heading = $request->web_sub_heading;
            $banner->mobile_heading = $request->mobile_sub_heading;
            $banner->mobile_sub_heading = $request->mobile_sub_heading;
            $banner->serial_no = $request->serial_no ? $request->serial_no : 0;
            $banner->status = $request->status;
            $banner->url = $request->url;

            //insert the images
            // if($request->web_image) {
            //     //delete old piture
            //     File::delete('storage/' . $banner->web_image);
            //     $name = time() . '-WebBannerImage-' . $request->file('web_image')->getClientOriginalName();
            //     $path = $request->file('web_image')->storeAs('images/banner', $name);
            //     $banner->web_image = $path;
            //     }

            //     if($request->mobile_image) {
            //     //delete old piture
            //     File::delete('storage/' . $banner->mobile_image);
            //     $name = time() . '-MobileBannerImage-' . $request->file('mobile_image')->getClientOriginalName();
            //     $path = $request->file('mobile_image')->storeAs('images/banner',$name);
            //     $banner->mobile_image = $path;
            // }
            if ($request->web_image) {
    // delete old picture
                if ($banner->web_image) {
                        File::delete('storage/' . $banner->web_image);
                    }
                    $name = time() . '-WebBannerImage-' . $request->file('web_image')->getClientOriginalName();
                    // store in storage/app/public/images/banner
                    $path = $request->file('web_image')->storeAs('images/banner', $name, 'public');
                    $banner->web_image = $path;
                }
                if ($request->mobile_image) {
                    // delete old picture
                    if ($banner->mobile_image) {
                        File::delete('storage/' . $banner->mobile_image);
                    }
                    $name = time() . '-MobileBannerImage-' . $request->file('mobile_image')->getClientOriginalName();
                    // store in storage/app/public/images/banner
                    $path = $request->file('mobile_image')->storeAs('images/banner', $name, 'public');
                    $banner->mobile_image = $path;
                }


            $banner->save();

            DB::commit();

            return $banner;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }
}
