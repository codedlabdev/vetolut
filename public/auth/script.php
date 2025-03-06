 <!-- MDB -->
    <script type="text/javascript" src="assets/login/js/mdb.umd.min.js"></script>
    <!-- Custom scripts -->
    
	<script>
    // Get the login and register password input fields and the toggle icons
    const loginPasswordInput = document.getElementById('loginPassword');
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');

    const registerPasswordInput = document.getElementById('registerPassword');
    const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');

    // Toggle visibility for login password
    toggleLoginPassword.addEventListener('click', function () {
        const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPasswordInput.setAttribute('type', type);

        // Switch between eye and eye-low-vision icons
        if (type === 'text') {
            toggleLoginPassword.classList.remove('far', 'fa-eye');
            toggleLoginPassword.classList.add('fas', 'fa-eye-low-vision');
        } else {
            toggleLoginPassword.classList.remove('fas', 'fa-eye-low-vision');
            toggleLoginPassword.classList.add('far', 'fa-eye');
        }
    });

    // Toggle visibility for register password
    toggleRegisterPassword.addEventListener('click', function () {
        const type = registerPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        registerPasswordInput.setAttribute('type', type);

        // Switch between eye and eye-low-vision icons
        if (type === 'text') {
            toggleRegisterPassword.classList.remove('far', 'fa-eye');
            toggleRegisterPassword.classList.add('fas', 'fa-eye-low-vision');
        } else {
            toggleRegisterPassword.classList.remove('fas', 'fa-eye-low-vision');
            toggleRegisterPassword.classList.add('far', 'fa-eye');
        }
    });
	
// JavaScript to handle country selection
const countryList = document.getElementById('countryList');
const countryInput = document.getElementById('countryInput');
const countryCodeInput = document.getElementById('countryCode');

// Add event listener for dropdown items
countryList.addEventListener('click', function(event) {
  if (event.target.closest('.dropdown-item')) {
    const selectedCountry = event.target.closest('.dropdown-item');
    const countryName = selectedCountry.getAttribute('data-country');
    const countryCode = selectedCountry.getAttribute('data-code');

    // Update the country and code inputs
    countryInput.value = countryName;  // Set country name
    countryCodeInput.value = countryCode; // Set country code

    // Transform label
    const label = document.querySelector(`label[for='countryInput']`);
    label.style.transform = 'translateY(-1rem) translateY(0.1rem) scale(0.8)'; // Transform the label
    label.style.color = ''; // Change label color to match active state
  }
});

// Optional: Reset label transformation if input is cleared
countryInput.addEventListener('input', function() {
  const label = document.querySelector(`label[for='countryInput']`);
  if (countryInput.value.trim() === '') {
    label.style.transform = 'translateY(0) scale(1)'; // Reset label transformation
    label.style.color = ''; // Reset to default color
  }
});


function validateForm() {
    let isValid = true; // Initialize as valid

    // Clear previous error messages
    document.getElementById('nameError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';
    document.getElementById('confirmPasswordError').style.display = 'none';
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('phoneError').style.display = 'none';
    document.getElementById('phoneCodeError').style.display = 'none';
    document.getElementById('countryError').style.display = 'none';

    // Get values from the form
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('emailInput').value.trim();
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const phoneNumber = document.querySelector('input[name="phone"]').value.trim(); // Adjust to your input name
    const phoneCode = document.getElementById('countryCode').value.trim();
    const country = document.getElementById('countryInput').value.trim(); // Adjust to your input name

    /* Validate Full Name (must be two words)
    if (!/^[a-zA-Z]+\s[a-zA-Z]+$/.test(name)) {
        document.getElementById('nameError').innerText = 'Please enter a valid Full Name (first & last name).';
        document.getElementById('nameError').style.display = 'block';
        isValid = false;
    }
	*/

    // Email validation (basic pattern)
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        document.getElementById('emailError').textContent = 'Invalid email format.';
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }

    // Validate Password
    if (password.length < 8) {
        document.getElementById('passwordError').innerText = 'Password must be at least 8 characters long.';
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
    }

    // Validate Confirm Password
    if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').innerText = 'Confirm Password does not match.';
        document.getElementById('confirmPasswordError').style.display = 'block';
        isValid = false;
    }

    /* Phone number validation (check if it's only digits)
    const phonePattern = /^[0-9]+$/; // Only numbers
    if (!phonePattern.test(phoneNumber)) {
        document.getElementById('phoneError').textContent = 'Phone number must contain only digits.';
        document.getElementById('phoneError').style.display = 'block';
        isValid = false;
    }
	*/

    /* Country validation
    if (country === '') {
        document.getElementById('countryError').textContent = 'Country is required.';
        document.getElementById('countryError').style.display = 'block';
        isValid = false;
    }
	*/

    /* Phone code validation
    if (phoneCode === '') {
        document.getElementById('phoneCodeError').textContent = 'Phone code is required.';
        document.getElementById('phoneCodeError').style.display = 'block';
        isValid = false;
    }
	*/

    return isValid; // Prevent form submission if invalid
}


setTimeout(function() {
    var message = document.getElementById("message");
    if (message) {
        message.style.display = "none"; // Hide the message
    }
}, 5000); // 5000 milliseconds = 5 seconds







</script>



	
	</body>
</html>