<?php

namespace App\Http\Controllers\POS_API;


use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function getProductData(Request $request) {

        //get top 8 brands [show in menu true]
        $data['menuBrands'] = Brand::where([['status',true],['show_in_menu',true]])->select('id','title')->orderby('serial_no','ASC')->limit(6)->get();

        //get top 50 features products
        $data['featuredProducts'] = Product::with('brand','variants','thumbnail')->where('status',true)->select('id', 'title', 'price', 'brand_id', 'is_new','discount_amount','discount_status','have_variants','available_stock')->limit(100)->orderBy('id','DESC')->get();

        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

$data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Product Page Content.');
    }

    public function getOutOfStockProductData(Request $request) {

        //get top 500 features products
        $data['featuredProducts'] = Product::with('brand','variants','thumbnail')->where('status',true)->select('id', 'title', 'price', 'brand_id', 'is_new','discount_amount','discount_status','have_variants')->limit(500)->orderBy('id','DESC')->get();



        $stores = Store::orderBy('name','ASC')->get();

        $storeVariants = array();
        $data['store'] = $request->get('store_id');
        foreach($data['featuredProducts'] as $product)
            foreach ($product->variants as $v) {
                $pur = StoreProductStock::where([['store_id',$request->store_id],['variant_id',$v->id]])->sum('purchase_qty');
                $sold = StoreProductStock::where([['store_id',$request->store_id],['variant_id',$v->id]])->sum('sold_qty');

                $storeVariants[$v->id] = $pur - $sold;
            }
        $data['storeVariants'] = $storeVariants;


$data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

$data['categories'] = Category::select('id','title')->get();
        return $this->sendResponse($data,'Product Page Content.');
    }

    public function getproductDetail(Request $request) {

        $productId = $request->get('product_id');
        $storeId = $request->get('store_id');
        $data['product'] = Product::where('id',$productId)->with(['brand','categories','variants'])->first();

        $purchases = StoreProductStock::where([['store_id',$storeId],['product_id',$productId]])->orderBy('created_at')->get();
        $sales = OrderProduct::where('product_id',$productId)->with(['order' => function($query) use($storeId){
            $query->where('store_id',$storeId);
        }])->orderBy('created_at')->get();

        $transactions = array();

        foreach($purchases as $p) {
            $temp = ['type' =>'IN','quantity' => $p->purchase_qty, 'date' => date('d-m-Y',strtotime($p->created_at))];

            array_push($transactions,$temp);
        }

        foreach($sales as $s) {
            $temp = ['type' =>'OUT','quantity' => -$s->qty, 'date' => date('d-m-Y',strtotime($s->created_at))];

            array_push($transactions,$temp);
        }

        $data['transactions'] = collect($transactions)->sortBy('date')->all();

        return $this->sendResponse($data,'Product Detail.');
    }


    public function getproductVariantDetail(Request $request) {

        $variantId = $request->get('variant_id');
        $storeId = $request->get('store_id');
        $data['product'] = Product::whereHas('variants',function($query) use($variantId){
            $query->where('id',$variantId);
        })->with(['brand','categories','variants' => function($query) use($variantId) {
            $query->where('id',$variantId);
        }])->first();

        $purchases = StoreProductStock::where([['store_id',$storeId],['variant_id',$variantId]])->orderBy('created_at')->get();
        $sales = OrderProduct::where('variant_id',$variantId)->with(['order' => function($query) use($storeId){
            $query->where('store_id',$storeId);
        }])->orderBy('created_at')->get();

        $transactions = array();

        foreach($purchases as $p) {
            $temp = ['type' =>'IN','quantity' => $p->purchase_qty, 'date' => date('d-m-Y',strtotime($p->created_at))];

            array_push($transactions,$temp);
        }

        foreach($sales as $s) {
            $temp = ['type' =>'OUT','quantity' => -$s->qty, 'date' => date('d-m-Y',strtotime($s->created_at))];

            array_push($transactions,$temp);
        }

        $data['transactions'] = collect($transactions)->sortBy('date')->all();

        return $this->sendResponse($data,'Product Detail.');
    }




}
