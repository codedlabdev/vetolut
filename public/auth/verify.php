<?php
// Start the session to access user details
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/db.php'; // Database connection
require_once BASE_DIR . 'lib/helpers.php'; // Helper functions

// Fetch the base URL from the database
$stmt = $pdo->prepare("SELECT value FROM app_config WHERE key_name = :key_name");
$stmt->execute(['key_name' => 'base_url']);
$base_url = $stmt->fetchColumn();

// Define BASE_URL constant
define('BASE_URL', rtrim($base_url, '/') . '/');

// Ensure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL);
    exit();
}

// Get user details from the session
$userId = $_SESSION['user_id'];
$fName = $_SESSION['f_name'];
$lName = $_SESSION['l_name'];
$userEmail = $_SESSION['user_email'];

// Handle form submission for verification
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle the verification logic
    $inputCode = implode('', $_POST['verification_code'] ?? []);

    // Fetch the expected verification code from the database
    $stmt = $pdo->prepare("SELECT email_vecode FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $expectedCode = $stmt->fetchColumn();

    if ($inputCode === $expectedCode) {
        // Update the user to mark them as verified
        $stmt = $pdo->prepare("UPDATE users SET verify = 1 WHERE id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        
        // Set session variables to indicate user is logged in
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['f_name'] = $fName;
        $_SESSION['l_name'] = $lName;
        $_SESSION['user_email'] = $userEmail;

        // Redirect to the dashboard
        header("Location: " . BASE_URL . "public/user/dashboard.php");
        exit();
    } else {
        echo '<div class="alert alert-danger" id="success-message" style="text-align: center;">Invalid verification code. Please try again.</div>';
    }
}

// Combine first name and last name into full name
$fullName = trim($fName . ' ' . $lName);

// Display any error messages stored in the session
if (isset($_SESSION['email_update_error'])) {
    echo '<div class="alert alert-danger" style="text-align: center;">' . htmlspecialchars($_SESSION['email_update_error']) . '</div>';
    // Clear the error message after displaying it
    unset($_SESSION['email_update_error']);
}

// Check if a success message is set
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" id="success-message" style="text-align: center;">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    // Clear the message after displaying it
    unset($_SESSION['success_message']);
}

// Fetch the email verification code to send to the user
$stmt = $pdo->prepare("SELECT email_vecode FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $userId]);
$emailVeCode = $stmt->fetchColumn();

// Check if the email verification code is found
if (!$emailVeCode) {
    echo "Error: Verification code not found.";
    exit();
}

// Send the verification email
$to = $userEmail;
$subject = "Email Verification Code";
$message = "
    <html>
    <head>
        <title>Email Verification</title>
    </head>
    <body>
        <p>Dear $fullName,</p>
        <p>Your verification code is: <strong>$emailVeCode</strong></p>
        <p>Please use this code to complete your registration process.</p>
        <p>Best regards,<br>Your Company Name</p>
    </body>
    </html>
";

// Set the headers to send HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: verify@vetolutz.devdigitalz.com" . "\r\n"; // Replace with your actual "From" address

// Attempt to send the verification email
if (mail($to, $subject, $message, $headers)) {
    //echo "Verification email sent successfully to $userEmail.";
} else {
    // Capture error details if any
    $error = error_get_last();
   echo '<div class="alert alert-success" id="success-message" style="text-align: center;">Failed to send verification email. Error details: ' . print_r($error, true) . '</div>';
    echo "<br>";
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.svg" type="image/png">
    <title>Verify</title>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/vender/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/vender/sidebar/demo.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/vender/materialdesign/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/style.css">
</head>
<body class="bg-light">
    <div class="verify p-4">
        <div class="d-flex align-items-start justify-content-between mb-4">
            <a class="toggle text-dark bg-white shadow rounded-circle icon d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>"> <i class="bi bi-arrow-left"></i></a>
           
		<a href="#" class="link-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottomRemove" aria-controls="offcanvasBottomRemove">
		   <span class="mdi mdi-account-check-outline display-1 text-primary" style="margin-top: -5px;"></span>
		</a>
        </div>
		
		
        <div class="align-items-start justify-content-between mb-4">
            <center>
                <h2 class="my-3 fw-bold">Verification Code</h2>
                <p class="text-muted mb-0">Hello,<b> <?php echo htmlspecialchars($fullName); ?>!</b> Please enter the verification code sent to your email: <b><?php echo htmlspecialchars($userEmail); ?></b> to complete the verification process.</p>
            </center>
        </div>



        <form id="verificationForm" method="POST">
            <div class="d-flex gap-1 mb-2">
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
                <b style="margin-top: 20px;"> - </b>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel verification-input" maxlength="1" required>
                </div>
            </div>
		
		<div id="countdown" class="alert alert-info" style="display: none;">
			Please wait <span id="time">05:00</span> before resending the code.
		</div>
		
			<div  id="resendButton"  style="display: none;">
            <p class="text-muted text-center mt-4">Didn't receive it? <a href="#" class="ml-2 text-primary">Resend Code</a>  </p>
			</div>
            <button type="submit" class="btn btn-info rounded-4" style="bottom: 10px; position: absolute; margin-left: 20px; width: 80%;">Verify Code</button>
        </form>
    </div>
	
	
	
	
<div class="offcanvas offcanvas-bottom bg-light" tabindex="-1" id="offcanvasBottomRemove" aria-labelledby="offcanvasBottomRemoveLabel" style="height: 40vh;">
    <div class="offcanvas-body">
        <div class="d-flex align-items-center gap-3 bg-white rounded-4 shadow-sm p-3 mb-3">
            <span class="mdi mdi-account-check-outline display-1 text-primary" style="margin-top: -5px;"></span>
            <div>
                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($fullName); ?></h6>
                <small class="text-muted"><?php echo htmlspecialchars($userEmail); ?></small>
            </div>
        </div>
        <h6 class="text-center text-info m-0">Update E-Mail</h6>
        <form action="update_email.php" method="POST"> <!-- Update to your PHP file -->
            <input type="email" name="email" style="height: 50px; margin-top: 20px;" class="form-control" value="<?php echo htmlspecialchars($userEmail); ?>" required>
    </div>
    
        <div class="offcanvas-footer d-flex gap-3">
            <a href="#" class="btn btn-outline-info btn-lg col" data-bs-dismiss="offcanvas" aria-label="Close">Cancel</a>
            <button type="submit" class="btn btn-info btn-lg col">Update</button>
        </div>
    </form>
</div>


</body>
</html>
<script>
    let countdownTime = 30; // 5 minutes in seconds =300
    let countdownInterval;

    function startCountdown() {
        // Show countdown and hide resend button
        document.getElementById('countdown').style.display = 'block';
        document.getElementById('resendButton').style.display = 'none';

        countdownInterval = setInterval(() => {
            const minutes = Math.floor(countdownTime / 60);
            const seconds = countdownTime % 60;

            document.getElementById('time').textContent = 
                String(minutes).padStart(2, '0') + ":" + String(seconds).padStart(2, '0');

            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdown').style.display = 'none'; // Hide countdown
                document.getElementById('resendButton').style.display = 'block'; // Show resend button
            }

            countdownTime--;
        }, 1000);
    }

    // Function to handle the Resend Code button click
    document.getElementById('resendButton').addEventListener('click', function() {
        // Logic to resend the verification code
        alert("Verification code has been resent!");

        // Reset the countdown
        countdownTime = 30; // Reset to 5 minutes
        startCountdown(); // Start the countdown again
    });

    // Start the countdown when the page loads
    window.onload = startCountdown;
	
	
	
	// JavaScript to remove the success message after 5 seconds
setTimeout(function() {
    var message = document.getElementById("success-message");
    if (message) {
        message.style.display = "none"; // Hide the message
    }
}, 5000); // 5000 milliseconds = 5 seconds
</script>



<script src="<?php echo BASE_URL; ?>assets/user/vender/bootstrap/js/bootstrap.bundle.min.js" type="d2b5a0859927c0396d9ca4b0-text/javascript"></script>

<script src="<?php echo BASE_URL; ?>assets/user/vender/jquery/jquery.min.js" type="d2b5a0859927c0396d9ca4b0-text/javascript"></script>

<script src="<?php echo BASE_URL; ?>assets/user/vender/sidebar/hc-offcanvas-nav.js" type="d2b5a0859927c0396d9ca4b0-text/javascript"></script>

<script src="<?php echo BASE_URL; ?>assets/user/js/script.js" type="d2b5a0859927c0396d9ca4b0-text/javascript"></script>
<script src="<?php echo BASE_URL; ?>assets/user/js/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="d2b5a0859927c0396d9ca4b0-|49" defer></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"8d2af5f26cabb906","version":"2024.10.1","r":1,"serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"dd471ab1978346bbb991feaa79e6ce5c","b":1}' crossorigin="anonymous"></script>
<script>
// Add this before your existing scripts
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.verification-input');

    // Handle input events
    inputs.forEach((input, index) => {
        // Handle keyup for normal input
        input.addEventListener('keyup', function(e) {
            if (this.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            } else if (this.value.length === 0 && e.key === 'Backspace' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste event
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, ''); // Get only numbers
            
            if (pastedData) {
                // Distribute pasted numbers across input fields
                inputs.forEach((input, i) => {
                    if (pastedData[i]) {
                        input.value = pastedData[i];
                        if (i < inputs.length - 1) {
                            inputs[i + 1].focus();
                        }
                    }
                });
            }
        });

        // Only allow numbers
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });
});
</script>
</body>