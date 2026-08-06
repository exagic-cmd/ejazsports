<?php

namespace App\Http\Controllers;
use App\Repositories\ProductRepository;
use App\Repositories\BrandRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected $BrandRepository, $ProductRepository;

    public function __construct(BrandRepository $BrandRepository, ProductRepository $ProductRepository)
    {
        $this->BrandRepository = $BrandRepository;
        $this->ProductRepository = $ProductRepository;
    }

    public function productDetail($slugOrId){
        // Support both slug and numeric ID
        $product = null;
        
        // 1. Try to find by ID if numeric (ensures excluded if deleted)
        if (is_numeric($slugOrId)) {
            $dbProduct = DB::table('products')->where('id', $slugOrId)->whereNull('deleted_at')->first();
            if ($dbProduct) {
                // If slug is missing in DB, populate it now from title
                if (empty($dbProduct->slug)) {
                    $newSlug = \Illuminate\Support\Str::slug($dbProduct->title);
                    DB::table('products')->where('id', $dbProduct->id)->update(['slug' => $newSlug]);
                }
                $product = $this->ProductRepository->product($dbProduct->id);
            }
        }
        
        // 2. Try by Exact Slug Column
        if (!$product) {
            $dbProduct = DB::table('products')->where('slug', $slugOrId)->whereNull('deleted_at')->first();
            if ($dbProduct) {
                $product = $this->ProductRepository->product($dbProduct->id);
            }
        }
        
        // 3. ON THE GO: If still no match, find a product (not deleted) where sluggified title matches the URL
        if (!$product) {
             $potentialProducts = DB::table('products')->whereNull('deleted_at')->where(function($q) {
                 $q->whereNull('slug')->orWhere('slug', '');
             })->get(['id', 'title']);

             foreach($potentialProducts as $p) {
                 if (\Illuminate\Support\Str::slug($p->title) === $slugOrId) {
                     // Found match by title! Save this slug for future requests
                     DB::table('products')->where('id', $p->id)->update(['slug' => $slugOrId]);
                     $product = $this->ProductRepository->product($p->id);
                     break;
                 }
             }
        }
        
        // 4. Fallback for slug-ID format (legacy)
        if (!$product && preg_match('/-(\d+)$/', $slugOrId, $matches)) {
            $productId = $matches[1];
            $dbProduct = DB::table('products')->where('id', $productId)->whereNull('deleted_at')->first();
            if ($dbProduct) {
                // Populate slug if missing
                if (empty($dbProduct->slug)) {
                    $newSlug = \Illuminate\Support\Str::slug($dbProduct->title);
                    DB::table('products')->where('id', $dbProduct->id)->update(['slug' => $newSlug]);
                }
                $product = $this->ProductRepository->product($dbProduct->id);
            }
        }
        
        if (!$product) {
            abort(404, 'Product not found');
        }
        
        $id = $product->id;
        // Always use the latest slug from the product model
        $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
        
        $category_id = DB::table('product_categories')->where('product_id', $id)->value('category_id');
        $category = DB::table('categories')
            ->where('id', $category_id)
            ->whereNull('deleted_at')
            ->first();

        $relatedProducts = DB::table('products')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->where('product_categories.category_id', $category_id)
            ->where('products.id', '!=', $id)
            ->whereNull('products.deleted_at')
            ->where('products.status', 1)
            ->select('products.*')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $brand = $this->BrandRepository->brand($product->brand_id);

        // Fetch variants if product has variants
        $variants = collect();
        $minVariantPrice = $product->price; // Default to base price
        $totalProductStock = $product->available_stock ?? 0; // Default to product stock
        
        if ($product->have_variants) {
            $variants = DB::table('product_variants')
                ->where('product_id', $id)
                ->where('status', 1)
                ->orderBy('id', 'asc')
                ->get();
                
            if ($variants->isNotEmpty()) {
                // Calculate minimum price (base price + lowest additional_price)
                $minAdditionalPrice = $variants->min('additional_price') ?? 0;
                $minVariantPrice = $product->price + $minAdditionalPrice;
                
                // Don't show 0 price - use base price if min is 0
                if ($minVariantPrice <= 0) {
                    $minVariantPrice = $product->price;
                }
                
                // Total stock is sum of all variant stocks
                $totalProductStock = $variants->sum('available_stock');
            }
        }
        
        // Generate slug for URL (use existing slug or create from title)
        $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);

        $footerData = $this->getFooterCategories();
        return view('product_details', array_merge(
            compact('brand', 'product', 'category', 'relatedProducts', 'variants', 'minVariantPrice', 'totalProductStock', 'productSlug'), 
            $footerData
        ));
    }
}

