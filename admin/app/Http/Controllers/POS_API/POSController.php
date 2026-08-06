<?php

namespace App\Http\Controllers\POS_API;

use App\Http\Controllers\POS_API\BaseController;
use App\Http\Controllers\SmsController;
use App\Mail\OrderReceipt;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Bundle;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Receiving;
use App\Models\ReceivingProduct;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\PriceupNotification;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\CustomerPayment;
use App\Models\StockAuditDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class POSController extends BaseController
{
    private function calculateProductPrice($product, $cart, $customerId, $variantId = 0, $manualReturnOnly = 0)
    {
        $manualPrice = is_numeric($cart['price']) ? (float) $cart['price'] : 0;
        $qty = $cart['qty'] ?? 1;
        $calculatedPrice = 0;

        if ($manualReturnOnly) {
            return $manualPrice;
        }

        if ($customerId && $customerId != 1) {
            // Non-default customer: use additional_price
            if ($variantId) {
                $basePrice = $product->variants[0]->additional_price ?? 0;
                $calculatedPrice = $manualPrice > 0 ? max($manualPrice, $basePrice) : $basePrice;
            } else {
                $basePrice = $product->price ?? 0;
                $calculatedPrice = $manualPrice > 0 ? max($manualPrice, $basePrice) : $basePrice;
            }
        } else {
            // Default customer: use retail price with dozen pricing
            if ($variantId) {
                $basePrice = $product->variants[0]->additional_price ?? 0;
                $dozenPrice = isset($product->variants[0]->dz_price) ? ($product->variants[0]->dz_price / 2) : $basePrice;
                if ($manualPrice > 0) {
                    $calculatedPrice = max($manualPrice, $basePrice);
                } elseif ($qty % 6 == 0) {
                    $calculatedPrice = $dozenPrice;
                } else {
                    $calculatedPrice = $basePrice;
                }
            } else {
                $basePrice = $product->price ?? 0;
                $dozenPrice = isset($product->dz_price) ? ($product->dz_price / 2) : $basePrice;
                if ($manualPrice > 0) {
                    $calculatedPrice = max($manualPrice, $basePrice);
                } elseif ($qty % 6 == 0) {
                    $calculatedPrice = $dozenPrice;
                } else {
                    $calculatedPrice = $basePrice;
                }
            }
        }

        return $calculatedPrice;
    }

    public function getPOSModuleData(Request $request)
    {
        $data['categories'] = Category::select('id', 'title')->get();
        return $this->sendResponse($data, 'POS Page Content.');
    }

    public function getBrandData(Request $request)
    {
        $brand_id = $request->get('brand_id');

        if ($brand_id == 0) {
            $data['products'] = Product::with('brand', 'variants', 'thumbnail')
                ->where([['status', true]])
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->limit(50)
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $data['products'] = Product::with('brand', 'variants', 'thumbnail')
                ->where([['status', true], ['brand_id', $brand_id]])
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->orderBy('id', 'DESC')
                ->get();
        }

        $data['categories'] = Category::select('id', 'title')->get();
        return $this->sendResponse($data, 'Brand Products.');
    }

    public function getCategoryData(Request $request)
    {
        $category_id = $request->get('category_id');

        if ($request->get('customer_id') != null)
            $data['priceShown'] = true;
        else
            $data['priceShown'] = false;

        if ($category_id == 0) {
            $data['products'] = Product::with('brand', 'variants', 'thumbnail')
                ->where([['status', true]])
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->limit(50)
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $data['products'] = Product::with('brand', 'variants', 'thumbnail')
                ->where([['status', true]])
                ->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                })
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->orderBy('id', 'DESC')
                ->get();
        }

        $data['categories'] = Category::select('id', 'title')->get();
        return $this->sendResponse($data, 'Categroy Products.');
    }

    public function getSearchData(Request $request)
    {
        $val = $request->get('val');

        if ($request->get('customer_id') != null)
            $data['priceShown'] = true;
        else
            $data['priceShown'] = false;

        $result = ProductVariant::where('barcode', $val)->first();

        if ($result) {
            $variant_id = $result->id;
            $data['products'] = Product::where('id', $result->product_id)
                ->with([
                    'variants' => function ($query) use ($variant_id) {
                        $query->where('id', $variant_id);
                    }
                ])
                ->with('thumbnail')
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->orderBy('id', 'DESC')
                ->get();

            $data['bundles'] = collect([]);
        } else {
            $productQuery = Product::with('brand', 'variants', 'thumbnail')
                ->where('status', true)
                ->where(function ($query) use ($val) {
                    $query->where('title', 'Like', '%' . $val . '%')
                        ->orWhere('barcode', $val);
                });

            $data['products'] = $productQuery
                ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                ->limit(50)
                ->orderBy('id', 'DESC')
                ->get();

            $data['bundles'] = Bundle::with([
                'images' => function ($query) {
                    $query->orderBy('id')->take(1);
                },
                'variants'
            ])
                ->where('status', true)
                ->where(function ($query) use ($val) {
                    $query->where('name', 'like', '%' . $val . '%')
                        ->orWhere('short_desc', 'like', '%' . $val . '%')
                        ->orWhere('id', $val);
                })
                ->select('id', 'name', 'short_desc', 'additional_price', 'purchase_price')
                ->limit(50)
                ->orderBy('id', 'DESC')
                ->get();
        }

        $data['categories'] = Category::select('id', 'title')->get();
        return $this->sendResponse($data, 'Search results.');
    }

    public function getCartData(Request $request)
    {
        try {
            $cartProducts = $request->get('cart', []);
            $cartBundles = $request->get('bundles', []);
            $products = array();
            $variants = array();
            $bundles = array();
            $subTotal = 0;
            $discount = 0;
            $price = array();
            $vPrice = array();
            $bundlePrice = array();

            // Get details of cart products
            if ($cartProducts) {
                foreach ($cartProducts as $cart) {
                    $productId = $cart['id'];
                    $variantId = $cart['variant_id'] ?? 0;

                    if ($variantId) {
                        $temp = Product::with([
                            'variants' => function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            }
                        ])->wherehas('variants', function ($query) use ($variantId) {
                            $query->where('id', $variantId);
                        })->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')->first();

                        if ($temp && $temp->variants->isNotEmpty()) {
                            $variants[$variantId] = $temp->variants[0];
                        } else {
                            $variants[$variantId] = null;
                        }
                    } else {
                        $temp = Product::where('id', $productId)->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')->first();
                        $variants[$variantId] = null;
                    }

                    if (!$temp)
                        continue;

                    if ($request->get('customer_id') != null) {
                        if ($cart['price'] == 0) {
                            if ($variants[$variantId] == null) {
                                if ($cart['qty'] % 6 == 0) {
                                    $h_price = $temp->dz_price / 2;
                                    $subTotal += $h_price * ($cart['qty'] / 6);
                                    $price[$cart['id']] = $h_price;
                                    $vPrice[$variantId] = 0;
                                } else {
                                    $subTotal += $temp->price * $cart['qty'];
                                    $price[$cart['id']] = $temp->price;
                                    $vPrice[$variantId] = 0;
                                }
                            } else {
                                if ($cart['qty'] % 6 == 0) {
                                    $h_price = $temp->variants[0]->dz_price / 2;
                                    $subTotal += $h_price * ($cart['qty'] / 6);
                                    $price[$cart['id']] = $h_price;
                                    $vPrice[$variantId] = $h_price;
                                } else {
                                    $subTotal += $temp->variants[0]->additional_price * $cart['qty'];
                                    $price[$cart['id']] = $temp->variants[0]->additional_price;
                                    $vPrice[$variantId] = $temp->variants[0]->additional_price;
                                }
                            }
                        } else {
                            if (!$request->get('manual_return_only')) {
                                if ($variantId) {
                                    if ($cart['price'] < $temp->variants[0]->additional_price) {
                                        $cart['price'] = $temp->variants[0]->additional_price;
                                    }
                                } else {
                                    if ($cart['price'] < $temp->price) {
                                        $cart['price'] = $temp->price;
                                    }
                                }
                            }
                            $price[$cart['id']] = $cart['price'];
                            $vPrice[$variantId] = $cart['price'];
                            $subTotal += $cart['price'] * $cart['qty'];
                        }
                    } else {
                        if ($cart['price'] != 0) {
                            if (!$request->get('manual_return_only')) {
                                if ($variantId) {
                                    if ($cart['price'] < $temp->variants[0]->additional_price) {
                                        $cart['price'] = $temp->variants[0]->additional_price;
                                    }
                                } else {
                                    if ($cart['price'] < $temp->price) {
                                        $cart['price'] = $temp->price;
                                    }
                                }
                            }
                        }
                        $price[$cart['id']] = $cart['price'];
                        $vPrice[$variantId] = $cart['price'];
                        $subTotal += $cart['price'] * $cart['qty'];
                    }
                    $products[$cart['id']] = $temp;
                }
            }

            // Get details of cart bundles
            if ($cartBundles) {
                foreach ($cartBundles as $bundle) {
                    $bundleId = $bundle['bundle_id'];

                    try {
                        $bundleData = Bundle::where('id', $bundleId)
                            ->select('id', 'name', 'short_desc', 'purchase_price', 'additional_price', 'status')
                            ->with([
                                'variants' => function ($query) {
                                    $query->with([
                                        'product' => function ($q) {
                                            $q->select('id', 'title', 'price', 'purchase_price', 'dz_price');
                                        },
                                        'variant' => function ($q) {
                                            $q->select('id', 'product_id', 'additional_price', 'purchase_price');
                                        }
                                    ]);
                                }
                            ])
                            ->first();

                        if ($bundleData) {
                            $bundles[$bundleId] = $bundleData;

                            // Calculate bundle price
                            if ($request->get('customer_id') != null) {
                                if ($bundle['price'] == 0) {
                                    $bundlePriceValue = $bundleData->additional_price ?? 0;
                                } else {
                                    if (!$request->get('manual_return_only')) {
                                        if ($bundle['price'] < $bundleData->additional_price) {
                                            $bundle['price'] = $bundleData->additional_price;
                                        }
                                    }
                                    $bundlePriceValue = $bundle['price'];
                                }
                            } else {
                                if ($bundle['price'] != 0) {
                                    if (!$request->get('manual_return_only')) {
                                        if ($bundle['price'] < $bundleData->additional_price) {
                                            $bundle['price'] = $bundleData->additional_price;
                                        }
                                    }
                                    $bundlePriceValue = $bundle['price'];
                                } else {
                                    $bundlePriceValue = 0;
                                }
                            }

                            // Calculate price per component for display
                            $componentCount = count($bundleData->variants);
                            $componentPrice = $componentCount > 0 ? $bundlePriceValue / $componentCount : 0;

                            // Update bundle variants with component price
                            foreach ($bundleData->variants as $variant) {
                                $variant->display_price = $componentPrice;
                            }

                            $bundlePrice[$bundleId] = $bundlePriceValue;
                            $subTotal += $bundlePriceValue * $bundle['qty'];
                        } else {
                            $bundles[$bundleId] = (object) [
                                'id' => $bundleId,
                                'name' => 'Bundle ' . $bundleId,
                                'short_desc' => 'Bundle description',
                                'purchase_price' => 0,
                                'additional_price' => 0,
                                'status' => 1,
                                'variants' => []
                            ];

                            if ($request->get('customer_id') != null) {
                                $bundlePriceValue = $bundle['price'] ?: 0;
                            } else {
                                $bundlePriceValue = $bundle['price'] ?: 0;
                            }

                            $bundlePrice[$bundleId] = $bundlePriceValue;
                            $subTotal += $bundlePriceValue * $bundle['qty'];
                        }
                    } catch (\Exception $e) {
                        Log::error('Bundle processing error for ID ' . $bundleId . ': ' . $e->getMessage());
                        continue;
                    }
                }
            }

            if ($request->get('discount_id')) {
                $discount = $request->get('discount_id');
            }

            $data['employees'] = Employee::get();
            $data['cartProducts'] = $cartProducts;
            $data['cartBundles'] = $cartBundles;
            $data['discount'] = $discount;
            $data['products'] = $products;
            $data['variants'] = $variants;
            $data['bundles'] = $bundles;
            $data['subTotal'] = $subTotal;
            $data['price'] = $price;
            $data['vPrice'] = $vPrice;
            $data['bundlePrice'] = $bundlePrice;

            return $this->sendResponse($data, 'Cart Data.');
        } catch (\Exception $e) {
            Log::error('getCartData API Error: ' . $e->getMessage());
            return $this->sendError('Internal Server Error', $e->getMessage(), 500);
        }
    }
    public function updatePayment(Request $request)
    {
        try {
            $cartProducts = $request->get('cart', []);
            $cartBundles = $request->get('bundles', []);

            $margin = 0;
            $total = 0;
            $discount = $request->get('discount_id') ? (float) $request->get('discount_id') : 0;

            foreach ($cartProducts as $cart) {
                if (!isset($cart['id']) || !isset($cart['qty']) || !is_numeric($cart['qty']) || $cart['qty'] <= 0) {
                    Log::warning('Invalid product data in updatePayment', ['cart' => $cart]);
                    continue;
                }

                $productId = $cart['id'];
                $variantId = $cart['variant_id'] ?? 0;

                try {
                    if ($variantId) {
                        $product = Product::with([
                            'variants' => function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            }
                        ])
                            ->whereHas('variants', function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            })
                            ->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')
                            ->firstOrFail();
                    } else {
                        $product = Product::select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')
                            ->findOrFail($productId);
                    }

                    $calculatedPrice = $this->calculateProductPrice($product, $cart, $request->customer_id, $variantId);

                    $total += $calculatedPrice * $cart['qty'];

                    if ($request->customer_id && $request->customer_id != 1) {
                        $basePrice = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);
                    } else {
                        $basePrice = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);
                    }
                    $margin += (($calculatedPrice - $basePrice) * $cart['qty']);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Product not found in updatePayment', ['product_id' => $productId, 'variant_id' => $variantId]);
                    continue;
                }
            }

            foreach ($cartBundles as $bundle) {
                if (!isset($bundle['bundle_id']) || !isset($bundle['qty']) || !is_numeric($bundle['qty']) || $bundle['qty'] <= 0) {
                    Log::warning('Invalid bundle data in updatePayment', ['bundle' => $bundle]);
                    continue;
                }

                try {
                    $bundleData = Bundle::select('id', 'name', 'additional_price', 'purchase_price', 'short_desc')
                        ->findOrFail($bundle['bundle_id']);

                    $qty = $bundle['qty'];
                    $manualPrice = is_numeric($bundle['price']) ? (float) $bundle['price'] : 0;

                    if ($request->customer_id && $request->customer_id != 1) {
                        $currentPrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                        $basePrice = $bundleData->additional_price ?? 0;
                    } else {
                        $currentPrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                        $basePrice = $bundleData->additional_price ?? 0;
                    }

                    $total += $currentPrice * $qty;
                    $margin += (($currentPrice - $basePrice) * $qty);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Bundle not found in updatePayment', ['bundle_id' => $bundle['bundle_id']]);
                    continue;
                }
            }

            $data = [
                'total' => $total - $discount,
                'customer' => Customer::find($request->get('customer_id')),
                'balance' => 0,
                'margin' => $margin
            ];

            if ($data['customer'] && $request->get('customer_id') != 1) {
                $totalBillAmount = Order::where('customer_id', $data['customer']->id)
                    ->where('status', '!=', 6)
                    ->sum('total_amount');

                $totalReturnAmount = Order::where('customer_id', $data['customer']->id)
                    ->where('return_type', 1)
                    ->sum('return_amount');

                $totalPayment = CustomerPayment::where('customer_id', $data['customer']->id)
                    ->where('status', 2)
                    ->sum('amount');

                $totalDiscount = CustomerPayment::where('customer_id', $data['customer']->id)
                    ->where('status', 2)
                    ->sum('discount');

                $data['balance'] = ((($data['customer']->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Payment Data.'
            ]);
        } catch (\Exception $e) {
            Log::error('updatePayment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function mannualReturnForm(Request $request)
    {
        try {
            $cartProducts = $request->get('cart', []);
            $cartBundles = $request->get('bundles', []);

            $margin = 0;
            $total = 0;
            $discount = $request->get('discount_id') ? (float) $request->get('discount_id') : 0;

            foreach ($cartProducts as $cart) {
                if (!isset($cart['id']) || !isset($cart['qty']) || !is_numeric($cart['qty']) || $cart['qty'] <= 0) {
                    Log::warning('Invalid product data in mannualReturnForm', ['cart' => $cart]);
                    continue;
                }

                $productId = $cart['id'];
                $variantId = $cart['variant_id'] ?? 0;

                try {
                    if ($variantId) {
                        $product = Product::with([
                            'variants' => function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            }
                        ])
                            ->whereHas('variants', function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            })
                            ->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')
                            ->firstOrFail();
                    } else {
                        $product = Product::select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price')
                            ->findOrFail($productId);
                    }

                    $calculatedPrice = $this->calculateProductPrice($product, $cart, $request->customer_id, $variantId, $request->get('manual_return_only'));

                    $total += $calculatedPrice * $cart['qty'];

                    if ($request->customer_id && $request->customer_id != 1) {
                        $basePrice = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);
                    } else {
                        $basePrice = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);
                    }
                    $margin += (($calculatedPrice - $basePrice) * $cart['qty']);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Product not found in mannualReturnForm', ['product_id' => $productId, 'variant_id' => $variantId]);
                    continue;
                }
            }

            foreach ($cartBundles as $bundle) {
                if (!isset($bundle['bundle_id']) || !isset($bundle['qty']) || !is_numeric($bundle['qty']) || $bundle['qty'] <= 0) {
                    Log::warning('Invalid bundle data in mannualReturnForm', ['bundle' => $bundle]);
                    continue;
                }

                try {
                    $bundleData = Bundle::select('id', 'name', 'additional_price', 'purchase_price', 'short_desc')
                        ->findOrFail($bundle['bundle_id']);

                    $qty = $bundle['qty'];
                    $manualPrice = is_numeric($bundle['price']) ? (float) $bundle['price'] : 0;
                    if ($request->get('manual_return_only')) {
                        $currentPrice = $manualPrice;
                    } else {
                        $currentPrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                    }
                    $basePrice = $bundleData->additional_price ?? 0;

                    $total += $currentPrice * $qty;
                    $margin += (($currentPrice - $basePrice) * $qty);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Bundle not found in mannualReturnForm', ['bundle_id' => $bundle['bundle_id']]);
                    continue;
                }
            }

            $data = [
                'total' => $total - $discount,
                'customer' => Customer::find($request->get('customer_id') ?: 1),
                'balance' => 0,
                'margin' => $margin
            ];

            if ($data['customer'] && $request->get('customer_id') != 1) {
                $totalBillAmount = Order::where('customer_id', $data['customer']->id)
                    ->where('status', '!=', 6)
                    ->sum('total_amount');

                $totalReturnAmount = Order::where('customer_id', $data['customer']->id)
                    ->where('return_type', 1)
                    ->sum('return_amount');

                $totalPayment = CustomerPayment::where('customer_id', $data['customer']->id)
                    ->where('status', 2)
                    ->sum('amount');

                $totalDiscount = CustomerPayment::where('customer_id', $data['customer']->id)
                    ->where('status', 2)
                    ->sum('discount');

                $data['balance'] = ((($data['customer']->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount;
            }

            return $this->sendResponse($data, 'Payment Data.');
        } catch (\Exception $e) {
            Log::error('mannualReturnForm failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing return data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $cartProducts = $request->get('cart', []);
            $cartBundles = $request->get('bundles', []);

            // Validate cart and bundles
            if (empty($cartProducts) && empty($cartBundles)) {
                Log::warning('Empty cart in createSale', ['request' => $request->all()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            foreach ($cartBundles as $bundle) {
                if (!isset($bundle['bundle_id']) || !isset($bundle['qty']) || !is_numeric($bundle['qty']) || $bundle['qty'] <= 0) {
                    Log::error('Invalid bundle data', ['bundle' => $bundle]);
                    throw new \Exception('Invalid bundle data: missing or invalid bundle_id or qty');
                }
            }

            foreach ($cartProducts as $cart) {
                if (!isset($cart['id']) || !isset($cart['qty']) || !is_numeric($cart['qty']) || $cart['qty'] <= 0) {
                    Log::error('Invalid product data in createSale', ['cart' => $cart]);
                    throw new \Exception('Invalid product data: missing or invalid id or qty');
                }
            }

            // Find or set default customer
            $customer = Customer::find($request->get('customer_id'));
            if (!$customer) {
                $customer = Customer::where('store_id', $request->get('store_id'))->orderBy('id', 'ASC')->first();
                if (!$customer) {
                    throw new \Exception('No customer found for store_id: ' . $request->get('store_id'));
                }
            }

            // Create new order
            $order = new Order();
            $order->order_no = Order::max('id') + 1;
            $order->customer_id = $customer->id;
            $order->name = $customer->first_name . ' ' . $customer->last_name;
            $order->email = $customer->email ?? 'email';
            $order->phone_number = $customer->phone_number ?? 'phone_number';
            $order->status = Order::COMPLETED;
            $order->payment_method = Order::CASH;
            $order->store_id = $request->store_id;
            $order->employee_id = $request->employee_id;
            $order->margin = $request->margin ?? 0;
            $order->paid_amount = $request->pay_amount ?? 0;
            $order->additional_notes = $request->comment;
            $order->save();

            // Handle existing order replacement
            if ($request->get('order_id') != 0) {
                $res = Order::with('products')->find($request->get('order_id'));
                if ($res) {
                    $order->order_no = $res->order_no;
                    $order->created_at = $res->created_at;
                    $order->save();

                    foreach ($res->products as $pro) {
                        if ($pro->variant_id) {
                            $variant = ProductVariant::where('id', $pro->variant_id)->with('product')->first();
                            if ($variant) {
                                Product::where('id', $variant->product_id)->update([
                                    'available_stock' => $variant->product->available_stock + $pro->qty
                                ]);
                                ProductVariant::where('id', $pro->variant_id)->update([
                                    'available_stock' => $variant->available_stock + $pro->qty
                                ]);
                            }
                        } else {
                            $product = Product::find($pro->product_id);
                            if ($product) {
                                Product::where('id', $pro->product_id)->update([
                                    'available_stock' => $product->available_stock + $pro->qty
                                ]);
                            }
                        }

                        $storeStocks = StoreProductStock::where([
                            ['product_id', $pro->product_id],
                            ['variant_id', $pro->variant_id ?: null]
                        ])
                            ->whereRaw('sold_qty < purchase_qty')
                            ->orderBy('id', 'ASC')
                            ->get();

                        $orderQty = $pro->qty;
                        foreach ($storeStocks as $stock) {
                            $availableQty = $stock->sold_qty;
                            if ($orderQty > $availableQty) {
                                $orderQty -= $availableQty;
                                StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                            } else {
                                StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                                $orderQty = 0;
                            }
                            if ($orderQty == 0)
                                break;
                        }

                        if ($orderQty != 0) {
                            $storeStocks = StoreProductStock::where([
                                ['product_id', $pro->product_id],
                                ['variant_id', $pro->variant_id ?: null]
                            ])
                                ->whereRaw('sold_qty = purchase_qty')
                                ->orderBy('id', 'DESC')
                                ->get();

                            foreach ($storeStocks as $stock) {
                                if ($orderQty > $stock->purchase_qty) {
                                    $orderQty -= $stock->purchase_qty;
                                    StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                                } else {
                                    StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                                    $orderQty = 0;
                                }
                                if ($orderQty == 0)
                                    break;
                            }
                        }

                        if ($pro->paid_amount > 0 && $pro->customer_id != 1) {
                            CustomerPayment::where([
                                ['customer_id', $pro->customer_id],
                                ['amount', $pro->paid_amount]
                            ])->delete();
                        }
                    }
                    $res->delete();
                }
            }

            $totalAmount = 0;
            $totalProducts = 0;
            $totalQuantity = 0;

            // Process individual products
            foreach ($cartProducts as $cart) {
                $productId = $cart['id'];
                $variantId = $cart['variant_id'] ?? 0;

                try {
                    if ($variantId) {
                        $product = Product::with([
                            'variants' => function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            }
                        ])
                            ->whereHas('variants', function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            })
                            ->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price', 'available_stock')
                            ->firstOrFail();
                    } else {
                        $product = Product::select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price', 'available_stock')
                            ->findOrFail($productId);
                    }

                    $calculatedPrice = $this->calculateProductPrice($product, $cart, $request->customer_id, $variantId);

                    Log::info('Product Price Calculation', [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variantId,
                        'qty' => $cart['qty'],
                        'calculated_price' => $calculatedPrice,
                        'total' => $calculatedPrice * $cart['qty'],
                        'manual_price' => $cart['price'] ?? 0
                    ]);

                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $product->id;
                    $orderProduct->variant_id = $variantId ? $product->variants[0]->id : null;
                    $orderProduct->qty = $cart['qty'];
                    $orderProduct->price = $calculatedPrice;
                    $orderProduct->barcode = $variantId ? $product->variants[0]->barcode : null;
                    $orderProduct->cost_price = $variantId ? ($product->variants[0]->purchase_price ?? 0) : ($product->purchase_price ?? 0);
                    $orderProduct->wholesale_price = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);

                    if ($variantId && isset($product->variants[0])) {
                        ProductVariant::where('id', $variantId)->update([
                            'available_stock' => $product->variants[0]->available_stock - $cart['qty'],
                            'online_available_stock' => $product->variants[0]->online_available_stock - $cart['qty']
                        ]);
                    }
                    Product::where('id', $product->id)->update([
                        'available_stock' => $product->available_stock - $cart['qty']
                    ]);

                    $storeStocks = StoreProductStock::where([
                        ['store_id', $request->get('store_id')],
                        ['product_id', $product->id],
                        ['variant_id', $variantId ? $product->variants[0]->id : null]
                    ])
                        ->whereRaw('sold_qty < purchase_qty')
                        ->orderBy('id', 'ASC')
                        ->get();

                    $orderQty = $cart['qty'];
                    $receivingId = 0;
                    foreach ($storeStocks as $stock) {
                        $availableQty = $stock->purchase_qty - $stock->sold_qty;
                        if ($orderQty > $availableQty) {
                            $orderQty -= $availableQty;
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->purchase_qty]);
                            $receivingId = $stock->receiving_id;
                        } else {
                            StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty + $orderQty]);
                            $orderQty = 0;
                            $receivingId = $stock->receiving_id;
                        }
                        $orderProduct->cost_price = $stock->cost ?? $orderProduct->cost_price;
                        if ($orderQty == 0)
                            break;
                    }

                    $res = ReceivingProduct::where([
                        ['product_id', $product->id],
                        ['product_variant_id', $variantId ? $product->variants[0]->id : null]
                    ])->count();

                    if ($res > 1) {
                        $rec = ReceivingProduct::where([
                            ['receiving_id', $receivingId],
                            ['product_id', $product->id],
                            ['product_variant_id', $variantId ? $product->variants[0]->id : null]
                        ])->first();

                        if ($rec && $rec->sale_price != ($variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0))) {
                            PriceupNotification::create([
                                'product_id' => $product->id,
                                'variant_id' => $variantId ? $product->variants[0]->id : null,
                                'old_price' => $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0),
                                'new_price' => $rec->sale_price,
                                'old_purchase' => $variantId ? ($product->variants[0]->purchase_price ?? 0) : ($product->purchase_price ?? 0),
                                'new_purchase' => $rec->cost_price
                            ]);
                        }
                    }

                    $orderProduct->save();
                    $totalAmount += $calculatedPrice * $cart['qty'];
                    $totalProducts++;
                    $totalQuantity += $cart['qty'];
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Product not found in createSale', ['product_id' => $productId, 'variant_id' => $variantId]);
                    continue;
                }
            }

            // Process bundles
            foreach ($cartBundles as $bundle) {
                try {
                    $bundleData = Bundle::with([
                        'variants' => function ($query) {
                            $query->with([
                                'product' => function ($q) {
                                    $q->select('id', 'title', 'price', 'purchase_price', 'dz_price', 'available_stock');
                                },
                                'variant' => function ($q) {
                                    $q->select('id', 'product_id', 'additional_price', 'purchase_price', 'barcode', 'available_stock');
                                }
                            ]);
                        }
                    ])
                        ->select('id', 'name', 'additional_price', 'purchase_price', 'short_desc')
                        ->findOrFail($bundle['bundle_id']);

                    $qty = $bundle['qty'];
                    $manualPrice = is_numeric($bundle['price']) ? (float) $bundle['price'] : 0;

                    // Calculate the bundle price
                    if ($request->customer_id && $request->customer_id != 1) {
                        $bundlePrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                    } else {
                        $bundlePrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                    }

                    // Log bundle price details
                    Log::info('Bundle Price Calculation', [
                        'order_id' => $order->id,
                        'bundle_id' => $bundleData->id,
                        'manual_price' => $manualPrice,
                        'bundle_price' => $bundlePrice,
                        'qty' => $qty,
                        'total' => $bundlePrice * $qty,
                        'additional_price' => $bundleData->additional_price ?? 0,
                        'purchase_price' => $bundleData->purchase_price ?? 0
                    ]);

                    // Save bundle as a single OrderProduct entry for invoice display
                    $bundleOrderProduct = new OrderProduct();
                    $bundleOrderProduct->order_id = $order->id;
                    $bundleOrderProduct->bundle_id = $bundleData->id;
                    $bundleOrderProduct->qty = $qty;
                    $bundleOrderProduct->price = $bundlePrice;
                    $bundleOrderProduct->is_bundle = true;
                    $bundleOrderProduct->is_bundle_item = false;
                    $bundleOrderProduct->cost_price = $bundleData->purchase_price ?? 0;
                    $bundleOrderProduct->wholesale_price = $bundleData->additional_price ?? 0;
                    $bundleOrderProduct->save();

                    // Process bundle components for stock updates
                    $componentCount = count($bundleData->variants);
                    $componentPrice = $componentCount > 0 ? $bundlePrice / $componentCount : 0;

                    foreach ($bundleData->variants as $bundleVariant) {
                        // Treat each bundle component like a regular product sale: reload product/variant
                        $variantId = $bundleVariant->product_variant_id ?? 0;

                        try {
                            if ($variantId) {
                                $componentProduct = Product::with([
                                    'variants' => function ($query) use ($variantId) {
                                        $query->where('id', $variantId);
                                    }
                                ])->whereHas('variants', function ($query) use ($variantId) {
                                    $query->where('id', $variantId);
                                })->select('title', 'price', 'id', 'purchase_price', 'dz_price', 'available_stock')->firstOrFail();
                            } else {
                                $componentProduct = Product::select('title', 'price', 'id', 'purchase_price', 'dz_price', 'available_stock')->findOrFail($bundleVariant->product_id);
                            }
                        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                            Log::warning('Bundle component product not found, skipping', ['bundle_id' => $bundleData->id, 'product_id' => $bundleVariant->product_id, 'variant_id' => $variantId]);
                            continue;
                        }

                        $pivotQuantity = 1; // no per-component quantity field present; default to 1
                        $componentQty = $pivotQuantity * $qty;

                        $component = new OrderProduct();
                        $component->order_id = $order->id;
                        $component->product_id = $componentProduct->id;
                        $component->variant_id = $variantId ? $componentProduct->variants[0]->id : null;
                        $component->bundle_id = $bundleData->id;
                        $component->parent_id = $bundleOrderProduct->id;
                        $component->qty = $componentQty;
                        $component->price = $componentPrice;
                        $component->is_bundle = false;
                        $component->is_bundle_item = true;
                        $component->cost_price = $variantId ? ($componentProduct->variants[0]->purchase_price ?? 0) : ($componentProduct->purchase_price ?? 0);
                        $component->wholesale_price = $variantId ? ($componentProduct->variants[0]->additional_price ?? 0) : ($componentProduct->price ?? 0);
                        $component->barcode = $variantId ? ($componentProduct->variants[0]->barcode ?? null) : null;

                        // Update available stock using freshly-loaded product/variant values
                        Product::where('id', $componentProduct->id)->update([
                            'available_stock' => $componentProduct->available_stock - $componentQty
                        ]);
                        if ($variantId && isset($componentProduct->variants[0])) {
                            ProductVariant::where('id', $componentProduct->variants[0]->id)->update([
                                'available_stock' => $componentProduct->variants[0]->available_stock - $componentQty,
                                'online_available_stock' => ($componentProduct->variants[0]->online_available_stock ?? 0) - $componentQty
                            ]);
                        }

                        // Deduct sold_qty from StoreProductStock same as single product flow
                        $storeStocks = StoreProductStock::where([
                            ['store_id', $request->get('store_id')],
                            ['product_id', $componentProduct->id],
                            ['variant_id', $variantId ? $componentProduct->variants[0]->id : null]
                        ])
                            ->whereRaw('sold_qty < purchase_qty')
                            ->orderBy('id', 'ASC')
                            ->get();

                        $orderQty = $componentQty;
                        $receivingId = 0;
                        foreach ($storeStocks as $stock) {
                            $availableQty = $stock->purchase_qty - $stock->sold_qty;
                            if ($orderQty > $availableQty) {
                                $orderQty -= $availableQty;
                                StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->purchase_qty]);
                                $receivingId = $stock->receiving_id;
                            } else {
                                StoreProductStock::where('id', $stock->id)->update(['sold_qty' => $stock->sold_qty + $orderQty]);
                                $orderQty = 0;
                                $receivingId = $stock->receiving_id;
                            }
                            $component->cost_price = $stock->cost ?? $component->cost_price;
                            if ($orderQty == 0)
                                break;
                        }

                        $component->save();
                        Log::info('Bundle component saved (product-like flow)', [
                            'order_id' => $order->id,
                            'bundle_id' => $bundleData->id,
                            'variant_id' => $variantId,
                            'product_id' => $componentProduct->id,
                            'qty' => $componentQty,
                            'component_price' => $componentPrice
                        ]);

                        $totalProducts++;
                        $totalQuantity += $componentQty;
                    }

                    $totalAmount += $bundlePrice * $qty;
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::warning('Bundle not found in createSale', ['bundle_id' => $bundle['bundle_id']]);
                    continue;
                }
            }

            // Save order with final totals
            $order->delivery_charges = 0;
            $order->discount_amount = $request->discount_id ? (float) $request->discount_id : 0;
            $order->total_amount = round($totalAmount - $order->discount_amount);
            $order->total_products = $totalProducts;
            $order->total_quantity = $totalQuantity;

            Log::info('Order Total Calculation', [
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
                'discount_amount' => $order->discount_amount,
                'final_total' => $order->total_amount,
                'total_products' => $totalProducts,
                'total_quantity' => $totalQuantity
            ]);

            $order->save();

            // Record customer payment if applicable
            if ($customer->id != 1 && $request->pay_amount > 0) {
                CustomerPayment::create([
                    'customer_id' => $customer->id,
                    'amount' => $request->pay_amount,
                    'tax' => 0,
                    'date' => Carbon::now(),
                    'received_by' => 'POS Manager',
                    'payment_method' => 1,
                    'depositor_bank' => '',
                    'cheque_no' => '',
                    'cheque_date' => null,
                    'notes' => '',
                    'created_by' => 1,
                    'status' => 2
                ]);
            }
            $this->autoRestockNegativeStock($order, $request->store_id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $order,
                    'order_id' => $order->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('createSale failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function createReturn(Request $request)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $cartProducts = $request->get('cart', []);
            $cartBundles = $request->get('bundles', []);

            if (empty($cartProducts) && empty($cartBundles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            $customer = Customer::find($request->get('customer_id'));
            if (!$customer) {
                $customer = Customer::where('store_id', $request->get('store_id'))->orderBy('id', 'ASC')->first();
            }

            $order = new Order();
            $order->order_no = Order::max('id') + 1;
            $order->customer_id = $customer?->id ?? 0;
            $order->name = ($customer?->first_name ?? 'customer') . ' ' . ($customer?->last_name ?? '');
            $order->email = $customer?->email ?? 'email';
            $order->phone_number = $customer?->phone_number ?? 'phone_number';
            $order->status = Order::RETURNED;
            $order->payment_method = Order::CASH;
            $order->store_id = $request->store_id;
            $order->employee_id = $request->employee_id;
            $order->margin = $request->margin;
            $order->paid_amount = 0;
            $order->additional_notes = $request->comment;
            $order->save();

            $totalAmount = 0;
            $totalProducts = 0;
            $totalQuantity = 0;

            foreach ($cartProducts as $cart) {
                if (!isset($cart['id']) || !isset($cart['qty']) || !is_numeric($cart['qty']) || $cart['qty'] <= 0) {
                    continue;
                }

                $productId = $cart['id'];
                $variantId = $cart['variant_id'] ?? 0;
                $returnQty = (int) $cart['qty'];

                try {
                    if ($variantId) {
                        $product = Product::with(['variants' => fn($q) => $q->where('id', $variantId)])
                            ->whereHas('variants', fn($q) => $q->where('id', $variantId))
                            ->select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price', 'available_stock')
                            ->firstOrFail();
                    } else {
                        $product = Product::select('title', 'price', 'id', 'discount_status', 'discount_amount', 'purchase_price', 'dz_price', 'available_stock')
                            ->findOrFail($productId);
                    }

                    $calculatedPrice = $this->calculateProductPrice($product, $cart, $request->customer_id, $variantId, $request->get('manual_return_only'));

                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $product->id;
                    $orderProduct->variant_id = $variantId ? ($product->variants[0]->id ?? null) : null;
                    $orderProduct->qty = $returnQty;
                    $orderProduct->price = $calculatedPrice;
                    $orderProduct->barcode = $variantId ? ($product->variants[0]->barcode ?? null) : null;
                    $orderProduct->cost_price = $variantId ? ($product->variants[0]->purchase_price ?? 0) : ($product->purchase_price ?? 0);
                    $orderProduct->wholesale_price = $variantId ? ($product->variants[0]->additional_price ?? 0) : ($product->price ?? 0);
                    $orderProduct->returned = true;
                    $orderProduct->return_qty = $returnQty;
                    $orderProduct->save();

                    $totalAmount += $calculatedPrice * $returnQty;
                    $totalProducts++;
                    $totalQuantity += $returnQty;

                    // ———————— SMART STOCK RETURN LOGIC (Only if adjust_type == 1) ————————
                    if ($request->get('adjust_type') == 1) {
                        $storeId = $request->store_id;

                        // Get current available stock
                        $currentStock = $variantId
                            ? ProductVariant::where('id', $variantId)->value('available_stock') ?? 0
                            : Product::where('id', $product->id)->value('available_stock') ?? 0;

                        $remainingQty = $returnQty;

                        // Step 1: Neutralize negative stock first
                        if ($currentStock < 0) {
                            $neutralize = min($remainingQty, abs($currentStock));
                            $remainingQty -= $neutralize;
                            // No need to touch StoreProductStock — just increasing stock offsets oversell
                        }

                        // Step 2: Deduct from actual sold_qty (FIFO)
                        if ($remainingQty > 0) {
                            $query = StoreProductStock::where('store_id', $storeId)
                                ->where('sold_qty', '>', 0);

                            if ($variantId) {
                                $query->where('variant_id', $variantId);
                            } else {
                                $query->where('product_id', $product->id)->whereNull('variant_id');
                            }

                            $records = $query->orderBy('id', 'DESC')->get();
                            $stillNeed = $remainingQty;

                            foreach ($records as $stock) {
                                if ($stillNeed <= 0)
                                    break;

                                $avail = $stock->sold_qty;
                                if ($stillNeed >= $avail) {
                                    $stillNeed -= $avail;
                                    StoreProductStock::where('id', $stock->id)->update(['sold_qty' => 0]);
                                } else {
                                    StoreProductStock::where('id', $stock->id)->decrement('sold_qty', $stillNeed);
                                    $stillNeed = 0;
                                }
                            }

                            // Step 3: If still leftover → create system receiving
                            if ($stillNeed > 0) {
                                $receiving = Receiving::create([
                                    'po_id' => null,
                                    'store_id' => $storeId,
                                    'cargo_no' => 'Return Excess - System',
                                    'date' => now(),
                                    'status' => 2,
                                    'created_by' => auth()->id() ?? 1,
                                    'comment' => 'Excess return - negative stock offset',
                                    'payment_method' => 1,
                                    'gross_amount' => 0,
                                    'net_amount' => 0,
                                    'total_products' => 1,
                                    'total_qty' => $stillNeed,
                                ]);

                                ReceivingProduct::create([
                                    'receiving_id' => $receiving->id,
                                    'product_id' => $product->id,
                                    'product_variant_id' => $variantId ?: null,
                                    'qty' => $stillNeed,
                                    'cost_price' => 0,
                                ]);

                                StoreProductStock::create([
                                    'store_id' => $storeId,
                                    'product_id' => $product->id,
                                    'variant_id' => $variantId ?: null,
                                    'purchase_qty' => $stillNeed,
                                    'sold_qty' => 0,
                                    'receiving_id' => $receiving->id,
                                    'cost' => 0,
                                ]);
                            }
                        }

                        // Finally: Always increase available_stock by full return qty
                        if ($variantId) {
                            ProductVariant::where('id', $variantId)->increment('available_stock', $returnQty);
                            optional($product->variants[0])->increment('online_available_stock', $returnQty);
                        }
                        Product::where('id', $product->id)->increment('available_stock', $returnQty);
                    }
                    // —————————————————————————————————————————————————————————————

                } catch (\Exception $e) {
                    Log::warning('Product processing failed in return', ['error' => $e->getMessage()]);
                    continue;
                }
            }

            foreach ($cartBundles as $bundle) {
                if (!isset($bundle['bundle_id']) || !isset($bundle['qty']) || !is_numeric($bundle['qty']) || $bundle['qty'] <= 0) {
                    continue;
                }

                try {
                    $bundleData = Bundle::with([
                        'variants' => function ($query) {
                            $query->with([
                                'product' => function ($q) {
                                    $q->select('id', 'title', 'price', 'purchase_price', 'dz_price', 'available_stock');
                                },
                                'variant' => function ($q) {
                                    $q->select('id', 'product_id', 'additional_price', 'purchase_price', 'barcode', 'available_stock', 'online_available_stock');
                                }
                            ]);
                        }
                    ])->select('id', 'name', 'additional_price', 'purchase_price', 'short_desc')
                        ->findOrFail($bundle['bundle_id']);

                    $bundleQty = (int) $bundle['qty'];
                    $manualPrice = is_numeric($bundle['price']) ? (float) $bundle['price'] : 0;
                    if ($request->get('manual_return_only')) {
                        $bundlePrice = $manualPrice;
                    } else {
                        $bundlePrice = $manualPrice > 0 ? max($manualPrice, $bundleData->additional_price ?? 0) : ($bundleData->additional_price ?? 0);
                    }

                    $bundleOrderProduct = new OrderProduct();
                    $bundleOrderProduct->order_id = $order->id;
                    $bundleOrderProduct->bundle_id = $bundleData->id;
                    $bundleOrderProduct->qty = $bundleQty;
                    $bundleOrderProduct->price = $bundlePrice;
                    $bundleOrderProduct->is_bundle = true;
                    $bundleOrderProduct->is_bundle_item = false;
                    $bundleOrderProduct->cost_price = $bundleData->purchase_price ?? 0;
                    $bundleOrderProduct->wholesale_price = $bundleData->additional_price ?? 0;
                    $bundleOrderProduct->returned = true;
                    $bundleOrderProduct->return_qty = $bundleQty;
                    $bundleOrderProduct->save();

                    $componentCount = count($bundleData->variants);
                    $componentPrice = $componentCount > 0 ? $bundlePrice / $componentCount : 0;

                    foreach ($bundleData->variants as $bundleVariant) {
                        $variantId = $bundleVariant->product_variant_id ?? 0;

                        if ($variantId) {
                            $componentProduct = Product::with([
                                'variants' => function ($query) use ($variantId) {
                                    $query->where('id', $variantId);
                                }
                            ])->whereHas('variants', function ($query) use ($variantId) {
                                $query->where('id', $variantId);
                            })->select('title', 'price', 'id', 'purchase_price', 'dz_price', 'available_stock')->firstOrFail();
                        } else {
                            $componentProduct = Product::select('title', 'price', 'id', 'purchase_price', 'dz_price', 'available_stock')
                                ->findOrFail($bundleVariant->product_id);
                        }

                        $componentQty = $bundleQty;

                        $component = new OrderProduct();
                        $component->order_id = $order->id;
                        $component->product_id = $componentProduct->id;
                        $component->variant_id = $variantId ? ($componentProduct->variants[0]->id ?? null) : null;
                        $component->bundle_id = $bundleData->id;
                        $component->parent_id = $bundleOrderProduct->id;
                        $component->qty = $componentQty;
                        $component->price = $componentPrice;
                        $component->is_bundle = false;
                        $component->is_bundle_item = true;
                        $component->cost_price = $variantId ? ($componentProduct->variants[0]->purchase_price ?? 0) : ($componentProduct->purchase_price ?? 0);
                        $component->wholesale_price = $variantId ? ($componentProduct->variants[0]->additional_price ?? 0) : ($componentProduct->price ?? 0);
                        $component->barcode = $variantId ? ($componentProduct->variants[0]->barcode ?? null) : null;
                        $component->returned = true;
                        $component->return_qty = $componentQty;
                        $component->save();

                        if ($request->get('adjust_type') == 1) {
                            if ($component->variant_id) {
                                ProductVariant::where('id', $component->variant_id)->increment('available_stock', $componentQty);
                                ProductVariant::where('id', $component->variant_id)->increment('online_available_stock', $componentQty);
                            }
                            Product::where('id', $componentProduct->id)->increment('available_stock', $componentQty);
                        }

                        $totalProducts++;
                        $totalQuantity += $componentQty;
                    }

                    $totalAmount += $bundlePrice * $bundleQty;
                } catch (\Exception $e) {
                    Log::warning('Bundle processing failed in return', ['error' => $e->getMessage(), 'bundle' => $bundle]);
                    continue;
                }
            }

            $order->delivery_charges = 0;
            $order->discount_amount = $request->discount_id ? (float) $request->discount_id : 0;
            $order->total_amount = $totalAmount;
            $order->total_products = $totalProducts;
            $order->total_quantity = $totalQuantity;
            $order->return_date = now();
            $order->return_amount = $totalAmount;
            $order->return_type = $request->return_type;
            $order->adjust_type = $request->adjust_type;
            $order->mannual_return = $request->mannual_return;
            $order->save();

            DB::commit();
            $data['order'] = Order::find($order->id);
            return $this->sendResponse($data, 'Order return successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('createReturn failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }
    public function orderInfo(Request $request)
    {
        $data['order'] = Order::where('id', $request->get('order_id'))->with('customer', 'employee', 'products', 'products.bundle')->first();

        $data['previousBalance'] = 0;
        $data['totalRemaining'] = 0;
        if ($data['order'] && $data['order']->customer_id != 1) {
            $totalBillAmount = Order::where('id', '!=', $data['order']->id)
                ->where('customer_id', $data['order']->customer_id)
                ->where('status', '!=', 6)
                ->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id', $data['order']->customer_id)
                ->where('return_type', 1)
                ->sum('return_amount');
            $totalPayment = CustomerPayment::where('customer_id', $data['order']->customer_id)
                ->where('status', 2)
                ->sum('amount') - $data['order']->paid_amount;
            $totalDiscount = CustomerPayment::where('customer_id', $data['order']->customer_id)
                ->where('status', 2)
                ->sum('discount');

            $data['previousBalance'] = ((($data['order']->customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount;

            $totalBillAmount = Order::where('customer_id', $data['order']->customer_id)
                ->where('status', '!=', 6)
                ->sum('total_amount');
            $data['totalRemaining'] = ((($data['order']->customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount;
        }

        $data['status'] = $data['order'] ? ($data['order']->status == Order::RETURNED ? 'Complete Return' : 'Partially Return') : '';
        $data['store'] = Store::where('id', $data['order']->store_id)->first();
        $data['categories'] = Category::select('id', 'title')->get();

        return $this->sendResponse($data, 'Order info.');
    }

    public function getProductDetailByBarcode(Request $request)
    {
        $barcode = $request->barcode;
        $variant = null;

        $product = Product::where('barcode', $barcode)
            ->where('status', true)
            ->with('thumbnail', 'brand', 'variants')
            ->where('have_variants', false)
            ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
            ->first();

        if (!$product) {
            $variant = ProductVariant::where('barcode', $barcode)->first();

            if ($variant) {
                $product = Product::where('id', $variant->product_id)
                    ->where('status', true)
                    ->with([
                        'thumbnail',
                        'brand',
                        'variants' => function ($query) use ($variant) {
                            $query->where('id', $variant->id);
                        }
                    ])
                    ->select('id', 'title', 'price', 'brand_id', 'is_new', 'discount_amount', 'discount_status', 'have_variants', 'available_stock', 'dz_price')
                    ->first();
            }
        }

        if ($product) {
            $variantId = $variant->id ?? null;
            $qty = $variantId ? $variant->available_stock : $product->available_stock;

            return [
                'product' => $product,
                'qty' => $qty,
                'status' => true
            ];
        }

        return ['status' => false];
    }
    private function autoRestockNegativeStock($order, $storeId = null)
    {
        $storeId = $storeId ?? $order->store_id;
        if (!$storeId) {
            Log::warning('autoRestockNegativeStock: No store_id provided', ['order_id' => $order->id]);
            return;
        }

        $itemsToRestock = [];

        foreach ($order->products as $op) {
            // Skip returned or bundle parent rows
            if ($op->returned || $op->is_bundle == 1) {
                continue;
            }

            $productId = $op->product_id;
            $variantId = $op->variant_id;
            $soldQty = $op->qty - ($op->return_qty ?? 0); // Net sold in this order

            if ($soldQty <= 0)
                continue;

            // Get current available stock
            $currentStock = $variantId
                ? (ProductVariant::where('id', $variantId)->value('available_stock') ?? 0)
                : (Product::where('id', $productId)->value('available_stock') ?? 0);

            // Only restock if stock went negative AND we sold more than what's available now
            if ($currentStock < 0) {
                $negativeAmount = abs($currentStock);
                $restockQty = min($soldQty, $negativeAmount); // Only restock what THIS order oversold

                if ($restockQty > 0) {
                    $itemsToRestock[] = [
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'qty' => $restockQty,
                        'sold_in_order' => $soldQty,
                    ];
                }
            }
        }

        if (empty($itemsToRestock)) {
            return;
        }

        // Consolidate duplicates
        $consolidated = [];
        foreach ($itemsToRestock as $item) {
            $key = ($item['variant_id'] ?? 'p') . '_' . $item['product_id'];
            if (!isset($consolidated[$key])) {
                $consolidated[$key] = $item;
            } else {
                $consolidated[$key]['qty'] += $item['qty'];
            }
        }

        DB::beginTransaction();
        try {
            $receiving = Receiving::create([
                'invoice_no' => 'AUTO-' . now()->format('Ymd') . '-' . $order->id,
                'cargo_no' => 'AUTO-RESTOCK-O' . $order->order_no,
                'date' => now()->format('Y-m-d'),
                'store_id' => $storeId,
                'supplier_id' => 1,
                'payment_method' => 1,
                'net_amount' => 0,
                'gross_amount' => 0,
                'total_products' => count($consolidated),
                'total_qty' => array_sum(array_column($consolidated, 'qty')),
                'status' => 2, // Approved
                'created_by' => auth()->id() ?? 1,
                'approved_by' => auth()->id() ?? 1,
                'comment' => "Auto-restock for oversold items in Order #{$order->order_no}",
            ]);

            $totalCost = 0;

            foreach ($consolidated as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'];
                $qty = $item['qty'];

                // Get last known cost (fallback to 0 if none)
                $last = ReceivingProduct::where('product_id', $productId)
                    ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId))
                    ->when(!$variantId, fn($q) => $q->whereNull('product_variant_id'))
                    ->orderByDesc('id')
                    ->first();

                $costPrice = $last?->cost_price ?? 0;

                ReceivingProduct::create([
                    'receiving_id' => $receiving->id,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'qty' => $qty,
                    'cost_price' => $costPrice,
                    'trade_price' => $costPrice,
                    'sale_price' => $last?->sale_price ?? 0,
                ]);

                StoreProductStock::create([
                    'receiving_id' => $receiving->id,
                    'store_id' => $storeId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'purchase_qty' => $qty,
                    'sold_qty' => $qty,
                    'cost' => $costPrice,
                ]);

                // Restore only the oversold portion
                Product::where('id', $productId)->increment('available_stock', $qty);
                if ($variantId) {
                    ProductVariant::where('id', $variantId)->increment('available_stock', $qty);
                    ProductVariant::where('id', $variantId)->increment('online_available_stock', $qty);
                }

                $totalCost += $costPrice * $qty;

                Log::info('Auto-restock applied', [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'restock_qty' => $qty,
                    'cost' => $costPrice,
                ]);
            }

            $receiving->update([
                'gross_amount' => $totalCost,
                'net_amount' => $totalCost,
            ]);

            DB::commit();

            Log::info('Auto-restock receiving created successfully', [
                'receiving_id' => $receiving->id,
                'order_id' => $order->id,
                'items' => count($consolidated),
                'total_qty' => array_sum(array_column($consolidated, 'qty')),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Auto-restock failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
