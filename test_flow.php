<?php
$baseUrl = 'http://127.0.0.1:8001/api';

echo "1. Checking out as customer...\n";
$checkoutPayload = json_encode([
    'customer_name' => 'Budi Tester',
    'phone' => '08111222333',
    'address' => 'Jalan Test',
    'items' => [
        ['product_id' => 1, 'quantity' => 2]
    ],
    'payment_method' => 'SALDO'
]);

$ch = curl_init("$baseUrl/customer/checkout");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $checkoutPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
$checkoutRes = curl_exec($ch);
curl_close($ch);
echo "Checkout Response: $checkoutRes\n\n";

$checkoutData = json_decode($checkoutRes, true);
if (!$checkoutData || empty($checkoutData['success'])) {
    die("Checkout failed!\n");
}
$orderId = $checkoutData['order_id'];

echo "2. Fetching Preparist orders (status=onprocess)...\n";
// Normally preparist has a token, but the route might not be protected by auth:sanctum if it's just api? Let's check routes.
// Wait, Preparist might need login. Let's see if we can just get a token or bypass.
