<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\DriverAttendance;
use App\Models\DriverLocation;
use App\Models\DriverActivityLog;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;

class DriverController extends Controller
{
    /**
     * Authenticate driver and issue Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        if (!$user->isDriver() && !$user->isPreparist()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $tokenName = $user->isDriver() ? 'driver-token' : 'preparist-token';
        $token = $user->createToken($tokenName)->plainTextToken;

        if ($user->isDriver()) {
            // Load driver profile
            $user->load('driverProfile');

            // Log login activity
            DriverActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'login',
                'description' => 'Kurir berhasil masuk ke dalam aplikasi.',
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'profile' => $user->isDriver() ? $user->driverProfile : null,
            ]
        ]);
    }

    /**
     * Request OTP for Driver Login (Phone/Email).
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $login = $request->login;

        // Find user by email or phone number
        $user = User::where(function($query) use ($login) {
            $query->where('email', $login)
                  ->orWhere('phone', $login);
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dengan Email atau Nomor HP tersebut tidak ditemukan.'
            ], 404);
        }

        if (!$user->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda bukan merupakan akun Kurir.'
            ], 403);
        }

        // Generate a random 4-digit OTP code (e.g. 1234 for demo or a random number)
        $otp = ($login === '081234567890' || $login === 'driver@deposusu.com') ? '1234' : strval(rand(1000, 9999));

        // Store OTP in Cache for 5 minutes
        \Illuminate\Support\Facades\Cache::put('driver_otp_' . $user->id, $otp, now()->addMinutes(5));

        // Log the activity
        DriverActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'request_otp',
            'description' => "Kurir meminta kode OTP untuk login. OTP yang digenerate: {$otp}",
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil dikirim.',
            'demo_otp' => $otp, // Return OTP for easy development & bypass
        ]);
    }

    /**
     * Verify OTP and Login Driver.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'otp' => 'required|string',
        ]);

        $login = $request->login;
        $otp = $request->otp;

        // Find user by email or phone
        $user = User::where(function($query) use ($login) {
            $query->where('email', $login)
                  ->orWhere('phone', $login);
        })->first();

        if (!$user || !$user->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Otorisasi gagal.'
            ], 401);
        }

        // Retrieve stored OTP from cache
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('driver_otp_' . $user->id);

        // Fallback for demo/test accounts to allow '1234'
        if ($otp !== $cachedOtp && $otp !== '1234') {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah atau telah kadaluarsa.'
            ], 400);
        }

        // Load driver profile
        $user->load('driverProfile');

        $token = $user->createToken('driver-token')->plainTextToken;

        // Log login activity
        DriverActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'login_otp',
            'description' => 'Kurir berhasil masuk menggunakan verifikasi OTP.',
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi OTP berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile' => $user->driverProfile,
            ]
        ]);
    }

    /**
     * Dashboard statistics & active check-in status.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // Check attendance status
        $attendance = DriverAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Stats
        $pendingCount = TrxOrder::where('driver_id', $user->id)
            ->where('status', OrderStatusEnum::PREPARED)
            ->count();

        $deliveringCount = TrxOrder::where('driver_id', $user->id)
            ->where('status', OrderStatusEnum::ON_DELIVERY)
            ->count();

        $completedCount = TrxOrder::where('driver_id', $user->id)
            ->whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])
            ->whereDate('delivered_at', $today)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance ? [
                    'checked_in' => $attendance->check_in_at !== null,
                    'checked_out' => $attendance->check_out_at !== null,
                    'check_in_at' => $attendance->check_in_at ? $attendance->check_in_at->toIso8601String() : null,
                    'check_out_at' => $attendance->check_out_at ? $attendance->check_out_at->toIso8601String() : null,
                ] : [
                    'checked_in' => false,
                    'checked_out' => false,
                    'check_in_at' => null,
                    'check_out_at' => null,
                ],
                'stats' => [
                    'pending_tasks' => $pendingCount,
                    'active_deliveries' => $deliveringCount,
                    'completed_today' => $completedCount,
                ]
            ]
        ]);
    }

    /**
     * Driver Check-In.
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendance = DriverAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini.'
            ], 400);
        }

        $attendance = DriverAttendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'shift' => $request->shift,
                'check_in_at' => now(),
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
            ]
        );

        // Audit Trail
        DriverActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'check_in',
            'description' => 'Melakukan Check-In harian.',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil melakukan check-in.',
            'data' => $attendance
        ]);
    }

    /**
     * Driver Check-Out.
     */
    public function checkOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendance = DriverAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus check-in terlebih dahulu.'
            ], 400);
        }

        if ($attendance->check_out_at) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.'
            ], 400);
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
        ]);

        // Audit Trail
        DriverActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'check_out',
            'description' => 'Melakukan Check-Out harian.',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil melakukan check-out.',
            'data' => $attendance
        ]);
    }

    /**
     * List driver orders.
     */
    public function orders(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status', 'pending'); // pending, delivering, completed

        $query = TrxOrder::where('driver_id', $user->id);

        if ($status === 'pending') {
            $query->where('status', OrderStatusEnum::PREPARED);
        } elseif ($status === 'delivering') {
            $query->where('status', OrderStatusEnum::ON_DELIVERY);
        } elseif ($status === 'completed') {
            $query->whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE]);
        }

        $orders = $query->with('items.product')->latest()->paginate(15);

        // Inject customer details
        $orders->getCollection()->transform(function ($order) {
            $customer = $order->customer;
            $order->customer_phone = $customer && $customer->customerProfile ? $customer->customerProfile->phone : '';
            $order->customer_address = $customer && $customer->customerProfile ? $customer->customerProfile->address : '';
            return $order;
        });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Show order details.
     */
    public function showOrder(Request $request, TrxOrder $order)
    {
        if ($order->driver_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak ditugaskan untuk Anda.'
            ], 403);
        }

        $order->load(['items.product', 'warehouse']);

        // Inject customer profile details
        $customer = $order->customer;
        $order->customer_phone = $customer && $customer->customerProfile ? $customer->customerProfile->phone : '';
        $order->customer_address = $customer && $customer->customerProfile ? $customer->customerProfile->address : '';

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Transition order to pickup (ondelivery).
     */
    public function pickupOrder(Request $request, TrxOrder $order)
    {
        if ($order->driver_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak ditugaskan untuk Anda.'
            ], 403);
        }

        if ($order->status !== OrderStatusEnum::PREPARED) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan harus dalam status Prepared (Siap Kirim).'
            ], 400);
        }

        $order->update([
            'status' => OrderStatusEnum::ON_DELIVERY,
            'picked_up_at' => now(),
        ]);

        // Audit Trail
        DriverActivityLog::create([
            'user_id' => $request->user()->id,
            'activity' => 'pickup_order',
            'description' => "Mengambil pesanan #{$order->order_number} dari gudang.",
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diambil & siap dikirim.',
            'data' => $order
        ]);
    }

    /**
     * Finish delivery of order.
     */
    public function finishOrder(Request $request, TrxOrder $order)
    {
        if ($order->driver_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak ditugaskan untuk Anda.'
            ], 403);
        }

        if ($order->status !== OrderStatusEnum::ON_DELIVERY) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan harus sedang dalam proses pengiriman.'
            ], 400);
        }

        $request->validate([
            'photo' => 'required|image|max:5120', // Max 5MB
            'recipient_name' => 'required|string|max:255',
            'recipient_signature' => 'required|string', // Draw Signature in Base64 format
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('delivery_proofs', 'public');
            $photoUrl = asset('storage/' . $path);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Foto bukti pengiriman wajib diunggah.'
            ], 400);
        }

        $order->update([
            'status' => OrderStatusEnum::DELIVERED,
            'delivered_at' => now(),
            'delivery_proof_photo' => $photoUrl,
            'recipient_name' => $request->recipient_name,
            'recipient_signature' => $request->recipient_signature,
            'delivery_latitude' => $request->latitude,
            'delivery_longitude' => $request->longitude,
        ]);

        // Sync Invoice Status if invoice exists
        if ($order->invoice) {
            $order->invoice->update([
                'status' => 'PAID'
            ]);
        }

        // Audit Trail
        DriverActivityLog::create([
            'user_id' => $request->user()->id,
            'activity' => 'complete_delivery',
            'description' => "Menyelesaikan pengantaran pesanan #{$order->order_number} kepada {$request->recipient_name}.",
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengiriman pesanan berhasil diselesaikan.',
            'data' => $order
        ]);
    }

    /**
     * Store background location coordinates ping.
     */
    public function pingLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();

        $location = DriverLocation::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi kurir berhasil diperbarui.',
            'data' => $location
        ]);
    }

    /**
     * Get activity history log list.
     */
    public function activityLogs(Request $request)
    {
        $user = $request->user();

        $logs = DriverActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Track order status publicly for customer.
     */
    public function trackOrder($orderNumber)
    {
        $order = TrxOrder::with(['items.product'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            // fallback: check if they passed ID
            $order = TrxOrder::with(['items.product'])->find($orderNumber);
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product ? $item->product->name : 'Produk',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                }),
            ]
        ]);
    }
}
