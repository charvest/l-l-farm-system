<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

final class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = $this->cart();

        if ($cart === []) {
            return redirect()->route('cart.index')->with('success', 'Your cart is empty.');
        }

        [$items, $subtotal] = $this->hydrateCartItems($cart);

        if ($items === []) {
            return redirect()->route('cart.index')->with('success', 'Your cart is empty.');
        }

        $deliveryFee = (float) config('checkout.delivery_fee', 0);
        $etaDays = max(0, (int) config('checkout.delivery_eta_days', 2));

        $defaultMethod = 'delivery';
        $shipping = $defaultMethod === 'delivery' ? $deliveryFee : 0.0;
        $total = $subtotal + $shipping;

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'deliveryFee' => $deliveryFee,
            'etaDays' => $etaDays,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cart();

        if ($cart === []) {
            return redirect()->route('cart.index')->with('success', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'fulfillment_method' => ['required', 'in:delivery,pickup'],

            'customer_first_name' => ['required', 'string', 'max:60'],
            'customer_last_name' => ['required', 'string', 'max:60'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'customer_address' => ['nullable', 'string', 'max:255', 'required_if:fulfillment_method,delivery'],
            'notes' => ['nullable', 'string', 'max:500'],

            'payment_method' => ['required', 'in:cash,gcash,bank_transfer'],
            'payment_reference' => ['nullable', 'string', 'max:80', 'required_unless:payment_method,cash'],
        ]);

        $data = [
            'fulfillment_method' => $validated['fulfillment_method'],
            'customer_name' => trim($validated['customer_first_name'] . ' ' . $validated['customer_last_name']),
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ];

        try {
            $orderId = DB::transaction(function () use ($cart, $data): int {
                [$items, $subtotal] = $this->hydrateCartItems($cart, strictStock: true);

                if ($items === []) {
                    throw new RuntimeException('Cart is empty.');
                }

                $shippingFee = $this->shippingFee($data['fulfillment_method'] ?? 'delivery');
                $grandTotal = $subtotal + $shippingFee;

                $orderId = $this->insertOrderRow($data, $subtotal, $shippingFee, $grandTotal);

                $this->insertOrderItemsRows($orderId, $items);
                $this->decrementStock($items);

                return $orderId;
            });

            session()->forget('cart');

            return redirect()
                ->route('checkout.success', ['order' => $orderId])
                ->with('success', 'Order placed successfully!');
        } catch (Throwable $e) {
            return back()->withInput()->with('success', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function success(int $order): View
    {
        $orderRow = Schema::hasTable('orders') ? DB::table('orders')->where('id', $order)->first() : null;

        $items = [];
        if (Schema::hasTable('order_items')) {
            $items = DB::table('order_items')->where('order_id', $order)->get()->map(fn ($r) => (array) $r)->all();
        }

        $total = 0.0;
        if ($orderRow) {
            $total = (float) ($orderRow->total ?? $orderRow->total_amount ?? $orderRow->amount ?? 0);
        }

        return view('checkout.success', [
            'orderId' => $order,
            'order' => $orderRow,
            'items' => $items,
            'total' => $total,
            'totalText' => Money::php($total),
        ]);
    }

    private function shippingFee(string $fulfillmentMethod): float
    {
        if ($fulfillmentMethod === 'pickup') {
            return 0.0;
        }

        return max(0.0, (float) config('checkout.delivery_fee', 0));
    }

    /** @return array<string,array{name:string,price:mixed,quantity:mixed}> */
    private function cart(): array
    {
        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
        return session()->get('cart', []);
    }

    /**
     * @param array<string,array{name:string,price:mixed,quantity:mixed}> $cart
     * @return array{0: array<int,array{id:int,name:string,price:float,quantity:int,stock:int,subtotal:float}>, 1: float}
     */
    private function hydrateCartItems(array $cart, bool $strictStock = false): array
    {
        $ids = array_map('intval', array_keys($cart));

        $products = Product::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $productsTable = (new Product())->getTable();

        $stockCol = Schema::hasColumn($productsTable, 'stock')
            ? 'stock'
            : (Schema::hasColumn($productsTable, 'quantity') ? 'quantity' : null);

        $items = [];
        $total = 0.0;

        foreach ($cart as $id => $row) {
            $pid = (int) $id;
            $p = $products->get($pid);

            if (!$p) {
                continue;
            }

            $qty = max(1, (int) ($row['quantity'] ?? 1));
            $price = (float) ($row['price'] ?? $p->price ?? 0);
            $stock = $stockCol ? (int) ($p->{$stockCol} ?? 0) : 0;

            if ($stockCol && $stock > 0) {
                if ($strictStock && $qty > $stock) {
                    throw new RuntimeException("Not enough stock for {$p->name}. Requested {$qty}, available {$stock}.");
                }
                $qty = min($qty, $stock);
            }

            $subtotal = $price * $qty;
            $total += $subtotal;

            $items[] = [
                'id' => $pid,
                'name' => (string) $p->name,
                'price' => $price,
                'quantity' => $qty,
                'stock' => $stock,
                'subtotal' => $subtotal,
            ];
        }

        return [$items, $total];
    }

    private function insertOrderRow(array $data, float $subtotal, float $shippingFee, float $total): int
    {
        if (!Schema::hasTable('orders')) {
            throw new RuntimeException('orders table is missing. Run migrations.');
        }

        $payload = [];
        $now = now();

        if (Schema::hasColumn('orders', 'user_id') && auth()->check()) {
            $payload['user_id'] = auth()->id();
        }

        if (Schema::hasColumn('orders', 'status')) {
            $payload['status'] = 'Pending';
        }

        if (Schema::hasColumn('orders', 'subtotal_amount')) {
            $payload['subtotal_amount'] = $subtotal;
        }

        if (Schema::hasColumn('orders', 'shipping_fee')) {
            $payload['shipping_fee'] = $shippingFee;
        }

        if (Schema::hasColumn('orders', 'total')) {
            $payload['total'] = $total;
        } elseif (Schema::hasColumn('orders', 'total_amount')) {
            $payload['total_amount'] = $total;
        } elseif (Schema::hasColumn('orders', 'amount')) {
            $payload['amount'] = $total;
        }

        foreach ([
            'customer_name' => 'customer_name',
            'customer_email' => 'customer_email',
            'customer_phone' => 'customer_phone',
            'customer_address' => 'customer_address',
            'notes' => 'notes',
            'fulfillment_method' => 'fulfillment_method',
            'payment_method' => 'payment_method',
            'payment_reference' => 'payment_reference',
        ] as $key => $col) {
            if (Schema::hasColumn('orders', $col)) {
                $payload[$col] = $data[$key] ?? null;
            }
        }

        if (Schema::hasColumn('orders', 'created_at')) {
            $payload['created_at'] = $now;
        }

        if (Schema::hasColumn('orders', 'updated_at')) {
            $payload['updated_at'] = $now;
        }

        return (int) DB::table('orders')->insertGetId($payload);
    }

    /** @param array<int,array{id:int,name:string,price:float,quantity:int,stock:int,subtotal:float}> $items */
    private function insertOrderItemsRows(int $orderId, array $items): void
    {
        if (!Schema::hasTable('order_items')) {
            throw new RuntimeException('order_items table is missing. Run migrations.');
        }

        $now = now();

        foreach ($items as $it) {
            $row = [];

            if (Schema::hasColumn('order_items', 'order_id')) {
                $row['order_id'] = $orderId;
            }

            if (Schema::hasColumn('order_items', 'product_id')) {
                $row['product_id'] = $it['id'];
            }

            if (Schema::hasColumn('order_items', 'quantity')) {
                $row['quantity'] = $it['quantity'];
            }

            if (Schema::hasColumn('order_items', 'price')) {
                $row['price'] = $it['price'];
            } elseif (Schema::hasColumn('order_items', 'unit_price')) {
                $row['unit_price'] = $it['price'];
            }

            if (Schema::hasColumn('order_items', 'created_at')) {
                $row['created_at'] = $now;
            }

            if (Schema::hasColumn('order_items', 'updated_at')) {
                $row['updated_at'] = $now;
            }

            DB::table('order_items')->insert($row);
        }
    }

    /** @param array<int,array{id:int,name:string,price:float,quantity:int,stock:int,subtotal:float}> $items */
    private function decrementStock(array $items): void
    {
        $productsTable = (new Product())->getTable();

        $stockCol = Schema::hasColumn($productsTable, 'stock')
            ? 'stock'
            : (Schema::hasColumn($productsTable, 'quantity') ? 'quantity' : null);

        if (!$stockCol) {
            return;
        }

        foreach ($items as $it) {
            Product::query()->where('id', $it['id'])->decrement($stockCol, $it['quantity']);
        }
    }
}
