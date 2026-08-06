<?php

namespace App\Models;

use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'serial_no',
        'image',
        'status',
        'show_in_menu',
        'discount_id'
    ];
    protected $dates = ['deleted_at'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
    public function discount() {
        return $this->belongsTo(Discount::class);
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $brand = new Brand();
            $brand->title = $request->title;
            $brand->serial_no = $request->serial_no ? $request->serial_no : 0;
           
            $brand->show_in_menu = $request->show_in_menu ? $request->show_in_menu : 0;
            $brand->status = $request->status;
            $brand->discount_id = null;


            //insert the images
            if($request->image) {
            $name = time() . '-Image-' . $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('images/brand',$name);

            $brand->image = $path;
            }

          
            $brand->save();

            DB::commit();

            return $brand;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function updateBrand($request,$brand) {

        DB::beginTransaction();
        try {

            //update the basic information
            $brand->title = $request->title;
          
            $brand->serial_no = $request->serial_no ? $request->serial_no : 0;
           
            $brand->show_in_menu = $request->show_in_menu ? $request->show_in_menu : 0;
        
            $brand->status = $request->status;
            $brand->discount_id = $request->discount_id ? $request->discount_id : null;

            //insert the images
            if($request->image) {

                //delete old piture
                File::delete('storage/' . $brand->image);

                $name = time() . '-Image-' . $request->file('image')->getClientOriginalName();
                $path = $request->file('image')->storeAs('images/brand', $name);

                $brand->image = $path;
            }

         
            $brand->save();

            //update the brand products discount
            if($request->discount_id) {
                $products = Product::where('brand_id',$brand->id)->get();
                foreach($products as $p) {
                    if($p->is_product_discount == false) {
                        $discount_obj= Discount::where([['id',$request->discount_id],['status',true]])->select('is_percent','amount','max_amount','id')->first();
                        if($discount_obj){
                            if($discount_obj->is_percent == true) {
                                $discount_amount = ($p->price * $discount_obj->amount)/100;
                                if($discount_amount > $discount_obj->max_amount) {
                                    $discount_amount = $discount_obj->max_amount;
                                }
                            }
                            else {
                                $discount_amount = $discount_obj->amount;
                            }
                            $discount = $discount_amount;

                            if($discount > 0 && $p->price > $discount)
                                Product::where('id',$p->id)->update(['discount_status' => true,'is_product_discount'=>false,'is_brand_discount'=>true,'implement_discount_id'=>$discount_obj->id,'is_category_discount'=>false,'discount_amount'=>$discount]);
                        }

                    }
                }
            }
            else {
                $products = Product::where([['brand_id',$brand->id],['is_brand_discount',true]])->select('id')->get();
                if($products) {
                    foreach($products as $p)
                        Product::where('id',$p->id)->update(['discount_status' => false,'discount_amount' => 0,'is_category_discount'=>false,'is_product_discount'=>false,'is_brand_discount'=>false,'implement_discount_id'=>null]);
                }

            }

            DB::commit();

            return $brand;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function getBrandReport($request) {

        $date = explode('-',$request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();

        $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->pluck('id')->toArray();
        $orderProducts = array();
        if($orderIds) {
            if($request->brand_id) {
                $brandId = $request->brand_id;
                $orderProducts = OrderProduct::whereHas('product' , function($query) use($brandId){
                    $query->where('brand_id',$brandId);
                })->whereIn('order_id',$orderIds)->selectRaw('product_id, sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->groupBy('product_id')->get();
            }
            else
                $orderProducts = OrderProduct::whereIn('order_id',$orderIds)->selectRaw('product_id, sum(qty) as tqty, sum(price * qty) as sale_price,sum(cost_price * qty) as cost_price')->groupBy('product_id')->get();


        }
        $brandIds = array();
        foreach($orderProducts as $oP) {
           $id = Product::where('id',$oP->product_id)->first()->brand_id;
           array_push($brandIds,$id);
        }

        $resultArray = array();
        for($i = 0 ;$i < count($brandIds); $i++) {
            $productIds = Product::where('brand_id',$brandIds[$i])->pluck('id')->toArray();
            $temp['name'] = Brand::where('id',$brandIds[$i])->first()->title;
            $temp['id'] = Brand::where('id',$brandIds[$i])->first()->id;
            $temp['sold_qty'] = $orderProducts->whereIn('product_id',$productIds)->sum('tqty');
            $temp['sale_price'] = $orderProducts->whereIn('product_id',$productIds)->sum('sale_price');
            $temp['cost_price'] = $orderProducts->whereIn('product_id',$productIds)->sum('cost_price');
            $temp['gross_profit'] = $temp['sale_price'] - $temp['cost_price'];
            $temp['contribution'] =  round(( $temp['sale_price'] / $orderProducts->sum('sale_price') ) * 100);

            array_push($resultArray,(object)$temp);
        }
        $data['brands'] = $resultArray;
        $data['orderProducts'] = $orderProducts;
        $data['from'] = $from;
        $data['to'] = $to;

        return $data;
    }

    public function getBrandDailyGraph($request) {

        $stores = Store::all();$temp = array(); $storeInfo = array();

        $data['brand'] = Brand::find($request->brand_id);
        $brandId = $request->brand_id;

        //last 12 month sales
        $storeMonthlySales = array();
        foreach ($stores as $store) {
            $storeMonthlySales[$store->id]['label'] = $store->name;
            $monthlySales = array();
            for($i = 12; $i > -1 ; $i--) {
                $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth($i)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth($i)->endOfMonth())->pluck('id')->toArray();
                $sale = OrderProduct::whereIn('order_id',$orderIds)->whereHas('product', function($query) use($brandId){
                    $query->where('brand_id',$brandId);
                })->sum(DB::raw('price + qty'));
                array_push($monthlySales,$sale);
            }
            $storeMonthlySales[$store->id]['monthlySales'] = $monthlySales;
        }

        $data['storeMonthlySales'] = $storeMonthlySales;

        $storeMonthlyOrders = array();
        foreach ($stores as $store) {
            $storeMonthlyOrders[$store->id]['label'] = $store->name;
            $monthlyOrders = array();
            for($i = 12; $i > -1 ; $i--) {
                $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth($i)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth($i)->endOfMonth())->pluck('id')->toArray();
                $qty = OrderProduct::whereIn('order_id',$orderIds)->whereHas('product', function($query) use($brandId){
                    $query->where('brand_id',$brandId);
                })->sum('qty');
                array_push($monthlyOrders,$qty);
            }
            $storeMonthlyOrders[$store->id]['monthlyOrders'] = $monthlyOrders;
        }

        $data['storeMonthlyOrders'] = $storeMonthlyOrders;

        $monthNames = array();
        for($i = 12; $i > -1 ; $i--) {
            $temp = Carbon::now()->subMonth($i)->getTranslatedShortMonthName();

            array_push($monthNames,$temp);
        }
        $data['monthNames'] = $monthNames;

        //last 30 days sales
        $storeDailySales = array();
        foreach ($stores as $store) {
            $storeDailySales[$store->id]['label'] = $store->name;
            $dailySales = array();
            for($i = 30; $i > -1 ; $i--) {
                $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subDay($i))->whereDate('created_at','<=',Carbon::now()->subDay($i))->pluck('id')->toArray();
                $sale = OrderProduct::whereIn('order_id',$orderIds)->whereHas('product', function($query) use($brandId){
                    $query->where('brand_id',$brandId);
                })->sum(DB::raw('price + qty'));
                array_push($dailySales,$sale);
            }
            $storeDailySales[$store->id]['dailySales'] = $dailySales;
        }

        $data['storeDailySales'] = $storeDailySales;

        $storeDailyOrders = array();
        foreach ($stores as $store) {
            $storeDailyOrders[$store->id]['label'] = $store->name;
            $dailyOrders = array();
            for($i = 30; $i > -1 ; $i--) {
                $orderIds = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subDay($i))->whereDate('created_at','<=',Carbon::now()->subDay($i))->pluck('id')->toArray();
                $qty = OrderProduct::whereIn('order_id',$orderIds)->whereHas('product', function($query) use($brandId){
                    $query->where('brand_id',$brandId);
                })->sum('qty');
                array_push($dailyOrders,$qty);
            }
            $storeDailyOrders[$store->id]['dailyOrders'] = $dailyOrders;
        }

        $data['storeDailyOrders'] = $storeDailyOrders;

        $dayNames = array();
        for($i = 30; $i > -1 ; $i--) {
            $temp = Carbon::now()->subDay($i)->getTranslatedShortDayName();

            array_push($dayNames,$temp);
        }
        $data['dayNames'] = $dayNames;

        $data['colors'] = ['rgba(44, 120, 220, 0.2)','rgba(117,217,121,0.2)','rgba(172,129,213,0.2)','rgba(213,151,129,0.2)','rgba(44, 120, 220, 0.2)'];
        $data['borders'] = ['rgba(44, 120, 220)','rgb(66,173,32)','rgb(78,21,140)','rgb(140,75,21)','rgba(44, 120, 220)'];

        return $data;
    }
    
    public function getBrandAvailableInventory($request)
    {
        if($request->store_id) {
            $brands = Brand::whereIn('id', Auth::user()->brands->pluck('id')->toArray())->orderBy('title', 'DESC')->get();
            $totalQty = array();
            $totalValue = array();
            foreach ($brands as $b) {
               
              $products = Product::where('brand_id',$b->id)->where('status',true)->get();
              
              $tQty = 0;
              $tValue = 0;
              foreach($products as $p) {
                  
                  $qty = StoreProductStock::where('store_id',$request->store_id)->where('product_id', $p->id)->sum(DB::raw('purchase_qty - sold_qty'));
                  
                  $tQty += $qty;
                  
                  $tValue  = $tValue + ($p->price * $qty);
                  
              }
    
                
                $totalQty[$b->id] = $tQty;
                $totalValue[$b->id] = $tValue;
            }
            $data['totalQty'] = $totalQty;
            $data['totalValue'] = $totalValue;
            $data['brands'] = $brands;
            $data['store_id'] = $request->store_id;
            $data['store'] = Store::find($request->store_id);
            return $data;
        }
        else {
            $brands = Brand::orderBy('title', 'DESC')->get();
            $totalQty = array();
            $totalValue = array();
            foreach ($brands as $b) {
               // $productIds = Product::where('brand_id', $b->id)->pluck('id')->toArray();
               // $result = StoreProductStock::whereIn('product_id', $productIds)->selectRaw('sum(purchase_qty - sold_qty) as pqty,sum((purchase_qty - sold_qty) * cost) as value')->get();
    
               // $totalQty[$b->id] = $result[0]->pqty;
              //  $totalValue[$b->id] = $result[0]->value;
              
              
              $products = Product::where('brand_id',$b->id)->where('status',true)->get();
              
              $tQty = 0;
              $tValue = 0;
              foreach($products as $p) {
                  
                  $qty = StoreProductStock::where('store_id','<',15)->where('product_id', $p->id)->sum(DB::raw('purchase_qty - sold_qty'));
                  
                  $tQty +=$qty;
                  
                  $tValue  = $tValue + ($p->price * $qty);
                  
              }
    
                // $totalQty[$b->id] = Product::where([['brand_id', $b->id],['status',true]])->sum('available_stock');
                // $totalValue[$b->id] = Product::where([['brand_id', $b->id],['status',true]])->sum(DB::raw('available_stock * price'));
                $totalQty[$b->id] = $tQty;
                $totalValue[$b->id] = $tValue;
            }
            $data['totalQty'] = $totalQty;
            $data['totalValue'] = $totalValue;
            $data['brands'] = $brands;
            $data['store_id'] = $request->store_id;
            $data['store'] = null;
            
            return $data;
        }
    }
    public function getSpecificBrandAvailableInventory($brand_id,$store_id)
    {
        if($store_id) {
            $products = Product::with('variants')->where([['brand_id', $brand_id],['status',true]])->orderBy('title', 'DESC')->get();
            $totalQty = array();
            $totalValue = array();
            $storeQty = array();
            $stores = Store::where('id',$store_id)->get();
            foreach ($products as $p) {
                foreach ($p->variants as $v) {
                    $result = StoreProductStock::where('variant_id', $v->id)->selectRaw('sum(purchase_qty - sold_qty) as pqty')->get();
                    $totalQty[$v->id] = $v->available_stock;
                    $totalValue[$v->id] = $v->available_stock * $p->price;
                    $tmp = array();
                    foreach ($stores as $s) {
                        $result = StoreProductStock::where([['variant_id', $v->id], ['store_id', $s->id]])->selectRaw('sum(purchase_qty - sold_qty) as pqty')->get();
                        $tmp[$s->id]['total_qty'] = $result[0]->pqty;
                        $tmp[$s->id]['total_value'] = $result[0]->pqty * $p->price;
                    }
                    $storeQty[$v->id] = $tmp;
                }
            }
            $data['totalQty'] = $totalQty;
            $data['totalValue'] = $totalValue;
            $data['products'] = $products;
            $data['stores'] = $stores;
            $data['storeQty'] = $storeQty;
            $data['brand'] = Brand::where('id', $brand_id)->first();
            return $data;
        }
        else {
            $products = Product::with('variants')->where([['brand_id', $brand_id],['status',true]])->orderBy('title', 'DESC')->get();
            $totalQty = array();
            $totalValue = array();
            $storeQty = array();
            $stores = Store::where('status', true)->get();
            foreach ($products as $p) {
                foreach ($p->variants as $v) {
                    $result = StoreProductStock::where('variant_id', $v->id)->selectRaw('sum(purchase_qty - sold_qty) as pqty')->get();
                    $totalQty[$v->id] = $v->available_stock;
                    $totalValue[$v->id] = $v->available_stock * $p->price;
                    $tmp = array();
                    foreach ($stores as $s) {
                        $result = StoreProductStock::where([['variant_id', $v->id], ['store_id', $s->id]])->selectRaw('sum(purchase_qty - sold_qty) as pqty')->get();
                        $tmp[$s->id]['total_qty'] = $result[0]->pqty;
                        $tmp[$s->id]['total_value'] = $result[0]->pqty * $p->price;
                    }
                    $storeQty[$v->id] = $tmp;
                }
            }
            $data['totalQty'] = $totalQty;
            $data['totalValue'] = $totalValue;
            $data['products'] = $products;
            $data['stores'] = $stores;
            $data['storeQty'] = $storeQty;
            $data['brand'] = Brand::where('id', $brand_id)->first();
            return $data;
        }
        
    }
 }
