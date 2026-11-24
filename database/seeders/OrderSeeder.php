<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Pesanan contoh 7 hari terakhir (2 pesanan per hari) agar dashboard
     * (grafik 7 hari, KPI, transaksi terbaru) dan laporan punya data demo.
     * Stok produk ikut berkurang sesuai item yang terjual.
     */
    public function run(): void
    {
        $kasir = User::where('username', 'kasir')->firstOrFail();
        $products = Product::orderBy('id')->get();
        $methods = ['tunai', 'qris', 'transfer', 'debit'];
        $namaPemesanList = ['Budi Santoso', 'Siti Rahma', 'Andi Wijaya', 'Dewi Lestari', 'Rina Marlina', 'Agus Prasetyo', 'Maya Putri', 'Joko Susilo'];
        $seqByDate = [];
        $productIndex = 0;

        for ($dayOffset = 6; $dayOffset >= 0; $dayOffset--) {
            $date = Carbon::today()->subDays($dayOffset);

            for ($orderNo = 0; $orderNo < 2; $orderNo++) {
                $dateKey = $date->format('Ymd');
                $seqByDate[$dateKey] = ($seqByDate[$dateKey] ?? 0) + 1;
                $invoice = sprintf('INV-POS-%s-%04d', $dateKey, $seqByDate[$dateKey]);

                $tipePesanan = ($dayOffset + $orderNo) % 2 === 0 ? 'dine_in' : 'takeaway';
                $nomorMeja = $tipePesanan === 'dine_in'
                    ? 'Meja ' . ((($dayOffset * 2 + $orderNo) % 8) + 1)
                    : null;

                // 2 item per pesanan, dipilih bergiliran agar merata.
                $items = [];
                $subtotal = 0;
                for ($i = 0; $i < 2; $i++) {
                    $product = $products[$productIndex % $products->count()];
                    $productIndex++;
                    $qty = ($i + $orderNo) % 2 + 1;
                    $lineTotal = (float) $product->harga_jual * $qty;
                    $subtotal += $lineTotal;
                    $items[] = [
                        'product' => $product,
                        'qty' => $qty,
                        'line_total' => $lineTotal,
                    ];
                }

                $diskon = ($orderNo === 1 && $dayOffset % 2 === 0) ? 5000 : 0;
                $pajak = round(($subtotal - $diskon) * Order::TAX_RATE);
                $grandTotal = ($subtotal - $diskon) + $pajak;
                $method = $methods[($dayOffset + $orderNo) % count($methods)];
                $uangDibayar = $method === 'tunai' ? $grandTotal + 10000 : $grandTotal;
                $uangKembali = $method === 'tunai' ? $uangDibayar - $grandTotal : 0;
                $status = ($dayOffset === 0 && $orderNo === 1) ? 'diproses' : 'selesai';
                $namaPemesan = $namaPemesanList[(($dayOffset * 2 + $orderNo) % count($namaPemesanList))];
                $createdAt = $date->copy()->setHour(9 + $orderNo * 4)->setMinute(15 + $orderNo * 10);

                DB::transaction(function () use ($kasir, $invoice, $tipePesanan, $nomorMeja, $namaPemesan, $subtotal, $pajak, $diskon, $grandTotal, $method, $uangDibayar, $uangKembali, $status, $createdAt, $items) {
                    $order = Order::create([
                        'invoice_number' => $invoice,
                        'user_id' => $kasir->id,
                        'tipe_pesanan' => $tipePesanan,
                        'nomor_meja' => $nomorMeja,
                        'nama_pemesan' => $namaPemesan,
                        'total_subtotal' => $subtotal,
                        'pajak_nominal' => $pajak,
                        'diskon_nominal' => $diskon,
                        'grand_total' => $grandTotal,
                        'metode_bayar' => $method,
                        'uang_dibayar' => $uangDibayar,
                        'uang_kembali' => $uangKembali,
                        'status' => $status,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    foreach ($items as $item) {
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product']->id,
                            'harga_satuan' => $item['product']->harga_jual,
                            'kuantiti' => $item['qty'],
                            'subtotal_baris' => $item['line_total'],
                            'catatan_khusus' => null,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                        $item['product']->decrement('stok', $item['qty']);
                    }
                });
            }
        }
    }
}
