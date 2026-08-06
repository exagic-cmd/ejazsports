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
use Illuminate\Support\Facades\Log;

use App\Models\StoreProductStock;

class SaleController extends BaseController
{
   public function getSaleData(Request $request)
{
    $storeId = $request->get('store_id');

    $data['orders'] = Order::where('store_id', $storeId)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->where('status', '!=', 6)
        ->select('id', 'order_no', 'created_at', 'total_amount')
        ->orderBy('id', 'desc')
        ->get();

    $data['latestOrder'] = Order::where('store_id', $storeId)
        ->where('status', '!=', 6)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->with([
            'products',
            'products.product',
            'products.variant',
            'products.bundle'
        ])
        ->orderBy('id', 'desc')
        ->limit(1)
        ->first();

    $data['totalAmount'] = Order::where('store_id', $storeId)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->sum('total_amount');

    $data['totalCount'] = Order::where('store_id', $storeId)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->count();

    $data['allProducts'] = Product::with('variants')
        ->select('id', 'title', 'have_variants')
        ->get();

    $data['categories'] = Category::select('id', 'title')->get();

    return $this->sendResponse($data, 'Sale Data.');
}

    public function getHoldList(Request $request)
    {

        $data['cart'] = $request->get('cart');

        $products = array();
        $variants = array();
        if ($data['cart']) {
            foreach ($data['cart'] as $c) {
                foreach (json_decode($c['products']) as $p) {
                    $cId = $c['id'];
                    $productId = $p->id;
                    $variantId = $p->variant_id;
                    if ($variantId) {
                        $products[$p->id] = Product::with(['variants' => function ($query) use ($variantId) {
                            $query->where('id', $variantId);
                        }])->wherehas('variants', function ($query) use ($variantId) {
                            $query->where('id', $variantId);
                        })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
                        $variants[$variantId] = $products[$p->id]->variants[0];
                    } else {
                        $products[$p->id] = Product::where('id', $productId)->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
                        $variants[$variantId] = null;
                    }
                }
            }
        }
        $data['products'] = $products;
        $data['variants'] = $variants;

        $data['allProducts'] = Product::with('variants')->select('id', 'title', 'have_variants')->get();
        $data['categories'] = Category::select('id', 'title')->get();

        return $this->sendResponse($data, 'Hold List Data.');
    }

   public function getReturnOrders(Request $request)
{
    $storeId = $request->get('store_id');

    $data['orders'] = Order::where('store_id', $storeId)
        ->where('is_website_order', 0)
        ->whereIn('status', [Order::RETURNED, Order::PARTIALLY_RETURNED])
        ->whereDate('return_date', \Carbon\Carbon::today())
        ->select('id', 'order_no', 'created_at', 'total_amount', 'status', 'return_date')
        ->orderBy('return_date', 'desc')
        ->get();

    $data['latestOrder'] = Order::where('store_id', $storeId)
        ->where('is_website_order', 0)
        ->whereIn('status', [Order::RETURNED, Order::PARTIALLY_RETURNED])
        ->whereDate('return_date', \Carbon\Carbon::today())
        ->with([
            'products',
            'products.product',
            'products.variant',
            'products.bundle'
        ])
        ->orderBy('return_date', 'desc')
        ->limit(1)
        ->first();

    $data['status'] = $data['latestOrder'] ?
        ($data['latestOrder']->status == Order::RETURNED ? 'Complete Return' : 'Partially Return') : '';

    $data['totalAmount'] = Order::where('store_id', $storeId)
        ->where('is_website_order', 0)
        ->whereIn('status', [Order::RETURNED, Order::PARTIALLY_RETURNED])
        ->whereDate('return_date', \Carbon\Carbon::today())
        ->sum('return_amount');

    $data['totalCount'] = Order::where('store_id', $storeId)
        ->where('is_website_order', 0)
        ->whereIn('status', [Order::RETURNED, Order::PARTIALLY_RETURNED])
        ->whereDate('return_date', \Carbon\Carbon::today())
        ->count();

    $data['allProducts'] = Product::with('variants')
        ->select('id', 'title', 'have_variants')
        ->get();

    $data['categories'] = Category::select('id', 'title')->get();

    return $this->sendResponse($data, 'Return Order Data.');
}

    public function getSearchOrder(Request $request)
    {

        $orderNo = $request->get('order_no');

        $data['order'] = Order::where([['store_id', $request->get('store_id')], ['order_no', $orderNo]])->with('products', 'employee')->first();

        $data['allProducts'] = Product::with('variants')->select('id', 'title', 'have_variants')
            ->get();

        return $this->sendResponse($data, 'Search Order Data.');
    }

 public function updateCompleteReturnOrder(Request $request)
{
    DB::beginTransaction();
    try {
        $order = Order::findOrFail($request->order_id);
        $order->status = Order::RETURNED;
        $order->return_date = now();
        $order->return_amount = $order->total_amount;
        $order->return_type = $request->type;
        $order->save();

        $storeId = $order->store_id;

        foreach ($order->products as $p) {
            if (isset($p->is_bundle_item) && $p->is_bundle_item == 1) continue;

            $toReturnQty = $p->qty - ($p->return_qty ?? 0);
            if ($toReturnQty <= 0) continue;

            // Handle bundle parent
            if (isset($p->is_bundle) && $p->is_bundle) {
                OrderProduct::where('id', $p->id)->update(['returned' => true, 'return_qty' => $p->qty]);

                $children = $order->products
                    ->where('bundle_id', $p->bundle_id)
                    ->where('is_bundle_item', 1)
                    ->filter(function ($child) use ($p) {
                        return $child->parent_id ? $child->parent_id == $p->id : true;
                    });
                $parentQty = $p->qty ?: 1;

                foreach ($children as $child) {
                    $perBundle = $parentQty > 0 ? $child->qty / $parentQty : $child->qty;
                    $restoreQty = $perBundle * $toReturnQty;

                    $this->applySmartStockReturn(
                        $child->product_id,
                        $child->variant_id,
                        $restoreQty,
                        $storeId
                    );

                    $childReturnQty = min(round($restoreQty), $child->qty - ($child->return_qty ?? 0));
                    if ($childReturnQty > 0) {
                        OrderProduct::where('id', $child->id)->increment('return_qty', $childReturnQty);
                        OrderProduct::where('id', $child->id)->update(['returned' => true]);
                    }
                }
                continue;
            }

            // Normal product
            $this->applySmartStockReturn($p->product_id, $p->variant_id, $toReturnQty, $storeId);

            OrderProduct::where('id', $p->id)->update([
                'returned' => true,
                'return_qty' => $p->qty
            ]);
        }

        DB::commit();
        return $this->sendResponse(['order' => $order], 'Order fully returned.');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('updateCompleteReturnOrder failed', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed'], 500);
    }
}

   public function updatePartiallyReturnOrder(Request $request)
{
    DB::beginTransaction();
    try {
        $order = Order::findOrFail($request->order_id);
        $order->status = Order::PARTIALLY_RETURNED;
        $order->return_date = now();
        $order->return_type = $request->type;
        $storeId = $request->get('store_id') ?? $order->store_id;

        $product_ids = $request->get('product_ids', []);
        $return_qty = $request->get('return_qty', []);
        $totalReturnAmount = 0;

        foreach ($order->products as $p) {
            $index = array_search($p->id, $product_ids);
            if ($index === false) continue;

            $qty = (int)($return_qty[$index] ?? 0);
            if ($qty <= 0) continue;

            $alreadyReturned = $p->return_qty ?? 0;
            $canReturn = $p->qty - $alreadyReturned;
            $qty = min($qty, $canReturn);

            if ($qty <= 0) continue;

            if (isset($p->is_bundle) && $p->is_bundle) {
                OrderProduct::where('id', $p->id)->increment('return_qty', $qty);
                OrderProduct::where('id', $p->id)->update(['returned' => true]);

                $children = $order->products
                    ->where('bundle_id', $p->bundle_id)
                    ->where('is_bundle_item', 1)
                    ->filter(function ($child) use ($p) {
                        return $child->parent_id ? $child->parent_id == $p->id : true;
                    });
                $parentQty = $p->qty ?: 1;

                foreach ($children as $child) {
                    $perBundle = $parentQty > 0 ? $child->qty / $parentQty : $child->qty;
                    $restoreQty = $perBundle * $qty;

                    $this->applySmartStockReturn($child->product_id, $child->variant_id, $restoreQty, $storeId);

                    $add = min(round($restoreQty), $child->qty - ($child->return_qty ?? 0));
                    if ($add > 0) {
                        OrderProduct::where('id', $child->id)->increment('return_qty', $add);
                        OrderProduct::where('id', $child->id)->update(['returned' => true]);
                    }
                }
                $totalReturnAmount += $p->price * $qty;
                continue;
            }

            // Normal product
            $this->applySmartStockReturn($p->product_id, $p->variant_id, $qty, $storeId);

            OrderProduct::where('id', $p->id)->increment('return_qty', $qty);
            OrderProduct::where('id', $p->id)->update(['returned' => true]);
            $totalReturnAmount += $p->price * $qty;
        }

        // Apply invoice-level discount proportionally to the returned amount
        $totalGross = ($order->total_amount ?? 0) + ($order->discount_amount ?? 0);
        $discountRatio = $totalGross > 0 ? (($order->discount_amount ?? 0) / $totalGross) : 0;

        $netReturnAmount = $totalReturnAmount * (1 - $discountRatio);

        $order->return_amount = ($order->return_amount ?? 0) + $netReturnAmount;
        $order->save();

        DB::commit();
        return $this->sendResponse(['order' => $order], 'Partial return processed.');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('updatePartiallyReturnOrder failed', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed'], 500);
    }
}

// ———— REUSABLE SMART RETURN LOGIC (Only one small inline method) ————
private function applySmartStockReturn($productId, $variantId, $returnQty, $storeId)
{
    if ($returnQty <= 0) return;

    $currentStock = $variantId
        ? ProductVariant::where('id', $variantId)->value('available_stock') ?? 0
        : Product::where('id', $productId)->value('available_stock') ?? 0;

    $remaining = $returnQty;

    // 1. Neutralize negative stock
    if ($currentStock < 0) {
        $neutralize = min($remaining, abs($currentStock));
        $remaining -= $neutralize;
    }

    // 2. Deduct from sold_qty
    if ($remaining > 0) {
        $query = StoreProductStock::where('store_id', $storeId)->where('sold_qty', '>', 0);
        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->where('product_id', $productId)->whereNull('variant_id');
        }

        foreach ($query->orderBy('id', 'DESC')->get() as $stock) {
            if ($remaining <= 0) break;
            $avail = $stock->sold_qty;
            if ($remaining >= $avail) {
                $remaining -= $avail;
                $stock->update(['sold_qty' => 0]);
            } else {
                $stock->decrement('sold_qty', $remaining);
                $remaining = 0;
            }
        }


    }

    // Always increase available_stock
    if ($variantId) {
        ProductVariant::where('id', $variantId)->increment('available_stock', $returnQty);
    }
    Product::where('id', $productId)->increment('available_stock', $returnQty);
}

    public function getOrderDetail(Request $request)
    {

        $orderNo = $request->get('order_no');

        $data['order'] = Order::where('id', $orderNo)->with('employee', 'products', 'customer')->first();


        return $this->sendResponse($data, 'Order Detail.');
    }
}
