<?php
header('Content-Type: application/json');
session_start();

// Include necessary files
include 'inc/p_others.php';
require_once BASE_DIR . 'lib/dhu.php';
require_once BASE_DIR . 'vendors/payments/vendor/autoload.php';

try {
    // Get JSON data from POST request
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (!$data) {
        throw new Exception('Invalid request data');
    }

    // Validate required fields
    if (empty($data['payment_method']) || empty($data['course_id']) || empty($data['amount'])) {
        throw new Exception('Missing required payment information');
    }

    // Add your Stripe secret key here
    \Stripe\Stripe::setApiKey('sk_test_51DnQGdEetogwNurNy5sbjtpv2cNom1YWcHJu2WKRlpo7fiePdcmIwzkQjlPwXif8IXMXNtcD2G3JWh8ZnGMo7dVx00ZSfhlhWM');

    // Create payment intent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $data['amount'] * 100, // Convert to cents
        'currency' => 'usd',
        'payment_method' => $data['payment_method'],
        'confirmation_method' => 'manual',
        'confirm' => true,
    ]);

    // If payment is successful, save transaction details to database
    if ($paymentIntent->status === 'succeeded') {
        // Here you would add code to:
        // 1. Save transaction details to your database
        // 2. Update course enrollment status
        // 3. Generate a transaction ID

        $response = [
            'success' => true,
            'transaction_id' => $paymentIntent->id, // Or your custom transaction ID
            'message' => 'Payment processed successfully'
        ];
    } else {
        throw new Exception('Payment failed');
    }
} catch (\Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
