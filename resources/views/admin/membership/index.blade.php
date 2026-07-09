@extends('layouts.admin')

@section('header', 'Membership & Loyalty VIP')

@section('content')
<div class="space-y-6">
    <!-- Membership Tier Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Bronze -->
        <div class="bg-white border-t-4 border-amber-700 rounded-2xl shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-amber-900 text-lg">BRONZE</h3>
                <span class="text-xs bg-amber-50 text-amber-800 px-2 py-0.5 rounded-full font-bold">Level 1</span>
            </div>
            <p class="text-xs text-slate-500">Tier awal pendaftaran customer baru Deposusu.</p>
            <div class="pt-3 border-t border-slate-100 text-xs space-y-1.5 text-slate-600">
                <p>✔️ Cashback 1% poin belanja</p>
                <p>✔️ Promo diskon hari ulang tahun</p>
                <p>❌ Free Delivery order</p>
            </div>
        </div>

        <!-- Silver -->
        <div class="bg-white border-t-4 border-slate-400 rounded-2xl shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-slate-700 text-lg">SILVER</h3>
                <span class="text-xs bg-slate-50 text-slate-600 px-2 py-0.5 rounded-full font-bold">Level 2</span>
            </div>
            <p class="text-xs text-slate-500">Minimal akumulasi belanja Rp 1.000.000 / bulan.</p>
            <div class="pt-3 border-t border-slate-100 text-xs space-y-1.5 text-slate-600">
                <p>✔️ Cashback 2% poin belanja</p>
                <p>✔️ Promo diskon hari ulang tahun</p>
                <p>✔️ Voucher free delivery 2x sebulan</p>
            </div>
        </div>

        <!-- Gold -->
        <div class="bg-white border-t-4 border-yellow-500 rounded-2xl shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-yellow-600 text-lg">GOLD</h3>
                <span class="text-xs bg-yellow-50 text-yellow-800 px-2 py-0.5 rounded-full font-bold">Level 3</span>
            </div>
            <p class="text-xs text-slate-500">Minimal akumulasi belanja Rp 5.000.000 / bulan.</p>
            <div class="pt-3 border-t border-slate-100 text-xs space-y-1.5 text-slate-600">
                <p>✔️ Cashback 5% poin belanja</p>
                <p>✔️ Akses awal produk susu baru</p>
                <p>✔️ Voucher free delivery 5x sebulan</p>
                <p>✔️ Layanan CS Fast-track Priority</p>
            </div>
        </div>

        <!-- VIP -->
        <div class="bg-white border-t-4 border-purple-600 rounded-2xl shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-purple-700 text-lg font-mono">VIP</h3>
                <span class="text-xs bg-purple-50 text-purple-800 px-2 py-0.5 rounded-full font-bold">Level 4</span>
            </div>
            <p class="text-xs text-slate-500">Minimal akumulasi belanja Rp 10.000.000 / bulan.</p>
            <div class="pt-3 border-t border-slate-100 text-xs space-y-1.5 text-slate-600">
                <p>✔️ Cashback 10% poin belanja</p>
                <p>✔️ Unlimited Free Delivery Gudang</p>
                <p>✔️ Undangan Event Deposusu Partners</p>
                <p>✔️ Personal Relationship Manager</p>
            </div>
        </div>
    </div>

    <!-- Customer tier lists -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Daftar Membership Aktif</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Nama Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Tier Membership</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Total Belanja (Bulan Ini)</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider text-right">Loyalty Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach(\App\Models\User::where('role', 'customer')->with('customerProfile')->get() as $customer)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $customer->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    @if($customer->customerProfile && $customer->customerProfile->membership === 'Gold') bg-yellow-50 text-yellow-700 border border-yellow-200
                                    @elseif($customer->customerProfile && $customer->customerProfile->membership === 'VIP') bg-purple-50 text-purple-700 border border-purple-200
                                    @elseif($customer->customerProfile && $customer->customerProfile->membership === 'Silver') bg-slate-100 text-slate-700 border border-slate-200
                                    @else bg-orange-50 text-orange-700 border border-orange-200 @endif">
                                    {{ $customer->customerProfile ? $customer->customerProfile->membership : 'Silver' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                Rp {{ number_format(\App\Models\TrxOrder::where('customer_name', $customer->name)->sum('total_amount'), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600">
                                {{ number_format(\App\Models\TrxOrder::where('customer_name', $customer->name)->sum('total_amount') / 10000, 0) }} Pts
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
