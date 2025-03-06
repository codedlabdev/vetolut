// PayPal Button Integration
function initializePayPalButton() {
    // Clear existing buttons
    const container = document.getElementById('paypal-button-container');
    if (!container) {
        console.error('PayPal container not found');
        return;
    }
    container.innerHTML = '<div class="text-center">Loading PayPal...</div>';

    // Remove existing PayPal scripts
    document.querySelectorAll('script[src*="paypal.com/sdk/js"]').forEach(script => script.remove());

    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        // Use the correct format for the client ID
        script.src = 'https://www.paypal.com/sdk/js' +
            '?client-id=AXVuNZMIMUn5DP-oczjT0E1stX342CCVmy7a3jNFtV1pW1JPW_5h6id-mcm058Jf6K_qXxZNyJtrFhn7' +
            '&currency=USD' +
            '&intent=capture' +
            '&disable-funding=credit,card';
        
        script.async = true;

        script.onload = () => {
            // Add a small delay to ensure PayPal is fully loaded
            setTimeout(() => {
                if (window.paypal) {
                    resolve(container);
                } else {
                    reject(new Error('PayPal SDK not loaded'));
                }
            }, 500);
        };
        
        script.onerror = () => {
            console.error('Failed to load PayPal script');
            reject(new Error('Failed to load PayPal SDK'));
        };
        
        document.body.appendChild(script);
    })
    .then(container => {
        return window.paypal.Buttons({
            fundingSource: window.paypal.FUNDING.PAYPAL,
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: coursePrice,
                            currency_code: 'USD'
                        },
                        description: courseTitle
                    }]
                });
            },
            onApprove: function(data, actions) {
                container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Processing payment...</p></div>';
                return actions.order.capture()
                    .then(details => {
                        return fetch(baseUrl + 'lib/user/paypal_payments_func.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                course_id: courseId,
                                course_owner_id: courseOwnerId,
                                paymentDetails: details,
                                customer_data: {
                                    email: userEmail,
                                    user_id: userId
                                }
                            })
                        });
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            window.location.href = baseUrl + 'public/user/courses/payment_success.php?id=' + result.transaction_id;
                        } else {
                            throw new Error(result.message || 'Payment failed');
                        }
                    });
            }
        }).render(container);
    })
    .catch(error => {
        console.error('PayPal Error:', error);
        container.innerHTML = '<div class="alert alert-danger">Failed to initialize PayPal. Please refresh and try again.</div>';
    });
}

// Update the script loading in your details.php
function togglePaymentMethod() {
    const cardDetails = document.getElementById('cardDetails');
    const processPaymentBtn = document.getElementById('processPayment');
    
    if (document.getElementById('mastercard').checked) {
        cardDetails.classList.add('active');
        processPaymentBtn.style.display = 'block';
    } else {
        cardDetails.classList.remove('active');
        processPaymentBtn.style.display = 'none';
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add payment method change listener
    const paymentMethods = document.querySelectorAll('input[name="card"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', togglePaymentMethod);
    });

    // Buy Now button handler
    const buyButton = document.querySelector('.btn-primary');
    if (buyButton) {
        buyButton.addEventListener('click', function() {
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
        });
    }
});