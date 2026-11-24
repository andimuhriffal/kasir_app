@extends('layouts.app')

@section('title', 'Buat Pesanan')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Buat Pesanan Baru</h2>
        <p class="text-sm text-stone-500">No. faktur dibuat otomatis oleh sistem saat pesanan disimpan.</p>
    </div>

    @if($products->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center">
            <p class="font-semibold">Belum ada menu.</p>
            <p class="mt-1 text-sm text-stone-500">Tambahkan menu terlebih dahulu sebelum membuat pesanan.</p>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="mt-4 inline-block rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Tambah Menu</a>
            @endif
        </div>
    @else
        @php
            $productData = $products->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->nama_produk,
                'kategori' => $p->category->nama_kategori ?? '-',
                'harga' => (float) $p->harga_jual,
                'stok' => $p->stok,
            ])->values();
        @endphp

        <form action="{{ route('orders.store') }}" method="POST" id="order-form">
            @csrf
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                {{-- Kolom kiri: pesanan --}}
                <div class="space-y-6 xl:col-span-2">
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700">No. Faktur (otomatis)</label>
                                <input type="text" value="{{ $invoicePreview }}" readonly
                                    class="w-full rounded-lg border border-stone-200 bg-stone-50 px-3.5 py-2.5 font-mono text-sm text-stone-500">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700">Tipe Pesanan <span class="text-red-600">*</span></label>
                                <div class="flex gap-2">
                                    <label class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-stone-300 px-3 py-2.5 text-sm font-medium has-checked:border-amber-700 has-checked:bg-amber-50 has-checked:text-amber-800">
                                        <input type="radio" name="tipe_pesanan" value="dine_in" class="accent-amber-700" checked> Dine-In
                                    </label>
                                    <label class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-stone-300 px-3 py-2.5 text-sm font-medium has-checked:border-amber-700 has-checked:bg-amber-50 has-checked:text-amber-800">
                                        <input type="radio" name="tipe_pesanan" value="takeaway" class="accent-amber-700"> Takeaway
                                    </label>
                                </div>
                            </div>
                            <div id="meja-field">
                                <label for="nomor_meja" class="mb-1.5 block text-sm font-medium text-stone-700">Nomor Meja <span class="text-red-600">*</span></label>
                                <input type="text" id="nomor_meja" name="nomor_meja" value="{{ old('nomor_meja') }}" placeholder="contoh: Meja 3"
                                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                            </div>
                            <div>
                                <label for="nama_pemesan" class="mb-1.5 block text-sm font-medium text-stone-700">Nama Pemesan</label>
                                <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan') }}" maxlength="100" placeholder="contoh: Budi Santoso"
                                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                            </div>
                            <div>
                                <label for="diskon_nominal" class="mb-1.5 block text-sm font-medium text-stone-700">Diskon (Rp, opsional)</label>
                                <input type="text" inputmode="numeric" autocomplete="off" id="diskon_nominal" name="diskon_nominal" value="{{ old('diskon_nominal', 0) }}" placeholder="0"
                                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                            </div>
                        </div>
                    </div>

                    {{-- Item dinamis --}}
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold">Item Pesanan</h3>
                            <button type="button" id="add-item" class="rounded-lg border border-stone-300 px-3.5 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">+ Tambah Item</button>
                        </div>
                        <div id="items-container" class="mt-4 space-y-3"></div>
                    </div>
                </div>

                {{-- Kolom kanan: ringkasan + pembayaran --}}
                <div class="space-y-6">
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <h3 class="font-bold">Ringkasan</h3>
                        <dl class="mt-4 space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-stone-500">Subtotal</dt>
                                <dd id="sum-subtotal" class="font-semibold">Rp 0</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-stone-500">Diskon</dt>
                                <dd id="sum-diskon" class="font-semibold">Rp 0</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-stone-500">Pajak PB1 (10%)</dt>
                                <dd id="sum-pajak" class="font-semibold">Rp 0</dd>
                            </div>
                            <div class="flex justify-between border-t border-stone-200 pt-2.5 text-base">
                                <dt class="font-bold">Grand Total</dt>
                                <dd id="sum-grand" class="font-bold">Rp 0</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <h3 class="font-bold">Pembayaran</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="metode_bayar" class="mb-1.5 block text-sm font-medium text-stone-700">Metode Bayar <span class="text-red-600">*</span></label>
                                <select id="metode_bayar" name="metode_bayar" class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                                    <option value="tunai" selected>Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="debit">Kartu Debit</option>
                                </select>
                            </div>
                            <div id="uang-field">
                                <label for="uang_dibayar" class="mb-1.5 block text-sm font-medium text-stone-700">Uang Dibayar (Rp) <span class="text-red-600">*</span></label>
                                <input type="text" inputmode="numeric" autocomplete="off" id="uang_dibayar" name="uang_dibayar" value="{{ old('uang_dibayar') }}" placeholder="contoh: 100.000"
                                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                                <p class="mt-1.5 text-sm">Kembalian: <span id="sum-kembali" class="font-semibold">Rp 0</span></p>
                                <p id="uang-warning" class="mt-1 hidden text-sm font-medium text-red-600">Uang dibayar kurang dari total tagihan.</p>
                            </div>
                        </div>
                        <button type="submit" class="mt-5 w-full rounded-lg bg-amber-700 px-4 py-3 text-sm font-semibold text-white hover:bg-amber-800">
                            Simpan Pesanan
                        </button>
                        <a href="{{ route('orders.index') }}" class="mt-2.5 block text-center text-sm font-medium text-stone-500 hover:underline">Batal</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Template baris item (di-clone oleh JS) --}}
        <template id="item-template">
            <div class="item-row rounded-xl border border-stone-200 bg-stone-50 p-3.5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                    <div class="sm:col-span-5">
                        <label class="mb-1 block text-xs font-medium text-stone-500">Menu</label>
                        <select name="items[__INDEX__][product_id]" class="item-product w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700" required>
                            <option value="">— Pilih menu —</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-harga="{{ $product->harga_jual }}" data-stok="{{ $product->stok }}">
                                    {{ $product->nama_produk }} — Rp {{ number_format($product->harga_jual, 0, ',', '.') }} (stok {{ $product->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-stone-500">Qty</label>
                        <input type="number" name="items[__INDEX__][kuantiti]" value="1" min="1" max="100" required
                            class="item-qty w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-stone-500">Catatan khusus</label>
                        <input type="text" name="items[__INDEX__][catatan_khusus]" maxlength="100" placeholder="mis. Less Sugar"
                            class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-stone-500">Subtotal</label>
                        <div class="flex items-center gap-2">
                            <span class="item-subtotal flex-1 text-sm font-semibold">Rp 0</span>
                            <button type="button" class="remove-item rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <script>
            (function () {
                var products = @json($productData);
                var byId = {};
                products.forEach(function (p) { byId[p.id] = p; });

                var container = document.getElementById('items-container');
                var template = document.getElementById('item-template');
                var index = 0;
                var diskonInput = document.getElementById('diskon_nominal');
                var metodeSelect = document.getElementById('metode_bayar');
                var uangInput = document.getElementById('uang_dibayar');
                var uangField = document.getElementById('uang-field');
                var uangWarning = document.getElementById('uang-warning');
                var mejaField = document.getElementById('meja-field');
                var mejaInput = document.getElementById('nomor_meja');

                function fmt(n) {
                    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
                }

                // Ambil angka dari nilai yang mungkin sudah berformat titik
                // (mis. "100.000" -> 100000). Dipakai recalc() dan saat submit.
                function toNumber(value) {
                    var digits = String(value || '').replace(/\D/g, '');
                    return digits ? parseInt(digits, 10) : 0;
                }

                // Format input nominal (Diskon / Uang Dibayar) dengan pemisah
                // ribuan titik saat mengetik: 100000 -> "100.000".
                function formatRupiahInput(input) {
                    var digits = input.value.replace(/\D/g, '');
                    input.value = digits ? parseInt(digits, 10).toLocaleString('id-ID') : '';
                }

                function addRow() {
                    var clone = template.content.cloneNode(true);
                    var html = clone.firstElementChild.outerHTML.replaceAll('__INDEX__', index);
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    var row = wrapper.firstElementChild;
                    container.appendChild(row);
                    index++;
                    bindRow(row);
                    recalc();
                }

                function bindRow(row) {
                    var productSelect = row.querySelector('.item-product');
                    var qtyInput = row.querySelector('.item-qty');
                    var removeBtn = row.querySelector('.remove-item');
                    productSelect.addEventListener('change', function () {
                        var p = byId[productSelect.value];
                        if (p) qtyInput.max = Math.max(1, p.stok);
                        recalc();
                    });
                    qtyInput.addEventListener('input', recalc);
                    removeBtn.addEventListener('click', function () {
                        if (container.children.length > 1) {
                            row.remove();
                            recalc();
                        }
                    });
                }

                function recalc() {
                    var subtotal = 0;
                    container.querySelectorAll('.item-row').forEach(function (row) {
                        var productSelect = row.querySelector('.item-product');
                        var qtyInput = row.querySelector('.item-qty');
                        var subEl = row.querySelector('.item-subtotal');
                        var p = byId[productSelect.value];
                        var qty = parseInt(qtyInput.value || '0', 10);
                        var line = p ? p.harga * qty : 0;
                        subEl.textContent = fmt(line);
                        subtotal += line;
                    });

                    var diskon = Math.max(0, Math.min(toNumber(diskonInput.value), subtotal));
                    var pajak = Math.round((subtotal - diskon) * 0.10);
                    var grand = (subtotal - diskon) + pajak;

                    document.getElementById('sum-subtotal').textContent = fmt(subtotal);
                    document.getElementById('sum-diskon').textContent = fmt(diskon);
                    document.getElementById('sum-pajak').textContent = fmt(pajak);
                    document.getElementById('sum-grand').textContent = fmt(grand);

                    if (metodeSelect.value === 'tunai') {
                        var dibayar = toNumber(uangInput.value);
                        var kembali = dibayar - grand;
                        document.getElementById('sum-kembali').textContent = fmt(Math.max(0, kembali));
                        uangWarning.classList.toggle('hidden', dibayar >= grand || dibayar === 0);
                    } else {
                        document.getElementById('sum-kembali').textContent = fmt(0);
                        uangWarning.classList.add('hidden');
                    }
                }

                function syncTipe() {
                    var tipe = document.querySelector('input[name="tipe_pesanan"]:checked').value;
                    if (tipe === 'dine_in') {
                        mejaField.classList.remove('hidden');
                        mejaInput.required = true;
                    } else {
                        mejaField.classList.add('hidden');
                        mejaInput.required = false;
                        mejaInput.value = '';
                    }
                }

                function syncMetode() {
                    if (metodeSelect.value === 'tunai') {
                        uangField.classList.remove('hidden');
                        uangInput.required = true;
                    } else {
                        uangField.classList.add('hidden');
                        uangInput.required = false;
                    }
                    recalc();
                }

                document.getElementById('add-item').addEventListener('click', addRow);
                document.querySelectorAll('input[name="tipe_pesanan"]').forEach(function (r) {
                    r.addEventListener('change', syncTipe);
                });
                metodeSelect.addEventListener('change', syncMetode);
                diskonInput.addEventListener('input', function () { formatRupiahInput(diskonInput); recalc(); });
                uangInput.addEventListener('input', function () { formatRupiahInput(uangInput); recalc(); });

                // Sebelum dikirim ke server, kembalikan nilai ke angka polos
                // (tanpa titik) agar validasi numeric dan perhitungan di
                // backend menerima 100000, bukan "100.000".
                document.getElementById('order-form').addEventListener('submit', function () {
                    diskonInput.value = String(toNumber(diskonInput.value));
                    uangInput.value = String(toNumber(uangInput.value));
                });

                // Format nilai awal (mis. dari old() setelah validasi gagal).
                formatRupiahInput(diskonInput);
                formatRupiahInput(uangInput);
                addRow();
                syncTipe();
                syncMetode();
            })();
        </script>
    @endif
@endsection
