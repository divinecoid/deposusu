{{--
    Shared Xendit payment-waiting overlay: QR code / VA number / e-wallet
    redirect, plus status polling. Included by both the checkout page and
    the order detail page (for retrying a failed/expired payment).

    Include once per page, then call `window.showXenditPaymentModal(payment)`
    with the `payment` object returned by /checkout/process or
    /transactions/{order}/pay.

    Optional: pass $paymentModalCancelUrl to change where "Batalkan" links to
    (defaults to the cart page).
--}}
<div id="payment-modal" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center">
            <h2 id="pm-title" class="text-lg font-bold text-slate-900 mb-1">Selesaikan Pembayaran</h2>
            <p id="pm-subtitle" class="text-sm text-slate-500 mb-5">Rp 0</p>

            <!-- QRIS -->
            <div id="pm-qris" class="hidden">
                <div class="w-56 h-56 mx-auto rounded-xl border border-slate-100 flex items-center justify-center p-3">
                    <canvas id="pm-qr-canvas"></canvas>
                </div>
                <p class="text-xs text-slate-400 mt-3">Buka aplikasi m-banking atau e-wallet, lalu scan kode QR di atas.</p>
            </div>

            <!-- Virtual Account -->
            <div id="pm-va" class="hidden">
                <p id="pm-va-bank" class="text-xs font-semibold text-slate-400 uppercase tracking-wide"></p>
                <div class="mt-2 flex items-center justify-center gap-2">
                    <span id="pm-va-number" class="text-xl font-mono font-bold text-slate-900 tracking-wide"></span>
                    <button type="button" id="pm-va-copy" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors" aria-label="Salin nomor virtual account">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-3">Transfer via ATM, m-banking, atau internet banking sejumlah nominal di atas.</p>
            </div>

            <!-- E-wallet redirect -->
            <div id="pm-ewallet" class="hidden">
                <div class="py-6">
                    <svg class="w-10 h-10 mx-auto text-brand-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
                <p class="text-sm text-slate-500">Mengarahkan ke aplikasi pembayaran...</p>
                <a id="pm-ewallet-link" href="#" class="mt-3 inline-block text-sm font-semibold text-brand-600 underline">Buka manual jika tidak otomatis</a>
            </div>

            <div class="mt-5 flex items-center justify-center gap-2 text-xs text-slate-400">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span id="pm-status">Menunggu pembayaran...</span>
            </div>

            <a id="pm-cancel" href="{{ $paymentModalCancelUrl ?? route('cart.index') }}" class="mt-4 block text-sm font-semibold text-slate-400 hover:text-slate-600">Batalkan</a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    (function () {
        const rupiah = value => 'Rp ' + Math.round(Number(value) || 0).toLocaleString('id-ID');
        const modal = document.getElementById('payment-modal');
        let pollTimer = null;

        function stopPolling() {
            if (pollTimer) clearInterval(pollTimer);
            pollTimer = null;
        }

        function pollStatus(pollUrl) {
            stopPolling();
            pollTimer = setInterval(async () => {
                try {
                    const response = await fetch(pollUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await response.json();

                    if (data.status === 'SUCCEEDED') {
                        stopPolling();
                        document.getElementById('pm-status').textContent = 'Pembayaran berhasil! Mengarahkan...';
                        window.location.href = data.redirect_url;
                    } else if (['FAILED', 'EXPIRED', 'CANCELED'].includes(data.status)) {
                        stopPolling();
                        modal.classList.add('hidden');
                        showNotification('Pembayaran ' + (data.status === 'EXPIRED' ? 'kedaluwarsa' : 'gagal') + '. Silakan coba lagi.', 'error');
                        window.dispatchEvent(new CustomEvent('xendit-payment-failed'));
                    }
                } catch (error) {
                    console.error('Gagal cek status pembayaran:', error);
                }
            }, 3000);
        }

        window.showXenditPaymentModal = async function (payment) {
            document.getElementById('pm-subtitle').textContent = rupiah(payment.total_amount) + ' — ' + payment.channel_label;
            ['pm-qris', 'pm-va', 'pm-ewallet'].forEach(id => document.getElementById(id).classList.add('hidden'));

            if (payment.group === 'qris' && payment.qr_string) {
                document.getElementById('pm-qris').classList.remove('hidden');
                await QRCode.toCanvas(document.getElementById('pm-qr-canvas'), payment.qr_string, { width: 208, margin: 1 });
            } else if (payment.group === 'va' && payment.virtual_account_number) {
                document.getElementById('pm-va').classList.remove('hidden');
                document.getElementById('pm-va-bank').textContent = payment.virtual_account_bank || '';
                document.getElementById('pm-va-number').textContent = payment.virtual_account_number;
            } else if (payment.group === 'ewallet' && payment.checkout_url) {
                document.getElementById('pm-ewallet').classList.remove('hidden');
                document.getElementById('pm-ewallet-link').href = payment.checkout_url;
                setTimeout(() => { window.location.href = payment.checkout_url; }, 1200);
            }

            modal.classList.remove('hidden');
            pollStatus(payment.poll_url);
        };

        window.hideXenditPaymentModal = function () {
            stopPolling();
            modal.classList.add('hidden');
        };

        document.getElementById('pm-va-copy')?.addEventListener('click', () => {
            const number = document.getElementById('pm-va-number').textContent;
            navigator.clipboard?.writeText(number);
            showNotification('Nomor VA disalin', 'success');
        });
    })();
</script>
