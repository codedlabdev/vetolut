<?php

//user_forget_pass.php

// Include the main layout file for shared CSS, JS, and other elements
include 'top_style.php';


// Check if there is a error in the session
$p_Error = isset($_SESSION['p_error']) ? $_SESSION['p_error'] : '';
// Clear the error after displaying it
unset($_SESSION['p_error']);

// Check if a success message is set
if (!empty($p_Error)) {
    echo '<div class="alert alert-danger" id="message" style="text-align: center;">' . htmlspecialchars($p_Error) . '</div>';
} 
?>

<div class="container">
<div class="align-items-center bg-body-tertiary mt-5" style="height: 100px;">

<center style="margin-bottom: 30px;"><img src="<?php echo BASE_URL; ?>assets/img/logo/main.png" width="80%" height="70px" /></center>

  <!-- Pills navs -->
<ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="tab-login" data-mdb-pill-init href="#pills-login" role="tab"
      aria-controls="pills-login" aria-selected="true"><b>Forgot Password</b></a>
  </li>
  <li class="nav-item" role="presentation">
    <a  class="nav-link" href="<?php echo BASE_URL; ?>"><b>Sign in</b></a>
  </li>
</ul>
<!-- Pills navs -->

<!-- Pills content -->
<div class="tab-content">
  <div class="tab-pane fade show active mt-5" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
		
		<!-- login form -->
   
		
  <form method="POST" action="<?php echo BASE_URL; ?>auth/forget_process.php">
      
      <!-- Email input -->
      <div class="form-outline mb-4" data-mdb-input-init>
          <i class="fas fa-envelope trailing"></i>
          <input type="text" id="form1" name="f_pass" class="form-control form-icon-trailing" style="border: 1px solid;" required />
          <label class="form-label" for="form1">Email</label>
      </div>

     
    <!-- Submit button -->
    <button type="submit" class="btn btn-primary btn-block mb-4">Send</button>

	</form>

	 
			
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