# Preparist API Technical Plan

## Overview
This document outlines the technical specifications for the Preparist mobile application APIs. The app handles order preparation workflow: Dashboard monitoring and Order Packing/Preparation.

## Database Schema Changes

To track who prepares the order and when, we need to add columns to `trx_orders` table.

### `trx_orders` Table
- `preparist_id`: `unsignedBigInteger`, nullable, foreign key to `users.id`.
- `on_preparation_at`: `timestamp`, nullable. Records when preparation started.
- `prepared_at`: `timestamp`, nullable. Records when preparation finished.

## Models

### `User` Model
- Add `isPreparist()` helper method.
- Ensure 'preparist' role is supported.

### `TrxOrder` Model
- Add relationship `preparist()` (belongsTo User).
- Add fields to `$fillable`.
- Add `$casts` for timestamps.

## API Endpoints

All endpoints should be prefixed with `/api/preparist` and require authentication (Sanctum/JWT).

### 1. Dashboard
**Endpoint:** `GET /api/preparist/dashboard`

**Response:**
```json
{
    "performance": {
        "hour": 12, // Packings per current hour
        "day": 45,  // Packings today
        "week": 250, // Packings this week
        "month": 900 // Packings this month
    }
}
```
**Logic:** Count orders where `preparist_id` is the current user and `prepared_at` falls within the time range.

### 2. Packing List (Order Transaction List)
**Endpoint:** `GET /api/preparist/orders`

**Query Parameters:**
- `status`: string (default: `onprocess`). Possible values: `onprocess` (Ready to prepare), `onpreparation` (Being prepared).

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "order_number": "ORD-001",
            "customer_name": "John Doe",
            "status": "onprocess",
            "total_items": 5,
            "created_at": "2023-10-27 10:00:00"
            // ... other order details
        }
    ]
    // ... pagination meta
}
```

### 3. Start Preparation
**Endpoint:** `POST /api/preparist/orders/{order}/start`

**Logic:**
1. Check if order status is `onprocess`.
2. Update status to `onpreparation`.
3. Set `preparist_id` to current user.
4. Set `on_preparation_at` to current timestamp.

**Response:**
- 200 OK: Order updated.
- 400 Bad Request: If status is not `onprocess`.

### 4. Finish Preparation (Submit)
**Endpoint:** `POST /api/preparist/orders/{order}/finish`

**Logic:**
1. Check if order status is `onpreparation`.
2. check if `preparist_id` matches current user (optional, but good for security).
3. Update status to `prepared`.
4. Set `prepared_at` to current timestamp.

**Response:**
- 200 OK: Order updated.
- 400 Bad Request: If status is not `onpreparation`.

## Authentication
Assuming existing authentication system. Preparist users must have `role: preparist`.
