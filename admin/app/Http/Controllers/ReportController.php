<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $order,$brand,$product,$category;

    public function __construct() {
        $this->order = new Order();
        $this->brand = new Brand();
        $this->product = new Product();
        $this->category = new Category();
        $this->middleware('permission:Graph Report', ['only' => ['getGraphReport']]);
        $this->middleware('permission:Brand Report', ['only' => ['getBrandReportForm','getBrandReport']]);
        $this->middleware('permission:Brand Daily Graph', ['only' => ['getBrandDailyGraphForm','getBrandDailyGraph']]);
        $this->middleware('permission:Category Report', ['only' => ['getCategoryReportForm','getCategoryReport']]);
        $this->middleware('permission:Product Report', ['only' => ['getProductReportForm','getProductReport']]);
    }


    /**
     * Display the Stats in graph
     *
     * @return \Illuminate\Http\Response
     */
    public function getGraphReport() {

        $data = $this->order->getGraphReport();

        activity('View')->log('Graph Report.');

        return view('report.graph',$data);
    }
    
    public function getDailyGraphReport()
    {
        $data = $this->order->getDailyGraphReport();
        activity('View')->log('Daily Graph Report.');
        return view('report.daily-graph', $data);
    }

    /**
     * Show the form for generating a brand report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandReportForm() {

        $data['brands'] = Brand::select('id','title')->get();

        return view('report.brand-form',$data);
    }

    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandReport(Request $request) {

        $request->validate([
            'date_range' => ['required'],
            'brand_id' => ['required']
        ]);

        $data = $this->brand->getBrandReport($request);

        activity('View')->log('Brand Report.');

        return view('report.brand',$data);
    }

    /**
     * Show the form for generating a brand daily graph.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandDailyGraphForm() {

        $data['brands'] = Brand::select('id','title')->get();

        return view('report.brand-daily-form',$data);
    }

    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandDailyGraph(Request $request) {

        $request->validate([
            'brand_id' => ['required']
        ]);

        $data = $this->brand->getBrandDailyGraph($request);

        activity('View')->log('Brand Daily Graph.');

        return view('report.brand-daily',$data);
    }

    /**
     * Show the form for generating a category report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoryReportForm() {

        $data['categories'] = Category::select('id','title')->get();

        return view('report.category-form',$data);
    }

    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoryReport(Request $request) {

        $request->validate([
            'date_range' => ['required'],
            'category_id' => ['required']
        ]);

        $data = $this->category->getCategoryReport($request);

        activity('View')->log('Category Report.');

        return view('report.category',$data);
    }


    /**
     * Show the form for generating a Product report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductReportForm() {

        $data['products'] = Product::select('id','title')->get();

        return view('report.product-form',$data);
    }

    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductReport(Request $request) {

        $request->validate([
            'date_range' => ['required'],
            'product_id' => ['required']
        ]);

        $data = $this->product->getProductReport($request);

        activity('View')->log('Product Report.');

        return view('report.product',$data);
    }
    
    
    /**
     * Show the form for generating a Product report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatsReportForm() {


        return view('report.stats-form');
    }

    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatsReport(Request $request) {

        $request->validate([
            'date_range' => ['required'],
        ]);

        $data = $this->product->getStatsReport($request);

        activity('View')->log('Stats Report.');

        return view('report.stats',$data);
    }
    
    /**
     * Show the form for generating a Out of Stock Product report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOutOfStockProductsReportForm()
    {
        $data['brands'] = Brand::where('status', true)->select('id', 'title')->get();
        return view('report.out-of-stock-products-form', $data);
    }
    /**
     * Display the brand stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOutOfStockProductsReport(Request $request)
    {
        $request->validate([
            'brand_id' => ['required']
        ]);
        $data = $this->product->getOutOfStockProductsReport($request);
        activity('View')->log('Out Of Stock Products Report.');
        return view('report.out-of-stock-products', $data);
    }
    
     /**
     * Show the form for generating a brand report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandAvailableInventoryForm()
    {
        $data['stores'] = Store::where('status',true)->get();
        return view('report.brand-available-inventory-form', $data);
    }
    /**
     * Brand Available Inventory
     *
     * @return \Illuminate\Http\Response
     */
    public function getBrandAvailableInventory(Request $request)
    {
        $data = $this->brand->getBrandAvailableInventory($request);
        activity('View')->log('Brand Available Inventory.');
        return view('report.brand-available-inventory', $data);
    }
    /**
     * Brand Available Inventory
     *
     * @return \Illuminate\Http\Response
     */
    public function getSpecificBrandAvailableInventory($brand_id,$store_id)
    {
        $data = $this->brand->getSpecificBrandAvailableInventory($brand_id,$store_id);
        activity('View')->log('Specific Brand Available Inventory.');
        return view('report.specific-brand-available-inventory', $data);
    }




}
