<?php
// forget_verify.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start the session to access user details
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/db.php'; // Database connection
require_once BASE_DIR . 'lib/helpers.php'; // Helper functions
require_once BASE_DIR . 'vendors/mail/vendor/autoload.php'; // For PHPMailer


// Fetch base URL from the database
$stmt = $pdo->prepare("SELECT value FROM app_config WHERE key_name = :key_name");
$stmt->execute(['key_name' => 'base_url']);
$base_url = $stmt->fetchColumn();
define('BASE_URL', rtrim($base_url, '/') . '/');

// Get user details from the session
$userEmail = $_SESSION['user_email'];


 
if (!$userEmail) {
    // Redirect to the forgot password form if the email is not set in the session
    header("Location: " . BASE_URL);
    exit();
}

// Fetch the existing verification code from the database
$stmt = $pdo->prepare("SELECT email_vecode FROM users WHERE email = :email");
$stmt->execute(['email' => $userEmail]);
$verificationCode = $stmt->fetchColumn();

if ($verificationCode) {
    // Send the existing verification code via email
    $mailSent = sendVerificationEmail($userEmail, $verificationCode);

    if ($mailSent) {
       echo  '<div class="alert alert-success" id="messages" style="text-align: center;">Verification email sent successfully.</div>';
    } else {
       echo  '<div class="alert alert-danger" id="messages" style="text-align: center;">Failed to send verification email. Please try again later.</div>';
    }
}

// Handle the verification code input from the user
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputCode = implode('', $_POST['verification_code'] ?? []);

    // Fetch the expected verification code from the database
    $stmt = $pdo->prepare("SELECT email_vecode FROM users WHERE email = :email");
    $stmt->execute(['email' => $userEmail]);
    $expectedCode = $stmt->fetchColumn();

    if ($inputCode === $expectedCode) {
        $_SESSION['is_logged_in'] = true;
        header("Location: " . BASE_URL . "auth/passw.php");
        exit();
    } else {
        echo '<div class="alert alert-danger" id="message" style="text-align: center;">Invalid verification code. Please try again.</div>';
    }
}


 

// Function to send verification email
function sendVerificationEmail($toEmail, $verificationCode) {
    $mail = new PHPMailer(true);
   try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'verify@vetolutz.devdigitalz.com'; // SMTP username
    $mail->Password = '@Vetolutz2024'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipient details
    $mail->setFrom('verify@vetolutz.devdigitalz.com', 'Vetolut Verification');
    $mail->addAddress($toEmail);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Vetolut Verification Code';
    $mail->Body = "
    <html>
    <head>
        <title>Your Vetolut Verification Code</title>
    </head>
    <body>
        <p>Hello</p>
        <p>Your Vetolut verification code is: <strong>$verificationCode</strong></p>
        <p>Please enter this code to verify your Vetolut account.</p>
    </body>
    </html>";
    $mail->AltBody = "Hello,\nYour Vetolut verification code is: $verificationCode\nPlease enter this code to verify your Vetolut account.";

    return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }

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
                <label>Enter the verification code sent to <?php echo htmlspecialchars($userEmail); ?>:</label>
            </center>
        </div>



        <form id="verificationForm" method="POST">
            <div class="d-flex gap-1 mb-2">
                <div class="col">
					<input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
				</div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                </div>
                <b style="margin-top: 20px;"> - </b>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                </div>
                <div class="col">
                    <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
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
    var message = document.getElementById("messages");
    if (message) {
        message.style.display = "none"; // Hide the message
    }
}, 5000); // 5000 milliseconds = 5 seconds

	// JavaScript to remove the success message after 5 seconds
setTimeout(function() {
    var message = document.getElementById("message");
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
</body>