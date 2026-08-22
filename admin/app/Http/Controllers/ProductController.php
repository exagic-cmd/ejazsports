<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use App\Models\Bundle;
use App\Models\BundleImage;
use App\Models\BundleVariant;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\PriceupNotification;
use App\Models\Receiving;
use DB;

class ProductController extends Controller
{
    protected $product;

    public function __construct()
    {

        $this->product = new Product();
        $this->middleware('permission:List Product', ['only' => ['index']]);
        $this->middleware('permission:View Product', ['only' => ['show']]);
        $this->middleware('permission:Create Product', ['only' => ['create', 'store']]);
        $this->middleware('permission:Edit Product', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Delete Product', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $result = Product::where('have_variants',false)->get();

        // $count = 1;
        // foreach($result as $res) {
        //     $res->barcode = time() . $count++;;
        //     $res->save();
        // }


        $data['products'] = Product::orderBy('created_at', 'DESC')->paginate(100);

        $data['categories'] = Category::orderBy('title', 'ASC')->get();
        $data['brands'] = Brand::orderBy('title', 'ASC')->get();

        activity('View')->log('List of Products');
        return view('catalog/product.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['brands'] = Brand::orderBy('id', 'DESC')->get();
        $data['categories'] = Category::orderBy('title', 'ASC')->get();
        $data['colors'] = Color::orderBy('name', 'ASC')->get();
        $data['sizes'] = Size::groupBy('name')->orderBy('name', 'ASC')->get();

        //  dd($data['sizes']);

        return view('catalog/product.create', $data);
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
            'title' => ['required', 'string', 'max:255', 'unique:products'],
            'status' => ['required', 'boolean'],
            'serial_no' => ['nullable', 'integer'],
            // 'images' => ['required','array','min:1'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,svg', 'max:5000'],
            'brand_id' => ['required'],
            'category_id' => ['required', 'array', 'min:1'],
            're_order_level' => ['required'],
            'price' => ['required']
        ]);

        $product = $this->product->store($request);

        activity('Create')->log('New [ <b>' . $product->title . ' </b> ] Product is created');
        return redirect()->route('products.index')->with('message', 'Product created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {

        $data = [];
        $hasMultiSizeVariants = false;
        $show_bundle_button = false; // Initialize here

        // Load variants if product has them
        if ($product->have_variants == 1) {
            $product->load(['variants' => function ($query) {
                $query->select('id', 'product_id', 'shade', 'size', 'barcode', 'additional_price', 'purchase_price', 'dz_price', 'status', 'available_stock');
            }]);

            // Group variants by shade and check for multiple sizes
            $data['variants'] = $product->variants
                ->groupBy('shade')
                ->map(function ($variants) {
                    $sizes = $variants->pluck('size')->unique()->values()->toArray();
                    return [
                        'sizes' => $sizes,
                        'variant_ids' => $variants->pluck('id')->toArray(),
                        'has_multiple_sizes' => count($sizes) > 1,
                        'size_count' => count($sizes)
                    ];
                });

            $show_bundle_button = $data['variants']->contains(fn($v) => $v['has_multiple_sizes']);
        }
         $product->load(['purchases' => function ($query) {
        $query->whereHas('receiving', function ($q) {
            $q->where('status', '!=', Receiving::APPROVAL_PENDING); // Exclude status 1
        })->with(['receiving.supplier', 'variant']);
    }]);

        // Store inventory calculations
        $stores = Store::orderBy('name', 'ASC')->get();
        $storeVariants = [];

        foreach ($stores as $store) {
            foreach ($product->variants as $variant) {
                $purchased = StoreProductStock::where([
                    ['store_id', $store->id],
                    ['variant_id', $variant->id]
                ])->sum('purchase_qty');

                $sold = StoreProductStock::where([
                    ['store_id', $store->id],
                    ['variant_id', $variant->id]
                ])->sum('sold_qty');

                $storeVariants[$store->id][$variant->id] = $purchased - $sold;
            }
        }

        // Stock status tracking
        $stockStatus = [];
        $stockSold = [];

        foreach ($product->purchases as $purchase) {
            $allStocks = StoreProductStock::where([
                ['product_id', $purchase->product_id],
                ['variant_id', $purchase->product_variant_id]
            ])->count();

            if ($allStocks == 1) {
                $stock = StoreProductStock::where([
                    ['receiving_id', $purchase->receiving_id],
                    ['product_id', $purchase->product_id],
                    ['variant_id', $purchase->product_variant_id]
                ])->first();

                if ($stock) {
                    $stockStatus[$purchase->id] = ($stock->sold_qty == $stock->purchase_qty)
                        ? 'SOLD'
                        : 'CURRENT';
                    $stockSold[$purchase->id] = $stock->sold_qty;
                } else {
                    $stockStatus[$purchase->id] = 'CURRENT';
                }
            } else {
                $stock = StoreProductStock::where([
                    ['receiving_id', $purchase->receiving_id],
                    ['product_id', $purchase->product_id],
                    ['variant_id', $purchase->product_variant_id]
                ])->first();

                if ($stock) {
                    if ($stock->sold_qty == $stock->purchase_qty) {
                        $stockStatus[$purchase->id] = 'SOLD';
                    } elseif ($stock->sold_qty > 0) {
                        $stockStatus[$purchase->id] = 'CURRENT';
                    } else {
                        $availableStock = StoreProductStock::where([
                            ['product_id', $purchase->product_id],
                            ['variant_id', $purchase->product_variant_id]
                        ])
                            ->whereRaw('sold_qty < purchase_qty')
                            ->orderBy('id', 'ASC')
                            ->first();

                        $stockStatus[$purchase->id] = ($availableStock && $availableStock->receiving_id == $purchase->receiving_id)
                            ? 'CURRENT'
                            : '';
                        $stockSold[$purchase->id] = $availableStock ? $availableStock->sold_qty : 0;
                    }
                    $stockSold[$purchase->id] = $stock->sold_qty;
                } else {
                    $stockStatus[$purchase->id] = '';
                }
            }
        }

        // Log activity
        activity('View')
            ->performedOn($product)
            ->log('<b>' . $product->title . '</b> Product detail viewed');

        return view('catalog/product.show', compact(
            'product',
            'data',
            'stores',
            'storeVariants',
            'stockStatus',
            'stockSold',
            'show_bundle_button'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['product'] = Product::find($id);
        $data['brands'] = Brand::orderBy('title', 'ASC')->get();
        $data['categories'] = Category::orderBy('title', 'ASC')->get();
        $data['relatedProducts'] = Product::orderBy('title', 'ASC')->get();
        $data['discounts'] = Discount::where([['type', Discount::PRODUCT], ['status', true]])->get();

        $selectedCategories = array();
        foreach ($data['product']->categories as $r) {
            array_push($selectedCategories, $r->category_id);
        }
        $data['selectedCategories'] = $selectedCategories;

        $selectedRelatedProducts = array();
        foreach ($data['product']->relatedProducts as $rP) {
            array_push($selectedRelatedProducts, $rP->related_product_id);
        }
        $data['selectedRelatedProducts'] = $selectedRelatedProducts;

        return view('catalog/product.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:products,title,' . $product->id],
            // 'status'=> ['required','boolean'],
            'serial_no' => ['nullable', 'integer'],
            //            'images' => ['required','array','min:1'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,svg', 'max:5000'],
            'brand_id' => ['required'],
            'category_id' => ['required', 'array', 'min:1'],
            're_order_level' => ['required'],
            'price' => ['required']
        ]);

        $product = $this->product->updateProduct($request, $product);

        activity('Update')->log(' [ <b>' . $product->title . ' </b> ] Product is updated');
        return redirect()->route('products.index')->with('message', 'Product updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        activity('Delete')->log('<b> ' . $product->title . '</b>  Product is deleted');
        return redirect()->route('products.index')
            ->with('message', 'Product deleted successfully');
    }

    public function checkProductBarcodeAvailability(Request $request)
    {

        $result = ProductVariant::where('barcode', $request->barcode)->first();

        if ($result)
            return ['result' => true, 'message' => ' ' . $result->product->title . ' [' . $result->shade . ' ' . $result->size . '] has already this barcode.'];
        else
            return ['result' => false];
    }

    public function searchProduct(Request $request)
    {

        $query = Product::query();
        if ($request->category_id) {
            $catId = $request->category_id;
            $query = $query->whereHas('categories', function ($query) use ($catId) {
                $query->where('category_id', $catId);
            });
        }
        if ($request->searchbox) {
            $query = $query->where('title', 'LIKE', '%' . $request->searchbox . '%')
                ->orWhere('id', $request->searchbox);
        }
        if ($request->brand_id)
            $query = $query->where('brand_id', $request->brand_id);
        if ($request->status)
            $query = $request->where('status', $request->status);

        $data['products'] = $query->get();

        return view('catalog/product.search', $data);
    }

    public function ajaxProductSearch(Request $request)
    {

        $query = $request->get('value');

        $data['filterResult'] = Product::where([['title', 'LIKE', '%' . $query . '%'], ['status', true]])->select('id', 'online_available_stock', 'product_heading', 'title', 'price', 'discount_amount', 'discount_status', 'have_variants')->limit(50)->get();

        return $data;
    }

    public function addProductOrder(Request $request)
    {

        $product = Product::where('id', $request->product_id)->with('variants')->first();


        return view('order.add-product-order-ajax', compact('product'));
    }

    public function updateImageModal(Request $request)
    {

        $data['image'] = ProductImage::where('id', $request->image_id)->first();

        return view('catalog/product/update-image-modal', $data);
    }

    public function updateImage(Request $request)
    {

        $productImage = ProductImage::where('id', $request->image_id)->first();

        if ($request->file('image')) {

            File::delete('storage/' . $productImage->url);
            $name = time() . '-' . '-' . $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('images/product', $name);
            $productImage->url = $path;
        }


        $productImage->serial_no = $request->image_serial_no;
        $productImage->status = isset($request->image_status) ? $request->image_status : 0;
        $productImage->save();

        return redirect()->back()->with('message', 'Product Image updated Successfully!');
    }

    public function removeImage(Request $request)
    {

        $productImage = ProductImage::where('id', $request->image_id)->first();

        if ($request->file('image')) {

            File::delete('storage/' . $productImage->url);
        }


        ProductImage::where('id', $request->image_id)->delete();

        return redirect()->back()->with('message', 'Product Image deleted Successfully!');
    }

    public function getPriceUpNotification()
    {

        $data['notifications'] = PriceupNotification::orderBy('id', 'DESC')->get();

        return view('catalog/product/priceup-notification', $data);
    }

    public function printBarcode($id)
    {

        $data['product'] = Product::where('id', $id)->first();

        return view('catalog/product/print-barcode', $data);
    }
    
    public function generateAutoSku(Request $request)
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $sku = 'SKU-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        while(Product::where('code', $sku)->exists()) {
            $nextId++;
            $sku = 'SKU-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        }

        return response()->json(['sku' => $sku]);
    }

    public function printVariantBarcode($id)
    {

        $data['variant'] = ProductVariant::with('product')->where('id', $id)->first();

        return view('catalog/product/print-v-barcode', $data);
    }

    public function zeroStockByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required'
        ]);

        $barcode = $request->barcode;
        
        // Find product by main barcode or product code
        $product = Product::where('barcode', $barcode)->orWhere('code', $barcode)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found with this barcode or code.'], 404);
        }

        DB::beginTransaction();
        try {
            // Update main product
            $product->available_stock = 0;
            $product->is_in_stock = 0;
            $product->save();

            // Update all variants if they exist
            $variantCount = 0;
            if ($product->have_variants) {
                $variantCount = ProductVariant::where('product_id', $product->id)->update([
                    'available_stock' => 0,
                    'online_available_stock' => 0
                ]);
            }

            // Also zero out store product stocks if applicable
            StoreProductStock::where('product_id', $product->id)->update([
                'sold_qty' => DB::raw('purchase_qty')
            ]);

            activity('Update')->log('<b> ' . $product->title . '</b> Product and variants stock set to 0 via barcode scan');

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Stock for ' . $product->title . ' (and ' . $variantCount . ' variants) has been set to 0 successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error updating stock: ' . $e->getMessage()], 500);
        }
    }

   public function generateBundles(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id'
    ]);

    try {
        $product = Product::with([
            'variants' => function ($query) {
                $query->where('status', 1);
            },
            'images' => function ($query) {
                $query->where('status', true);
            }
        ])->findOrFail($request->product_id);

        if ($product->variants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Product has no active variants to bundle'
            ], 400);
        }

        DB::beginTransaction();

        $generatedBundles = 0;
        $existingBundles = 0;
        $shadeGroups = $product->variants->groupBy('shade');

        foreach ($shadeGroups as $shade => $variants) {
            if (empty($shade)) {
                continue;
            }

            $expectedVariantIds = $variants->pluck('id')->sort()->values()->toArray();

            $existingBundle = Bundle::where('name', $product->title . ' - ' . $shade)
                ->whereHas('variants', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->with(['variants' => function ($q) {
                    $q->select('product_variant_id', 'bundle_id');
                }])
                ->get()
                ->first(function ($bundle) use ($expectedVariantIds) {
                    $actualVariantIds = $bundle->variants->pluck('product_variant_id')->sort()->values()->toArray();
                    return $actualVariantIds === $expectedVariantIds;
                });

            if ($existingBundle) {
                $existingBundles++;
                continue;
            }

            $totalPurchasePrice = $variants->sum('purchase_price');
            $totalAdditionalPrice = $variants->sum('additional_price');

            $bundle = Bundle::create([
                'name' => $product->title . ' - ' . $shade,
                'short_desc' => 'Complete bundle of sizes for ' . $shade . ' shade',
                'full_desc' => 'This bundle includes all available sizes for the ' . $shade . ' shade of ' . $product->name,
                'purchase_price' => $totalPurchasePrice,
                'additional_price' => $totalAdditionalPrice,
                'status' => 1
            ]);

            foreach ($variants as $variant) {
                BundleVariant::create([
                    'bundle_id' => $bundle->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'path' => optional($product->images->first())->path ?? ''
                ]);
            }

            $defaultImageSet = false;

            foreach ($product->images as $image) {
                if (empty($image->path)) continue;

                BundleImage::create([
                    'bundle_id' => $bundle->id,
                    'path' => $image->path,
                    'is_default' => !$defaultImageSet
                ]);
                $defaultImageSet = true;
            }

            $generatedBundles++;
        }

        DB::commit();

        if ($generatedBundles > 0 && $existingBundles > 0) {
            $message = "Successfully generated $generatedBundles bundle(s) ($existingBundles already existed)";
        } elseif ($generatedBundles > 0) {
            $message = "Successfully generated $generatedBundles bundle(s)";
        } elseif ($existingBundles > 0) {
            $message = "All bundles already exist ($existingBundles bundle(s) found)";
        } else {
            $message = "No bundles could be generated";
        }

        return response()->json([
            'success' => $generatedBundles > 0,
            'message' => $message,
            'bundles_count' => $generatedBundles,
            'existing_bundles' => $existingBundles,
            'data_modified' => $generatedBundles > 0
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate bundles: ' . $e->getMessage(),
            'exception' => get_class($e)
        ], 500);
    }
}

}
