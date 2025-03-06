<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-selection mb-4">
                    <label>Select payment method:</label>
                    <div class="card-options">
                        <input type="radio" id="mastercard" name="card" value="mastercard" checked />
                        <label for="mastercard">
                            <img src="<?php echo BASE_URL; ?>assets/img/payment/master.jpg" alt="MasterCard" />
                        </label>
                        <input type="radio" id="paypal" name="card" value="paypal" />
                        <label for="paypal">
                            <img src="<?php echo BASE_URL; ?>assets/img/payment/paypal.png" alt="PayPal" />
                        </label>
                    </div>
                </div>

                <!-- Card Details Section -->
                <div class="card-details active" id="cardDetails">
                    <form id="payment-form">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <input type="hidden" name="amount" value="<?php echo $course['price']; ?>">

                        <div class="mb-3">
                            <label for="cardholder">Card holder:</label>
                            <input type="text" id="cardholder" class="form-control"
                                value="<?php
                                        $userName = getLoggedInUserName();
                                        if ($userName) {
                                            echo htmlspecialchars($userName['f_name'] . ' ' . $userName['l_name']);
                                        }
                                        ?>"
                                required>
                            <div id="cardholder-errors" class="text-danger mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <label for="card-element">Credit or debit card:</label>
                            <div id="card-element" class="form-control">
                                <!-- Stripe Element will be inserted here -->
                            </div>
                            <div id="card-errors" class="alert alert-danger mt-3" style="display:;" role="alert"></div>
                            
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted powered-by">Powered by Stripe Gateway</small>
                        </div>
                    </form>
                </div>
                <!-- After Stripe script -->
                <!-- Update the PayPal button section in the payment modal -->
                <!-- Replace the existing PayPal script and button container -->
                <script src="https://www.paypal.com/sdk/js?client-id=AXVuNZMIMUn5DP-oczjT0E1stX342CCVmy7a3jNFtV1pW1JPW_5h6id-mcm058Jf6K_qXxZNyJtrFhn7&currency=USD"></script>

                <!-- Update the PayPal button section -->
                <div class="paypal-button" id="paypalButton">
                <div id="paypal-errors" class="alert alert-danger mt-3" style="display: none;"></div>  
                </div>
                
            </div>
            <div class="modal-footer">
            <div id="cardholder-errors" class="text-danger mt-1"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="processPayment">Process Payment</button>
            </div>
        </div>
    </div>
</div>