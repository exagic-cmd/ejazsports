<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends BaseController
{


    public function thankYou()
    {
        $footerData = $this->getFooterCategories();
        return view('thankyou', $footerData);
    }

    public function index()
    {
        // Banners data
        $banners = DB::table('banners')->where('status', 1)->get();

        // Featured products
        $featuredProducts = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'products.id', '=', 'pv.product_id')
            ->where('products.status', 1)
            ->whereNull('products.deleted_at')
            ->where('products.is_featured', 1)
            ->select('products.*', 'brands.title as brand_title', 'pi.url as image_url', 'pv.min_additional')
            ->orderBy('products.id', 'desc')
            ->limit(12)->get(); // Fetch more to filter later

        // Sale products
        $saleProducts = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'products.id', '=', 'pv.product_id')
            ->where('products.status', 1)
            ->whereNull('products.deleted_at')
            ->where('products.discount_status', 1)
            ->whereColumn('products.price', '<', 'products.purchase_price')
            ->select('products.*', 'brands.title as brand_title', 'pi.url as image_url', 'pv.min_additional')
            ->orderBy('products.id', 'desc')
            ->limit(12)->get();

        // Top rated products (placeholder)
        $topratedProducts = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'products.id', '=', 'pv.product_id')
            ->where('products.status', 1)
            ->whereNull('products.deleted_at')
            ->select('products.*', 'brands.title as brand_title', 'pi.url as image_url', 'pv.min_additional')
            ->orderBy('products.id', 'desc')
            ->limit(12)->get();

        // Recently viewed (random)
        $recent_products = DB::table('products')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->leftJoin('categories', 'product_categories.category_id', '=', 'categories.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'products.id', '=', 'pv.product_id')
            ->where('products.status', 1)
            ->whereNull('products.deleted_at')
            ->select('products.*', 'brands.title as brand_title', 'pi.url as image_url', 'product_categories.category_id', 'categories.title as category_name', 'pv.min_additional')
            ->inRandomOrder()
            ->limit(20)->get();

        // Brands
        $brands = DB::table('brands')
            ->where('status', 1)
            ->limit(15)
            ->get();

        // First product per brand
        $firstProducts = [];
        foreach ($brands as $brand) {
            $product = DB::table('products as p')
                ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
                ->where('p.status', 1)
                ->whereNull('p.deleted_at')
                ->where('p.brand_id', $brand->id)
                ->orderBy('p.id', 'asc')
                ->select('p.*', 'pi.url as image')
                ->first();

            if ($product) {
                $product->have_variants = $product->have_variants ?? 0;
                $product = $this->attachVariantToProduct($product);
                $firstProducts[$brand->id] = $product;
            }
        }

        // Products by brand (second/third + last two)
        $productsByBrand = [];
        foreach ($brands as $brand) {
            $secondThird = DB::table('products as p')
                ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
                ->where('p.status', 1)
                ->whereNull('p.deleted_at')
                ->where('p.brand_id', $brand->id)
                ->orderBy('p.id', 'asc')
                ->skip(1)
                ->limit(4)
                ->select('p.*', 'pi.url as image')
                ->get();

            $lastTwo = DB::table('products as p')
                ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
                ->where('p.status', 1)
                ->whereNull('p.deleted_at')
                ->where('p.brand_id', $brand->id)
                ->orderBy('p.id', 'desc')
                ->limit(4)
                ->select('p.*', 'pi.url as image')
                ->get();

            $secondThird = $this->attachFirstVariants($secondThird)->values();
            $lastTwo = $this->attachFirstVariants($lastTwo)->values();

            $productsByBrand[$brand->id] = [
                'secondThird' => $secondThird,
                'lastTwo' => $lastTwo,
            ];
        }

        // Top 16 best-selling products
        $top_16_products = DB::table('products')
            ->join('order_products', 'products.id', '=', 'order_products.product_id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->leftJoin('categories', 'product_categories.category_id', '=', 'categories.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
            ->where('products.status', 1)
            ->select(
                'products.id',
                'products.title',
                'products.price',
                'products.brand_id',
                'products.is_new',
                'products.is_featured',
                'products.slug',
                'products.discount_amount',
                'products.discount_status',
                'products.have_variants',
                'products.available_stock',
                'brands.title as brand_title',
                DB::raw('MIN(categories.title) as category_title'),
                'pi.url as image_url',
                DB::raw('SUM(order_products.qty) as total_qty')
            )
            ->groupBy(
                'products.id',
                'products.title',
                'products.price',
                'products.brand_id',
                'products.is_new',
                'products.is_featured',
                'products.slug',
                'products.discount_amount',
                'products.discount_status',
                'products.have_variants',
                'products.available_stock',
                'brands.title',
                'pi.url'
            )
            ->having('total_qty', '>', 1)
            ->orderByDesc('total_qty')
            ->limit(20)->get(); // Fetch more to filter

        $top_16_products_first = $top_16_products->take(10);
        $top_16_products_second = $top_16_products->slice(10, 10);

        // Top categories by sales
        $top_categories = DB::table('categories')
            ->join('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->join('products', 'product_categories.product_id', '=', 'products.id')
            ->join('order_products', 'products.id', '=', 'order_products.product_id')
            ->where('categories.status', 1)
            ->whereNull('products.deleted_at')
            ->where('products.status', 1)
            ->select('categories.id', 'categories.title as name', DB::raw('SUM(order_products.qty) as total_qty'))
            ->groupBy('categories.id', 'categories.title')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        // Category products
        $category_products = [];
        foreach ($top_categories as $category) {
            $products = DB::table('products')
                ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->join('order_products', 'products.id', '=', 'order_products.product_id')
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin(DB::raw('(SELECT product_id, MIN(url) as url FROM product_images GROUP BY product_id) as pi'), 'products.id', '=', 'pi.product_id')
                ->where('products.status', 1)
                ->whereNull('products.deleted_at')
                ->where('product_categories.category_id', $category->id)
                ->select(
                    'products.id',
                    'products.title',
                    'products.price',
                    'products.brand_id',
                    'products.is_new',
                    'products.is_featured',
                    'products.slug',
                    'products.discount_amount',
                    'products.discount_status',
                    'products.have_variants',
                    'products.available_stock',
                    'brands.title as brand_title',
                    'pi.url as image_url',
                    DB::raw('SUM(order_products.qty) as total_qty')
                )
                ->groupBy(
                    'products.id',
                    'products.title',
                    'products.price',
                    'products.brand_id',
                    'products.is_new',
                    'products.is_featured',
                    'products.slug',
                    'products.discount_amount',
                    'products.discount_status',
                    'products.have_variants',
                    'products.available_stock',
                    'brands.title',
                    'pi.url'
                )
                ->having('total_qty', '>', 1)
                ->orderByDesc('total_qty')
                ->limit(30)->get();

            $products = $this->attachFirstVariants($products)->values();

            $category_products[$category->id] = [
                'first' => $products->take(10),
                'second' => $products->slice(10, 10),
            ];
        }

        // Categories for search dropdown
        $categories = DB::table('categories')->where('status', 1)->select('id', 'title')->orderBy('title', 'asc')->get();

        // Categories with images for promo section (first 4)
        $promoCategories = DB::table('categories')
            ->where('status', 1)
            ->whereNull('deleted_at')  // Exclude soft-deleted entries
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->select('id', 'title', 'image')
            ->orderBy('id', 'asc')
            ->limit(4)
            ->get();
        //dd($promoCategories);
        $footerData = $this->getFooterCategories();

        // Attach variants (show all products, out-of-stock will have badge)
        $featuredProducts = $this->attachFirstVariants($featuredProducts)->values()->take(6);
        $saleProducts = $this->attachFirstVariants($saleProducts)->values()->take(6);
        $topratedProducts = $this->attachFirstVariants($topratedProducts)->values()->take(6);
        $recent_products = $this->attachFirstVariants($recent_products)->values()->take(12);

        $top_16_products_first = $this->attachFirstVariants($top_16_products_first)->values();
        $top_16_products_second = $this->attachFirstVariants($top_16_products_second)->values();

        return view('home', array_merge(compact(
            'banners',
            'featuredProducts',
            'saleProducts',
            'topratedProducts',
            'brands',
            'productsByBrand',
            'firstProducts',
            'top_16_products_first',
            'top_16_products_second',
            'top_categories',
            'category_products',
            'categories',
            'recent_products',
            'promoCategories'
        ), $footerData));
    }

    /**
     * Attach first variant data to a collection of products
     */
    private function attachFirstVariants($products)
    {
        if (!$products || $products->isEmpty()) {
            return collect();
        }

        return $products->map(function ($product) {
            return $this->attachVariantToProduct($product);
        });
    }

    /**
     * Attach variant data + stock logic to a single product
     */
    private function attachVariantToProduct($product)
    {
        if (!$product) {
            return null;
        }

        $product->have_variants = $product->have_variants ?? 0;
        $product->effective_price = $product->price ?? 0;
        $product->total_stock = 0;
        $product->in_stock = false;
        $product->first_variant_id = null;
        $product->first_variant_stock = 0;
        $product->variant_size = null;
        $product->variant_shade = null;

        if ($product->have_variants == 1) {
            $variants = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->where('status', 1)
                ->orderBy('id', 'asc')
                ->get();

            if ($variants->isNotEmpty()) {
                // Total stock from all variants
                $product->total_stock = $variants->sum('available_stock');

                // In stock if ANY variant has stock > 0
                $product->in_stock = $variants->contains(fn($v) => ($v->available_stock ?? 0) > 0);

                // Prefer first variant with stock > 0 for cart, fallback to first variant
                $firstInStock = $variants->first(fn($v) => ($v->available_stock ?? 0) > 0);
                $firstVariant = $firstInStock ?? $variants->first();

                $product->first_variant_id = $firstVariant->id;
                $product->first_variant_stock = $firstVariant->available_stock ?? 0;
                $product->variant_size = $firstVariant->size;
                $product->variant_shade = $firstVariant->shade;
                $product->variant_barcode = $firstVariant->barcode;
                $product->variant_price_add = $firstVariant->additional_price ?? 0;
                $product->variant_stock = $firstVariant->available_stock ?? 0;

                // Effective price from selected variant
                $product->effective_price = ($product->price ?? 0) + ($firstVariant->additional_price ?? 0);
            }
        } else {
            // No variants → use base stock
            $product->total_stock = $product->available_stock ?? 0;
            $product->in_stock = ($product->total_stock > 0);
            $product->effective_price = $product->price ?? 0;
        }

        return $product;
    }
    // need id to display specific data
    public function productDetail()
    {
        return view('web.product-detail');
    }

    public function account()
    {
        $customerId = session('customer_id');

        if (!$customerId) {
            return redirect()->route('customer.login')->with('error', 'Please login to view your account.');
        }

        $footerData = $this->getFooterCategories();

        return view('account', $footerData);
    }

    public function myOrders(Request $request)
    {
        $customerId = session('customer_id');

        if (!$customerId) {
             return redirect()->route('customer.login')->with('error', 'Please login to view your orders.');
        }

        $query = \App\Models\Order::where('customer_id', $customerId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->query());

        $footerData = $this->getFooterCategories();

        return view('orders', array_merge(compact('orders'), $footerData));
    }

    public function orderDetail($id)
    {
        $customerId = session('customer_id');

        if (!$customerId) {
             return redirect()->route('customer.login')->with('error', 'Please login to view order details.');
        }

        // Fetch order and verify ownership
        $order = \App\Models\Order::with('products')->where('id', $id)->where('customer_id', $customerId)->first();

        if (!$order) {
            return redirect()->route('account')->with('error', 'Order not found.');
        }

        $footerData = $this->getFooterCategories();
        return view('account-order-detail', array_merge(compact('order'), $footerData));
    }

    public function getAllCategories(Request $request)
    {
        // Categories with Pagination (fixed at 24 per page)
        $data['categories'] = DB::table('categories')
            ->where('status', true)
            ->select('id', 'title', 'serial_no', 'image')
            ->orderBy('serial_no', 'ASC')
            ->paginate(21);

        // Best Sellers (unchanged)
        $data['bestSellers'] = DB::table('products as p')
            ->join('brands as b', 'p.brand_id', '=', 'b.id')
            ->join('product_categories as pc', 'p.id', '=', 'pc.product_id')
            ->join('categories as c', 'pc.category_id', '=', 'c.id')
            ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->whereIn('p.brand_id', [4, 5, 6, 7])
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->where('p.available_stock', '>', 0)
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.have_variants',
                'p.purchase_price',
                'p.available_stock',
                'pi.url as image_url',
                'b.title as brand_name',
                'c.title as category_title',
                'pv.min_additional'
            )
            ->orderBy('p.available_stock', 'DESC')
            ->limit(24)
            ->get();
             $footerData = $this->getFooterCategories();
        return view('categories', $data,$footerData);
    }

    // Get category
    public function getCategoryProducts(Request $request, $id)
    {
        // Validate input parameters
        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|array',
            'brand.*' => 'exists:brands,id',
            'sort' => 'nullable|in:default,popularity,latest,price_low,price_high',
        ]);

        // Get category details
        $data['category'] = DB::table('categories')
            ->where('id', $id)
            ->first();

        if (!$data['category']) {
            abort(404, 'Category not found');
        }

        // Get max price for the current category (including variant additions)
        $data['max_price'] = DB::table('products as p')
            ->join('product_categories as pc', 'p.id', '=', 'pc.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('pc.category_id', $id)
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->selectRaw('MAX(p.price + IFNULL(pv.min_additional, 0)) as max_val')
            ->value('max_val') ?? 1000;

        // Get categories for sidebar
        $data['categories'] = DB::table('categories as c')
            ->join('product_categories as pc', 'c.id', '=', 'pc.category_id')
            ->where('c.status', true)
            ->groupBy('c.id', 'c.title', 'c.serial_no')
            ->havingRaw('COUNT(pc.product_id) > 0')
            ->orderBy('c.serial_no', 'ASC')
            ->select('c.id', 'c.title', 'c.serial_no')
            ->get()
            ->map(function ($cat) {
                $cat->product_count = DB::table('product_categories')->where('category_id', $cat->id)->count();
                return $cat;
            });

        // Get brands for filter
        $data['brands'] = DB::table('brands')
            ->select('id', 'title')
            ->get()
            ->map(function ($brand) use ($id) {
                $brand->product_count = DB::table('products')
                    ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                    ->where('products.brand_id', $brand->id)
                    ->whereNull('products.deleted_at')
                    ->where('product_categories.category_id', $id)
                    ->count();
                return $brand;
            });

        // Base query for products
        $query = DB::table('products as p')
            ->join('product_categories as pc', 'p.id', '=', 'pc.product_id')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('categories as c', 'pc.category_id', '=', 'c.id')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->whereRaw('pi.id = (SELECT MIN(id) FROM product_images WHERE product_id = p.id)');
            })
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('pc.category_id', $id)
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.short_description as short_desc',
                'p.have_variants',
                'p.available_stock',
                'pi.url as image_url',
                'b.title as brand_name',
                'c.title as category_title',
                'pv.min_additional'
            );

        // Apply brand filter
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $query->whereIn('p.brand_id', $request->input('brand'));
        }

        // Apply price filter
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', $data['max_price']);

        if ($minPrice > $maxPrice) {
            $minPrice = 0; // Reset to avoid invalid range
        }

        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) >= ?', [$minPrice]);
        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) <= ?', [$maxPrice]);

        // Sorting logic
        $sort = $request->input('sort', 'default');
        switch ($sort) {
            case 'popularity':
                $query->orderBy('p.available_stock', 'DESC');
                break;
            case 'latest':
                $query->orderBy('p.created_at', 'DESC');
                break;
            case 'price_low':
                $query->orderBy('p.price', 'ASC');
                break;
            case 'price_high':
                $query->orderBy('p.price', 'DESC');
                break;
            default:
                $query->orderBy('p.title', 'ASC');
                break;
        }

        // Paginate products (24 per page)
        $data['products'] = $query->paginate(24)->appends($request->query());
          $footerData = $this->getFooterCategories();
        return view('category-products', $data,$footerData);
    }

    public function brands()
    {
        $data['brands'] = DB::table('brands')
            ->where('status', 1)
            // ->where('show_in_menu', 1)
            ->get();

        $bestSellers = DB::table('products as p')
            ->join('product_images as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->whereRaw('pi.id = (SELECT MIN(id) FROM product_images WHERE product_id = p.id)');
            })
            ->join('order_products as op', 'p.id', '=', 'op.product_id')
            ->join('brands as b', 'p.brand_id', '=', 'b.id') // Join brands for brand_title
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.have_variants',
                DB::raw('SUM(op.qty) as total_qty'),
                'pi.url', // Select the url column
                'b.title as brand_title', // Select brand title
                'pv.min_additional'
            )
            ->groupBy(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.have_variants',
                'pi.url', // Use actual column name
                'b.title', // Group by brand title
                'pv.min_additional'
            )
            ->having('total_qty', '>', 1)
            ->orderByDesc('total_qty')
            ->limit(24)
            ->get();

        $data['bestSellers'] = $bestSellers;
          $footerData = $this->getFooterCategories();
        // dd($data['bestSellers']); // Uncomment for debugging
        return view('brands', $data,$footerData);
    }
    /**
     * Search products across site, optionally filter by category.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|exists:categories,id',
            'brand' => 'nullable|array',
        ]);

        // Get dynamic max price for results
        $data['max_price'] = DB::table('products as p')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->selectRaw('MAX(p.price + IFNULL(pv.min_additional, 0)) as max_val')
            ->value('max_val') ?? 1000;

        $q = trim($request->input('q', ''));
        $categoryId = $request->input('category');

        // Base product query
        $query = DB::table('products as p')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->whereRaw('pi.id = (SELECT MIN(id) FROM product_images WHERE product_id = p.id)');
            })
            ->leftJoin('product_categories as pc', 'p.id', '=', 'pc.product_id')
            ->leftJoin('categories as c', 'pc.category_id', '=', 'c.id')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.short_description as short_desc',
                'p.have_variants',
                'p.available_stock',
                'pi.url as image_url',
                'b.title as brand_name',
                'c.title as category_title',
                'c.id as category_id',
                'pv.min_additional'
            );

        if ($categoryId) {
            $query->where('pc.category_id', $categoryId);
        }

        // Apply brand filters if present
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $query->whereIn('p.brand_id', $request->input('brand'));
        }

        // Apply price filter
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', $data['max_price']); // use dynamic max
        if ($minPrice > $maxPrice) {
            $minPrice = 0; // reset to avoid invalid range
        }

        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) >= ?', [$minPrice]);
        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) <= ?', [$maxPrice]);

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('p.title', 'like', "%{$q}%")
                    ->orWhere('p.short_description', 'like', "%{$q}%")
                    ->orWhere('b.title', 'like', "%{$q}%");
            });
        }

        $query->orderBy('p.title', 'ASC');

        $data['products'] = $query->groupBy(
            'p.id', 'p.title', 'p.price', 'p.brand_id', 'p.is_new', 'p.is_featured', 'p.slug', 'p.discount_amount', 'p.discount_status', 'p.short_description', 'p.have_variants', 'p.available_stock', 'pi.url', 'b.title', 'c.title', 'c.id', 'pv.min_additional'
        )->paginate(24)->appends($request->query());

        // For sidebar filters
        $data['categories'] = DB::table('categories as c')
            ->join('product_categories as pc', 'c.id', '=', 'pc.category_id')
            ->where('c.status', true)
            ->groupBy('c.id', 'c.title', 'c.serial_no')
            ->havingRaw('COUNT(pc.product_id) > 0')
            ->orderBy('c.serial_no', 'ASC')
            ->select('c.id', 'c.title', 'c.serial_no')
            ->get()->map(function ($cat) {
                $cat->product_count = DB::table('product_categories')->where('category_id', $cat->id)->count();
                return $cat;
            });

        $data['brands'] = DB::table('brands')
            ->where('status', true)
            ->select('id', 'title')
            ->get()->map(function ($brand) {
                $brand->product_count = DB::table('products')->where('brand_id', $brand->id)->whereNull('deleted_at')->count();
                return $brand;
            });

        // get max price for slider (including variants)
        $data['max_price'] = DB::table('products as p')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->selectRaw('MAX(p.price + IFNULL(pv.min_additional, 0)) as max_val')
            ->value('max_val') ?? 1000;

        // Category metadata to display
        if ($categoryId) {
            $data['category'] = DB::table('categories')->where('id', $categoryId)->first();
        } else {
            $fakeCategory = new \stdClass();
            $fakeCategory->id = 0;
            $fakeCategory->title = $q ? "Search results for \"{$q}\"" : 'Search results';
            $data['category'] = $fakeCategory;
        }
          $footerData = $this->getFooterCategories();
        return view('category-products', $data,$footerData);
    }
    public function brandsProducts(Request $request, $id)
    {
        // Validate input parameters
        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|array',
            'brand.*' => 'exists:brands,id',
            'sort' => 'nullable|in:default,popularity,latest,price_low,price_high',
        ]);

        // Get brand details
        $data['brand'] = DB::table('brands')
            ->where('id', $id)
            ->first();

        if (!$data['brand']) {
            abort(404, 'Brand not found');
        }

        // Get max price for the current brand (including variants)
        $data['max_price'] = DB::table('products as p')
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.brand_id', $id)
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->selectRaw('MAX(p.price + IFNULL(pv.min_additional, 0)) as max_val')
            ->value('max_val') ?? 1000;

        // Get categories for sidebar
        $data['categories'] = DB::table('categories as c')
            ->join('product_categories as pc', 'c.id', '=', 'pc.category_id')
            ->where('c.status', true)
            ->groupBy('c.id', 'c.title', 'c.serial_no')
            ->havingRaw('COUNT(pc.product_id) > 0')
            ->orderBy('c.serial_no', 'ASC')
            ->select('c.id', 'c.title', 'c.serial_no')
            ->get()
            ->map(function ($cat) {
                $cat->product_count = DB::table('product_categories')->where('category_id', $cat->id)->count();
                return $cat;
            });

        // Get brands for filter
        $data['brands'] = DB::table('brands')
            ->select('id', 'title')
            ->get()
            ->map(function ($brand) use ($id) {
                $brand->product_count = DB::table('products')
                    ->where('brand_id', $brand->id)
                    ->whereNull('deleted_at')
                    ->count();
                return $brand;
            });

        // Base query for products
        $query = DB::table('products as p')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->whereRaw('pi.id = (SELECT MIN(id) FROM product_images WHERE product_id = p.id)');
            })
            ->leftJoin(DB::raw('(SELECT product_id, MIN(additional_price) as min_additional FROM product_variants GROUP BY product_id) as pv'), 'p.id', '=', 'pv.product_id')
            ->where('p.brand_id', $id)
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.short_description as short_desc',
                'p.have_variants',
                'p.available_stock',
                'pi.url as image_url',
                'b.title as brand_name',
                'pv.min_additional'
            );

        // Apply brand filter (if additional brands are selected)
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $query->whereIn('p.brand_id', $request->input('brand'));
        }

        // Apply price filter
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', $data['max_price']);

        if ($minPrice > $maxPrice) {
            $minPrice = 0; // Reset to avoid invalid range
        }

        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) >= ?', [$minPrice]);
        $query->whereRaw('(p.price + IFNULL(pv.min_additional, 0)) <= ?', [$maxPrice]);

        // Sorting logic
        $sort = $request->input('sort', 'default');
        switch ($sort) {
            case 'popularity':
                $query->orderBy('p.available_stock', 'DESC');
                break;
            case 'latest':
                $query->orderBy('p.created_at', 'DESC');
                break;
            case 'price_low':
                $query->orderBy('p.price', 'ASC');
                break;
            case 'price_high':
                $query->orderBy('p.price', 'DESC');
                break;
            default:
                $query->orderBy('p.title', 'ASC');
                break;
        }

        // Paginate products
        $data['products'] = $query->paginate(24)->appends($request->query());

        // Bestsellers
        $data['bestsellers'] = DB::table('products as p')
            ->join('order_products as op', 'p.id', '=', 'op.product_id')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->whereRaw('pi.id = (SELECT MIN(id) FROM product_images WHERE product_id = p.id)');
            })
            ->where('p.brand_id', $id)
            ->where('p.status', true)
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.have_variants',
                'p.available_stock',
                'pi.url as image_url',
                DB::raw('SUM(op.qty) as total_qty')
            )
            ->groupBy(
                'p.id',
                'p.title',
                'p.price',
                'p.brand_id',
                'p.is_new',
                'p.is_featured',
                'p.slug',
                'p.discount_amount',
                'p.discount_status',
                'p.have_variants',
                'p.available_stock',
                'pi.url'
            )
            ->having('total_qty', '>', 1)
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();
                  $footerData = $this->getFooterCategories();
        return view('brands-products', $data, $footerData);
    }

    public function updateProfile(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->route('customer.login');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone,
            'updated_at' => now(),
        ];

        // Handle Password Change
        if ($request->filled('new_password')) {
            $customer = DB::table('customers')->where('id', $customerId)->first();

            if (!Hash::check($request->current_password, $customer->password)) {
                 return back()->with('error', 'Current password is incorrect.');
            }

            if (Hash::check($request->new_password, $customer->password)) {
                 return back()->with('error', 'New password cannot be the same as your current password.');
            }

            $data['password'] = Hash::make($request->new_password);
        }

        DB::table('customers')->where('id', $customerId)->update($data);

        // Update Session
        session([
            'customer_first_name' => $request->first_name,
            'customer_last_name' => $request->last_name,
            'customer_phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}

