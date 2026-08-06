<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;


class Product extends Model
{
    use HasFactory ;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'code',
        'short_description',
        'long_description',
        'brand_id',
        'price',
        'discount_amount',
        'is_product_discount',
        'is_brand_discount',
        'is_category_discount',
        'discount_status',
        'implement_discount_id',
        'available_stock',
        'is_in_stock',
        'status',
        'serial_no',
        're_order_level',
        'weight',
        'have_variants',
        'purchase_price',
        'dz_price',
        'barcode',
        'is_featured'
    ];
    protected $dates = ['deleted_at'];



    public function categories() {
        return $this->hasMany(ProductCategory::class)->with('category');
    }
    public function category() {
        return $this->hasOne(ProductCategory::class)->with('category');
    }

    public function relatedProducts() {
        return $this->hasMany(ProductRelated::class)->with('relatedProduct');
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function thumbnail() {
        return $this->hasOne(ProductImage::class)->where('status',true)->orderBy('serial_no','ASC');
    }

    public function images() {
        return $this->hasMany(ProductImage::class)->where('status',true);
    }
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
    public function discount() {
        return $this->belongsTo(Discount::class,'implement_discount_id');
    }
    
    public function purchases() {
        return $this->hasMany(ReceivingProduct::class,'product_id');
    }
    public function stock() {
        return $this->hasMany(StoreProductStock::class,'product_id');
    }
    
    public function audit() {

        return $this->hasMany(StockAuditDetail::class);

    }
    
    public function sales() {
        return $this->hasMany(OrderProduct::class,'product_id');
    }

    public function store($request) {

        DB::beginTransaction();
        try {
            $product = new Product();
            $product->title = $request->title;
            
            if (empty($request->code)) {
                $lastProduct = Product::orderBy('id', 'desc')->first();
                $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
                $sku = 'SKU-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                while(Product::where('code', $sku)->exists()) {
                    $nextId++;
                    $sku = 'SKU-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                }
                $product->code = $sku;
            } else {
                $product->code = $request->code;
            }

            $product->short_description = $request->short_description;
            $product->long_description = $request->full_description;
            $product->brand_id = $request->brand_id;
            $product->price = $request->price;
            $product->purchase_price = $request->purchase_price;
            $product->dz_price = $request->dz_price;
            $product->discount_amount = 0;
            $product->is_product_discount = false;
            $product->is_brand_discount = false;
            $product->is_category_discount = false;
            $product->discount_status = false;
            $product->available_stock = 0;
            $product->is_in_stock = false;
            $product->status = $request->status;
            $product->serial_no = $request->serial_no;
            $product->re_order_level = $request->re_order_level;
            $product->weight = $request->weight ? $request->weight : 0;
            $product->have_variants = false;
            $product->barcode = time() . '000';
            // $product->is_featured = 0;
            $product->is_featured = $request->has('is_featured') ? 1 : 0;
            $product->save();
            //manage categories
            if($request->category_id) {
                foreach ($request->category_id as $key => $val) {
                    $productCategory = new ProductCategory();
                    $productCategory->product_id = $product->id;
                    $productCategory->category_id = $val;
                    $productCategory->save();
                }
            }

            //manage product images
            if($request->images) {
                $serialNo = 1;
                for($i = 0; $i<count($request->images);$i++) {

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->serial_no = $serialNo++;;
                    $productImage->status = 1 ;

                    $name = time() . '-'.$i . '-' . $request->file('images')[$i]->getClientOriginalName();
                    $path = $request->file('images')[$i]->storeAs('images/product',$name,'public');

                    $productImage->url = $path;
                    $productImage->save();
                }
            }

            $count = 1;
            if($request->color_id && $request->size_id) {
                foreach ($request->color_id as $key => $color) {
                    
                    foreach ($request->size_id as $key => $size) {
                   
                        $productVariant = new ProductVariant();
                        $productVariant->product_id = $product->id;
                        $variantSku = $product->code . '-' . str_replace(' ', '', strtoupper($color)) . '-' . str_replace(' ', '', strtoupper($size));
                        $productVariant->barcode = $variantSku;
                        $productVariant->shade = $color;
                        $productVariant->size = $size;
                        $productVariant->additional_price = 0;


                    $productVariant->save();
                    }
                }
            }
            elseif($request->color_id) {
                foreach ($request->color_id as $key => $color) {
                
                        $productVariant = new ProductVariant();
                        $productVariant->product_id = $product->id;
                        $variantSku = $product->code . '-' . str_replace(' ', '', strtoupper($color)) . '-' . str_pad($count++, 3, '0', STR_PAD_LEFT);
                        $productVariant->barcode = $variantSku;
                        $productVariant->shade = $color;
                        $productVariant->size = null;
                        $productVariant->additional_price = 0;


                    $productVariant->save();
                    
                }
            }
            elseif($request->size_id) {
                foreach ($request->size_id as $key => $size) {
                
                        $productVariant = new ProductVariant();
                        $productVariant->product_id = $product->id;
                        $variantSku = $product->code . '-' . str_replace(' ', '', strtoupper($size)) . '-' . str_pad($count++, 3, '0', STR_PAD_LEFT);
                        $productVariant->barcode = $variantSku;
                        $productVariant->shade = null;
                        $productVariant->size = $size;
                        $productVariant->additional_price = 0;

                        $productVariant->save();
                    
                }
            }
            
            if($request->color_id || $request->size_id)
                        Product::where('id',$product->id)->update(['have_variants' => true]);

                    

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateProduct($request,$product) {

        DB::beginTransaction();
        try {

            $product->title = $request->title;
            $product->code = $request->code;
            $product->short_description = $request->short_description;
            $product->long_description = $request->full_description;
            
            $product->brand_id = $request->brand_id;
            $product->price = $request->price;
            $product->purchase_price = $request->purchase_price;
            $product->dz_price = $request->dz_price;
            $product->status = $request->status ? $request->status : 0;
            $product->serial_no = $request->serial_no;
            $product->re_order_level = $request->re_order_level;
            $product->barcode = $request->mbarcode;
            $product->weight = $request->weight;
            $product->is_featured = $request->has('is_featured') ? 1 : 0;

            $product->save();

            //manage discounts
            if ($request->discount_id) {

                $discount_obj = Discount::where([['id', $request->discount_id], ['status', true]])->select('is_percent', 'amount', 'max_amount', 'id')->first();
                if ($discount_obj) {
                    if ($discount_obj->is_percent == true) {
                        $discount_amount = ($product->price * $discount_obj->amount) / 100;
                        if ($discount_amount > $discount_obj->max_amount) {
                            $discount_amount = $discount_obj->max_amount;
                        }
                    } else {
                        $discount_amount = $discount_obj->amount;
                    }
                    $discount = $discount_amount;

                    Product::where('id', $product->id)->update(['is_product_discount' => true, 'is_brand_discount' => false, 'is_category_discount' => false, 'discount_amount' => $discount, 'discount_status' => true, 'implement_discount_id' => $discount_obj->id]);
                }
            } //check brand wise discount
            elseif ($product->brand_id) {
                $brand = Brand::where('id', $request->brand_id)->first();
                if ($brand) {
                    if ($brand->discount_id) {
                        $discount_obj = Discount::where([['id', $product->brand->discount_id], ['status', true]])->select('is_percent', 'amount', 'max_amount', 'id')->first();
                        if ($discount_obj) {
                            if ($discount_obj->is_percent == true) {
                                $discount_amount = ($product->price * $discount_obj->amount) / 100;
                                if ($discount_amount > $discount_obj->max_amount) {
                                    $discount_amount = $discount_obj->max_amount;
                                }
                            } else {
                                $discount_amount = $discount_obj->amount;
                            }
                            $discount = $discount_amount;
                            Product::where('id', $product->id)->update(['is_brand_discount' => true, 'is_product_discount' => false, 'is_category_discount' => false, 'implement_discount_id' => $discount_obj->id, 'discount_status' => true, 'discount_amount' => $discount]);
                        }
                    }
                }
            } //check categories wise discount
            elseif ($request->category_id) {
                $id = null;
                foreach ($request->category_id as $c) {
                    $temp = 0;
                    $category = Category::where('id', $c)->select('discount_id')->first();
                    if ($category->discount_id != null) {
                        $discount_obj = Discount::where([['id', $category->discount_id], ['status', true]])->select('is_percent', 'amount', 'max_amount', 'id')->first();
                        if ($discount_obj) {
                            if ($discount_obj->is_percent == 1) {
                                $temp = ($product->price * $discount_obj->amount) / 100;
                                if ($temp > $discount_obj->max_amount) {
                                    $temp = $discount_obj->max_amount;
                                }
                            } else {
                                $temp = $discount_obj->amount;
                            }
                            if ($discount < $temp) {
                                $discount = $temp;
                                $id = $discount_obj->id;
                            }

                        }
                    }
                }
                if ($discount > 0) {
                    Product::where('id', $product->id)->update(['is_category_discount' => true, 'is_product_discount' => false, 'is_brand_discount' => false, 'implement_discount_id' => $id, 'discount_amount' => $discount, 'discount_status' => true]);
                }
            }

            //manage categories
            if($request->category_id) {
                ProductCategory::where('product_id',$product->id)->delete();
                foreach ($request->category_id as $key => $val) {
                    $productCategory = new ProductCategory();
                    $productCategory->product_id = $product->id;
                    $productCategory->category_id = $val;
                    $productCategory->save();
                }
            }

            //manage related products
            if($request->related_products) {
                ProductRelated::where('product_id',$product->id)->delete();
                foreach ($request->related_products as $key => $val) {
                    $productRelated = new ProductRelated();
                    $productRelated->product_id = $product->id;
                    $productRelated->related_product_id = $val;
                    $productRelated->save();
                }
            }


            //manage product images
            if($request->image_status) {

            //    ProductImage::where('product_id',$product->id)->whereNotIn('id',$request->img_id)->delete();
                    // store image in storage/app/images/product/ 
                for($i = 0; $i<count($request->image_status);$i++) {
                    if($request->img_id) {
                        $productImage = ProductImage::where('id',$request->img_id)->first();
                        $productImage->serial_no = $request->image_serial_no[$i];
                        $productImage->status = isset($request->image_status[$i]) ? $request->image_status[$i] : 0;
                        if($request->file('images')) {
                            if ($request->file('images')[$i]) {
                                File::delete('storage/' . $productImage->url);
                                $name = time() . '-' . $i . '-' . $request->file('images')[$i]->getClientOriginalName();
                                $path = $request->file('images')[$i]->storeAs('images/product', $name,'public');
                                $productImage->url = $path;
                            }
                        }
                        $productImage->save();
                    }
                    else {
                        $productImage = new ProductImage();
                        $productImage->product_id = $product->id;
                        $productImage->serial_no = $request->image_serial_no[$i];
                        $productImage->status = $request->image_status[$i];
                        $name = time() . '-' . $i . '-' . $request->file('images')[$i]->getClientOriginalName();
                        $path = $request->file('images')[$i]->storeAs('images/product', $name,'public');
                        $productImage->url = $path;
                        $productImage->save();
                    }
                }
            }
            //manage product variants
            $count = 1;
            if($request->barcode) {
                 ProductVariant::where('product_id',$product->id)->whereNotIn('id',$request->variant_id)->delete();
                for($i =0;$i < count($request->barcode); $i++) {

                    if($request->variant_id[$i] != 0)
                        $productVariant = ProductVariant::find($request->variant_id[$i]);
                    else
                        $productVariant = new ProductVariant();
                    $productVariant->product_id = $product->id;
                    $productVariant->barcode = ($request->barcode[$i]) ? $request->barcode[$i] : time() . str_pad($count++, 3, '0', STR_PAD_LEFT);
                    $productVariant->shade = $request->shade[$i];
                    $productVariant->size = $request->size[$i];
                    $productVariant->additional_price = $request->additional_price[$i];
                    
                    $productVariant->purchase_price = $request->v_purchase_price[$i];
            $productVariant->dz_price = $request->v_dz_price[$i];
            $productVariant->re_order_level = $request->v_re_order_level[$i];

                   

                     if($request->shade[$i] || $request->size[$i])
                        Product::where('id',$product->id)->update(['have_variants' => true]);

                    $productVariant->save();
                }
                
              
                
            }
            
            PriceupNotification::where('product_id',$product->id)->delete();

            DB::commit();

            return $product;
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
    
    public function getOutOfStockProductsReport($request) {



        $data['brand'] = Brand::where('id',$request->brand_id)->select('id','title')->first();



        $data['products'] = Product::where('brand_id',$request->brand_id)->with('variants',function($query){

            $query->where('available_stock','<',5);

        })->get();

        return $data;

    }
    
    public function getStatsReport($request) {
        
        
        
        $storeId = 11;
        
        $date = explode('-',$request->date_range);
        $from = Carbon::parse($date[0])->startOfDay();
        $to = Carbon::parse($date[1])->endOfDay();

        $data['todayBillsCount'] = Order::where('store_id',$storeId)->whereBetween('created_at',[$from,$to])->count();
        $data['todayBillsAmount'] = Order::where('store_id',$storeId)->whereBetween('created_at',[$from,$to])->sum('total_amount');
        $data['todaySales'] = Order::where('store_id',$storeId)->whereBetween('created_at',[$from,$to])->get();

        $data['todayCashBillsCount'] = Order::where([['store_id',$storeId],['customer_id',1]])->whereBetween('created_at',[$from,$to])->count();
        $data['todayCashBillsAmount'] = Order::where([['store_id',$storeId],['customer_id',1]])->whereBetween('created_at',[$from,$to])->sum('total_amount');
        
        $data['todayRetailSales'] = Order::where([['store_id',$storeId],['customer_id',1]])->whereBetween('created_at',[$from,$to])->get();

        $data['todayCardBillsCount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->whereBetween('created_at',[$from,$to])->count();
        $data['todayCardBillsAmount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->whereBetween('created_at',[$from,$to])->sum('total_amount');
        
         $data['todayWholeSales'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->whereBetween('created_at',[$from,$to])->get();
        
        
        $data['todayCreditBillsCount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount',0)->whereBetween('created_at',[$from,$to])->count();
        $data['todayCreditBillsAmount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount',0)->whereBetween('created_at',[$from,$to])->sum(DB::raw('total_amount - paid_amount'));
        
        $data['todayWholeSalesCredit'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount',0)->whereBetween('created_at',[$from,$to])->get();
        
        
        $data['todayPaidBillsCount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount','>',0)->whereBetween('created_at',[$from,$to])->count();
        $data['todayPaidBillsAmount'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount','>',0)->whereBetween('created_at',[$from,$to])->sum('paid_amount');
        
        $data['todayWholeSalesPaid'] = Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount','>',0)->whereBetween('created_at',[$from,$to])->get();
        
        
        $data['todayCustomerPaymentA'] = CustomerPayment::whereBetween('created_at',[$from,$to])->sum('amount') - Order::whereBetween('created_at',[$from,$to])->where([['store_id',$storeId],['customer_id','!=',1]])->sum('paid_amount');
        
        // Find customer payments made today
        $customerPayments = CustomerPayment::whereBetween('created_at',[$from,$to])->get();
       
        // Find orders placed today
        $orders = Order::whereBetween('created_at',[$from,$to])->where([['store_id',$storeId],['customer_id','!=',1]])->get();
        
      
        
        // Filter out customer payments not associated with today's orders
        $data['todayLedgerPayment'] = $customerPayments->reject(function ($customerPayment) use ($orders) {
            return $orders->contains('customer_id', $customerPayment->customer_id);
        });

        
        
        $data['todayCustomerPaymentC'] = CustomerPayment::whereDate('date',Carbon::today())->count() - Order::where([['store_id',$storeId],['customer_id','!=',1]])->where('paid_amount','>',0)->whereBetween('created_at',[$from,$to])->count();
        
        $data['todayExpenseA'] = Expense::whereBetween('created_at',[$from,$to])->sum('amount');
        
         $data['todayMargin'] = Order::whereBetween('created_at',[$from,$to])->sum('margin');
         
         $data['todayProfit'] = OrderProduct::whereBetween('created_at',[$from,$to])->sum(DB::raw('(price * qty) - (cost_price * qty)'));
        
        $data['todayExpenseC'] = Expense::whereBetween('created_at',[$from,$to])->count();

        $data['todayCReturnBillsCount'] = Order::where([['store_id',$storeId]])->whereIn('status',[Order::PARTIALLY_RETURNED,Order::RETURNED])->where('return_type',2)->whereBetween('return_date',[$from,$to])->count();
        $data['todayCReturnBillsAmount'] = Order::where([['store_id',$storeId]])->whereIn('status',[Order::PARTIALLY_RETURNED,Order::RETURNED])->where('return_type',2)->whereBetween('return_date',[$from,$to])->sum('return_amount');
        
        
          $data['todayLReturnBillsCount'] = Order::where([['store_id',$storeId]])->whereIn('status',[Order::PARTIALLY_RETURNED,Order::RETURNED])->where('return_type',1)->whereBetween('return_date',[$from,$to])->count();
        $data['todayLReturnBillsAmount'] = Order::where([['store_id',$storeId]])->whereIn('status',[Order::PARTIALLY_RETURNED,Order::RETURNED])->where('return_type',1)->whereBetween('return_date',[$from,$to])->sum('return_amount');

        $orderIds = Order::where('store_id',$storeId)->whereBetween('created_at',[$from,$to])->pluck('id')->toArray();
        $result = OrderProduct::groupBy('variant_id')->with('variant')->whereIn('order_id',$orderIds)->selectRaw('sum(price * qty) as tprice, variant_id,sum(qty) as tqy')->get();
        $data['variants'] = $result->sortByDESC('tprice');
        $products = array();
        foreach($result as $res) {
            $variant_id = $res->variant_id;
            $products[$res->variant_id] = Product::wherehas('variants', function ($query) use ($variant_id) {
                $query->where('id', $variant_id);
            })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
        }
        $data['products'] = $products;
        
        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

 $data['categories'] = Category::select('id','title')->get();
 
 
 return $data;
    }
}
