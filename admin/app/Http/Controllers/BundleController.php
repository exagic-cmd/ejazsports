<?php
    namespace App\Http\Controllers;

    use App\Models\{Bundle, BundleVariant, Product, ProductVariant, BundleImage};
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\{DB, Log, Storage, Validator};

    class BundleController extends Controller
    {
        private const PAGINATION_COUNT = 5;
        private const IMAGE_DISK = 'public';
        private const IMAGE_DIRECTORY = 'bundles';

        public function index(Request $request)
        {
            $bundles = $this->buildSearchQuery($request)
                ->paginate(self::PAGINATION_COUNT)
                ->appends($request->except('page'));

            if ($request->ajax()) {
                return $this->ajaxSearchResponse($bundles, $request);
            }

            return view('catalog.bundle.index', compact('bundles'));
        }


    public function search(Request $request)
        {
            try {
                $bundles = $this->buildSearchQuery($request)
                    ->paginate(self::PAGINATION_COUNT)
                    ->appends($request->except('page'));

                if ($request->ajax()) {
                    return $this->ajaxSearchResponse($bundles, $request);
                }

                return view('catalog.bundle.index', ['bundles' => $bundles]);
            } catch (\Exception $e) {
                return $this->handleSearchError($e, $request);
            }
        }

    private function ajaxSearchResponse($bundles, $request)
        {
            try {
                $resultsHtml = view('catalog.bundle.partials.results', ['bundles' => $bundles])->render();

                $paginationView = view()->exists('custom.pagination') ? 'custom.pagination' : null;
                $paginationHtml = $bundles->appends($request->except('page'))
                    ->links($paginationView)
                    ->toHtml();

                return response()->json([
                    'success' => true,
                    'results' => $resultsHtml,
                    'pagination' => $paginationHtml,
                    'total' => $bundles->total(),
                    'current_page' => $bundles->currentPage(),
                    'last_page' => $bundles->lastPage(),
                    'per_page' => $bundles->perPage(),
                    'from' => $bundles->firstItem(),
                    'to' => $bundles->lastItem()
                ]);
            } catch (\Exception $e) {
                Log::error('Ajax response error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error rendering search results'
                ], 500);
            }
        }

        private function buildSearchQuery(Request $request)
        {
            $query = Bundle::with(['firstImage'])
                ->withCount('variants');

            if ($request->filled('searchbox')) {
                $searchTerm = trim($request->searchbox);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('short_desc', 'like', '%' . $searchTerm . '%');

                    if (is_numeric($searchTerm)) {
                        $q->orWhere('id', $searchTerm);
                    }
                });
            }

            if ($request->filled('status') && $request->status !== '') {
                $query->where('status', (int) $request->status);
            }

            $this->applySorting($query, $request->sort);

            return $query;
        }

        private function applySorting($query, $sort)
        {
            switch ($sort) {
                case 'purchase_price_asc':
                    $query->orderBy('purchase_price', 'asc');
                    break;
                case 'purchase_price_desc':
                    $query->orderBy('purchase_price', 'desc');
                    break;
                case 'additional_price_asc':
                    $query->orderBy('additional_price', 'asc');
                    break;
                case 'additional_price_desc':
                    $query->orderBy('additional_price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        }

        // Add a method to change pagination count dynamically
        public function setPaginationCount(Request $request)
        {
            $request->validate([
                'per_page' => 'required|integer|min:5|max:100'
            ]);

            session(['bundles_per_page' => $request->per_page]);

            return response()->json(['success' => true]);
        }

        public function create()
        {
            $products = Product::where('status', 1)->with('variants')->get();
            return view('catalog.bundle.create', compact('products'));
        }

        public function getProductVariants(Product $product)
        {
            try {
                return response()->json([
                    'variants' => $product->variants()
                        ->select('id', 'size as name', 'shade as sku', 'purchase_price', 'additional_price')
                        ->get(),
                    'has_variants' => $product->variants()->exists(),
                    'product_price' => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'additional_price' => $product->additional_price,
                    'success' => true
                ]);
            } catch (\Exception $e) {
                return $this->jsonErrorResponse($e, 'Failed to load product variants');
            }
        }

        public function store(Request $request)
        {
            $validator = $this->validateBundleRequest($request);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            return DB::transaction(function () use ($request) {
                try {
                    $bundle = $this->createBundle($request);
                    $this->attachBundleItems($bundle, $request);

                    return redirect()->route('bundles.index')
                        ->with('success', 'Bundle created successfully!');
                } catch (\Exception $e) {
                    Log::error('Bundle creation error: ' . $e->getMessage());
                    return back()->with('error', 'Failed to create bundle. Please try again.');
                }
            });
        }

        public function show($id)
        {
            try {
                $bundle = Bundle::with([
                    'firstImage',
                    'images',
                    'variants' => fn($query) => $query->with(['product', 'variant'])
                        ->groupBy('product_id', 'product_variant_id')
                ])->findOrFail($id);

                return view('catalog.bundle.show', compact('bundle'));
            } catch (\Exception $e) {
                Log::error('Error showing bundle: ' . $e->getMessage());
                return redirect()->route('bundles.index')->with('error', 'Bundle not found');
            }
        }

        public function edit($id)
        {
            try {
                $bundle = Bundle::with(['variants', 'images', 'products'])->findOrFail($id);

                // Get all products with variants
                $products = Product::where('status', 1)
                    ->with(['variants' => function ($query) {
                        $query->select('id', 'product_id', 'size', 'shade', 'purchase_price', 'additional_price');
                    }])
                    ->get();

                // Get unique selected products (no duplicates)
                $selectedProducts = $bundle->products->unique('id')->pluck('id')->toArray();

                // Get selected variants grouped by product
                $selectedVariants = $bundle->variants->groupBy('product_id')
                    ->map(function ($variants) {
                        return $variants->pluck('product_variant_id')->filter()->values()->toArray();
                    })->toArray();

                return view('catalog.bundle.edit', [
                    'bundle' => $bundle,
                    'products' => $products,
                    'selectedProducts' => $selectedProducts,
                    'selectedVariants' => $selectedVariants
                ]);
            } catch (\Exception $e) {
                Log::error('Error editing bundle: ' . $e->getMessage());
                return redirect()->route('bundles.index')->with('error', 'Bundle not found');
            }
        }

        public function update(Request $request, $id)
        {
            // Debug incoming request
            Log::info('Bundle update request data:', [
                'id' => $id,
                'title' => $request->title,
                'products' => $request->products,
                'variant_ids' => $request->variant_ids,
                'additional_price' => $request->additional_price,
                'purchase_price' => $request->purchase_price,
            ]);

            $validator = $this->validateBundleRequest($request);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            return DB::transaction(function () use ($request, $id) {
                try {
                    $bundle = Bundle::findOrFail($id);
                    Log::info("Found bundle {$id} for update");

                    $this->updateBundle($bundle, $request);
                    $this->syncBundleItems($bundle, $request);

                    Log::info("Bundle {$id} updated successfully");

                    return redirect()->route('bundles.index')
                        ->with('success', 'Bundle updated successfully!');
                } catch (\Exception $e) {
                    Log::error('Bundle update error: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    return back()->with('error', 'Failed to update bundle. Please try again.');
                }
            });
        }

        public function destroy($id)
        {
            return DB::transaction(function () use ($id) {
                try {
                    $bundle = Bundle::with('images')->findOrFail($id);
                    $this->deleteBundleResources($bundle);

                    return redirect()->route('bundles.index')
                        ->with('success', 'Bundle deleted successfully!');
                } catch (\Exception $e) {
                    Log::error('Bundle deletion error: ' . $e->getMessage());
                    return back()->with('error', 'Failed to delete bundle. Please try again.');
                }
            });
        }

        public function deleteImage($id)
        {
            try {
                $image = BundleImage::findOrFail($id);
                Storage::disk(self::IMAGE_DISK)->delete($image->path);
                $image->delete();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                Log::error('Bundle image deletion error: ' . $e->getMessage());
                return $this->jsonErrorResponse($e, 'Failed to delete image');
            }
        }

        protected function validateBundleRequest(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'products' => 'required|array|min:1',
                'products.*' => 'exists:products,id',
                'variant_ids' => 'required|array',
                'images' => 'sometimes|array',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $validator->after(function ($validator) use ($request) {
                // Debug the incoming data
                Log::info('Validation - Products:', $request->products ?? []);
                Log::info('Validation - Variant IDs:', $request->variant_ids ?? []);

                foreach ($request->products as $productId) {
                    $product = Product::find($productId);
                    if (!$product) continue;

                    $hasVariants = $product->variants()->exists();
                    $selectedVariants = $request->variant_ids[$productId] ?? [];

                    // Filter out empty values
                    $validVariants = array_filter($selectedVariants, function ($id) {
                        return !empty($id) && $id !== '';
                    });

                    if ($hasVariants && empty($validVariants)) {
                        $validator->errors()->add(
                            'variant_ids',
                            "Please select at least one variant for {$product->title}"
                        );
                    }
                }
            });

            return $validator;
        }

        protected function calculateBundleAdditionalPrice($variantIds)
        {
            Log::info('calculateBundleAdditionalPrice called with:', $variantIds);

            $total = 0;

            foreach ($variantIds as $productId => $variants) {
                $product = Product::find($productId);

                if (!$product) {
                    Log::warning("Product {$productId} not found");
                    continue;
                }

                // Ensure variants is an array
                if (!is_array($variants)) {
                    $variants = [$variants];
                }

                // Filter out empty values
                $validVariants = array_filter($variants, function ($id) {
                    return !empty($id) && $id !== '';
                });

                Log::info("Product {$productId} - Valid variants:", $validVariants);

                if (empty($validVariants)) {
                    // No variants selected, use base product price
                    $productPrice = $product->price ?? 0;
                    $total += $productPrice;
                    Log::info("Product {$productId} - Using base price: {$productPrice}");
                } else {
                    // Process selected variants
                    foreach ($validVariants as $variantId) {
                        $variant = ProductVariant::find($variantId);
                        if ($variant) {
                            $variantPrice = $variant->additional_price ?? 0;
                            $total += $variantPrice;
                            Log::info("Variant {$variantId} - Adding price: {$variantPrice}");
                        } else {
                            Log::warning("Variant {$variantId} not found");
                        }
                    }
                }
            }

            Log::info("Total additional price calculated: {$total}");
            return $total;
        }

        protected function calculateBundlePurchasePrice($variantIds)
        {
            Log::info('calculateBundlePurchasePrice called with:', $variantIds);

            $total = 0;

            foreach ($variantIds as $productId => $variants) {
                $product = Product::find($productId);

                if (!$product) {
                    Log::warning("Product {$productId} not found");
                    continue;
                }

                // Ensure variants is an array
                if (!is_array($variants)) {
                    $variants = [$variants];
                }

                // Filter out empty values
                $validVariants = array_filter($variants, function ($id) {
                    return !empty($id) && $id !== '';
                });

                Log::info("Product {$productId} - Valid variants:", $validVariants);

                if (empty($validVariants)) {
                    // No variants selected, use base product purchase price
                    $productPurchasePrice = $product->purchase_price ?? 0;
                    $total += $productPurchasePrice;
                    Log::info("Product {$productId} - Using base purchase price: {$productPurchasePrice}");
                } else {
                    // Process selected variants
                    foreach ($validVariants as $variantId) {
                        $variant = ProductVariant::find($variantId);
                        if ($variant) {
                            $variantPurchasePrice = $variant->purchase_price ?? 0;
                            $total += $variantPurchasePrice;
                            Log::info("Variant {$variantId} - Adding purchase price: {$variantPurchasePrice}");
                        } else {
                            Log::warning("Variant {$variantId} not found");
                        }
                    }
                }
            }

            Log::info("Total purchase price calculated: {$total}");
            return $total;
        }

        private function createBundle(Request $request)
        {
            // Calculate prices if not provided
            $additionalPrice = $request->additional_price;
            $purchasePrice = $this->calculateBundlePurchasePrice($request->variant_ids);

            // If additional price is empty, calculate it
            if (empty($additionalPrice)) {
                $additionalPrice = $this->calculateBundleAdditionalPrice($request->variant_ids);
            }

            return Bundle::create([
                'name' => $request->title,
                'short_desc' => $request->short_desc,
                'full_desc' => $request->full_desc,
                'status' => $request->status ?? 1,
                'additional_price' => $additionalPrice,
                'purchase_price' => $purchasePrice,
            ]);
        }

        private function updateBundle(Bundle $bundle, Request $request)
        {
            // Calculate prices
            $purchasePrice = $this->calculateBundlePurchasePrice($request->variant_ids);

            // Use provided additional price or calculate it
            $additionalPrice = $request->additional_price;
            if (empty($additionalPrice) || $additionalPrice == 0) {
                $additionalPrice = $this->calculateBundleAdditionalPrice($request->variant_ids);
            }

            Log::info("Updating bundle with prices:", [
                'purchase_price' => $purchasePrice,
                'additional_price' => $additionalPrice,
                'provided_additional_price' => $request->additional_price
            ]);

            $bundle->update([
                'name' => $request->title,
                'short_desc' => $request->short_desc,
                'full_desc' => $request->full_desc,
                'status' => $request->status ?? 1,
                'additional_price' => $additionalPrice,
                'purchase_price' => $purchasePrice,
            ]);
        }
        private function attachBundleItems(Bundle $bundle, Request $request)
        {
            $this->attachVariants($bundle, $request->variant_ids);

            if ($request->hasFile('images')) {
                $this->storeImages($bundle, $request->file('images'));
            }
        }

        private function syncBundleItems(Bundle $bundle, Request $request)
        {
            $bundle->variants()->delete();
            $this->attachVariants($bundle, $request->variant_ids);

            if ($request->hasFile('images')) {
                $this->storeImages($bundle, $request->file('images'));
            }

            // Handle removed images
            if ($request->removed_images) {
                $removedImageIds = explode(',', $request->removed_images);
                $imagesToDelete = BundleImage::whereIn('id', $removedImageIds)->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk(self::IMAGE_DISK)->delete($image->path);
                    $image->delete();
                }
            }
        }

        private function attachVariants(Bundle $bundle, array $variantIds)
        {
            Log::info('attachVariants called with:', $variantIds);

            $variantsToAttach = [];

            foreach ($variantIds as $productId => $variantIdsArray) {
                Log::info("Processing product {$productId} with variants:", $variantIdsArray);

                // Ensure we have an array
                if (!is_array($variantIdsArray)) {
                    $variantIdsArray = [$variantIdsArray];
                }

                // Filter out empty values
                $validVariants = array_filter($variantIdsArray, function ($id) {
                    return !empty($id) && $id !== '';
                });

                Log::info("Valid variants for product {$productId}:", $validVariants);

                if (!empty($validVariants)) {
                    // Product has variants selected
                    foreach ($validVariants as $variantId) {
                        $variantsToAttach[] = [
                            'product_id' => $productId,
                            'product_variant_id' => $variantId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } else {
                    // No variants selected - check if product has variants
                    $product = Product::find($productId);
                    if ($product && !$product->variants()->exists()) {
                        // Product has no variants - use base product
                        $variantsToAttach[] = [
                            'product_id' => $productId,
                            'product_variant_id' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        // Product has variants but none selected - this should have been caught in validation
                        Log::warning("Product {$productId} has variants but none selected");
                    }
                }
            }

            Log::info('Final variants to attach:', $variantsToAttach);

            if (!empty($variantsToAttach)) {
                $bundle->variants()->createMany($variantsToAttach);
            }
        }
        private function storeImages(Bundle $bundle, array $images)
        {
            foreach ($images as $image) {
                $path = $image->store(self::IMAGE_DIRECTORY, self::IMAGE_DISK);
                BundleImage::create([
                    'bundle_id' => $bundle->id,
                    'path' => $path,
                ]);
            }
        }

        private function deleteBundleResources(Bundle $bundle)
        {
            foreach ($bundle->images as $image) {
                Storage::disk(self::IMAGE_DISK)->delete($image->path);
            }

            $bundle->variants()->delete();
            $bundle->images()->delete();
            $bundle->delete();
        }



        private function handleSearchError(\Exception $e, Request $request)
        {
            Log::error('Bundle search error: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error searching bundles: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error searching bundles: ' . $e->getMessage());
        }

        private function jsonErrorResponse(\Exception $e, string $message)
        {
            Log::error($message . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $message,
                'message' => $e->getMessage()
            ], 500);
        }
        public function batchDelete(Request $request)
        {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer' // Changed from exists to just validate they are integers
            ]);

            try {
                DB::beginTransaction();

                // First verify which IDs actually exist
                $existingIds = Bundle::whereIn('id', $request->ids)
                    ->pluck('id')
                    ->toArray();

                if (empty($existingIds)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'None of the selected bundles exist in the database'
                    ], 404);
                }

                // Get count before deletion for accurate reporting
                $countBefore = Bundle::whereIn('id', $existingIds)->count();

                // Delete related records
                BundleImage::whereIn('bundle_id', $existingIds)->delete();
                BundleVariant::whereIn('bundle_id', $existingIds)->delete();

                // Delete bundles
                $deletedCount = Bundle::whereIn('id', $existingIds)->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Successfully deleted $deletedCount out of " . count($request->ids) . " selected bundles",
                    'deleted_count' => $deletedCount,
                    'requested_count' => count($request->ids),
                    'existing_count' => $countBefore
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Batch delete failed: " . $e->getMessage(), [
                    'exception' => $e,
                    'request_ids' => $request->ids
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete bundles: ' . $e->getMessage()
                ], 500);
            }
        }
public function getBundle($id)
{
    try {
        $bundle = Bundle::with(['variants.product', 'images'])
            ->select(
                'id',
                'name',
                'short_desc',
                'additional_price',
                'purchase_price',
                'status'
            )
            ->findOrFail($id);

        // Calculate total price including variants if needed
        $totalPrice = $bundle->additional_price;

        return response()->json([
            'success' => true,
            'data' => [
                'bundle' => $bundle,
                'price' => $totalPrice,
                'image_url' => $bundle->images->isNotEmpty()
                    ? asset('storage/'.$bundle->images->first()->path)
                    : asset('images/default-bundle.jpg')
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Bundle not found',
            'error' => $e->getMessage()
        ], 404);
    }
}
    }
