<?php

namespace App\Models;

use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'serial_no_brand_listing',
        'image',
        'status',
        'parent_category_id',
        'show_in_menu',
        'discount_id'
    ];
    protected $dates = ['deleted_at'];

    public function parentCategory() {
        return $this->hasMany(CategoryParent::class,'category_id');
    }

    public function childCategory() {
        return $this->hasMany(CategoryParent::class,'parent_category_id')->with('category');
    }

    public function discount() {
        return $this->belongsTo(Discount::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $category = new Category();
            $category->title = $request->title;
            $category->serial_no = $request->serial_no ? $request->serial_no : 0;
            $category->show_in_menu = $request->show_in_menu ? $request->show_in_menu : 0;
            $category->status = $request->status;

            //insert the images
            if($request->image) {
            $name = time() . '-Image-' . $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('images/category',$name);

            $category->image = $path;
            
            }

            $category->save();

            //check parents categories
            if($request->parent_ids) {
                foreach ($request->parent_ids as $key => $val) {
                    $parentCategory = new CategoryParent();
                    $parentCategory->category_id = $category->id;
                    $parentCategory->parent_category_id = $val;
                    $parentCategory->save();
                }
            }

            DB::commit();

            return $category;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function updateCategory($request,$category) {

        DB::beginTransaction();
        try {

            //update the basic information
            $category->title = $request->title;
            $category->serial_no = $request->serial_no ? $request->serial_no : 0;
      
            $category->show_in_menu = $request->show_in_menu ? $request->show_in_menu : 0;
            $category->status = $request->status;
            $category->parent_category_id = $request->parent_category_id;
            $category->discount_id = $request->discount_id ? $request->discount_id : null;

            //insert the images
            if($request->image) {

                //delete old piture
                File::delete('storage/' . $category->image);

                $name = time() . '-Image-' . $request->file('image')->getClientOriginalName();
                $path = $request->file('image')->storeAs('images/category', $name);

                $category->image = $path;
            }

            $category->save();

            //check parents categories
            if($request->parent_ids) {
                CategoryParent::where('category_id',$category->id)->delete();
                foreach ($request->parent_ids as $key => $val) {
                    $parentCategory = new CategoryParent();
                    $parentCategory->category_id = $category->id;
                    $parentCategory->parent_category_id = $val;
                    $parentCategory->save();
                }
            }

            //update the category wise products discount
            if($request->discount_id) {

                $category = Category::where('id',$category->id)->first();
                $category_id = $category->id;
                $products = Product::whereHas('categories', function($query) use($category_id) {
                    $query->where('category_id',$category_id);
                })->get();

                foreach($products as $p) {
                    $temp = 0;$discount = 0;$id = null;
                    if($p->categories) {
                        foreach($p->categories as $c) {
                            if($p->is_product_discount == 0 && $p->is_brand_discount == 0) {
                                $discount_obj= Discount::where([['id',$request->discount_id],['status',true]])->select('is_percent','amount','max_amount','id')->first();
                                if($discount_obj) {

                                    if($discount_obj->is_percent == true) {
                                        $discount_amount = ($p->price * $discount_obj->amount)/100;
                                        if($discount_amount > $discount_obj->max_amount) {
                                            $temp = $discount_obj->max_amount;
                                        }
                                    }
                                    else {
                                        $temp = $discount_obj->amount;
                                    }
                                    if($discount < $temp) {
                                        $discount = $temp;
                                        $id = $discount_obj->id;
                                    }
                                }
                            }
                        }
                    }
                    if($discount > 0 && $p->price > $discount) {
                        Product::where('id',$p->id)->update(['discount_status' => true,'is_category_discount'=>true,'is_product_discount'=>false,'is_brand_discount'=>false,'implement_discount_id'=>$id,'discount_amount' => $discount]);
                    }
                }
            }
            elseif($category->discount_id != null) {

                $category_id = $category->id;
                $products = Product::whereHas('categories', function($query) use($category_id) {
                    $query->where('category_id',$category_id);
                })->get();

                foreach($products as $p) {
                    $temp = 0;$id = null;$discount = 0;
                    if($p->categories) {
                        foreach($p->categories as $c) {
                            if($p->is_product_discount == 0 && $p->is_brand_discount == 0) {
                                $discount_obj= Discount::where([['id',$c->category->discount_id],['status',true]])->select('is_percent','amount','max_amount','id')->first();
                                if($discount_obj) {

                                    if($discount_obj->is_percent == true) {
                                        $discount_amount = ($p->price * $discount_obj->amount)/100;
                                        if($discount_amount > $discount_obj->max_amount) {
                                            $temp = $discount_obj->max_amount;
                                        }
                                    }
                                    else {
                                        $temp = $discount_obj->amount;
                                    }
                                    if($discount < $temp) {
                                        $discount = $temp;
                                        $id = $discount_obj->id;
                                        $category_id = $category->id;
                                    }
                                }
                            }
                        }
                    }
                    if($discount > 0 && $p->price > $discount ) {
                        if($category_id == $category->id) {
                            Product::where('id',$p->id)->update(['discount_status' => false,'is_category_discount'=>false,'is_product_discount'=>false,'is_brand_discount'=>false,'implement_discount_id'=>null,'discount_amount' => 0]);
                        }
                        else {
                            Product::where('id',$p->id)->update(['discount_status' => true,'is_category_discount'=>true,'is_product_discount'=>false,'is_brand_discount'=>false,'implement_discount_id'=>$id,'discount_amount' => $discount]);
                        }
                    }
                }
            }

            DB::commit();

            return $category;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function getCategoryReport($request) {

        $date = explode('-',$request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();

        $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->pluck('id')->toArray();
        $orderProducts = array();
        if($orderIds) {
            if($request->category_id) {
                $categoryId = $request->category_id;
                $orderProducts = OrderProduct::whereHas('product' , function($query) use($categoryId){
                    $query->whereHas('categories' , function($query) use($categoryId){
                        $query->where('category_id',$categoryId);
                    });})->whereIn('order_id',$orderIds)->selectRaw('product_id, sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->groupBy('product_id')->get();
            }
            else
                $orderProducts = OrderProduct::whereIn('order_id',$orderIds)->selectRaw('product_id, sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->groupBy('product_id')->get();


        }
        $categoryIds = array();
        foreach($orderProducts as $oP) {
            $products = Product::with('categories')->where('id',$oP->product_id)->first();
            foreach($products->categories as $cat) {
                array_push($categoryIds,$cat->category_id);
            }

        }

        $resultArray = array();
        for($i = 0 ;$i < count($categoryIds); $i++) {
            $categoryId = $categoryIds[$i];
            $productIds = Product::whereHas('categories',function($query) use($categoryId){
                $query->where('category_id',$categoryId);
            })->pluck('id')->toArray();
            $temp['name'] = Category::where('id',$categoryIds[$i])->first()->title;
            $temp['id'] = Category::where('id',$categoryIds[$i])->first()->id;
            $temp['sold_qty'] = $orderProducts->whereIn('product_id',$productIds)->sum('tqty');
            $temp['sale_price'] = $orderProducts->whereIn('product_id',$productIds)->sum('sale_price');
            $temp['cost_price'] = $orderProducts->whereIn('product_id',$productIds)->sum('cost_price');
            $temp['gross_profit'] = $temp['sale_price'] - $temp['cost_price'];
            $temp['contribution'] =  round(( $temp['sale_price'] / $orderProducts->sum('sale_price') ) * 100);

            array_push($resultArray,(object)$temp);
        }
        $data['categories'] = $resultArray;
        $data['orderProducts'] = $orderProducts;
        $data['from'] = $from;
        $data['to'] = $to;

        return $data;
    }
}
