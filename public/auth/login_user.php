<?php
// Include the main layout file for shared CSS, JS, and other elements
include 'top_style.php';

require BASE_DIR . '/vendors/sso_auth/vendor/autoload.php';

// Facebook App Credentials
use Facebook\Facebook;

$fb = new Facebook([
    'app_id' => '589299403470885', // Replace with your Facebook App ID
    'app_secret' => '367379335a5a0fd35a94171afa835f0e', // Replace with your Facebook App Secret
    'default_graph_version' => 'v2.4',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Optional permissions to request from the user
$fbLoginUrl = $helper->getLoginUrl(BASE_URL . 'public/auth/login_process_facebook.php', $permissions);
//$fbLoginUrl = str_replace('www.facebook.com', 'm.facebook.com', $fbLoginUrl);

// Google App Credentials
$client = new Google\Client;

$client->setHttpClient(new GuzzleHttp\Client([
    'verify' => false, // Disable SSL certificate verification
]));


$client->setClientId("1027379866529-bp21notc4ua98lu391pkn17e1jd0mlsg.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri(BASE_URL . "public/auth/login_process_gmail.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();


// LinkedIn App Credentials
$linkedinClientId = '789ds4evuk3hcv';
$linkedinRedirectUri = BASE_URL . 'public/auth/login_process_linkedin.php';
$linkedinScope = 'openid profile w_member_social email'; // Supported scopes

// Construct the LinkedIn Authorization URL
$linkedinLoginUrl = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
    'response_type' => 'code',
    'client_id' => $linkedinClientId,
    'redirect_uri' => $linkedinRedirectUri,
    'scope' => $linkedinScope,
]);


// Check if there is an error message in the session
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']); // Clear error after displaying it

// Check if error message for Login
 if (!empty($error)) {
    echo '<div class="alert alert-danger" id="message" style="text-align: center;">' .htmlspecialchars($error) . '</div>';

}


// Check if there is a registration error in the session
$registerError = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
// Clear the error after displaying it
unset($_SESSION['register_error']);

// Check if a success message is set
if (!empty($registerError)) {
    echo '<div class="alert alert-danger" id="message" style="text-align: center;">' . htmlspecialchars($registerError) . '</div>';
}

?>





<div class="container">
    <div class="align-items-center bg-body-tertiary mt-5" style="height: 100px;">

        <center style="margin-bottom: 30px;"><img src="<?php echo BASE_URL; ?>assets/img/logo/main.png" width="80%"
                height="70px" /></center>

        <!-- Pills navs -->
        <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-login" data-mdb-pill-init href="#pills-login" role="tab"
                    aria-controls="pills-login" aria-selected="true"><b>Sign in</b></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-register" data-mdb-pill-init href="#pills-register" role="tab"
                    aria-controls="pills-register" aria-selected="false"><b>Sign up</b></a>
            </li>
        </ul>
        <!-- Pills navs -->

        <!-- Pills content -->
        <div class="tab-content">
            <div class="tab-pane fade show active mt-5" id="pills-login" role="tabpanel" aria-labelledby="tab-login">

                <!-- login form -->


                <form method="POST" action="<?php echo BASE_URL; ?>auth/login_process.php">

                    <!-- Email input -->
                    <div class="form-outline mb-4" data-mdb-input-init>
                        <i class="fas fa-envelope trailing"></i>
                        <input type="text" id="form1" name="login" class="form-control form-icon-trailing" required />
                        <label class="form-label" for="form1">Email</label>
                    </div>

                    <!-- Password input with eye toggle -->
                    <div class="form-outline mb-4" data-mdb-input-init>
                        <i class="far fa-eye" id="toggleLoginPassword"
                            style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                        <input type="password" name="password" id="loginPassword"
                            class="form-control form-icon-trailing" required />
                        <label class="form-label" for="loginPassword">Password</label>
                    </div>

                    <!-- Forgot password -->
                    <div class="row mb-4">
                        <div class="col-md-6 justify-content-center">
                            <a href="<?php echo BASE_URL; ?>auth/user_forget_pass.php"><b>Forgot password?</b></a>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary btn-block mb-4">Sign in</button>

                </form>


                <p class="text-center"><b>OR</b></p>

                <div class="text-center mb-3">
                    <p>Sign in with:</p>


                    <button style="background: #ea4335;" type="button" onclick="window.location.href='<?= $url ?>'"
                        data-mdb-button-init data-mdb-ripple-init class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-google"></i>
                    </button>

                    <button style="background: #1877f2;" type="button" onclick="window.location.href='<?php echo $fbLoginUrl; ?>'"
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-facebook-f"></i>
                    </button>


                    <button style="background: #171a1f;" type="button" data-mdb-button-init data-mdb-ripple-init
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-apple"></i>
                    </button>

                   <button style="background: #379ae6;" type="button" onclick="window.location.href='<?= $linkedinLoginUrl ?>'"
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-linkedin"></i>
                    </button>

                  <div style="margin-top: 50px;bottom: 20px;position: relative;">
                  <p>
                       <a href=""><b>About Us</b></a> |
                       <a href="<?php echo BASE_URL; ?>pages/privacy.php"><b>Privacy</b></a> |
                       <a href="<?php echo BASE_URL; ?>pages/terms.php"><b>Terms</b></a> |
                       <a href="<?php echo BASE_URL; ?>pages/contact.php"><b>Contact Us</b></a>


                   </p>
                 </div>
                </div>
            </div>


            <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
                <form method="POST" action="<?php echo BASE_URL; ?>auth/register_process.php"
                    onsubmit="return validateForm()">

                    <div class="text-center mb-3">


                        <!-- Name input -->
                        <span id="nameError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>
                            <i class="fas fa-user trailing"></i>
                            <input type="text" id="name" name="f_name" class="form-control form-icon-trailing"
                                required />
                            <label class="form-label" for="name">First Name</label>
                        </div>

                        <!-- Name input -->
                        <span id="nameError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>
                            <i class="fas fa-user trailing"></i>
                            <input type="text" id="l_name" name="l_name" class="form-control form-icon-trailing"
                                required />
                            <label class="form-label" for="name">Last Name</label>
                        </div>


                        <!-- Email input -->
                        <span id="emailError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>
                            <i class="fas fa-envelope trailing"></i>
                            <input type="email" id="emailInput" name="email" class="form-control form-icon-trailing"
                                required />
                            <label class="form-label" for="emailInput">Email</label>
                        </div>


                        <!-- Password input with eye toggle -->
                        <span id="passwordError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>
                            <i class="far fa-eye" id="toggleRegisterPassword"
                                style="cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="password" name="password" id="registerPassword"
                                class="form-control form-icon-trailing" required />
                            <label class="form-label" for="registerPassword">Password</label>
                        </div>


                        <!-- Password confirmation input -->
                        <span id="confirmPasswordError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>
                            <i class="fas fa-eye trailing"></i>
                            <input type="password" name="password_confirmation" required id="confirmPassword"
                                class="form-control form-icon-trailing" />
                            <label class="form-label" for="confirmPassword">Confirm Password</label>
                        </div>




                        <span id="phoneCodeError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="input-group mb-3">

                            <!-- Dropdown button for countries -->
                            <button id="dropdownCountry" class="btn btn-primary dropdown-toggle" type="button"
                                data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                                Select Country
                            </button>

                            <!-- Dropdown menu for countries with scrolling enabled -->
                           <?php include 'country_list.php';?>
                            <!-- Phone code input (populated by selected country code) -->

                            <input id="countryCode" style="margin-right: 10px; width: 20%;" name="phone_code"
                                type="text" class="form-control form-icon-trailing" placeholder="+1"
                                aria-label="Phone code input" readonly required />

                            <!-- Phone number input -->
                            <span id="phoneError" class="text-danger" style="display:none; font-weight: bold;"></span>
                            <input style="width: 50%;" type="number" class="form-control form-icon-trailing"
                                name="phone" placeholder="Phone Number" aria-label="Phone number input" />

                        </div>

                        <!-- Country input -->
                        <span id="countryError" class="text-danger"
                            style="display:none; font-weight: bold; text-align: justify;"></span>
                        <div class="form-outline mb-4" data-mdb-input-init>

                            <input type="text" id="countryInput" name="country" class="form-control form-icon-trailing"
                                placeholder="Selected Country" readonly required />
                            <label class="form-label" for="countryInput">Country</label>
                        </div>

                        <!-- Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input me-2" type="checkbox" value="" id="registerCheck" checked
                                aria-describedby="registerCheckHelpText" required />
                            <label class="form-check-label" for="registerCheck" style=" margin-left: -86px;">
                                I have read and agree to the terms
                            </label>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-3">Sign up</button>

                </form>

                <p class="text-center"><b>OR</b></p>

                <div class="text-center mb-3">
                    <p>Sign up with:</p>

                    <button style="background: #ea4335;" type="button" onclick="window.location.href='<?= $url ?>'"
                        data-mdb-button-init data-mdb-ripple-init class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-google"></i>
                    </button>



                     <button style="background: #1877f2;" type="button" onclick="window.location.href='<?php echo $fbLoginUrl; ?>'"
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-facebook-f"></i>
                    </button>

                    <button style="background: #171a1f;" type="button" data-mdb-button-init data-mdb-ripple-init
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-apple"></i>
                    </button>

                    <button style="background: #379ae6;" type="button" data-mdb-button-init data-mdb-ripple-init
                        class="btn i btn-primary btn-floating mx-1">
                        <i class="fab fa-linkedin"></i>
                    </button>
                    <div style="margin-top: 50px;">
                   <p>
                       <a href=""><b>About Us</b></a> |
                        <a href="<?php echo BASE_URL; ?>pages/privacy.php"><b>Privacy</b></a> |
                        <a href="<?php echo BASE_URL; ?>pages/terms.php"><b>Terms</b></a> |
                        <a href="<?php echo BASE_URL; ?>pages/contact.php"><b>Contact Us</b></a>

                   </p>
                 </div>
                </div>


                <!-- Check if an error message is set -->
                <?php if (isset($_SESSION['register_error'])): ?>
                <div class="alert alert-danger" id="message" style="text-align: center;">
                    <?php 
            echo htmlspecialchars($_SESSION['register_error']); 
            unset($_SESSION['register_error']); // Clear the message after displaying it
            ?>
                </div>
                <?php endif; ?>

                <div style="margin-top:150px">
                    <p> <br> </p>
                </div>

            </div>


        </div>

    </div>
    <!-- Pills content -->
</div>
</div>

<?php
// Include the main layout file for shared CSS, JS, and other elements
include 'script.php';
?>