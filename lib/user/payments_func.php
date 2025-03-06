<?php
/**
 * Payment functions for user
 */

// Define BASE_DIR first
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Now include the database connection
require_once BASE_DIR . 'lib/dhu.php';

function process_stripe_payment($amount, $currency = 'usd', $description = '', $customer_data = [], $payment_method_id = '', $course_id = null) {
    try {
        // Load Stripe SDK
        require_once BASE_DIR . 'vendors/payments/vendor/autoload.php';
        
        // If course_id is provided, get the actual price from the database
        if ($course_id) {
            // We'll use a direct database query
            $conn = getDBConnection(); // Use your connection method
            
            if ($conn) {
                // Using PDO prepared statement syntax
                $stmt = $conn->prepare("SELECT price FROM courses WHERE id = ? AND status = 1 AND admin_status = 1");
                $stmt->execute([$course_id]); // PDO style parameter binding
                
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $amount = (float)$row['price'];
                }
            }
        }
        
        // Set Stripe API key
        \Stripe\Stripe::setApiKey('sk_test_51DnQGdEetogwNurNy5sbjtpv2cNom1YWcHJu2WKRlpo7fiePdcmIwzkQjlPwXif8IXMXNtcD2G3JWh8ZnGMo7dVx00ZSfhlhWM');
        
        // Ensure minimum amount based on currency
        // For USD, the minimum is 50 cents
        $minimum_amount = 0.50;
        if ($amount < $minimum_amount) {
            $amount = $minimum_amount;
        }
        
        // Convert amount to cents (Stripe requires amounts in smallest currency unit)
        $amount_in_cents = (int)($amount * 100);
        
        // Create a customer if customer data is provided
        $customer_id = null;
        if (!empty($customer_data) && isset($customer_data['email'])) {
            // Check if customer already exists
            $customers = \Stripe\Customer::all(['email' => $customer_data['email'], 'limit' => 1]);
            
            if (!empty($customers->data)) {
                $customer = $customers->data[0];
                $customer_id = $customer->id;
                
                // Attach the payment method to the existing customer
                if ($payment_method_id) {
                    $paymentMethod = \Stripe\PaymentMethod::retrieve($payment_method_id);
                    $paymentMethod->attach(['customer' => $customer_id]);
                }
            } else {
                // Create new customer
                $customer = \Stripe\Customer::create([
                    'email' => $customer_data['email'],
                    'name' => isset($customer_data['name']) ? $customer_data['name'] : '',
                    'payment_method' => $payment_method_id,
                ]);
                $customer_id = $customer->id;
            }
        }
        
        // In the process_stripe_payment function, modify the payment intent parameters:
        
        // Create payment intent
        $intent_params = [
            'amount' => $amount_in_cents,
            'currency' => $currency,
            'description' => $description,
            'payment_method' => $payment_method_id,
            // Remove confirmation_method and use automatic_payment_methods only
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never'
            ],
            'confirm' => true,
            'return_url' => BASE_URL . 'public/user/courses/payment_success.php'
        ];
        
        // If we have a customer ID, use that
        if ($customer_id) {
            $intent_params['customer'] = $customer_id;
        }
        
        // Add metadata if needed
        if (isset($customer_data['user_id'])) {
            $intent_params['metadata'] = [
                'user_id' => $customer_data['user_id'],
                'course_id' => $course_id
            ];
        }
        
        // Create the payment intent
        $intent = \Stripe\PaymentIntent::create($intent_params);
        
        // In the process_stripe_payment function, modify the database insertion part:
        
        // If payment succeeded, record the transaction in our database
        // In the process_stripe_payment function, modify the enrollment insertion:
        if ($intent->status === 'succeeded' && isset($customer_data['user_id']) && $course_id) {
            $conn = getDBConnection();
            if ($conn) {
                $user_id = $customer_data['user_id'];
                $transaction_id = $intent->id;
                $payment_date = date('Y-m-d H:i:s');
                
                // Get course owner ID
                $stmt = $conn->prepare("SELECT user_id FROM courses WHERE id = ?");
                $stmt->execute([$course_id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                $course_owner_id = $course ? $course['user_id'] : 0;
                
                // Insert into course_payment table
                $stmt = $conn->prepare("INSERT INTO course_payment (course_owner_id, purchase_user_id, course_id, price, discount, payment_method, payment_date, status, transaction_id) 
                                       VALUES (?, ?, ?, ?, '0', 'stripe', ?, 'completed', ?)");
                $stmt->execute([$course_owner_id, $user_id, $course_id, $amount, $payment_date, $transaction_id]);
                
                // Enroll user in the course - using the correct table name
                $stmt = $conn->prepare("INSERT INTO course_enrollments (author_id, course_id, user_id, status, enroll_date) 
                                      VALUES (?, ?, ?, 'active', ?) 
                                      ON DUPLICATE KEY UPDATE status = 'active'");
                $stmt->execute([$course_owner_id, $course_id, $user_id, $payment_date]);
            }
        }
        
        // Check if payment requires additional action
        if ($intent->status === 'requires_action' && $intent->next_action->type === 'use_stripe_sdk') {
            // Tell the client to handle the action
            return [
                'success' => false,
                'requires_action' => true,
                'payment_intent_client_secret' => $intent->client_secret,
                'message' => 'Payment requires additional action'
            ];
        } else if ($intent->status === 'succeeded') {
            // Payment is complete
            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_id' => $intent->id,
                'amount' => $amount,
                'currency' => $currency,
                'data' => $intent
            ];
        } else {
            // Invalid status
            return [
                'success' => false,
                'message' => 'Payment failed with status: ' . $intent->status
            ];
        }
    } catch (\Stripe\Exception\CardException $e) {
        // Card was declined
        return [
            'success' => false,
            'message' => 'Card declined: ' . $e->getMessage(),
            'error_code' => $e->getStripeCode()
        ];
    } catch (\Exception $e) {
        // Something else went wrong
        return [
            'success' => false,
            'message' => 'Payment error: ' . $e->getMessage()
        ];
    }
}

/**
 * Get Stripe publishable key
 * 
 * @return string Publishable key
 */
function get_stripe_publishable_key() {
    return 'pk_test_V7RN02YyVxmHATgBQiAcloqU';
}

/**
 * Record successful payment in database
 * 
 * @param int $user_id User ID
 * @param int $course_id Course ID
 * @param float $amount Payment amount
 * @param string $transaction_id Transaction ID from payment processor
 * @return bool Success status
 */
function recordPayment($user_id, $course_id, $amount, $transaction_id) {
    $conn = getDBConnection();
    if (!$conn) {
        return false;
    }
    
    $payment_date = date('Y-m-d H:i:s');
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Get course owner ID
        $stmt = $conn->prepare("SELECT user_id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        $course_owner_id = $course ? $course['user_id'] : 0;
        
        // Insert into course_payment table
        $stmt = $conn->prepare("INSERT INTO course_payment (course_owner_id, purchase_user_id, course_id, price, discount, payment_method, payment_date, status, transaction_id) 
                               VALUES (?, ?, ?, ?, '0', 'stripe', ?, 'completed', ?)");
        $stmt->execute([$course_owner_id, $user_id, $course_id, $amount, $payment_date, $transaction_id]);
        
        // Enroll user in the course using course_enrollments table
        $stmt = $conn->prepare("INSERT INTO course_enrollments (author_id, course_id, user_id, status, enroll_date) 
                              VALUES (?, ?, ?, 'active', ?) 
                              ON DUPLICATE KEY UPDATE status = 'active'");
        $stmt->execute([$course_owner_id, $course_id, $user_id, $payment_date]);
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (\Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Payment record error: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle payment request from AJAX
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)) {
    
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        // Check if this is a payment intent confirmation
        if (isset($data['payment_intent_id'])) {
            try {
                // Load Stripe SDK if not already loaded
                if (!defined('BASE_DIR')) {
                    define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
                }
                require_once BASE_DIR . 'vendors/payments/vendor/autoload.php';
                
                // Set Stripe API key
                \Stripe\Stripe::setApiKey('sk_test_51DnQGdEetogwNurNy5sbjtpv2cNom1YWcHJu2WKRlpo7fiePdcmIwzkQjlPwXif8IXMXNtcD2G3JWh8ZnGMo7dVx00ZSfhlhWM');
                
                // Confirm the payment intent
                $intent = \Stripe\PaymentIntent::retrieve($data['payment_intent_id']);
                $intent->confirm();
                
                // Check the status
                if ($intent->status === 'succeeded') {
                    // Record the payment in our database if we have metadata
                    if (isset($intent->metadata->user_id) && isset($intent->metadata->course_id)) {
                        $user_id = $intent->metadata->user_id;
                        $course_id = $intent->metadata->course_id;
                        $amount = $intent->amount / 100; // Convert from cents
                        
                        recordPayment($user_id, $course_id, $amount, $intent->id);
                    }
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Payment processed successfully',
                        'transaction_id' => $intent->id
                    ]);
                    exit;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Payment failed with status: ' . $intent->status
                    ]);
                    exit;
                }
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Payment error: ' . $e->getMessage()
                ]);
                exit;
            }
        } 
        // Process new payment
        else if (isset($data['payment_method'])) {
            // Extract data from request
            $payment_method = $data['payment_method'];
            $course_id = isset($data['course_id']) ? (int)$data['course_id'] : null;
            $amount = isset($data['amount']) ? (float)$data['amount'] : 0;
            $currency = isset($data['currency']) ? $data['currency'] : 'usd';
            $description = isset($data['description']) ? $data['description'] : 'Course payment';
            $customer_data = isset($data['customer_data']) ? $data['customer_data'] : [];
            
            // Process payment - pass course_id as the last parameter
            $result = process_stripe_payment($amount, $currency, $description, $customer_data, $payment_method, $course_id);
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            // Invalid request
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data - missing payment_method or payment_intent_id'
            ]);
            exit;
        }
    } else {
        // Invalid JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data: ' . json_last_error_msg()
        ]);
        exit;
    }
}
?>