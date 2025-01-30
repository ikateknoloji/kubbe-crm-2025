<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Exception;

class StockHelper
{
    /**
     * Sipariş içindeki ürünleri stoktan düş.
     *
     * @param Order $order
     * @throws Exception
     */
    public static function reduceStockForOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $groupedItems = $order->orderItems->groupBy('stock_id')->map(fn($items) => $items->sum('quantity'));

            foreach ($groupedItems as $stockId => $totalQuantity) {
                $stock = Stock::find($stockId) ?? throw new Exception("Stok kaydı bulunamadı! Stock ID: {$stockId}");
                if ($stock->quantity < $totalQuantity) throw new Exception("Yetersiz stok! Ürün ID: {$stock->id}, Mevcut: {$stock->quantity}, İstenen: {$totalQuantity}");
            }

            $order->orderItems->each(fn($item) => $item->stock->decrement('quantity', $item->quantity));
        });
    }
}
