<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'details.product'])->orderByDesc('created_at');

        // Kasir hanya melihat pesanannya sendiri; admin melihat semua.
        if ($request->user()->isKasir()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where('invoice_number', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $products = Product::with('category')->orderBy('nama_produk')->get();
        $invoicePreview = $this->orderService->generateInvoiceNumber();

        return view('orders.create', compact('products', 'invoicePreview'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->createOrder($request->validated(), $request->user()->id);

        return redirect()->route('orders.show', $order)
            ->with('success', "Pesanan {$order->invoice_number} berhasil dibuat.");
    }

    public function show(Order $order): View
    {
        // Kasir hanya boleh melihat pesanannya sendiri.
        if (auth()->user()->isKasir() && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['user', 'details.product.category']);

        return view('orders.show', compact('order'));
    }

    /**
     * JSON endpoint untuk polling realtime: kembalikan status terbaru dari
     * daftar pesanan berdasarkan id (?ids=1,2,3). Dipakai agar halaman
     * admin/manajer memperbarui badge status tanpa refresh manual saat
     * kasir membatalkan/menyelesaikan pesanan.
     */
    public function statuses(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(100)
            ->values();

        $query = Order::whereIn('id', $ids);

        // Kasir hanya boleh melihat status pesanannya sendiri.
        if ($request->user()->isKasir()) {
            $query->where('user_id', $request->user()->id);
        }

        $orders = $query->get(['id', 'invoice_number', 'status', 'updated_at']);

        return response()->json($orders->map(fn (Order $order) => [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
            'updated_at' => $order->updated_at->toIso8601String(),
        ]));
    }

    public function complete(Order $order): RedirectResponse
    {
        if ($order->status !== 'diproses') {
            return back()->with('error', 'Hanya pesanan berstatus Diproses yang bisa diselesaikan.');
        }

        $order->update(['status' => 'selesai']);

        return back()->with('success', "Pesanan {$order->invoice_number} ditandai Selesai.");
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === 'dibatalkan') {
            return back()->with('error', 'Pesanan sudah dibatalkan.');
        }
        if ($order->status === 'selesai') {
            return back()->with('error', 'Pesanan yang sudah Selesai tidak bisa dibatalkan.');
        }

        $this->orderService->cancelOrder($order);

        return back()->with('success', "Pesanan {$order->invoice_number} dibatalkan dan stok dikembalikan.");
    }

    public function receipt(Order $order): View
    {
        if (auth()->user()->isKasir() && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['user', 'details.product.category']);

        return view('orders.receipt', compact('order'));
    }
}
