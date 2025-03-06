<?php
// Define BASE_DIR first
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Include database connection
require_once BASE_DIR . 'lib/dhu.php';

header('Content-Type: application/json');

// Add error logging
error_log("PayPal Payment Request: " . file_get_contents('php://input'));

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    // Validate required fields
    if (!isset($data['payment_type']) || $data['payment_type'] !== 'paypal') {
        throw new Exception('Invalid payment type');
    }
    
    if (!isset($data['order_id']) || !isset($data['course_id']) || 
        !isset($data['user_id']) || !isset($data['course_owner_id'])) {
        throw new Exception('Missing required fields');
    }
    
    if (!isset($data['payment_details']) || 
        !isset($data['payment_details']['purchase_units']) ||
        !isset($data['payment_details']['purchase_units'][0]['amount']['value'])) {
        throw new Exception('Invalid payment details');
    }
    // Log successful validation
    error_log("Payment data validated successfully");

    // Get database connection
    $conn = getDBConnection();

    // Start transaction - Fix the method name
    $conn->beginTransaction();

    try {
        // Insert into course_payment table
        $sql = "INSERT INTO course_payment (
            course_owner_id, 
            purchase_user_id, 
            course_id, 
            price, 
            discount,
            payment_method, 
            status, 
            transaction_id
        ) VALUES (?, ?, ?, ?, ?, 'paypal', 'completed', ?)";
        
        $price = $data['payment_details']['purchase_units'][0]['amount']['value'];
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $data['course_owner_id'], PDO::PARAM_INT);
        $stmt->bindValue(2, $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(3, $data['course_id'], PDO::PARAM_INT);
        $stmt->bindValue(4, $price, PDO::PARAM_STR);
        $stmt->bindValue(5, $data['discount'] ?? 0, PDO::PARAM_STR);
        $stmt->bindValue(6, $data['order_id'], PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to record payment: ' . $stmt->errorInfo()[2]);
        }

        // Get the last insert ID using PDO method
        $payment_id = $conn->lastInsertId();

        // Check if user is already enrolled
        $checkSql = "SELECT COUNT(*) FROM course_enrollments 
                    WHERE author_id = ? AND course_id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindValue(1, $data['course_owner_id'], PDO::PARAM_INT);
        $checkStmt->bindValue(2, $data['course_id'], PDO::PARAM_INT);
        $checkStmt->bindValue(3, $data['user_id'], PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            // Only insert if not already enrolled
            $sql = "INSERT INTO course_enrollments (
                author_id,
                course_id, 
                user_id,
                status,
                enroll_date
            ) VALUES (?, ?, ?, 'active', NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1, $data['course_owner_id'], PDO::PARAM_INT);
            $stmt->bindValue(2, $data['course_id'], PDO::PARAM_INT);
            $stmt->bindValue(3, $data['user_id'], PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to enroll user in course: ' . $stmt->errorInfo()[2]);
            }
        }

        // Commit transaction
        $conn->commit();
        error_log("Payment processed successfully. Transaction ID: " . $payment_id);

        echo json_encode([
            'success' => true,
            'transaction_id' => $payment_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Payment processing error: " . $e->getMessage());
        throw $e;
    }

} catch (Exception $e) {
    error_log("PayPal payment error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>