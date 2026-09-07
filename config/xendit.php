<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Xendit API credentials
    |--------------------------------------------------------------------------
    |
    | Get these from your Xendit Dashboard: Settings > API Keys, and
    | Settings > Webhooks for the callback verification token.
    |
    */

    'secret_key' => env('XENDIT_SECRET_KEY'),
    'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
    'api_version' => '2024-11-11',
    'is_production' => env('XENDIT_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Payment channels & convenience fees
    |--------------------------------------------------------------------------
    |
    | The convenience fee is added on top of the order total and charged to
    | the customer, so the store doesn't absorb Xendit's per-transaction fee.
    |
    | IMPORTANT — these fee_percent / fee_flat numbers are DEFAULTS, not
    | guaranteed rates:
    |   - QRIS is regulated Indonesia-wide by Bank Indonesia at a flat 0.7%
    |     merchant discount rate, so that figure is safe to trust as-is.
    |   - E-wallet (OVO/DANA/ShopeePay/LinkAja) and Virtual Account rates are
    |     negotiated per merchant in your Xendit Service Agreement (PKS) —
    |     they are NOT a public flat-rate card. Confirm your actual contracted
    |     rates in the Xendit Dashboard (Settings > Pricing) and update the
    |     numbers below before going live; what's here is a reasonable
    |     placeholder based on publicly reported ranges.
    |
    */

    'channels' => [
        'QRIS' => [
            'label' => 'QRIS',
            'description' => 'GoPay, OVO, DANA, ShopeePay, m-banking, dll',
            'group' => 'qris',
            'fee_percent' => 0.7,
            'fee_flat' => 0,
            'min_amount' => 1,
            'max_amount' => 10_000_000,
        ],
        'OVO' => [
            'label' => 'OVO',
            'description' => 'Bayar langsung dari aplikasi OVO',
            'group' => 'ewallet',
            'fee_percent' => 1.5,
            'fee_flat' => 0,
        ],
        'DANA' => [
            'label' => 'DANA',
            'description' => 'Bayar langsung dari aplikasi DANA',
            'group' => 'ewallet',
            'fee_percent' => 1.5,
            'fee_flat' => 0,
        ],
        'SHOPEEPAY' => [
            'label' => 'ShopeePay',
            'description' => 'Bayar langsung dari aplikasi Shopee',
            'group' => 'ewallet',
            'fee_percent' => 2.0,
            'fee_flat' => 0,
        ],
        'LINKAJA' => [
            'label' => 'LinkAja',
            'description' => 'Bayar langsung dari aplikasi LinkAja',
            'group' => 'ewallet',
            'fee_percent' => 1.5,
            'fee_flat' => 0,
        ],
        'BCA_VIRTUAL_ACCOUNT' => [
            'label' => 'BCA Virtual Account',
            'description' => 'Transfer via ATM, m-banking, atau internet banking BCA',
            'group' => 'va',
            'fee_percent' => 0,
            'fee_flat' => 4000,
        ],
        'BNI_VIRTUAL_ACCOUNT' => [
            'label' => 'BNI Virtual Account',
            'description' => 'Transfer via ATM, m-banking, atau internet banking BNI',
            'group' => 'va',
            'fee_percent' => 0,
            'fee_flat' => 4000,
        ],
        'BRI_VIRTUAL_ACCOUNT' => [
            'label' => 'BRI Virtual Account',
            'description' => 'Transfer via ATM, m-banking, atau internet banking BRI',
            'group' => 'va',
            'fee_percent' => 0,
            'fee_flat' => 4000,
        ],
        'MANDIRI_VIRTUAL_ACCOUNT' => [
            'label' => 'Mandiri Virtual Account',
            'description' => 'Transfer via ATM, m-banking, atau internet banking Mandiri',
            'group' => 'va',
            'fee_percent' => 0,
            'fee_flat' => 4000,
        ],
        'PERMATA_VIRTUAL_ACCOUNT' => [
            'label' => 'Permata Virtual Account',
            'description' => 'Transfer via ATM, m-banking, atau internet banking Permata',
            'group' => 'va',
            'fee_percent' => 0,
            'fee_flat' => 4000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiry windows
    |--------------------------------------------------------------------------
    */

    'expiry_minutes' => [
        'qris' => 30,     // QRIS supports up to 48h; 30 min matches a live checkout
        'ewallet' => 60,
        'va' => 1440,     // 24h — customers often pay VA later via ATM/m-banking
    ],

];
