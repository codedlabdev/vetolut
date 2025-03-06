<?php
// Include necessary files
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Get transaction ID from URL
$transaction_id = isset($_GET['id']) ? $_GET['id'] : '';

// Get payment details if needed
// $payment_details = getPaymentDetails($transaction_id);

// You can add logic here to verify the payment status if needed
?>

<div class="payment-success-container">
    <div class="success-card">
        <div class="success-icon-container">
            <div class="checkmark-circle">
                <div class="checkmark-stem"></div>
                <div class="checkmark-kick"></div>
            </div>
        </div>

        <h1 class="success-title">Payment Successful!</h1>

        <p class="success-message">
            Thank you for your purchase. Your transaction has been completed successfully.
            <?php if (!empty($transaction_id)): ?>
                <br>
                <span style="display:none" class="transaction-id">Transaction ID: <?php echo htmlspecialchars($transaction_id); ?></span>
            <?php endif; ?>
        </p>

        <div class="success-details">
           <!-- <p>A confirmation email has been sent to your registered email address.</p>-->
            <p>You can now access your course content in your dashboard.</p>
        </div>

        <div class="action-buttons">
            <a href="<?php echo BASE_URL; ?>public/user/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="<?php echo BASE_URL; ?>public/user/courses/main.php" class="btn btn-outline-primary">Browse More Courses</a>
        </div>
    </div>
</div>

<style>
    .payment-success-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background-color: #f8f9fa;
    }

    .success-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        padding: 3rem;
        max-width: 600px;
        width: 100%;
        text-align: center;
        animation: fadeIn 0.6s ease-out;
        margin-bottom: 100px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
.success-icon-container {
        margin: 0 auto 3rem;
        position: relative;
        width: 120px;
        height: 120px;
    }

    .checkmark-circle {
        width: 100%;
        height: 100%;
        position: relative;
        background-color: #4BB543;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(75, 181, 67, 0.3);
        transform: scale(0);
        animation: scaleIn 0.5s ease-out forwards;
    }

    @keyframes scaleIn {
        0% { 
            transform: scale(0); 
        }
        70% { 
            transform: scale(1.1);
        }
        100% { 
            transform: scale(1);
        }
    }

    .checkmark-circle::after {
        content: '';
        position: absolute;
        width: 35px;
        height: 60px;
        border: solid white;
        border-width: 0 10px 10px 0;
        transform: rotate(45deg) translate(-5px, -5px) scale(0);
        opacity: 0;
        animation: checkmark-animation 0.4s ease-out 0.4s forwards;
    }

    @keyframes checkmark-animation {
        0% {
            transform: rotate(45deg) translate(-5px, -5px) scale(0);
            opacity: 0;
        }
        100% {
            transform: rotate(45deg) translate(-5px, -5px) scale(1);
            opacity: 1;
        }
    }
    .success-title {
        color: #2c3e50;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .success-message {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 1.5rem;
    }

    .transaction-id {
        display: inline-block;
        background: #f1f8ff;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #0066cc;
        margin-top: 0.5rem;
    }

    .success-details {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .success-details p {
        margin-bottom: 0.5rem;
        color: #555;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
        color: white;
    }

    .btn-primary:hover {
        background-color: #3a56d4;
        border-color: #3a56d4;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-outline-primary {
        background-color: transparent;
        border: 2px solid #4361ee;
        color: #4361ee;
    }

    .btn-outline-primary:hover {
        background-color: #f0f4ff;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
    }

    @media (max-width: 768px) {
        .success-card {
            padding: 2rem;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.8rem;
        }

        .success-title {
            font-size: 1.8rem;
        }
    }

    .fix-osahan-footer {
        position: fixed !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove the manual transform as it's now handled by the animation
        const checkmarkCircle = document.querySelector('.checkmark-circle');
        if (checkmarkCircle) {
            checkmarkCircle.style.opacity = '1';
        }
    });
    <div class="success-icon-container">
        <div class="checkmark-circle"></div>
    </div>
    
</script>
 

<?php
include '../inc/float_nav.php';
include '../footer.php';
?>
