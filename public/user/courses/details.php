<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// At the top of the file, after session_start()
// Add this before including other files
$_SERVER['SCRIPT_NAME'] = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);

// Include files using absolute paths
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/case_lists.php';
require_once BASE_DIR . 'lib/user/courses_func.php';
require BASE_DIR . '/vendors/payments/vendor/autoload.php';
// Add this near the top of the file, after other includes
require_once BASE_DIR . 'lib/user/table.php';

// Rest of your code remains the same...
// Get the complete URL including query parameters
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
$full_url = $current_path . ($query_string ? '?' . $query_string : '');

/*
// Debug section
echo '<div style="background: #f8f9fa; padding: 10px; margin: 10px; border: 1px solid #ddd;">';
echo '<h4>Debug Information:</h4>';
echo 'Session Status: ' . session_status() . '<br>';
echo 'Session ID: ' . session_id() . '<br>';
if (isset($_SESSION['user_id'])) {
    echo 'User ID: ' . $_SESSION['user_id'] . '<br>';
}
echo 'Current Path: ' . $full_url . '<br>';
echo '</div>';
*/


// Get course ID from URL parameter
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch course details
$course = getCourseDetails($course_id); // Changed from get_course_details to getCourseDetails


// Redirect if course doesn't exist
if (!$course) {  // The function already checks status and admin_status
    header('Location: ' . BASE_URL . 'public/user/courses/main.php');
    exit;
}

// Add Stripe keys at the top of the file
$stripe_publishable_key = 'pk_test_V7RN02YyVxmHATgBQiAcloqU';

// Continue with the rest of the page if course exists
?>

<link href="<?php echo BASE_URL; ?>public/user/courses/main.css" rel="stylesheet">
<link href="<?php echo BASE_URL; ?>public/user/courses/details.css" rel="stylesheet">
<script src="https://js.stripe.com/v3/"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AbFS50hO2mYc4NNP1HJ8ZEaNs2HL-0QvC_2BbSvfw7Jd13yYU5-JYPp6HVz-SXFNcNbhKapZkMWjjSgv&currency=USD"></script>
<!-- Remove all <style> tags and their contents from the file -->
<div class="container mt-4">
    <!-- Course Header Section -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Course Preview Section -->
            <div class="course-preview position-relative mb-4">
                <div class="preview-image position-relative">
                    <div class="video-container" style="display: none;">
                        <video id="courseVideo" class="w-100" controls preload="metadata">
                            <source src="<?php echo BASE_URL . $course['intro_video']; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="banner-container">
                        <img src="<?php echo !empty($course['banner_image']) ? BASE_URL . $course['banner_image'] : 'https://placehold.co/600x400'; ?>" class="img-fluid w-100" alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php if (!empty($course['intro_video'])): ?>
                            <div class="play-button-overlay position-absolute top-50 start-50 translate-middle">
                                <button class="btn btn-light rounded-circle p-3" id="playButton">
                                    <i class="fas fa-play fs-4"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div>
        <div>
            <h1 class="course-title pe-5"><?php echo htmlspecialchars($course['title']); ?></h1>
            <div class="course-meta pe-3">
                <span class="h3 fw-bold mb-0">
                    <?php echo ($course['price'] == 0) ? 'Free' : '$' . number_format($course['price'], 2); ?>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="d-flex align-items-center">
                <div class="rating me-2">
                    <i class="fas fa-star text-warning"></i>
                    <span class="ms-1 fw-bold">4.5</span>
                </div>
            </div>

        </div>

        <div class="instructor-info d-flex align-items-center mt-3">
            <img src="<?php echo !empty($course['instructor_image']) ? $course['instructor_image'] : 'assets/user/img/noimage.png'; ?>" class="rounded-circle" alt="<?php echo htmlspecialchars($course['instructor_name']); ?>" width="50" height="50">
            <div class="ms-3">
                <p class="mb-0" style="font-size: large;"><?php echo htmlspecialchars($course['instructor_name']); ?></p>
                <span class="ms-1" style="font-size: large;"><small><?php echo htmlspecialchars($course['instructor_profession']); ?></small></span>
            </div>

            <button class="btn btn-primary px-4 py-2" style="right: 20px;position: absolute;">
                <i class="fas fa-shopping-cart me-2"></i>
                <?php echo ($course['price'] == 0) ? 'Enroll Now' : 'Buy Now'; ?>
            </button>

        </div>

    </div>
</div>

</div>
</div>
<!-- Course Navigation -->
<div class="container">
    <div class="course-navigation">
        <ul class="nav nav-tabs nav-fill" id="courseTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-uppercase position-relative" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-uppercase position-relative" id="lessons-tab" data-bs-toggle="tab" data-bs-target="#lessons" type="button" role="tab">
                    Lessons
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-uppercase position-relative" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">
                    Review
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content Sections -->
<div class="container">
    <div class="tab-content p-4" id="courseTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="course-description rounded p-4 mb-4">
                        <h4 class="mb-4 border-bottom pb-2">Description</h4>
                        <div class="description-content">
                            <p>
                                <?php
                                if (!empty($course['description'])) {
                                    echo htmlspecialchars($course['description']);
                                } else {
                                    echo 'No description available.';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    </<link rel="pingback" href="xmlrpc.php" />
                 </div>
            </div>
        </div>

        <!-- Lessons Tab -->
        <div class="tab-pane fade" id="lessons" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="lessons-list">
                        <?php
                        $sections = getCourseSections($course_id);
                        if (!empty($sections)):
                            foreach ($sections as $section):
                        ?>
                                <div class="lesson-section mb-3">
                                    <div class="lesson-section-header d-flex justify-content-between align-items-center p-3" data-bs-toggle="collapse" href="#section<?php echo $section['id']; ?>">
                                        <span class="fw-bold">Sections</span>
                                        <i class="fas fa-chevron-up"></i>
                                    </div>
                                    <div id="section<?php echo $section['id']; ?>" class="collapse show">
                                        <div class="lesson">
                                            <div class="d-flex align-items-start w-100">
                                                <div class="lesson-content flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h5 class="lesson-title mb-2"><?php echo htmlspecialchars($section['title']); ?></h5>

                                                        <i class="fas fa-lock text-muted"></i>

                                                    </div>
                                                    <p class="lesson-description mb-2"><?php echo substr(htmlspecialchars($section['description']), 0, 150); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            endforeach;
                        else:
                            ?>
                            <div class="alert alert-info">
                                No sections available for this course yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Tab -->
        <!-- Review Tab -->
        <div class="tab-pane fade" id="review" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="review-summary mb-4">
                        <h4>Student Reviews</h4>
                        <div class="d-flex align-items-center">
                            <div class="display-4 fw-bold me-3">4.5</div>
                            <div class="stars">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <div class="text-muted mt-1">Based on 1233 reviews</div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Cards -->
                    <div class="review-cards">
                        <div class="review-card mb-4">
                            <div class="d-flex align-items-start">
                                <img src="https://placehold.co/60x60" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;" alt="Jimy Oslin">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Jimy Oslin</h6>
                                        <div class="stars">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mb-2">A day ago</small>
                                    <p class="mb-0">Nostrud excepteur magna id est quis in aliqua consequat. L'exercitation enim eiusmod elit sint laborum</p>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>



<?php
include 'payment_modal.php';
include '../inc/float_nav.php';
?>


<script>
    // Add these variables for the PayPal integration
    const baseUrl = '<?php echo BASE_URL; ?>';
    const courseId = <?php echo $course_id; ?>;
    const coursePrice = '<?php echo $course['price']; ?>';
    const courseTitle = '<?php echo addslashes($course['title']); ?>';
    const courseOwnerId = <?php echo $course['user_id']; ?>;
    const userEmail = '<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>';
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

    $(document).ready(function() {
    const videoContainer = $('.video-container');
    const bannerContainer = $('.banner-container');
    const video = document.getElementById('courseVideo');

    // Play button click handler
    $('#playButton').on('click', function() {
        console.log('Play button clicked');
        videoContainer.show();
        bannerContainer.hide();

        if (video) {
            console.log('Playing video:', video.src);
            video.play()
                .then(() => {
                    console.log('Video playing successfully');
                })
                .catch((error) => {
                    console.error('Video playback error:', error);
                    // Show error message to user
                    const errorElement = document.createElement('div');
                    errorElement.className = 'alert alert-danger';
                    errorElement.textContent = 'Error playing video. Please try again.';
                    videoContainer.prepend(errorElement);
                    // Reset to banner view
                    videoContainer.hide();
                    bannerContainer.show();
                });
        }
    });

    // Add video error handler
    video.addEventListener('error', function(e) {
        console.error('Video error:', e);
        const errorElement = document.createElement('div');
        errorElement.className = 'alert alert-danger';
        errorElement.textContent = 'Error loading video. Please try again.';
        videoContainer.prepend(errorElement);
        videoContainer.hide();
        bannerContainer.show();
    });
});



$(document).ready(function() {
    // Find the "Buy Now" button and add click handler
    $('.btn-primary').on('click', function() {
        $('#paymentModal').modal('show');
    });

    // Update the payment method toggle handler
    $('input[name="card"]').on('change', function() {
        const cardDetails = $("#cardDetails");
        const paypalButton = $("#paypalButton");
        const processPaymentBtn = $("#processPayment");

        if ($(this).val() === 'mastercard') {
            cardDetails.addClass('active');
            paypalButton.removeClass('active');
            processPaymentBtn.show();
        } else {
            cardDetails.removeClass('active');
            paypalButton.addClass('active');
            processPaymentBtn.hide();
        }
    });

    // Add form submit handler for the payment form
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('processPayment');

    submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit'));
    });

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const submitButton = document.getElementById('processPayment');
        const errorElement = document.getElementById('card-errors');

        // Clear previous errors
        errorElement.textContent = '';

        // Validate cardholder name
        const cardholder = document.getElementById('cardholder').value.trim();
        const cardholderError = document.getElementById('cardholder-errors');
        const namePattern = /^[a-zA-Z]+\s+[a-zA-Z]+/;

        if (!cardholder) {
            cardholderError.textContent = 'Full name is required';
            return;
        } else if (!namePattern.test(cardholder)) {
            cardholderError.textContent = 'Please enter your full name (First and Last name)';
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        try {
            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: cardholder
                }
            });

            if (error) {
                throw new Error(error.message);
            }

            // Send payment data to server
            const response = await fetch('<?php echo BASE_URL; ?>lib/user/paypal_payments_func.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod.id,
                    course_id: <?php echo $course_id; ?>,
                    amount: <?php echo $course['price']; ?>,
                    currency: 'usd',
                    description: 'Payment for course: <?php echo addslashes($course['title']); ?>',
                    customer_data: {
                        email: '<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>',
                        name: cardholder,
                        user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>
                    },
                    return_url: '<?php echo BASE_URL; ?>public/user/courses/payment_success.php'
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                window.location.href = '<?php echo BASE_URL; ?>public/user/courses/payment_success.php?id=' + result.transaction_id;
            } else {
                throw new Error(result.message || 'Payment failed');
            }

        } catch (error) {
            errorElement.textContent = error.message;
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Process Payment';
        }
    });
});


// Initialize Stripe
const stripe = Stripe('<?php echo $stripe_publishable_key; ?>');
  const elements = stripe.elements();

  // Custom styling for the card Element
  const style = {
      base: {
          fontSize: '16px',
          color: '#32325d',
          fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
          '::placeholder': {
              color: '#aab7c4'
          }
      },
      invalid: {
          color: '#dc3545',
          iconColor: '#dc3545'
      }
  };

  const cardElement = elements.create('card', {
      style: style,
      hidePostalCode: true,
      classes: {
          base: 'form-control'
      }
  });

  cardElement.mount('#card-element');

  // Handle real-time validation errors
  cardElement.addEventListener('change', function(event) {
      const displayError = document.getElementById('card-errors');
      if (event.error) {
          displayError.textContent = event.error.message;
      } else {
          displayError.textContent = '';
      }
  });

  // Handle form submission
  const form = document.getElementById('payment-form');
  // Replace the form.addEventListener('submit') section with this:
  form.addEventListener('submit', async function(event) {
      event.preventDefault();

      const submitButton = document.getElementById('processPayment');
      const errorElement = document.getElementById('card-errors');

      // Clear previous errors
      errorElement.textContent = '';

      // Validate cardholder name
      const cardholder = document.getElementById('cardholder').value.trim();
      const cardholderError = document.getElementById('cardholder-errors');
      const namePattern = /^[a-zA-Z]+\s+[a-zA-Z]+/;

      if (!cardholder) {
          cardholderError.textContent = 'Full name is required';
          return;
      } else if (!namePattern.test(cardholder)) {
          cardholderError.textContent = 'Please enter your full name (First and Last name)';
          return;
      }

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

      try {
          const { paymentMethod, error } = await stripe.createPaymentMethod({
              type: 'card',
              card: cardElement,
              billing_details: {
                  name: cardholder
              }
          });

          if (error) {
              throw new Error(error.message);
          }

          // Get course price and ensure it's at least 0.50 for USD
          let amount = <?php echo $course['price']; ?>;
          if (amount < 0.50) {
              amount = 0.50; // Minimum charge amount for USD
          }

          // Send payment data to server
          const response = await fetch('<?php echo BASE_URL; ?>lib/user/payments_func.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json'
              },
              body: JSON.stringify({
                  payment_method: paymentMethod.id,
                  course_id: <?php echo $course_id; ?>,
                  amount: amount,
                  currency: 'usd',
                  description: 'Payment for course: <?php echo addslashes($course['title']); ?>',
                  customer_data: {
                      email: '<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>',
                      name: cardholder,
                      user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>
                  },
                  return_url: '<?php echo BASE_URL; ?>public/user/courses/payment_success.php'
              })
          });

          if (!response.ok) {
              const errorText = await response.text();
              console.error('Server response:', errorText);
              throw new Error(`HTTP error! status: ${response.status}`);
          }

          const contentType = response.headers.get('content-type');
          if (!contentType || !contentType.includes('application/json')) {
              const responseText = await response.text();
              console.error('Invalid response format:', responseText);
              throw new Error('Received invalid response from server');
          }

          const result = await response.json();
          console.log('Payment response:', result);

          if (result.success) {
              window.location.href = '<?php echo BASE_URL; ?>public/user/courses/payment_success.php?id=' + result.transaction_id;
          } else if (result.requires_action) {
              // Handle 3D Secure authentication if needed
              const { error, paymentIntent } = await stripe.handleCardAction(
                  result.payment_intent_client_secret
              );

              if (error) {
                  throw new Error(error.message);
              } else {
                  // The card action has been handled
                  // The PaymentIntent can now be confirmed again on the server
                  const secondResponse = await fetch('<?php echo BASE_URL; ?>lib/user/payments_func.php', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json'
                      },
                      body: JSON.stringify({
                          payment_intent_id: paymentIntent.id
                      })
                  });

                  if (!secondResponse.ok) {
                      throw new Error(`HTTP error! status: ${secondResponse.status}`);
                  }

                  const secondResult = await secondResponse.json();

                  if (secondResult.success) {
                      window.location.href = '<?php echo BASE_URL; ?>public/user/courses/payment_success.php?id=' + secondResult.transaction_id;
                  } else {
                      throw new Error(secondResult.message || 'Payment authentication failed');
                  }
              }
          } else {
              throw new Error(result.message || 'Payment failed');
          }

      } catch (error) {
          console.error('Payment error:', error);
          errorElement.textContent = error.message;
      } finally {
          submitButton.disabled = false;
          submitButton.innerHTML = 'Process Payment';
      }
  });
 
  
  


 
function showError(message, elementId = 'card-errors') {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = message ? 'none' : 'none';
    }
}

// Add real-time validation for cardholder name
document.getElementById('cardholder').addEventListener('input', function(e) {
    const cardholderError = document.getElementById('cardholder-errors');
    const namePattern = /^[a-zA-Z]+\s+[a-zA-Z]+/; // Requires at least first and last name

    if (!this.value.trim()) {
        cardholderError.textContent = 'Full name is required';
    } else if (!namePattern.test(this.value.trim())) {
        cardholderError.textContent = 'Please enter your full name (First and Last name)';
    } else {
        cardholderError.textContent = '';
    }
});


$(document).ready(function() {
    $('.lesson-section-header').on('click', function() {
        const icon = $(this).find('i.fas');
        if (icon.hasClass('fa-chevron-up')) {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    // Update the radio button change handler
    $('input[name="card"]').on('change', function() {
        const cardDetails = $("#cardDetails");
        const paypalButton = $("#paypalButton");
        const processPaymentBtn = $("#processPayment");

        if ($(this).val() === 'mastercard') {
            cardDetails.addClass('active');
            paypalButton.removeClass('active');
            processPaymentBtn.show(); // Show process payment button for mastercard
        } else {
            cardDetails.removeClass('active');
            paypalButton.addClass('active');
            processPaymentBtn.hide(); // Hide process payment button for paypal
        }
    });

    // Rest of your JavaScript remains the same
    // ...existing code...
});
 
$(document).ready(function() {
    // Find the "Buy Now" button and add click handler
    $('.btn-primary').on('click', function() {
        $('#paymentModal').modal('show');
    });

    // Add click handlers for payment method radio buttons
    $('input[name="card"]').on('change', function() {
        const cardDetails = $("#cardDetails");
        const paypalButton = $("#paypalButton");

        if ($(this).val() === 'mastercard') {
            cardDetails.addClass('active');
            paypalButton.removeClass('active');
        } else {
            cardDetails.removeClass('active');
            paypalButton.addClass('active');
        }
    });

    $('#processPayment').on('click', function() {
        const cardholder = $("#cardholder").val();
        const cardholderError = document.getElementById('cardholder-errors');
        const cardError = document.getElementById('card-errors');

        // Reset previous errors
        cardholderError.textContent = '';
        cardError.textContent = '';

        // Validate fields
        if (!cardholder.trim()) {
            cardholderError.textContent = 'Full name is required';
            return;
        }

        if ($("#mastercard").prop('checked')) {
            form.dispatchEvent(new Event('submit'));
        }
    });

    // Replace alert in PayPal function
    function payWithPayPal() {
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = 'PayPal integration coming soon';
    }
});

function payWithPayPal() {
    const errorElement = document.getElementById('card-errors');
    errorElement.textContent = 'PayPal integration coming soon';
}



// Replace the existing payWithPayPal function with this:
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: coursePrice
                },
                description: courseTitle
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Send the payment details to your server
            return fetch('<?php echo BASE_URL; ?>lib/user/paypal_payments_func.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_type: 'paypal',
                    order_id: data.orderID,
                    course_id: courseId,
                    course_owner_id: courseOwnerId,
                    user_id: userId,
                    payment_details: {
                        purchase_units: [{
                            amount: {
                                value: coursePrice
                            }
                        }]
                    }
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = baseUrl + 'public/user/courses/payment_success.php?id=' + result.transaction_id;
                } else {
                    const paypalError = document.getElementById('paypal-errors');
                    paypalError.textContent = result.message || 'Payment failed';
                    paypalError.style.display = 'block';
                }
            });
        });
    },
    onError: function(err) {
        console.error('PayPal Error:', err);
        const paypalError = document.getElementById('paypal-errors');
        paypalError.textContent = 'PayPal payment failed. Please try again.';
        paypalError.style.display = 'block';
    }
}).render('#paypalButton');

</script>
<script src="<?php echo BASE_URL; ?>public/user/courses/details.js"></script>

