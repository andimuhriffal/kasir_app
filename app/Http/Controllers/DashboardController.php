<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $kpis = $this->kpisData($today);

        return view('dashboard.index', array_merge($kpis, [
            'lowStockProducts' => $this->lowStockProducts(),
            'chartData' => $this->chartData(),
            'recentOrders' => $this->recentOrders(),
        ]));
    }

    /**
     * JSON endpoint untuk polling realtime seluruh dashboard (setiap 5 detik).
     * Mengembalikan 4 KPI + tren 7 hari + stok menipis + transaksi terbaru,
     * agar seluruh dashboard ikut berubah tanpa refresh saat kasir membuat /
     * membatalkan pesanan.
     */
    public function kpis(): JsonResponse
    {
        $kpis = $this->kpisData(Carbon::today());
        $menuTerlaris = $kpis['menuTerlaris'];

        return response()->json([
            'omset_hari_ini' => (float) $kpis['omsetHariIni'],
            'omset_hari_ini_formatted' => 'Rp ' . number_format($kpis['omsetHariIni'], 0, ',', '.'),
            'jumlah_pesanan' => (int) $kpis['jumlahPesanan'],
            'menu_terlaris' => $menuTerlaris ? [
                'nama' => $menuTerlaris->product->nama_produk ?? '-',
                'total_terjual' => (int) $menuTerlaris->total_terjual,
            ] : null,
            'menu_terlaris_jumlah_formatted' => $menuTerlaris
                ? number_format($menuTerlaris->total_terjual) . ' terjual'
                : 'Belum ada penjualan',
            'low_stock_count' => (int) $kpis['lowStockCount'],
            'chart_7_hari' => $this->chartData(),
            'low_stock' => $this->lowStockProducts()->map(fn ($p) => [
                'nama' => $p->nama_produk,
                'kategori' => $p->category->nama_kategori ?? '-',
                'stok' => (int) $p->stok,
            ])->values()->all(),
            'transaksi_terbaru' => $this->recentOrders()->map(fn ($o) => [
                'id' => (int) $o->id,
                'invoice_number' => $o->invoice_number,
                'created_at' => $o->created_at->format('d/m/Y H:i'),
                'kasir' => $o->user->nama_lengkap ?? '-',
                'grand_total_formatted' => 'Rp ' . number_format($o->grand_total, 0, ',', '.'),
                'status' => $o->status,
                'status_label' => $o->statusLabel(),
                'url' => route('orders.show', $o),
            ])->values()->all(),
        ]);
    }

    /**
     * Hitung 4 KPI dashboard (dipakai halaman index + endpoint JSON).
     * Sesuai panduan MED-WEB-01: Omset & Jumlah Pesanan hanya menghitung
     * transaksi berstatus Selesai (lunas) hari ini, bukan yang masih Diproses.
     */
    private function kpisData(Carbon $today): array
    {
        // KPI 1: Total omset hari ini (hanya pesanan Selesai).
        $omsetHariIni = Order::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->sum('grand_total');

        // KPI 2: Jumlah pesanan hari ini (hanya yang berstatus Selesai).
        $jumlahPesanan = Order::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->count();

        // KPI 3: Menu terlaris (kuantiti terjual terbanyak, semua waktu).
        $menuTerlaris = OrderDetail::select('product_id', DB::raw('SUM(kuantiti) as total_terjual'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->with('product')
            ->first();

        // KPI 4: Peringatan stok rendah (stok <= 10).
        $lowStockCount = Product::where('stok', '<=', 10)->count();

        return compact('omsetHariIni', 'jumlahPesanan', 'menuTerlaris', 'lowStockCount');
    }

    /** Grafik tren 7 hari terakhir (omset per hari, hanya pesanan Selesai). */
    private function chartData(): array
    {
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Order::whereDate('created_at', $date)
                ->where('status', 'selesai')
                ->sum('grand_total');
            $chartData[] = [
                'label' => $date->isoFormat('ddd, D MMM'),
                'total' => (float) $total,
            ];
        }

        return $chartData;
    }

    /** Daftar produk stok rendah (stok <= 10), 5 terendah. */
    private function lowStockProducts()
    {
        return Product::with('category')
            ->where('stok', '<=', 10)
            ->orderBy('stok')
            ->take(5)
            ->get();
    }

    /** Transaksi terbaru (8 terakhir). */
    private function recentOrders()
    {
        return Order::with(['user', 'details.product'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();
    }
}
