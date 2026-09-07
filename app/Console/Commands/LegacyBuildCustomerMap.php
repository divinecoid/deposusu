<?php

namespace App\Console\Commands;

use App\Models\MdxCustomer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LegacyBuildCustomerMap extends Command
{
    protected $signature = 'legacy:build-customer-map {--create-missing : Create a new User+MdxCustomer for legacy customers that cannot be matched}';

    protected $description = 'Match legacy customer rows to users by phone/decrypted email, recording the result in legacy_customer_map';

    /**
     * AES-256-CBC key from the legacy app's api-client/config.php ($key
     * variable) — the only place this app's PII encryption key exists.
     * Used exclusively by this one-off migration tool.
     */
    private const LEGACY_KEY = 'D3P05U5U8ERJ4Y4M4NT4P8UK4C4B4N653LURUH1ND0N3514';

    public function handle(): int
    {
        $legacyCustomers = DB::connection('legacy')->table('customer')
            ->where('idcust', '!=', '')
            ->where('kodearea', '!=', 'TEST')
            ->get();

        $this->info("Found {$legacyCustomers->count()} legacy customers to map.");

        $counts = ['idcust_as_phone' => 0, 'decrypted_email' => 0, 'decrypted_phone' => 0, 'created_new' => 0, 'unmapped' => 0];

        foreach ($legacyCustomers as $legacy) {
            $email = $this->decrypt($legacy->email);
            $phone = $this->decrypt($legacy->phone) ?: $legacy->idcust;
            $address = $this->decrypt($legacy->alamat);

            $user = User::where('phone', $legacy->idcust)->first();
            $matchedBy = $user ? 'idcust_as_phone' : null;

            if (!$user && $email) {
                $user = User::where('email', $email)->first();
                $matchedBy = $user ? 'decrypted_email' : null;
            }

            if (!$user && $phone) {
                $user = User::where('phone', $phone)->first();
                $matchedBy = $user ? 'decrypted_phone' : null;
            }

            if (!$user && $this->option('create-missing')) {
                $user = User::create([
                    'name' => $legacy->namacust ?: 'Legacy Customer ' . $legacy->idcust,
                    'email' => $email ?: 'legacy-' . $legacy->idcust . '@deposusu.invalid',
                    'phone' => $phone,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'customer',
                ]);

                MdxCustomer::create([
                    'user_id' => $user->id,
                    'phone' => $phone,
                    'address' => $address,
                    'is_verified' => false,
                ]);

                $matchedBy = 'created_new';
            } elseif ($user) {
                // Matched an existing account — only backfill address/phone
                // on their profile if it's genuinely missing, never overwrite
                // something the customer already entered themselves.
                $profile = MdxCustomer::firstOrCreate(['user_id' => $user->id], ['is_verified' => false]);
                $profile->update(array_filter([
                    'address' => $profile->address ? null : $address,
                    'phone' => $profile->phone ? null : $phone,
                ], fn ($v) => $v !== null));
            }

            $matchedBy = $matchedBy ?: 'unmapped';
            $counts[$matchedBy]++;

            DB::table('legacy_customer_map')->updateOrInsert(
                ['legacy_idcust' => $legacy->idcust],
                [
                    'legacy_name' => $legacy->namacust,
                    'legacy_email_decrypted' => $email,
                    'legacy_phone_decrypted' => $phone,
                    'legacy_address_decrypted' => $address,
                    'customer_id' => $user?->id,
                    'matched_by' => $matchedBy,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        foreach ($counts as $type => $count) {
            $this->info("{$type}: {$count}");
        }

        return self::SUCCESS;
    }

    private function decrypt(?string $data): ?string
    {
        if (!$data) {
            return null;
        }

        $encryptionKey = base64_decode(self::LEGACY_KEY);
        $decoded = base64_decode($data);

        if (!str_contains($decoded, '::')) {
            return null;
        }

        [$encrypted, $iv] = array_pad(explode('::', $decoded, 2), 2, null);
        $result = openssl_decrypt($encrypted, 'aes-256-cbc', $encryptionKey, 0, $iv);

        return $result !== false ? $result : null;
    }
}
