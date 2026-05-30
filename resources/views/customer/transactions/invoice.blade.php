<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} - Deposusu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            body { background-color: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-area { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: none !important; }
            
            /* Hide print options dialog if open */
            #print-modal { display: none !important; }

            /* A6 Compact */
            @page { margin: 0; }
            .format-a6 { 
                width: 105mm; min-height: 148mm; padding: 10mm !important; font-size: 0.8em; 
            }
            .format-a6 h2 { font-size: 1.5rem !important; }
            .format-a6 th, .format-a6 td { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }

            /* A5 Detail */
            .format-a5 { 
                width: 148mm; min-height: 210mm; padding: 15mm !important; font-size: 0.9em; 
            }

            /* Thermal 80mm */
            .format-thermal { 
                width: 80mm !important; font-size: 0.7em !important; padding: 5mm !important; padding-right: 15mm !important; 
            }
            .format-thermal h2 { font-size: 1.2rem !important; text-align: center; }
            .format-thermal .grid { display: block !important; }
            .format-thermal .mb-8 { margin-bottom: 1rem !important; }
            .format-thermal table th, .format-thermal table td { padding: 0.2rem 0 !important; }
            .format-thermal .text-right { text-align: right !important; }
            .format-thermal .invoice-header { flex-direction: column !important; align-items: center !important; text-align: center !important; gap: 0.5rem !important; }
        }
        
        /* Modal Backdrop */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased min-h-screen pb-12">

    <!-- Top Action Bar (No Print) -->
    <div class="no-print bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('transactions.index', ['tab' => 'completed']) }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h1 class="font-bold text-gray-800 leading-none">Detail Invoice</h1>
                    <p class="text-xs text-gray-500 mt-1">#{{ $order->invoice ? $order->invoice->invoice_number : $order->order_number }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($order->invoice && $order->invoice->print_count > 0)
                    <div id="print-badge" class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs font-bold text-blue-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Printed &bull; <span id="print-count">{{ $order->invoice->print_count }}</span>x
                    </div>
                @endif
                <button onclick="openPrintModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Invoice
                </button>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="mt-8 px-4 flex flex-col md:flex-row gap-8 max-w-6xl mx-auto justify-center items-start">
        
        <!-- Live Preview -->
        <div class="w-full">
            <p class="no-print text-center text-sm font-bold text-gray-400 mb-4 uppercase tracking-widest">Live Preview</p>
            <div id="invoice-container" class="print-area format-a6 bg-white p-8 rounded-xl shadow-lg mx-auto transition-all duration-300 origin-top">
                
                <!-- Header -->
                <div class="invoice-header flex justify-between items-start gap-6 border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center gap-3">
                        <div id="el-logo" class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black text-xl flex-shrink-0">
                            D
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-blue-600 tracking-tight leading-none">Deposusu</h2>
                            <p class="text-xs text-gray-500 mt-1">Invoice Resmi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h3 class="text-lg font-bold text-gray-800 mb-0.5">INVOICE</h3>
                        <p class="text-xs font-mono text-gray-600 mb-0.5">#{{ $order->invoice ? $order->invoice->invoice_number : $order->order_number }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid grid-cols-2 gap-8 mb-6 text-sm">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Diterbitkan Oleh</p>
                        <p class="font-bold text-gray-800">Deposusu Official</p>
                        <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">
                            {{ $order->warehouse ? $order->warehouse->name : 'Gudang Utama' }}<br>
                            Jakarta, Indonesia
                        </p>
                    </div>
                    <div id="el-address">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Ditujukan Kepada</p>
                        <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="py-2 px-1 text-xs font-bold text-gray-700">Produk</th>
                                <th class="py-2 px-1 text-xs font-bold text-gray-700 text-center">Qty</th>
                                <th class="py-2 px-1 text-xs font-bold text-gray-700 text-right hidden sm:table-cell">Harga</th>
                                <th class="py-2 px-1 text-xs font-bold text-gray-700 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-3 px-1">
                                    <p class="text-xs font-bold text-gray-800">{{ $item->product ? $item->product->name : 'Produk Terhapus' }}</p>
                                    <p class="text-[10px] text-gray-500 sm:hidden">@Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </td>
                                <td class="py-3 px-1 text-xs text-gray-600 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 px-1 text-xs text-gray-600 text-right hidden sm:table-cell">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 px-1 text-xs font-bold text-gray-800 text-right">Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="flex justify-end mb-8">
                    <div class="w-full sm:w-2/3 md:w-1/2">
                        <div class="flex justify-between py-1.5 text-xs">
                            <span class="text-gray-500">Subtotal Produk</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 text-xs">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-bold text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between py-2.5 border-t border-gray-200 mt-1">
                            <span class="text-sm font-bold text-gray-900">Total Pembayaran</span>
                            <span class="text-base font-black text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer & QR -->
                <div class="flex justify-between items-end border-t border-gray-200 pt-6">
                    <div>
                        <p class="text-xs font-bold text-gray-800 mb-0.5">Metode Pembayaran</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(strtolower($order->payment_status)) }} - Lunas</p>
                        <p class="text-[10px] text-gray-400 mt-4">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
                    </div>
                    <div id="el-qr" class="text-right flex flex-col items-end">
                        <div id="qrcode" class="mb-1 border p-1 rounded bg-white"></div>
                        <p class="text-[9px] text-gray-400">Scan untuk validasi</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Print Config Modal -->
    <div id="print-modal" class="no-print fixed inset-0 z-50 modal-backdrop hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-200" id="print-modal-content">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Pilih Format Cetak</h3>
                <button onclick="closePrintModal()" class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <!-- Size Options -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Ukuran Kertas</label>
                    <div class="space-y-3">
                        <!-- A6 -->
                        <label class="flex items-center justify-between p-3 border-2 border-blue-500 bg-blue-50 rounded-xl cursor-pointer" id="lbl-a6" onclick="selectFormat('format-a6')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="format" value="format-a6" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">A6 Compact <span class="text-blue-600 text-[10px] ml-1 px-1.5 py-0.5 bg-blue-100 rounded">Rekomendasi</span></span>
                                    <span class="block text-xs text-gray-500">105×148mm • Hemat kertas</span>
                                </div>
                            </div>
                        </label>
                        <!-- A5 -->
                        <label class="flex items-center justify-between p-3 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-gray-300" id="lbl-a5" onclick="selectFormat('format-a5')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="format" value="format-a5" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">A5 Detail</span>
                                    <span class="block text-xs text-gray-500">148×210mm • Untuk item banyak</span>
                                </div>
                            </div>
                        </label>
                        <!-- Thermal -->
                        <label class="flex items-center justify-between p-3 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-gray-300" id="lbl-thermal" onclick="selectFormat('format-thermal')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="format" value="format-thermal" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Thermal Receipt</span>
                                    <span class="block text-xs text-gray-500">80mm • Printer kasir toko</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Toggles -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Tampilkan Elemen</label>
                    <div class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm font-medium text-gray-700">Logo Toko</span>
                            <input type="checkbox" id="toggle-logo" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="updatePreview()">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm font-medium text-gray-700">Alamat Customer Lengkap</span>
                            <input type="checkbox" id="toggle-address" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="updatePreview()">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm font-medium text-gray-700">QR Code Validasi</span>
                            <input type="checkbox" id="toggle-qr" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="updatePreview()">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row gap-3">
                <button onclick="executePrint()" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print Sekarang
                </button>
                <button onclick="executeDownloadPDF()" class="flex-1 py-3 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-blue-600 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        const itemCount = {{ $order->items->count() }};
        let currentFormat = itemCount > 5 ? 'format-a5' : 'format-a6';
        let isModalOpen = false;

        // Init QR Code
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ route('transactions.invoice', $order->id) }}",
            width: 64,
            height: 64,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.L
        });

        function selectFormat(format) {
            currentFormat = format;
            
            // Update UI Radio & Borders
            ['format-a6', 'format-a5', 'format-thermal'].forEach(f => {
                const lbl = document.getElementById('lbl-' + f.split('-')[1]);
                const radio = lbl.querySelector('input[type="radio"]');
                if(f === format) {
                    lbl.classList.remove('border-gray-100');
                    lbl.classList.add('border-blue-500', 'bg-blue-50');
                    radio.checked = true;
                } else {
                    lbl.classList.remove('border-blue-500', 'bg-blue-50');
                    lbl.classList.add('border-gray-100');
                    radio.checked = false;
                }
            });

            updatePreview();
        }

        function updatePreview() {
            const container = document.getElementById('invoice-container');
            container.classList.remove('format-a6', 'format-a5', 'format-thermal');
            container.classList.add(currentFormat);

            // Toggles
            document.getElementById('el-logo').style.display = document.getElementById('toggle-logo').checked ? 'flex' : 'none';
            document.getElementById('el-address').style.display = document.getElementById('toggle-address').checked ? 'block' : 'none';
            document.getElementById('el-qr').style.display = document.getElementById('toggle-qr').checked ? 'flex' : 'none';
        }

        function openPrintModal() {
            const modal = document.getElementById('print-modal');
            const content = document.getElementById('print-modal-content');
            
            // Smart Recommendation Setup
            if(itemCount > 5) {
                selectFormat('format-a5');
                document.querySelector('#lbl-a5 span.text-blue-600')?.remove();
                document.querySelector('#lbl-a6 span.text-blue-600')?.remove();
                document.querySelector('#lbl-a5 div > span').innerHTML = `A5 Detail <span class="text-blue-600 text-[10px] ml-1 px-1.5 py-0.5 bg-blue-100 rounded">Rekomendasi</span>`;
            } else {
                selectFormat('format-a6');
                document.querySelector('#lbl-a5 span.text-blue-600')?.remove();
                document.querySelector('#lbl-a6 span.text-blue-600')?.remove();
                document.querySelector('#lbl-a6 div > span').innerHTML = `A6 Compact <span class="text-blue-600 text-[10px] ml-1 px-1.5 py-0.5 bg-blue-100 rounded">Rekomendasi</span>`;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            isModalOpen = true;
        }

        function closePrintModal() {
            const modal = document.getElementById('print-modal');
            const content = document.getElementById('print-modal-content');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
            isModalOpen = false;
        }

        async function trackPrint() {
            try {
                const response = await fetch("{{ route('transactions.invoice.logPrint', $order->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                if(data.success) {
                    // Update DOM badge
                    let badge = document.getElementById('print-badge');
                    if(badge) {
                        document.getElementById('print-count').innerText = data.print_count;
                    } else {
                        // Create badge if not exist
                        badge = document.createElement('div');
                        badge.id = 'print-badge';
                        badge.className = 'hidden sm:flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs font-bold text-blue-700';
                        badge.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg> Printed &bull; <span id="print-count">${data.print_count}</span>x`;
                        document.querySelector('.flex.items-center.gap-3:last-child').prepend(badge);
                    }
                }
            } catch (error) {
                console.error("Failed to track print:", error);
            }
        }

        async function executePrint() {
            await trackPrint();
            closePrintModal();
            setTimeout(() => {
                window.print();
            }, 300);
        }

        async function executeDownloadPDF() {
            await trackPrint();
            closePrintModal();
            // Since we don't have a backend PDF generator installed (like dompdf),
            // We use window.print() but the UI instructs them to save as PDF.
            // In a real advanced scenario, this would redirect to a PDF download route.
            setTimeout(() => {
                alert("Pilih 'Save as PDF' (Simpan sebagai PDF) pada menu tujuan printer (Destination) di layar berikutnya.");
                window.print();
            }, 300);
        }

        // Close modal on click outside
        window.addEventListener('click', function(e) {
            if (isModalOpen && e.target === document.getElementById('print-modal')) {
                closePrintModal();
            }
        });
    </script>
</body>
</html>
