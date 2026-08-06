<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderProduct;

class CartController extends BaseController
{
    /**
     * Show the cart page (Blade view)
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 300;
        $total = $subtotal + $shipping;

        $footerData = $this->getFooterCategories();

        return view('cart', array_merge(compact('cart', 'subtotal', 'shipping', 'total'), $footerData));
    }

    // ==================== AJAX METHODS ====================

    public function add(Request $request) {
        try {
            // Check if it's a bulk request (has 'items' array) or single request
            $items = $request->items ?? [
                [
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity'   => $request->quantity ?? 1
                ]
            ];

            if (!is_array($items) || empty($items)) {
                 return response()->json(['success' => false, 'message' => 'Invalid request data'], 400);
            }

            $addedCount = 0;
            $errors = [];

            foreach ($items as $item) {
                // Determine item properties (handle both array and object if needed, using array here from typical JSON/Form)
                $productId = $item['product_id'] ?? null;
                $variantId = $item['variant_id'] ?? null;
                $quantity  = $item['quantity'] ?? 1;

                if (!$productId) continue;

                // --- Existing Logic Inner Loop ---
                $product = DB::table('products')
                    ->where('id', $productId)
                    ->select('id', 'title', 'slug', 'price', 'barcode', 'have_variants', 'available_stock', 'is_in_stock')
                    ->first();

                if (!$product) {
                    $errors[] = "Product ID {$productId} not found";
                    continue;
                }

                if ($product->have_variants && !$variantId) {
                    $errors[] = "Product '{$product->title}' requires a variant selection.";
                    continue;
                }

                $finalPrice = $product->price;
                $finalStock = $product->available_stock ?? 0;
                $variantLabel = '';
                $variantBarcode = $product->barcode ?? null;
                $variant = null;

                if ($variantId) {
                    $variant = DB::table('product_variants')
                        ->where('id', $variantId)
                        ->where('product_id', $productId)
                        ->where('status', 1)
                        ->first();

                    if (!$variant) {
                        $errors[] = "Variant for '{$product->title}' not found.";
                        continue;
                    }

                    $finalPrice = $product->price + ($variant->additional_price ?? 0);
                    $finalStock = $variant->available_stock ?? 0;
                    $variantLabel = trim(($variant->size ?? '') . ' ' . ($variant->shade ?? ''));
                    $variantBarcode = $variant->barcode ?? $variantBarcode;
                }

                if ($finalStock <= 0) {
                     $errors[] = "'{$product->title} " . ($variantLabel) . "' is out of stock.";
                     continue;
                }

                 // Get product image (first one)
                $imageUrl = DB::table('product_images')
                    ->where('product_id', $productId)
                    ->orderBy('serial_no')
                    ->value('url');
                
                $imageUrl = $imageUrl ? env('BACKEND_IMAGE_URL') . $imageUrl : 'storage/images/default/default.jpg';


                // Use unique key: product_id + variant_id
                $cartKey = $variantId ? "p{$productId}_v{$variantId}" : "p{$productId}";
                $cart = session()->get('cart', []);

                $existingQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
                $newTotalQty = $existingQty + $quantity;

                if ($newTotalQty > $finalStock) {
                    $canAdd = max(0, $finalStock - $existingQty);
                     if ($canAdd <= 0) {
                        $errors[] = "You already have the max stock of '{$product->title} {$variantLabel}' in your cart.";
                     } else {
                         $errors[] = "Only {$canAdd} more of '{$product->title} {$variantLabel}' can be added.";
                     }
                     // Optional: Add what we can? For now, skip to match previous strictness or proceed with partial?
                     // Let's matching previous strict behavior -> skip adding if full amt not avail
                     continue;
                }

                // Generate slug
                $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);

                $cart[$cartKey] = [
                    'key'          => $cartKey,
                    'product_id'   => $product->id,
                    'variant_id'   => $variantId,
                    'slug'         => $productSlug,
                    'title'        => $product->title . ($variantLabel ? " ($variantLabel)" : ''),
                    'price'        => $finalPrice,
                    'image'        => $imageUrl,
                    'quantity'     => $newTotalQty, // Intentionally setting new TOTAL
                    'stock'        => $finalStock,
                    'barcode'      => $variantBarcode,
                    'size'         => $variant?->size,
                    'shade'        => $variant?->shade,
                ];

                session()->put('cart', $cart);
                $addedCount++;
            }

            if ($addedCount === 0 && count($errors) > 0) {
                return response()->json(['success' => false, 'message' => implode(' ', $errors)], 400);
            }

            $msg = $addedCount > 1 ? "{$addedCount} products added to cart!" : "Product added to cart!";
            if (count($errors) > 0) {
                $msg .= " (Some items failed: " . implode(', ', $errors) . ")";
            }

            return $this->getCartResponse($msg);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'key'      => 'required|string', // Now uses cart key (p1_v5 or p10)
            'quantity' => 'required|integer|min:0'
        ]);

        $cartKey = $request->key;
        $quantity = (int)$request->quantity;

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            if ($quantity <= 0) {
                unset($cart[$cartKey]);
            } else {
                // Optional: re-check stock here if needed
                $cart[$cartKey]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }

        return $this->getCartResponse('Cart updated');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        $cartKey = $request->key;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        return $this->getCartResponse('Item removed');
    }

    public function getCartItems()
    {
        return $this->getCartResponse();
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        // Optional: redirect if empty, but user might want to see empty checkout? usually not.
        if (empty($cart)) {
            return redirect()->route('cart');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 300; 
        $total = $subtotal + $shipping;

        $footerData = $this->getFooterCategories();

        // Get customer data for auto-fill if logged in
        $customerData = null;
        if (session('customer_id')) {
            $customer = \DB::table('customers')->where('id', session('customer_id'))->first();
            if ($customer) {
                $customerData = [
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number,
                    'country' => $customer->country,
                    'state' => $customer->state,
                    'city' => $customer->city,
                    'zip' => $customer->zip,
                    'street_address' => $customer->street_address,
                    'apt_suite' => $customer->apt_suite,
                ];
            }
        }

        return view('checkout', array_merge(compact('cart', 'subtotal', 'shipping', 'total', 'customerData'), $footerData));
    }

    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Cart is empty');
        }

        \Log::info('Checkout Request Data:', $request->all());

        $rules = [
            'firstName' => 'required',
            'lastName'  => 'required',
            'streetAddress' => 'required',
            'cityAddress' => 'required',
            'emailAddress' => 'required|email',
            'phone' => 'required',
            'country' => 'required',
            'postcode' => 'required',
        ];

        // If 'Ship to different address' is checked, validate those fields too
        if ($request->has('shippingdiffrentAddress')) {
            $rules['shipping_firstName'] = 'required';
            $rules['shipping_lastName']  = 'required';
            $rules['shipping_streetAddress'] = 'required';
            $rules['shipping_cityAddress'] = 'required';
            $rules['shipping_emailAddress'] = 'required|email';
            $rules['shipping_country'] = 'required';
            $rules['shipping_postcode'] = 'required';
        }

        // If 'createAnaccount' is checked, validate password
        if ($request->has('createAnaccount')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Prepare Customer Data
            // For logged-in customers, use their session email (email cannot be changed)
            $customerEmail = session('customer_id') ? session('customer_email') : $request->emailAddress;
            
            $customerData = [
                'first_name' => $request->firstName,
                'last_name'  => $request->lastName,
                'email'      => $customerEmail,
                'phone_number' => $request->phone,
                'country'    => $request->country,
                'state'      => $request->state,
                'city'       => $request->cityAddress,
                'zip'        => $request->postcode,
                'street_address' => $request->streetAddress,
                'is_website_customer' => 1,
                'store_id'   => 1, // Default Store ID
                'area_id'    => 1, // Default Area ID
                'closing_balance' => 0,
                'updated_at' => now(),
            ];

            // Handle Password if Account Creation Requested
            if ($request->has('createAnaccount') && $request->filled('password')) {
                $customerData['password'] = Hash::make($request->password);
            }

            // Add Shipping Data to Customer Array if present
            if ($request->has('shippingdiffrentAddress')) {
                $customerData['shipping_first_name'] = $request->shipping_firstName;
                $customerData['shipping_last_name']  = $request->shipping_lastName;
                $customerData['shipping_email']      = $request->shipping_emailAddress;
                $customerData['shipping_phone_number'] = $request->shipping_phone;
                $customerData['shipping_country']    = $request->shipping_country;
                $customerData['shipping_state']      = $request->shipping_state;
                $customerData['shipping_city']       = $request->shipping_cityAddress;
                $customerData['shipping_zip']        = $request->shipping_postcode;
                $customerData['shipping_street_address'] = $request->shipping_streetAddress;
            }

            // 1. Handle Customer using DB Query Builder
            if (session('customer_id')) {
                // Logged-in customer - update their info
                $customerId = session('customer_id');
                DB::table('customers')->where('id', $customerId)->update($customerData);
                
                // Update session with new customer info
                session([
                    'customer_first_name' => $request->firstName,
                    'customer_last_name' => $request->lastName,
                    'customer_phone' => $request->phone,
                ]);
            } else {
                // Guest customer
                $existingCustomer = DB::table('customers')->where('email', $customerEmail)->first();

                if ($existingCustomer) {
                    // Update existing customer
                    DB::table('customers')->where('id', $existingCustomer->id)->update($customerData);
                    $customerId = $existingCustomer->id;
                } else {
                    // Insert new customer
                    $customerData['created_at'] = now();
                    $customerData['status'] = 1; // Default status active
                    $customerId = DB::table('customers')->insertGetId($customerData);
                }
            }

            // 2. Determine Order Address (Billing vs Shipping)
            $orderAddress = [
                'name'    => $request->firstName . ' ' . $request->lastName,
                'email'   => $request->emailAddress,
                'phone'   => $request->phone,
                'address' => $request->streetAddress,
                'city'    => $request->cityAddress,
                'country' => $request->country,
                'zipcode' => $request->postcode,
            ];

            if ($request->has('shippingdiffrentAddress')) {
                $orderAddress['name']    = $request->shipping_firstName . ' ' . $request->shipping_lastName;
                $orderAddress['email']   = $request->shipping_emailAddress;
                $orderAddress['phone']   = $request->shipping_phone ?? $request->phone;
                $orderAddress['address'] = $request->shipping_streetAddress;
                $orderAddress['city']    = $request->shipping_cityAddress;
                $orderAddress['country'] = $request->shipping_country;
                $orderAddress['zipcode'] = $request->shipping_postcode;
            }

            // 3. Create Order using DB Query Builder
            $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $shipping = 300;
            $total = $subtotal + $shipping;

            $orderData = [
                'order_no'      => 'ORD-' . strtoupper(uniqid()),
                'customer_id'   => $customerId,
                'name'          => $orderAddress['name'],
                'email'         => $orderAddress['email'],
                'phone_number'  => $orderAddress['phone'],
                'address'       => $orderAddress['address'],
                'city'          => $orderAddress['city'],
                'status'        => 1, // PENDING
                'delivery_charges' => $shipping,
                'total_amount'  => $total,
                'total_products' => count($cart),
                'total_quantity' => collect($cart)->sum('quantity'),
                'payment_method' => 1, // CASH
                'is_website_order' => 1,
                'store_id'      => 1, // Default Store ID
                'order_notes'   => $request->order_notes,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            $orderId = DB::table('orders')->insertGetId($orderData);

            // 4. Save Order Products
            $orderProductsData = [];
            foreach ($cart as $item) {
                // Fetch cost_price and wholesale_price from product_variants
                $costPrice = 0;
                $wholesalePrice = 0;
                
                if ($item['variant_id']) {
                    $variant = DB::table('product_variants')
                        ->where('id', $item['variant_id'])
                        ->select('purchase_price', 'additional_price')
                        ->first();
                    
                    if ($variant) {
                        $costPrice = $variant->purchase_price ?? 0;
                        $wholesalePrice = $variant->additional_price ?? 0;
                    }
                }
                
                $orderProductsData[] = [
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'qty'        => $item['quantity'],
                    'price'      => $item['price'],
                    'barcode'    => $item['barcode'] ?? null,
                    'cost_price' => $costPrice,
                    'wholesale_price' => $wholesalePrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($orderProductsData)) {
                DB::table('order_products')->insert($orderProductsData);
            }

            // 5. Update Stock (Product, Variant, StoreProductStock)
            foreach ($cart as $item) {
                $qtyToDeduct = $item['quantity'];
                $prodId = $item['product_id'];
                $varId  = $item['variant_id'] ?: null; // Ensure null if empty/0

                // A. Product Table
                DB::table('products')->where('id', $prodId)->decrement('available_stock', $qtyToDeduct);

                // B. Variant Table
                if ($varId) {
                    DB::table('product_variants')->where('id', $varId)->decrement('available_stock', $qtyToDeduct);
                    DB::table('product_variants')->where('id', $varId)->decrement('online_available_stock', $qtyToDeduct);
                }

                // C. Store Product Stocks (FIFO)
                // Assuming Store ID 1 as per default in this controller
                $storeId = 1; 

                $stocks = DB::table('store_product_stocks')
                    ->where('store_id', $storeId)
                    ->where('product_id', $prodId)
                    ->where('variant_id', $varId)
                    ->whereRaw('sold_qty < purchase_qty')
                    ->orderBy('id', 'asc') // FIFO
                    ->get();

                $remaining = $qtyToDeduct;

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) break;

                    $availableInBatch = $stock->purchase_qty - $stock->sold_qty;

                    if ($remaining >= $availableInBatch) {
                        // Consume this entire batch
                        DB::table('store_product_stocks')
                            ->where('id', $stock->id)
                            ->update(['sold_qty' => $stock->purchase_qty]);
                        
                        $remaining -= $availableInBatch;
                    } else {
                        // Consume partial batch
                        DB::table('store_product_stocks')
                            ->where('id', $stock->id)
                            ->increment('sold_qty', $remaining);
                        
                        $remaining = 0;
                    }
                }
            }

            DB::commit();
            session()->forget('cart');

            // Send Order Confirmation Email
            try {
                $order = \App\Models\Order::find($orderId);
                if ($order && $order->email) {
                    \Illuminate\Support\Facades\Mail::to($order->email)->send(new \App\Mail\OrderPlaced($order));
                }
            } catch (\Exception $e) {
                \Log::error('Email sending failed: ' . $e->getMessage());
            }

            return redirect()->route('account.thankyou');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    private function getCartResponse($message = null)
    {
        $cart = session()->get('cart', []);

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 300;
        $total = $subtotal + $shipping;

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'count'    => count($cart),
            'subtotal' => number_format($subtotal, 2),
            'shipping' => number_format($shipping, 2),
            'total'    => number_format($total, 2),
            'html'     => view('cart-table', compact('cart', 'subtotal', 'shipping', 'total'))->render(),
            'miniHtml' => view('mini-cart', ['cart' => $cart])->render(), // Adjust path if needed
        ]);
    }
}
