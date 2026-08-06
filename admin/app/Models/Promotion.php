<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'web_count',
        'mobile_count',
        'serial_no',
        'status'
    ];

    public function banners() {
        return $this->hasMany(PromotionBanner::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $promotion = new Promotion();
            $promotion->name = $request->name;
            $promotion->web_count = $request->web_count;
            $promotion->mobile_count = $request->mobile_count;
            $promotion->serial_no = $request->serial_no;
            $promotion->status = $request->status;

            $promotion->save();

            if($request->images) {
                for ($i = 0; $i < count($request->images); $i++) {
                    $promotionBanner = new PromotionBanner();
                    $promotionBanner->promotion_id = $promotion->id;
                    $promotionBanner->url = $request->url[$i];

                    $name = time() . $request->file('images')[$i]->getClientOriginalName();
                    $path = $request->file('images')[$i]->storeAs('images/promotion', $name);
                    $promotionBanner->image = $path;

                    $promotionBanner->save();
                }
            }

            DB::commit();

            return $promotion;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updatePromotion($request,$promotion) {

        DB::beginTransaction();
        try {//dd($request);

            //insert the basic information
            $promotion->name = $request->name;
            $promotion->web_count = $request->web_count;
            $promotion->mobile_count = $request->mobile_count;
            $promotion->serial_no = $request->serial_no;
            $promotion->status = $request->status;

            $promotion->save();

            $bannerIds = array();
            for ($i = 0; $i < count($request->banner_id); $i++) {
                    if($request->banner_id[$i] > 1) {
                        $promotionBanner = PromotionBanner::find($request->banner_id[$i]);

                    }
                    else
                        $promotionBanner = new PromotionBanner();
                    $promotionBanner->promotion_id = $promotion->id;
                    $promotionBanner->url = $request->url[$i];
                    $promotionBanner->serial_no = $request->serial_no_i[$i];

                    if( isset($request->file('images')[$i])) {
                        $name = time() . $request->file('images')[$i]->getClientOriginalName();
                        $path = $request->file('images')[$i]->storeAs('images/promotion', $name);
                        $promotionBanner->image = $path;
                    }

                    $promotionBanner->save();

                array_push($bannerIds,$promotionBanner->id);
            }
            PromotionBanner::whereNotIn('id',$bannerIds)->where('promotion_id',$promotion->id)->delete();


            DB::commit();

            return $promotion;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
