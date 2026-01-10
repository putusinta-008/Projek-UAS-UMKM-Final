<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * STAFF - lihat semua order
     */
    public function index()
    {
        return response()->json(
            Order::with('items.product')->latest()->get()
        );
    }

    /**
     * USER - lihat order sendiri
     */
    public function myOrders()
    {
        return response()->json(
            Order::with('items.product')
                ->where('user_id', auth()->id())
                ->latest()
                ->get()
        );
    }

    /**
     * USER - buat order
     */
    public function store(Request $request)
    {
        // Proteksi role
        if (auth()->user()->role !== 'user') {
            return response()->json([
                'message' => 'Hanya user yang dapat melakukan transaksi'
            ], 403);
        }

        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($validated) {

                $total = 0;
                $items = [];

                foreach ($validated['items'] as $item) {

                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($product->stock < $item['qty']) {
                        throw new \Exception(
                            "Stok produk '{$product->name}' tidak cukup"
                        );
                    }

                    // Kurangi stok
                    $product->stock -= $item['qty'];
                    $product->save();

                    $total += $product->price * $item['qty'];

                    $items[] = [
                        'product_id' => $product->id,
                        'qty'        => $item['qty'],
                        'price'      => $product->price,
                    ];
                }

                $order = Order::create([
                    'user_id'     => auth()->id(),
                    'total_price' => $total,
                    'status'      => 'pending'
                ]);

                foreach ($items as &$item) {
                    $item['order_id'] = $order->id;
                }

                OrderItem::insert($items);

                return response()->json([
                    'message' => 'Transaksi berhasil',
                    'order'   => $order->load('items.product')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * USER - cancel order + rollback stok
     */
    public function cancel($id)
    {
        // hanya USER
        if (auth()->user()->role !== 'user') {
            return response()->json([
                'message' => 'Hanya user yang dapat membatalkan order'
            ], 403);
        }

        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Cegah cancel ulang
        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Order ini sudah dibatalkan'
            ], 400);
        }

        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                $product->stock += $item->qty;
                $product->save();
            }

            $order->update([
                'status' => 'cancelled'
            ]);
        });

        return response()->json([
            'message' => 'Order berhasil dibatalkan dan stok dikembalikan'
        ]);
    }
}