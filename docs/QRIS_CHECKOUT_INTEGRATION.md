# QRIS Checkout Integration

## Overview
This document explains the QRIS dynamic payment integration in the checkout process.

## Features
- Automatic QRIS generation when QRIS payment method is selected
- Dynamic QRIS with calculated total amount
- QR code display on checkout and thank-you pages
- Integration with existing QRIS Dynamic Generator system

## How It Works

### 1. Checkout Page
When a user selects a QRIS payment method:
1. The system detects the payment method name contains "qris"
2. Automatically calls `/checkout/generate-qris` endpoint
3. Generates a dynamic QRIS code with the order total amount
4. Displays the QR code for scanning

### 2. Payment Processing
When the user submits the payment:
1. The `qris_dynamic_id` is included in the order data
2. Order is created with reference to the generated QRIS
3. User is redirected to thank-you page

### 3. Thank You Page
On the thank-you page:
1. If order has QRIS payment and status is pending
2. Display the generated QR code
3. Show merchant name and total amount
4. User can scan to complete payment

## Database Schema

### Orders Table
Added column:
- `qris_dynamic_id` (unsignedBigInteger, nullable) - Foreign key to qris_dynamics table

### Relationships
- Order `belongsTo` QrisDynamic
- QrisDynamic `hasMany` Orders

## API Endpoints

### Generate QRIS
**POST** `/checkout/generate-qris`

Request:
```json
{
  "amount": 100000
}
```

Response:
```json
{
  "success": true,
  "qris_dynamic_id": 123,
  "qr_image_url": "http://domain.com/storage/qris-checkout/qris-xxx.png",
  "amount_formatted": "100.000",
  "merchant_name": "Merchant Name"
}
```

## Configuration

### Payment Method Setup
To enable QRIS checkout:
1. Create a payment method with name containing "QRIS" (case-insensitive)
2. Ensure at least one active QRIS Static configuration exists
3. The system will automatically detect and generate dynamic QRIS

### QRIS Static Configuration
Required:
- At least one active QRIS Static record in `qris_statics` table
- Valid QRIS string format

## Files Modified

### Views
- `resources/views/checkout.blade.php` - Added QRIS display and generation logic
- `resources/views/thank-you.blade.php` - Added QRIS display on order confirmation

### Controllers
- `app/Http/Controllers/CheckoutController.php` - Added QRIS generation methods

### Models
- `app/Models/Order.php` - Added qris_dynamic_id field and relationship

### Routes
- `routes/web.php` - Added `/checkout/generate-qris` route

### Migrations
- `database/migrations/2025_11_08_162645_add_qris_dynamic_id_to_orders_table.php`

## Usage Example

1. User adds products to cart
2. User goes to checkout
3. User selects QRIS payment method
4. System automatically generates QRIS with order total
5. User scans QR code with payment app
6. User completes payment
7. Order status updated via webhook (if configured)

## Notes
- QRIS codes are stored in `storage/app/public/qris-checkout/`
- Each checkout generates a new QRIS code
- QRIS codes are linked to orders for tracking
- Fee calculation can be added by modifying the generation parameters
