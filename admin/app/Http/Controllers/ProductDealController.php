<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductDeal;
use Illuminate\Http\Request;


class ProductDealController extends Controller
{
    protected $productDeal;

    public function __construct() {

        $this->productDeal = new ProductDeal();
        $this->middleware('permission:List Product Deal', ['only' => ['index']]);
        $this->middleware('permission:View Product Deal', ['only' => ['show']]);
        $this->middleware('permission:Create Product Deal', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Product Deal', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Product Deal', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['deals'] = ProductDeal::orderBy('created_at','DESC')->paginate(100);

        $data['categories'] = Category::orderBy('title','ASC')->get();
        $data['brands'] = Brand::orderBy('title','ASC')->get();

        activity('View')->log('List of Product Deals');
        return view('catalog/deal.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['brands'] = Brand::orderBy('title','ASC')->get();
        $data['categories'] = Category::orderBy('title','ASC')->get();
        $data['relatedDeals'] = ProductDeal::orderBy('deal_heading','ASC')->get();
        $data['products'] = [];

        return view('catalog/deal.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => ['required', 'string', 'max:255','unique:products'],
            'slug' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:255'],
            'keywords' => ['required', 'string', 'max:255'],
            'menu_text' => ['required', 'string', 'max:255'],
            'deal_heading' => ['required', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
            'images' => ['required','array','min:1'],
            'images.*' =>['image','mimes:jpeg,jpg,png,svg', 'max:100'],
            'brand_id' => ['required'],
            'category_id' => ['required','array','min:1'],
            're_order_level' => ['required'],
            'price' => ['required']
        ]);

        $productDeal = $this->productDeal->store($request);

        activity('Create')->log('New [ <b>' . $productDeal->title. ' </b> ] Product is created');
        return redirect()->route('deals.index')->with('message', 'Product Deal created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductDeal $deal)
    {

        activity('View')->log('<b>' . $deal->title. ' </b> Product detail.');

        return view('catalog/deal.show',compact('deal',));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['deal'] = ProductDeal::find($id);
        $data['brands'] = Brand::orderBy('title','ASC')->get();
        $data['categories'] = Category::orderBy('title','ASC')->get();
        $data['relatedProducts'] = Product::orderBy('product_heading','ASC')->get();

        $selectedCategories = array();
        foreach ($data['deal']->categories as $r) {
            array_push($selectedCategories, $r->category_id);
        }
        $data['selectedCategories'] = $selectedCategories;

        $selectedProducts = array();
        foreach ($data['deal']->relatedProducts as $r) {
            array_push($selectedProducts, $r->product_id);
        }
        $data['selectedProducts'] = $selectedProducts;

        $selectedBrands = array();
        foreach ($data['deal']->brands as $r) {
            array_push($selectedBrands, $r->brand_id);
        }
        $data['selectedBrands'] = $selectedBrands;

        $selectedRelatedProducts = array();
        foreach ($data['product']->relatedProducts as $rP) {
            array_push($selectedRelatedProducts, $rP->related_product_id);
        }
        $data['selectedRelatedProducts'] = $selectedRelatedProducts;

        return view('catalog/deal.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductDeal $productDeal)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255','unique:product_deals,title,'.$productDeal->id],
            'slug' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:255'],
            'keywords' => ['required', 'string', 'max:255'],
            'menu_text' => ['required', 'string', 'max:255'],
            'product_heading' => ['required', 'string', 'max:255'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
//            'images' => ['required','array','min:1'],
            'images.*' =>['image','mimes:jpeg,jpg,png,svg', 'max:100'],
            'brand_id' => ['required'],
            'category_id' => ['required','array','min:1'],
            're_order_level' => ['required'],
            'price' => ['required']
        ]);

        $deal = $this->productDeal->updateProduct($request,$productDeal);

        activity('Update')->log(' [ <b>' . $productDeal->title. ' </b> ] Deal is updated');
        return redirect()->route('deals..index')->with('message', 'Deal updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductDeal $productDeal)
    {
        $productDeal->delete();

        activity('Delete')->log('<b> ' . $productDeal->title. '</b>  Deal is deleted');
        return redirect()->route('desls.index')
            ->with('message','Deal deleted successfully');
    }

    public function checkProductBarcodeAvailability(Request $request) {

        $result = ProductVariant::where('barcode',$request->barcode)->first();

        if($result)
            return ['result'=>true,'message' => ' ' . $result->product->title . ' [' . $result->shade . ' ' . $result->size . '] has already this barcode.'];
        else
            return ['result' =>false];
    }

    public function searchProduct(Request $request) {

        $query = Product::query();
        if($request->category_id) {
            $catId = $request->category_id;
            $query = $query->whereHas('categories', function($query) use($catId){
                $query->where('category_id',$catId);
            });
        }
        if($request->brand_id)
            $query = $query->where('brand_id',$request->brand_id);
        if($request->status)
            $query = $request->where('status',$request->status);

        $data['products'] = $query->get();

        return view('catalog/product.search',$data);
    }

    public function ajaxProductSearch(Request $request) {

        $query = $request->get('value');

        $data['filterResult'] = Product::where([['title', 'LIKE', '%'. $query. '%'],['status',true]])->select('id','online_available_stock', 'product_heading', 'title', 'price' ,'discount_amount','discount_status','have_variants')->limit(50)->get();

        return $data;

    }

    public function addProductOrder(Request $request) {

        $product = Product::where('id',$request->product_id)->with('variants')->first();


        return view('order.add-product-order-ajax',compact('product'));
    }

    public function getBrandProducts(Request $request) {

        return Product::whereIn('brand_id',$request->brand_id)->where('status',true)->select('id','title')->get();
    }

    public function getProductVariants(Request $request) {

        return ProductVariant::where('product_id',$request->product_id)->get();
    }

    public function getDealProduct(Request $request) {

        $result = array();$price = 0;
        foreach(json_decode($request->dealProducts) as $product) {
            $temp['product'] = Product::where('id',$product->product_id)->select('id','title','price')->first();
            $temp['quan'] = $product->quantity;
            $temp['variant'] = ProductVariant::whereIn('id',$product->variants)->get();
            array_push($result,$temp);
        }

        $dealProducts = $request->dealProducts;

        return view('catalog/deal.update-price',compact('result','dealProducts'));

    }
}
