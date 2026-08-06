<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;


class ProductDeal extends Model
{
    use HasFactory,SoftDeletes ;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'code',
        'description',
        'keywords',
        'menu_text',
        'deal_heading',
        'slug',
        'short_description',
        'long_description',
        'ingredients',
        'how_to_use',
        'about_brand',
        'price',
        'discount_amount',
        'is_new',
        'is_featured',
        'is_premium',
        'is_last_pick',
        'available_stock',
        'online_available_stock',
        'is_in_stock',
        'status',
        'serial_no',
        're_order_level',
        'weight',
        'have_variants'
    ];
    protected $dates = ['deleted_at'];



    public function categories() {
        return $this->hasMany(ProductDealCategory::class,'deal_id')->with('category');
    }
    public function category() {
        return $this->hasOne(ProductDealCategory::class,'deal_id')->with('category');
    }

    public function relatedDeals() {
        return $this->hasMany(ProductDealRelated::class,'deal_id')->with('relatedDeals');
    }

    public function brands() {
        return $this->hasMany(ProductDealBrand::class,'deal_id')->with('brand');
    }
    public function products() {
        return $this->hasMany(ProductDealProduct::class,'deal_id')->with('product');
    }

    public function thumbnail() {
        return $this->hasOne(ProductDealImage::class,'deal_id')->where('status',true)->orderBy('serial_no','ASC');
    }

    public function images() {
        return $this->hasMany(ProductDealImage::class,'deal_id')->where('status',true);
    }
    public function variants() {
        return $this->hasMany(ProductDealVariant::class,'deal_id');
    }

    public function store($request) {

        DB::beginTransaction();
        try {
            $productDeal = new ProductDeal();
            $productDeal->title = $request->title;
            $productDeal->description = $request->meta_description;
            $productDeal->keywords = $request->keywords;
            $productDeal->menu_text = $request->menu_text;
            $productDeal->deal_heading = $request->deal_heading;
            $productDeal->slug = $request->slug;
            $productDeal->short_description = $request->short_description;
            $productDeal->long_description = $request->full_description;
            $productDeal->ingredients = $request->ingredients;
            $productDeal->how_to_use = $request->how_to_use;
            $productDeal->price = $request->price;
            $productDeal->discount_amount = $request->discount_amount;
            $productDeal->is_new = $request->is_new ? $request->is_new : false;
            $productDeal->is_featured = $request->is_featured ? $request->is_featured : false;
            $productDeal->is_premium = $request->is_premium ? $request->is_premium : false;
            $productDeal->is_last_pick = $request->is_last_pick ? $request->is_last_pick : false;
            $productDeal->available_stock = 0;
            $productDeal->is_in_stock = false;
            $productDeal->status = $request->status;
            $productDeal->serial_no = $request->serial_no;
            $productDeal->re_order_level = $request->re_order_level;
            $productDeal->weight = $request->weight ? $request->weight : 0;
            $productDeal->have_variants = false;

            $productDeal->save();

            //manage categories
            if($request->category_id) {
                foreach ($request->category_id as $key => $val) {
                    $productDealCategory = new ProductDealCategory();
                    $productDealCategory->deal_id = $productDeal->id;
                    $productDealCategory->category_id = $val;
                    $productDealCategory->save();
                }
            }

            //manage categories
            if($request->brand_id) {
                foreach ($request->brand_id as $key => $val) {
                    $productDealBrand = new ProductDealBrand();
                    $productDealBrand->deal_id = $productDeal->id;
                    $productDealBrand->brand_id = $val;
                    $productDealBrand->save();
                }
            }

            //manage related products
            if($request->related_products) {
                foreach ($request->related_products as $key => $val) {
                    $productRelated = new ProductDealRelated();
                    $productRelated->deal_id = $productDeal->id;
                    $productRelated->related_product_id = $val;
                    $productRelated->save();
                }
            }

            //manage product images
            if($request->images) {
                for($i = 0; $i<count($request->images);$i++) {

                    $productImage = new ProductDealImage();
                    $productImage->deal_id = $productDeal->id;
                    $productImage->serial_no = $request->image_serial_no[$i] ? $request->image_serial_no[$i] : 1;
                    $productImage->status = isset($request->image_status[$i]) ? $request->image_status[$i] : 0;

                    $name = time() . '-'.$i . '-' . $request->file('images')[$i]->getClientOriginalName();
                    $path = $request->file('images')[$i]->storeAs('images/deal',$name);

                    $productImage->url = $path;
                    $productImage->save();
                }
            }

            //manage product variants
            foreach(json_decode($request->deal_products) as $deal_product) {

                $productDealProduct = new ProductDealProduct();
                $productDealProduct->deal_id = $productDeal->id;
                $productDealProduct->product_id = $deal_product->product_id;
                $productDealProduct->quantity = $deal_product->quantity;
                $productDealProduct->save();

                if ($deal_product->variants) {
                    foreach ($deal_product->variants as $key => $val) {
                        $productDealVariant = new ProductDealVariant();
                        $productDealVariant->deal_id = $productDeal->id;
                        $productDealVariant->variant_id = $val;
                        $productDealVariant->save();
                    }
                }
            }


            DB::commit();

            return $productDeal;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateProduct($request,$productDeal) {

        DB::beginTransaction();
        try {

            $productDeal->title = $request->title;
            $productDeal->code = $request->code;
            $productDeal->description = $request->meta_description;
            $productDeal->keywords = $request->keywords;
            $productDeal->menu_text = $request->menu_text;
            $productDeal->deal_heading = $request->deal_heading;
            $productDeal->slug = $request->slug;
            $productDeal->short_description = $request->short_description;
            $productDeal->long_description = $request->full_description;
            $productDeal->ingredients = $request->ingredients;
            $productDeal->how_to_use = $request->how_to_use;
            $productDeal->brand_id = $request->brand_id;
            $productDeal->price = $request->price;
            $productDeal->status = $request->status;
            $productDeal->serial_no = $request->serial_no;
            $productDeal->re_order_level = $request->re_order_level;
            $productDeal->weight = $request->weight;
            $productDeal->is_new = $request->is_new ? $request->is_new : false;
            $productDeal->is_featured = $request->is_featured ? $request->is_featured : false;
            $productDeal->is_premium = $request->is_premium ? $request->is_premium : false;
            $productDeal->is_last_pick = $request->is_last_pick ? $request->is_last_pick : false;


            $productDeal->save();



            //manage categories
            if($request->category_id) {
                ProductDealCategory::where('deal_id',$productDeal->id)->delete();
                foreach ($request->category_id as $key => $val) {
                    $productDealCategory = new ProductDealCategory();
                    $productDealCategory->product_id = $productDeal->id;
                    $productDealCategory->category_id = $val;
                    $productDealCategory->save();
                }
            }

            if($request->brand_id) {
                ProductDealBrand::where('deal_id',$productDeal->id)->delete();
                foreach ($request->brand_id as $key => $val) {
                    $productDealBrand = new ProductDealBrand();
                    $productDealBrand->product_id = $productDeal->id;
                    $productDealBrand->brand_id = $val;
                    $productDealBrand->save();
                }
            }

            //manage related products
            if($request->related_products) {
                ProductDealRelated::where('deal_id',$productDeal->id)->delete();
                foreach ($request->related_products as $key => $val) {
                    $productDealRelated = new ProductDealRelated();
                    $productDealRelated->product_id = $productDeal->id;
                    $productDealRelated->related_product_id = $val;
                    $productDealRelated->save();
                }
            }


            //manage product images
            if($request->image_status) {

                ProductDealImage::where('product_id',$productDeal->id)->whereNotIn('id',$request->img_id)->delete();
                for($i = 0; $i<count($request->image_status);$i++) {

                    if($request->img_id) {

                        $productImage = ProductImage::where('id',$request->img_id)->first();
                        $productImage->serial_no = $request->image_serial_no[$i];
                        $productImage->status = isset($request->image_status[$i]) ? $request->image_status[$i] : 0;

                        if($request->file('images')) {
                            if ($request->file('images')[$i]) {
                                File::delete('storage/' . $productImage->url);
                                $name = time() . '-' . $i . '-' . $request->file('images')[$i]->getClientOriginalName();
                                $path = $request->file('images')[$i]->storeAs('images/deal', $name);
                                $productImage->url = $path;
                            }
                        }
                        $productImage->save();
                    }

                    else {
                        $productImage = new ProductDealImage();
                        $productImage->product_id = $productDeal->id;
                        $productImage->serial_no = $request->image_serial_no[$i];
                        $productImage->status = $request->image_status[$i];

                        $name = time() . '-' . $i . '-' . $request->file('images')[$i]->getClientOriginalName();
                        $path = $request->file('images')[$i]->storeAs('images/deal', $name);

                        $productImage->url = $path;
                        $productImage->save();
                    }

                }
            }



            DB::commit();

            return $productDeal;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function getProductReport($request) {

        $date = explode('-',$request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();



        $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->pluck('id')->toArray();


        if($orderIds) {
            if($request->product_id != 0)
                $orderProducts = OrderProduct::with('product')->where('product_id',$request->product_id)->whereIn('order_id',$orderIds)->groupBy('product_id')->selectRaw('product_id,sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->get();
            else
                $orderProducts = OrderProduct::with('product')->whereIn('order_id',$orderIds)->groupBy('product_id')->selectRaw('product_id,sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->get();

        }

        $data['products'] = $orderProducts;

        $data['from'] = $from;
        $data['to'] = $to;

        return $data;
    }
}
