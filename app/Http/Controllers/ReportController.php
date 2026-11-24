<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request);
        $summary = $this->summaryFor($query);
        $orders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('reports.index', compact('summary', 'orders', 'categories'));
    }

    public function exportPdf(Request $request)
    {
        $query = $this->filteredQuery($request);
        $orders = $query->orderBy('created_at')->with(['user', 'details'])->get();
        $summary = $this->summaryFor($query);

        $pdf = Pdf::loadView('reports.pdf', [
            'orders' => $orders,
            'summary' => $summary,
            'filters' => $request->only(['dari', 'sampai', 'category_id', 'metode_bayar']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penjualan-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $this->filteredQuery($request);
        $orders = $query->orderBy('created_at')->with(['user', 'details.product'])->get();
        $filename = 'laporan-penjualan-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['No Faktur', 'Tanggal & Waktu', 'Kasir', 'Tipe', 'Item Terjual', 'Subtotal', 'Pajak', 'Grand Total', 'Status'], ';');
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->invoice_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->nama_lengkap ?? '-',
                    $order->typeLabel(),
                    $order->details->sum('kuantiti'),
                    $order->total_subtotal,
                    $order->pajak_nominal,
                    $order->grand_total,
                    $order->statusLabel(),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Laporan Laba Rugi: pendapatan vs HPP dari pesanan SELESAI saja
     * (pesanan 'diproses' belum diakui sebagai pendapatan).
     * HPP memakai harga_pokok produk saat ini, karena order_details
     * tidak menyimpan harga pokok saat transaksi.
     */
    public function labaRugi(Request $request): View
    {
        $data = $this->labaRugiData($request);

        return view('reports.laba-rugi', [
            'summary' => $data['summary'],
            'breakdown' => $data['breakdown'],
        ]);
    }

    public function labaRugiPdf(Request $request)
    {
        $data = $this->labaRugiData($request);

        $pdf = Pdf::loadView('reports.laba-rugi-pdf', [
            'summary' => $data['summary'],
            'breakdown' => $data['breakdown'],
            'filters' => $request->only(['dari', 'sampai']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-laba-rugi-' . now()->format('Ymd-His') . '.pdf');
    }

    public function labaRugiCsv(Request $request): StreamedResponse
    {
        $data = $this->labaRugiData($request);
        $filename = 'laporan-laba-rugi-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Produk', 'Kategori', 'Qty Terjual', 'Penjualan', 'HPP', 'Laba', 'Margin %'], ';');
            foreach ($data['breakdown'] as $row) {
                fputcsv($handle, [
                    $row->nama_produk,
                    $row->nama_kategori,
                    $row->qty,
                    $row->penjualan,
                    $row->hpp,
                    $row->laba,
                    round($row->margin, 2),
                ], ';');
            }
            // Baris total.
            fputcsv($handle, [
                'TOTAL',
                '',
                $data['breakdown']->sum('qty'),
                $data['summary']['penjualan_kotor'],
                $data['summary']['total_hpp'],
                $data['summary']['laba_kotor'],
                round($data['summary']['margin'], 2),
            ], ';');
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Hitung ringkasan + rincian per produk untuk laporan laba rugi.
     * Hanya pesanan berstatus 'selesai' dalam rentang tanggal yang dihitung.
     */
    protected function labaRugiData(Request $request): array
    {
        $orders = Order::query()->where('status', 'selesai');
        if ($request->filled('dari')) {
            $orders->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $orders->whereDate('created_at', '<=', $request->sampai);
        }

        $penjualanKotor = (float) (clone $orders)->sum('total_subtotal');
        $totalDiskon = (float) (clone $orders)->sum('diskon_nominal');
        $totalPajak = (float) (clone $orders)->sum('pajak_nominal');
        $jumlahTransaksi = (clone $orders)->count();

        // HPP = jumlah (kuantiti x harga_pokok produk saat ini) dari
        // semua detail pesanan yang sudah selesai dalam rentang tanggal.
        $detailQuery = \App\Models\OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 'selesai');
        if ($request->filled('dari')) {
            $detailQuery->whereDate('orders.created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $detailQuery->whereDate('orders.created_at', '<=', $request->sampai);
        }
        $totalHpp = (float) (clone $detailQuery)->sum(\DB::raw('order_details.kuantiti * products.harga_pokok'));

        $pendapatanBersih = $penjualanKotor - $totalDiskon;
        $labaKotor = $pendapatanBersih - $totalHpp;
        $margin = $pendapatanBersih > 0 ? ($labaKotor / $pendapatanBersih) * 100 : 0;

        // Rincian per produk, diurutkan dari laba terbesar.
        // Laba per produk dihitung sebelum alokasi diskon (diskon adalah
        // per pesanan, bukan per item), jadi jumlah laba per produk =
        // Laba Kotor + Total Diskon.
        $breakdown = (clone $detailQuery)
            ->select('products.nama_produk', 'categories.nama_kategori')
            ->selectRaw('SUM(order_details.kuantiti) as qty')
            ->selectRaw('SUM(order_details.subtotal_baris) as penjualan')
            ->selectRaw('SUM(order_details.kuantiti * products.harga_pokok) as hpp')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('products.id', 'products.nama_produk', 'categories.nama_kategori')
            ->orderByRaw('SUM(order_details.subtotal_baris) - SUM(order_details.kuantiti * products.harga_pokok) DESC')
            ->get()
            ->map(function ($row) {
                $row->laba = (float) $row->penjualan - (float) $row->hpp;
                $row->margin = (float) $row->penjualan > 0
                    ? ($row->laba / (float) $row->penjualan) * 100
                    : 0;
                return $row;
            });

        return [
            'summary' => [
                'penjualan_kotor' => $penjualanKotor,
                'total_diskon' => $totalDiskon,
                'pendapatan_bersih' => $pendapatanBersih,
                'total_hpp' => $totalHpp,
                'laba_kotor' => $labaKotor,
                'margin' => $margin,
                'total_pajak' => $totalPajak,
                'jumlah_transaksi' => $jumlahTransaksi,
            ],
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Query laporan dengan filter: rentang tanggal, kategori menu, metode bayar.
     * Hanya pesanan yang tidak dibatalkan yang masuk laporan.
     */
    protected function filteredQuery(Request $request)
    {
        $query = Order::query()->where('status', '!=', 'dibatalkan');

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('category_id')) {
            $query->whereHas('details.product', fn ($q) => $q->where('category_id', $request->category_id));
        }

        return $query;
    }

    protected function summaryFor($query): array
    {
        $omsetBruto = (float) (clone $query)->sum('grand_total');
        $totalDiskon = (float) (clone $query)->sum('diskon_nominal');
        $totalPajak = (float) (clone $query)->sum('pajak_nominal');
        $jumlah = (clone $query)->count();

        return [
            'omset_bruto' => $omsetBruto,
            'total_diskon' => $totalDiskon,
            'total_pajak' => $totalPajak,
            // Omset Bersih = Bruto − Pajak (pendapatan yang disimpan kafe).
            // Diskon TIDAK dikurangkan lagi di sini karena grand_total sudah
            // mengurangkan diskon (grand = subtotal − diskon + pajak).
            'omset_bersih' => $omsetBruto - $totalPajak,
            'jumlah_transaksi' => $jumlah,
            'rata_rata' => $jumlah > 0 ? $omsetBruto / $jumlah : 0,
        ];
    }
}
