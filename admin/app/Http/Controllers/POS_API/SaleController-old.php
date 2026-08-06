<?php

namespace App\Http\Controllers\POS_API;


use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Order;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\StoreProductStock;

class SaleController extends BaseController
{
    public function getSaleData(Request $request) {

        $storeId = $request->get('store_id');

        $data['orders'] = Order::where('store_id',$storeId)->whereDate('created_at',\Carbon\Carbon::today())->where('status','!=',6)->select('id','order_no','created_at','total_amount')->orderBy('id','desc')->get();

        $data['latestOrder'] = Order::where('store_id',$storeId)->whereDate('created_at',\Carbon\Carbon::today())->with('products')->orderBy('id','desc')->limit(1)->first();

        $data['totalAmount'] = Order::where('store_id',$storeId)->whereDate('created_at',\Carbon\Carbon::today())->sum('total_amount');
        $data['totalCount'] = Order::where('store_id',$storeId)->whereDate('created_at',\Carbon\Carbon::today())->count();

        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

 $data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Sale Data.');
    }

    public function getHoldList(Request $request) {

        $data['cart'] = $request->get('cart');

        $products = array();
        $variants = array();
        if($data['cart']) {
            foreach ($data['cart'] as $c) {
                foreach (json_decode($c['products']) as $p) {
                    $cId = $c['id'];
                    $productId = $p->id;
                    $variantId = $p->variant_id;
                    if($variantId) {
                        $products[$p->id] = Product::with(['variants' => function ($query) use ($variantId) {
                            $query->where('id', $variantId);
                        }])->wherehas('variants', function ($query) use ($variantId) {
                            $query->where('id', $variantId);
                        })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
                        $variants[$variantId] = $products[$p->id]->variants[0];
                    }
                    else {
                        $products[$p->id] = Product::where('id',$productId)->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
                        $variants[$variantId] = null;
                    }

                }
            }
        }
        $data['products'] = $products;
        $data['variants'] = $variants;

        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')->get();
        $data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Hold List Data.');
    }

    public function getReturnOrders(Request $request) {

        $storeId = $request->get('store_id');

        $data['orders'] = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',\Carbon\Carbon::today())->select('id','order_no','created_at','total_amount','status')->orderBy('id','desc')->get();

        $data['latestOrder'] = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',\Carbon\Carbon::today())->with('products')->orderBy('id','desc')->limit(1)->first();

        $data['status'] = $data['latestOrder'] ? $data['latestOrder']->status == Order::RETURNED ? 'Complete Return' : 'Partially Return' : '';

        $data['totalAmount'] = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',\Carbon\Carbon::today())->sum('return_amount');
        $data['totalCount'] = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',\Carbon\Carbon::today())->count();

        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

$data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Return Order Data.');
    }

    public function getSearchOrder(Request $request) {

        $orderNo =$request->get('order_no');

        $data['order'] = Order::where([['store_id',$request->get('store_id')],['order_no',$orderNo]])->with('products','employee')->first();

        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

        return $this->sendResponse($data,'Search Order Data.');
    }

    public function updateCompleteReturnOrder(Request $request) {

        DB::beginTransaction();
        try {

        $data['order'] = Order::find($request->order_id);
        $data['order']->status = Order::RETURNED;
        $data['order']->return_date = Carbon::now();
        $data['order']->return_amount = $data['order']->total_amount;
        $data['order']->return_type = $request->type;
        $data['order']->save();



        foreach($data['order']->products as $p) {

            OrderProduct::where('id', $p->id)->update([
                'returned' => true,
                'return_qty' => $p->qty
            ]);
            $variant_id = $p->variant_id;

              if($p->variant_id) {
                $variant_id = $p->variant_id;
                $product = Product::with(['variants' => function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                }])->wherehas('variants', function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                })->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'available_stock')->first();

                    ProductVariant::where('id', $p->variant_id)->update(['available_stock' => $product->variants[0]->available_stock + $p->qty]);
                     Product::where('id', $product->id)->update(['available_stock' => $product->available_stock + $p->qty]);


                     $records = StoreProductStock::where([['variant_id', $p->variant_id], ['sold_qty', '>', 0]])->orderBy('id', 'DESC')->get();
                $orderQty = $p->qty;


                foreach ($records as $stock) {
                    $availableQty = $stock->sold_qty;
                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                    } else {
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                            $orderQty = 0;
                    }
                    if ($orderQty == 0) {
                            break;
                    }
                }
               }
               else {


                    $product = Product::where('id',$p->product_id)->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'available_stock')->first();
                    Product::where('id', $product->id)->update(['available_stock' => $product->available_stock + $p->qty]);


                     $records = StoreProductStock::where([['product_id', $p->product_id], ['sold_qty', '>', 0]])->orderBy('id', 'DESC')->get();
                $orderQty = $p->qty;


                foreach ($records as $stock) {
                    $availableQty = $stock->sold_qty;
                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                    } else {
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                            $orderQty = 0;
                    }
                    if ($orderQty == 0) {
                            break;
                    }
                }
               }

            // $product = Product::with(['variants' => function ($query) use ($variant_id) {
            //     $query->where('id', $variant_id);
            // }])->wherehas('variants', function ($query) use ($variant_id) {
            //     $query->where('id', $variant_id);
            // })->select('title', 'price', 'id', 'discount_status', 'discount_amount','available_stock')->first();
            // if($store->available_for_online) {
            //     ProductVariant::where('id', $p->variant_id)->update(['available_stock' => $product->variants[0]->available_stock - $p->qty , 'online_available_stock' => $product->online_available_stock - $p->qty]);
            //     Product::where('id', $product->id)->update(['available_stock' => $product->available_stock - $p->qty , 'online_available_stock' => $product->online_available_stock - $p->qty]);
            // }else {
                // ProductVariant::where('id', $p->variant_id)->update(['available_stock' => $product->variants[0]->available_stock - $p->qty]);
                // Product::where('id', $product->id)->update(['available_stock' => $product->available_stock - $p->qty]);
            // }
        }
            DB::commit();
        return $this->sendResponse($data,'Order Status Data.');
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            dd($e);
            // something went wrong
        }
    }

    public function updatePartiallyReturnOrder(Request $request) {

        DB::beginTransaction();
        try {

        $data['order'] = Order::find($request->order_id);
        $data['order']->status = Order::PARTIALLY_RETURNED;
        $data['order']->return_date = Carbon::now();
        $data['order']->return_type = $request->type;


        $store = Store::find($request->get('store_id'));
        $product_ids = $request->get('product_ids');

        $product_qty = $request->get('return_qty');
        $amount = 0;



        foreach($data['order']->products as $p) {



            if(in_array($p->id,$product_ids)) {

                $key = array_search($p->id, $product_ids);

                $qty = $product_qty[$key];




               if($p->variant_id) {
                $variant_id = $p->variant_id;
                $product = Product::with(['variants' => function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                }])->wherehas('variants', function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                })->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'available_stock')->first();

                    ProductVariant::where('id', $p->variant_id)->update(['available_stock' => $product->variants[0]->available_stock + $qty]);
                     Product::where('id', $product->id)->update(['available_stock' => $product->available_stock + $qty]);


                      $records = StoreProductStock::where([['variant_id', $p->variant_id], ['sold_qty', '>', 0]])->orderBy('id', 'DESC')->get();
                $orderQty = $qty;


                foreach ($records as $stock) {
                    $availableQty = $stock->sold_qty;
                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                    } else {
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                            $orderQty = 0;
                    }
                    if ($orderQty == 0) {
                            break;
                    }
                }
               }
               else {


                    $product = Product::where('id',$p->product_id)->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'available_stock')->first();
                    Product::where('id', $product->id)->update(['available_stock' => $product->available_stock + $qty]);

                     $records = StoreProductStock::where([['product_id', $p->product_id], ['sold_qty', '>', 0]])->orderBy('id', 'DESC')->get();
                $orderQty = $qty;


                foreach ($records as $stock) {
                    $availableQty = $stock->sold_qty;
                    if ($orderQty > $availableQty) {
                        $orderQty -= $availableQty;
                        StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                    } else {
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                            $orderQty = 0;
                    }
                    if ($orderQty == 0) {
                            break;
                    }
                }
               }


                    OrderProduct::where('id', $p->id)->update(['returned' => true,'return_qty' =>( $qty) ]);


                $amount += ($p->price * $qty);
            }
        }
            $data['order']->return_amount = $amount;
            $data['order']->save();

            DB::commit();
        return $this->sendResponse($data,'Order Status Data.');
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            dd($e);
            // something went wrong
        }
    }


    public function getOrderDetail(Request $request) {

        $orderNo =$request->get('order_no');

        $data['order'] = Order::where('id',$orderNo)->with('employee','products','customer')->first();


        return $this->sendResponse($data,'Order Detail.');
    }

}
