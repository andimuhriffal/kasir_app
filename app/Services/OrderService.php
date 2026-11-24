<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Generate nomor faktur: INV-POS-YYYYMMDD-XXXX (urutan per hari, 4 digit).
     */
    public function generateInvoiceNumber(?string $date = null): string
    {
        $dateKey = $date ?? now()->format('Ymd');
        $prefix = "INV-POS-{$dateKey}-";

        $last = Order::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->value('invoice_number');

        $seq = 1;
        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        }

        return sprintf('INV-POS-%s-%04d', $dateKey, $seq);
    }

    /**
     * Hitung total dari item: subtotal, diskon, pajak PB1 10%, grand total.
     * Pajak dihitung dari (subtotal - diskon).
     *
     * @param  array<int, array{kuantiti: int, harga_satuan: float}>  $items
     */
    public function calculateTotals(array $items, float $diskon = 0): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['harga_satuan'] * $item['kuantiti'];
        }

        $diskon = max(0, min($diskon, $subtotal));
        $pajak = round(($subtotal - $diskon) * Order::TAX_RATE);
        $grandTotal = ($subtotal - $diskon) + $pajak;

        return [
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'pajak' => $pajak,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Buat pesanan baru (ACID): validasi stok, simpan header + detail,
     * kurangi stok. Rollback otomatis jika ada kegagalan.
     * Harga selalu diambil dari DB, bukan dari form.
     */
    public function createOrder(array $data, int $userId): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $items = [];
            foreach ($data['items'] as $row) {
                $product = Product::lockForUpdate()->findOrFail($row['product_id']);

                if ($product->stok < $row['kuantiti']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->nama_produk} tidak mencukupi (tersisa {$product->stok}).",
                    ]);
                }

                $items[] = [
                    'product' => $product,
                    'kuantiti' => (int) $row['kuantiti'],
                    'harga_satuan' => (float) $product->harga_jual,
                    'catatan_khusus' => $row['catatan_khusus'] ?? null,
                ];
            }

            $totals = $this->calculateTotals($items, (float) ($data['diskon_nominal'] ?? 0));

            $metode = $data['metode_bayar'];
            if ($metode === 'tunai') {
                $uangDibayar = (float) $data['uang_dibayar'];
                if ($uangDibayar < $totals['grand_total']) {
                    throw ValidationException::withMessages([
                        'uang_dibayar' => 'Uang dibayar kurang dari total tagihan.',
                    ]);
                }
                $uangKembali = $uangDibayar - $totals['grand_total'];
            } else {
                $uangDibayar = $totals['grand_total'];
                $uangKembali = 0;
            }

            $order = Order::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'user_id' => $userId,
                'tipe_pesanan' => $data['tipe_pesanan'],
                'nomor_meja' => $data['tipe_pesanan'] === 'dine_in' ? ($data['nomor_meja'] ?? null) : null,
                'nama_pemesan' => $data['nama_pemesan'] ?? null,
                'total_subtotal' => $totals['subtotal'],
                'pajak_nominal' => $totals['pajak'],
                'diskon_nominal' => $totals['diskon'],
                'grand_total' => $totals['grand_total'],
                'metode_bayar' => $metode,
                'uang_dibayar' => $uangDibayar,
                'uang_kembali' => $uangKembali,
                'status' => 'diproses',
            ]);

            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'harga_satuan' => $item['harga_satuan'],
                    'kuantiti' => $item['kuantiti'],
                    'subtotal_baris' => $item['harga_satuan'] * $item['kuantiti'],
                    'catatan_khusus' => $item['catatan_khusus'],
                ]);
                $item['product']->decrement('stok', $item['kuantiti']);
            }

            return $order;
        });
    }

    /**
     * Batalkan pesanan: kembalikan stok, ubah status.
     */
    public function cancelOrder(Order $order): Order
    {
        if ($order->status === 'dibatalkan') {
            return $order;
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $detail->product->increment('stok', $detail->kuantiti);
            }
            $order->update(['status' => 'dibatalkan']);

            return $order->fresh();
        });
    }
}
