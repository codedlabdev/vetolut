<?php
// Include the main layout file for shared CSS, JS, and other elements
include 'top_style.php';

require_once BASE_DIR . 'lib/db.php'; // Database connection
require_once BASE_DIR . 'lib/helpers.php'; // Helper functions

$userEmail = $_SESSION['user_email'];

if (!$userEmail) {
    // Redirect to the forgot password form if the email is not set in the session
    header("Location: " . BASE_URL);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';

    // Server-side validation
    if (strlen($newPassword) > 8) {
        echo '<div class="alert alert-danger" id="message" style="text-align: center;">Password must not exceed 8 characters.</div>';
    } elseif ($newPassword !== $confirmPassword) {
        echo '<div class="alert alert-danger" id="message" style="text-align: center;">Passwords do not match.</div>';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        if ($stmt->execute(['password' => $hashedPassword, 'email' => $userEmail])) {
            echo '<div class="alert alert-success" style="text-align: center;">Password updated successfully. Please Login...</div>';

            // Redirect after a delay
            echo "<script>
                    setTimeout(function() {
                        window.location.href = '" . BASE_URL . "';
                    }, 2000);
                  </script>";
        } else {
            echo '<div class="alert alert-danger" id="message" style="text-align: center;">Error updating password. Please try again.</div>';
        }
    }
}
?>
 <!-- Alert div for displaying client-side validation errors -->
  <div id="clientError" class="alert alert-danger"  style="display: none; text-align: center;"></div>
		
<div class="container">
    <div class="align-items-center bg-body-tertiary mt-5" style="height: 100px;">
        <center style="margin-bottom: 30px;">
            <img src="<?php echo BASE_URL; ?>assets/img/logo/main.png" width="70" height="70" />
        </center>

        <!-- Pills navs -->
        <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active"><b>Reset Password</b></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="<?php echo BASE_URL; ?>"><b>Sign in</b></a>
            </li>
        </ul>

        <!-- Reset Password Form -->
        <form method="POST" action="" onsubmit="return handleFormSubmit(event)">
            <!-- Password input with eye toggle -->
            <label class="form-label" for="registerPassword">Password</label>
            <div class="form-outline mb-4" data-mdb-input-init>
                <i class="far fa-eye" id="toggleRegisterPassword" style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="password" name="new_password" id="registerPassword" placeholder="Password" class="form-control form-icon-trailing" required />
            </div>

            <!-- Password confirmation input -->
            <label class="form-label" for="confirmPassword">Confirm Password</label>
            <div class="form-outline mb-4" data-mdb-input-init>
                <i class="fas fa-eye trailing"></i>
                <input type="password" name="password_confirmation" placeholder="******" id="confirmPassword" class="form-control form-icon-trailing" required />
            </div>
			  <i id="toggleLoginPassword"></i>

            <!-- Submit button with loading spinner -->
            <button type="submit" id="submitButton" class="btn btn-primary btn-block mb-4">
                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span id="buttonText">Update Password</span>
            </button>
        </form>
    </div>
</div>

<script>
function handleFormSubmit(event) {
    const registerPassword = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorDiv = document.getElementById('clientError');

    // Reset the error message display
    errorDiv.style.display = 'none';
    errorDiv.innerText = '';

    // Check for password length
    if (registerPassword.length < 8) {
        errorDiv.innerText = "Password must be at least 8 characters long.";
        errorDiv.style.display = 'block';
        return false; // Prevent form submission
    }

    // Check if passwords match
    if (registerPassword !== confirmPassword) {
        errorDiv.innerText = "Passwords do not match.";
        errorDiv.style.display = 'block';
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}
 
</script>


<style>
.form-outline .form-control {
    border: solid 1px!important;
}
</style>

<?php
// Include the main layout file for shared CSS, JS, and other elements
include 'script.php';
?>