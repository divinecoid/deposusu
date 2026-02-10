# Plan Implementasi Fitur Diskon

## Goal Description
Implementasi fitur diskon produk yang dapat diatur oleh admin. Diskon memiliki masa berlaku (start/end date) dan tipe (persentase/nominal). **Fitur ini mendukung historical tracking**, artinya setiap perubahan diskon akan disimpan sebagai record baru, memungkinkan admin melihat riwayat diskon suatu produk.

## User Review Required
> [!IMPORTANT]
> - Konfirmasi format tipe diskon: `PERCENTAGE` atau `FIXED`.
> - Data diskon akan disimpan di tabel terpisah `mdx_product_discounts` (One-to-Many), bukan di kolom produk langsung.

## Proposed Changes

### Database Changes
#### [NEW] [Migration: Create mdx_product_discounts table](file:///database/migrations/xxxx_xx_xx_xxxxxx_create_mdx_product_discounts_table.php)
- `id`: BIGINT (PK)
- `product_id`: BIGINT (FK to `mdx_products`)
- `discount_type`: ENUM('PERCENTAGE', 'FIXED')
- `discount_value`: DECIMAL(15, 2)
- `start_date`: DATETIME
- `end_date`: DATETIME
- `created_by`: BIGINT (FK to `users`, nullable) - untuk tracking siapa yang buat
- timestamps()

#### [NEW] [Migration: Add discount columns to trx_order_items and trx_orders](file:///database/migrations/xxxx_xx_xx_xxxxxx_add_discount_to_orders.php)
- `trx_order_items`:
    - `original_price`: DECIMAL(15, 2) (Harga sebelum diskon)
    - `discount_amount`: DECIMAL(15, 2) (Total diskon per item)
    - `discount_id`: BIGINT (FK to `mdx_product_discounts`, nullable) - Snapshots discount used
- `trx_orders`:
    - `total_discount`: DECIMAL(15, 2) (Total diskon semua item)

### Backend Logic
#### [NEW] [MdxProductDiscount Model](file:///app/Models/MdxProductDiscount.php)
- Fillable: `product_id`, `discount_type`, `discount_value`, `start_date`, `end_date`, `created_by`.
- Relation: `product()` belongsTo `MdxProduct`.
- Scope: `active()`: `where('start_date', '<=', now())` AND `where('end_date', '>=', now())`.

#### [Modify] [MdxProduct Model](file:///app/Models/MdxProduct.php)
- Relation: `discounts()` hasMany `MdxProductDiscount`.
- Helper: `activeDiscount()`: returns `discounts()->active()->latest()->first()`.
- Accessor: `getDiscountedPriceAttribute()`: Calculates price based on `activeDiscount()`.

#### [Modify] [TrxOrder Logic](file:///app/Http/Controllers/OrderController.php)
- Saat checkout, ambil `activeDiscount()` dari produk.
- Jika ada, simpan `discount_id` ke `trx_order_items`.

### Web UI
#### [Modify] [Admin Product Form](file:///resources/views/admin/products/form.blade.php) (or separate tab)
- **Current Active Discount Section**:
    - Form to add NEW discount (Start/End Date, Type, Value).
    - Saving this creates a new row in `mdx_product_discounts`.
- **Discount History Table**:
    - List of past/future discounts for this product.
    - Columns: Type, Value, Start Date, End Date, Status (Active/Expired/Upcoming).

#### [Modify] [Customer Product Page](file:///resources/views/customer/home.blade.php)
- Logic remains similar: Check for active discount and display discounted price.

## Verification Plan

### Automated Tests
- Test `MdxProductDiscount` scope `active()`.
- Test Product price calculation with overlapping discounts (should pick latest or throw error - for now assume latest created is active).

### Manual Verification
1.  **Admin**:
    - Create a discount for "Susu UHT" (10% off, Today-Tomorrow).
    - Verify it appears in "History" table.
    - Edit/Add another discount for next week.
    - Verify both exist in history.
2.  **Customer**:
    - Verify "Susu UHT" shows 10% off today.
3.  **Checkout**:
    - Buy "Susu UHT".
    - Check `trx_order_items` has `discount_id` linking to the specific discount record.
