/**
 * realtime.js — Semua JavaScript interaktif layout aplikasi.
 *
 * Dipisah dari resources/views/layouts/app.blade.php agar layout
 * hanya berisi markup, dan logika JS tinggal di folder resources/js
 * (lebih modular dan mudah dirawat).
 *
 * Isi file ini (3 bagian):
 *   1. Sidebar — buka/tutup sidebar di layar kecil (mobile).
 *   2. Status pesanan — polling /orders/statuses setiap 5 detik,
 *      memperbarui badge [data-order-id] tanpa refresh.
 *   3. Dashboard — polling /dashboard/kpis setiap 5 detik,
 *      memperbarui KPI, grafik 7 hari, stok menipis, dan transaksi
 *      terbaru tanpa refresh (DOM disentuh hanya bila data berubah).
 *
 * URL endpoint dibaca dari atribut data-* pada <body> di app.blade.php
 * (data-status-url dan data-kpi-url), karena file .js tidak bisa
 * memakai helper Blade route(). Ada fallback ke path literal bila
 * atribut tidak ditemukan.
 */

// ---------------------------------------------------------------------------
// 1. Sidebar (mobile)
// ---------------------------------------------------------------------------
(function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var toggle = document.getElementById('sidebar-toggle');
    var closeBtn = document.getElementById('sidebar-close');
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
    if (toggle) toggle.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
})();

// ---------------------------------------------------------------------------
// 2. Polling status pesanan realtime: setiap 5 detik, ambil status terbaru
//    dari /orders/statuses untuk semua badge [data-order-id] di halaman ini.
//    Jadi kalau kasir membatalkan pesanan, badge di layar admin/manajer
//    berubah menjadi "Dibatalkan" tanpa refresh manual.
// ---------------------------------------------------------------------------
(function () {
    var STATUS_URL = document.body.dataset.statusUrl || '/orders/statuses';
    var STATUS_META = {
        diproses: { label: 'Diproses', classes: ['bg-amber-50', 'text-amber-700'] },
        selesai: { label: 'Selesai', classes: ['bg-green-50', 'text-green-700'] },
        dibatalkan: { label: 'Dibatalkan', classes: ['bg-red-50', 'text-red-700'] },
    };
    var COLOR_CLASSES = ['bg-amber-50', 'text-amber-700', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700'];

    function setBadge(el, status) {
        var meta = STATUS_META[status];
        if (!meta) return;
        COLOR_CLASSES.forEach(function (c) { el.classList.remove(c); });
        meta.classes.forEach(function (c) { el.classList.add(c); });
        el.textContent = meta.label;
        el.dataset.status = status;
    }

    function collectIds() {
        var ids = [];
        document.querySelectorAll('[data-order-id]').forEach(function (el) {
            var id = parseInt(el.dataset.orderId, 10);
            if (id && ids.indexOf(id) === -1) ids.push(id);
        });
        return ids;
    }

    function poll() {
        if (document.hidden) return;
        var ids = collectIds();
        if (!ids.length) return;
        fetch(STATUS_URL + '?ids=' + ids.join(','), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                data.forEach(function (item) {
                    document.querySelectorAll('[data-order-id="' + item.id + '"]').forEach(function (el) {
                        var oldStatus = el.dataset.status;
                        if (oldStatus && oldStatus !== item.status) {
                            setBadge(el, item.status);
                            document.dispatchEvent(new CustomEvent('order-status-changed', {
                                detail: { id: item.id, oldStatus: oldStatus, newStatus: item.status, label: item.status_label },
                            }));
                        }
                    });
                });
            })
            .catch(function () { /* abaikan, coba lagi di interval berikutnya */ });
    }

    // Aktif hanya di halaman yang menampilkan status pesanan.
    if (document.querySelectorAll('[data-order-id]').length) {
        setInterval(poll, 5000);
    }
})();

// ---------------------------------------------------------------------------
// 3. Polling realtime seluruh dashboard: setiap 5 detik, ambil KPI,
//    grafik 7 hari, stok menipis, dan transaksi terbaru dari /dashboard/kpis
//    lalu perbarui halaman tanpa refresh manual. DOM hanya disentuh bila
//    datanya benar-benar berubah (tanda tangan JSON), agar tidak berkedip
//    dan tidak mengganggu klik di tabel transaksi.
// ---------------------------------------------------------------------------
(function () {
    var KPI_URL = document.body.dataset.kpiUrl || '/dashboard/kpis';
    var lastChartSig = '';
    var lastStockSig = '';
    var lastOrdersSig = '';

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function renderChart(days) {
        var container = document.getElementById('chart-7-hari');
        if (!container || !days) return;
        var max = 1;
        days.forEach(function (d) { if (d.total > max) max = d.total; });
        var html = '';
        days.forEach(function (d) {
            var height = Math.max(4, Math.round(d.total / max * 100));
            var exact = 'Rp ' + Math.round(d.total).toLocaleString('id-ID');
            var rounded = 'Rp ' + Math.round(d.total / 1000) + 'rb';
            html += '<div class="group flex h-full flex-1 cursor-pointer flex-col items-center justify-end gap-2">'
                + '<span class="text-[11px] font-semibold text-stone-600 transition-colors duration-200 group-hover:text-amber-800">'
                + '<span class="group-hover:hidden">' + rounded + '</span>'
                + '<span class="hidden whitespace-nowrap group-hover:inline">' + exact + '</span>'
                + '</span>'
                + '<div class="trend-chart-bar w-full max-w-10 origin-bottom rounded-t-lg bg-amber-700/90 transition-all duration-300 ease-out group-hover:scale-y-105 group-hover:bg-amber-600 group-hover:shadow-md" style="--bar-height: ' + height + '%;"></div>'
                + '<span class="text-center text-[11px] leading-tight text-stone-500 transition-colors duration-200 group-hover:font-semibold group-hover:text-stone-800">' + escapeHtml(d.label) + '</span>'
                + '</div>';
        });
        container.innerHTML = html;
    }

    function renderLowStock(items) {
        var container = document.getElementById('low-stock-list');
        if (!container || !items) return;
        if (!items.length) {
            container.innerHTML = '<p class="mt-3 text-sm text-stone-500">Semua stok aman.</p>';
            return;
        }
        var html = '<ul class="mt-3 divide-y divide-stone-100">';
        items.forEach(function (p) {
            html += '<li class="flex items-center justify-between py-2.5 text-sm">'
                + '<span><span class="block font-medium">' + escapeHtml(p.nama) + '</span>'
                + '<span class="text-xs text-stone-400">' + escapeHtml(p.kategori) + '</span></span>'
                + '<span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Sisa ' + p.stok + '</span>'
                + '</li>';
        });
        html += '</ul>';
        container.innerHTML = html;
    }

    function renderRecentOrders(orders) {
        var tbody = document.getElementById('recent-orders-body');
        if (!tbody || !orders) return;
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="py-2.5 text-sm text-stone-500">Belum ada transaksi.</td></tr>';
            return;
        }
        var html = '';
        orders.forEach(function (o) {
            var badgeClass = o.status === 'selesai' ? 'bg-green-50 text-green-700'
                : o.status === 'dibatalkan' ? 'bg-red-50 text-red-700'
                : 'bg-amber-50 text-amber-700';
            html += '<tr>'
                + '<td class="py-2.5 pr-3">'
                + '<a href="' + escapeHtml(o.url) + '" class="font-mono text-xs font-semibold text-amber-700 hover:underline">' + escapeHtml(o.invoice_number) + '</a>'
                + '<span class="block text-xs text-stone-400">' + escapeHtml(o.created_at) + '</span>'
                + '</td>'
                + '<td class="py-2.5 pr-3">' + escapeHtml(o.kasir) + '</td>'
                + '<td class="py-2.5 pr-3 font-semibold">' + escapeHtml(o.grand_total_formatted) + '</td>'
                + '<td class="py-2.5"><span data-order-id="' + o.id + '" data-status="' + escapeHtml(o.status) + '" class="rounded-full px-2.5 py-1 text-xs font-semibold ' + badgeClass + '">' + escapeHtml(o.status_label) + '</span></td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function updateKpis(data) {
        var omset = document.querySelector('[data-kpi="omset"]');
        if (omset) omset.textContent = data.omset_hari_ini_formatted;

        var jumlah = document.querySelector('[data-kpi="jumlah-pesanan"]');
        if (jumlah) jumlah.textContent = data.jumlah_pesanan;

        var nama = document.querySelector('[data-kpi="menu-terlaris-nama"]');
        if (nama) {
            nama.textContent = data.menu_terlaris ? data.menu_terlaris.nama : '—';
            nama.classList.toggle('text-stone-300', !data.menu_terlaris);
        }

        var jumlahMenu = document.querySelector('[data-kpi="menu-terlaris-jumlah"]');
        if (jumlahMenu) jumlahMenu.textContent = data.menu_terlaris_jumlah_formatted;

        var stok = document.querySelector('[data-kpi="stok-rendah"]');
        if (stok) {
            stok.textContent = data.low_stock_count;
            // Warna merah pada kartu saat ada stok rendah.
            if (stok.parentElement) {
                stok.parentElement.classList.toggle('text-red-600', data.low_stock_count > 0);
            }
        }

        // Grafik, stok menipis, dan transaksi terbaru: render ulang
        // hanya bila datanya berubah dari polling sebelumnya.
        var chartSig = JSON.stringify(data.chart_7_hari || null);
        if (chartSig !== lastChartSig) { lastChartSig = chartSig; renderChart(data.chart_7_hari); }
        var stockSig = JSON.stringify(data.low_stock || null);
        if (stockSig !== lastStockSig) { lastStockSig = stockSig; renderLowStock(data.low_stock); }
        var ordersSig = JSON.stringify(data.transaksi_terbaru || null);
        if (ordersSig !== lastOrdersSig) { lastOrdersSig = ordersSig; renderRecentOrders(data.transaksi_terbaru); }
    }

    function pollKpis() {
        if (document.hidden) return;
        fetch(KPI_URL, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(updateKpis)
            .catch(function () { /* abaikan, coba lagi di interval berikutnya */ });
    }

    // Aktif hanya di halaman dashboard (yang menampilkan kartu KPI).
    if (document.querySelectorAll('[data-kpi]').length) {
        setInterval(pollKpis, 5000);
    }
})();
