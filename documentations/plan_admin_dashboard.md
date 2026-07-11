# Admin Dashboard Implementation Plan

## Overview
Build a comprehensive Admin Dashboard for DEPOSUSU to manage Master Data, Orders, and Stock.

## 1. Master Data Management
CRUD functionality for the following entities:
-   **Kategori Produk** (Product Categories)
-   **Produk** (Products)
    -   Attributes: Name, SKU, Price, Image, Description, Category ID.
-   **Customer**
    -   Data: Name, Phone, Address, Email.
-   **Driver**
    -   Data: Name, Phone, Vehicle Details.
-   **Kasir** (Cashier)
    -   Data: Name, User Account.
-   **Gudang** (Warehouse)
    -   Data: Name, Location.
-   **Rak** (Racks)
    -   Data: Name/Code, Warehouse ID.

## 2. Order Management
Orders will be categorized tabs or filters based on status:
-   **Pending**: New orders waiting for confirmation.
-   **On Process**: Orders being packed/prepared.
-   **On Delivery**: Orders currently with drivers.
-   **Delivered**: Orders arrived at location.
-   **Partial Delivered**: Orders with some items missing/rejected.
-   **Done**: Completed and settled orders.
-   **Cancelled**: Orders cancelled by user/admin before processing.
-   **Rejected**: Orders rejected by admin.

### Features per Status
-   **Pending**: Accept/Reject actions.
-   **On Process**: Assign Driver, Print Label.
-   **On Delivery**: Track status, Mark as Delivered.
-   **Delivered**: Confirm payment/completion.

## 3. Stock Opname (Inventory)
-   **Stock Adjustment**: Ability to manually adjust stock levels per Product/Warehouse/Rack.
-   **History**: Log of all stock changes (In/Out/Opname).
-   **Low Stock Alerts**: Visual indicators for items below threshold.

## 4. Database Schema Changes & New Models

### New Models
-   `MdxCategory`: `id`, `name`, `slug`, `icon`.
-   `MdxWarehouse`: `id`, `name`, `address`.
-   `MdxRack`: `id`, `name`, `warehouse_id`.
-   `MdxDriver`: `id`, `user_id`, `license_plate`, `vehicle_type`. (Link to User)
-   `MdxCustomer`: `id`, `user_id`, `phone`, `address`. (Link to User)

### Updates to Existing Models
-   **User**: Add `role` column (enum: admin, driver, cashier, customer, warehouse_staff).
-   **MdxProduct**: Add `category_id` (FK), `sku` (string), `low_stock_threshold` (int).
-   **TrxOrder**:
    -   Add `driver_id` (FK to Users/Drivers).
    -   Add `warehouse_id` (FK).
    -   Add `status_history` (JSON or separate table `trx_order_histories`).
    -   Enforce Status Enum: `pending`, `onprocess`, `ondelivery`, `delivered`, `partialdelivered`, `done`, `cancelled`, `rejected`.

    -   Create `MdxCategory`, `MdxWarehouse`, `MdxRack`, `MdxDriver`, `MdxCustomer` tables.
    -   Update `User`, `MdxProduct`, `TrxOrder` tables.
    -   Create `TrxInvoice` table (linked to `orders`).

## 5. Invoice & Payment Management
Karena dokumen transaksi bersifat per-customer (bukan laporan agregat), fitur ini berdiri sebagai menu utama (terpisah dari menu Reports & Export).

### 5.1. Invoice Management
Menu untuk mengelola seluruh invoice transaksi dari berbagai sumber (Customer App, Manual Order Admin/WhatsApp, POS/Kasir, Corporate Order).

**Fitur:**
- **Invoice List**
  - **Filter:** Nomor invoice, Customer, Tanggal transaksi, Status pembayaran, Cabang, Area.
  - **Tampilan Tabel:** No Invoice, Customer, Tanggal, Total, Status, Action (View / Print).
- **Invoice Detail**
  - **Informasi:** Nomor Invoice, Customer Information, Alamat, Detail produk (Qty, Harga, Subtotal), Diskon, Total pembayaran, Status pembayaran.
  - **Action:**
    - ✅ Preview Invoice
    - ✅ Print Invoice
    - ✅ Download PDF
    - ✅ Kirim Invoice ke Customer

### 5.2. Payment Management
Menu khusus untuk menangani dan memvalidasi konfirmasi pembayaran.

**Fitur:**
- List pembayaran
- Bukti transfer
- Status pembayaran
- Alur Status: `Pending` → `Waiting Confirmation` → `Paid` → `Cancelled`

### 5.3. Kuitansi Management
Kuitansi dibuat setelah pembayaran dikonfirmasi (Status Paid). Sangat penting bagi Customer PT, Corporate customer, atau customer yang membutuhkan bukti pembayaran resmi.

**Isi Kuitansi:**
- No Kuitansi (contoh: KWT-001)
- Telah diterima dari (contoh: PT ABC)
- Nominal
- Untuk pembayaran (contoh: Invoice INV-001)
- Tanggal
- Nama penerima pembayaran

**Action:**
- ✅ Generate Kuitansi
- ✅ Print Kuitansi
- ✅ Download PDF

### 5.4. Final Document Flow
`Order` → `Sales Order` → `Generate Invoice` → `Customer Payment` → `Payment Confirmation` → `Generate Kuitansi` → `Archive Document`

## 6. Technical Implementation Steps
1.  **Migrations**: Create migrations for new tables and updates.
    -   New: `mdx_categories`, `mdx_warehouses`, `mdx_racks`, `mdx_drivers`, `mdx_customers`, `trx_invoices`.
    -   Modify: `users` (role), `mdx_products` (category_id, etc), `trx_orders` (driver_id, warehouse_id).
2.  **Models**: Generate Models with relationships.
3.  **Filament / Backend**: (Assuming custom Blade/Controller or Filament? User said "Admin Dashboard", I will stick to standard Blade/Controller unless specified otherwise).
    -   *Note*: The current stack seems to be Laravel + Blade + Tailwind. I will build standard Controllers and Views.
4.  **Views**:
    -   `admin.dashboard.index`: Stats and quick links.
    -   `admin.master.*`: CRUD indexes and forms.
    -   `admin.orders.*`: Kanban or List view filtered by status.
    -   `admin.stock.*`: Stock management interface.
