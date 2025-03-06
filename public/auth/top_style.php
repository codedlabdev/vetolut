<?php
session_start(); // Start session to check for errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fix BASE_DIR to point to the root folder
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Require the db.php file
require_once BASE_DIR . 'lib/db.php'; // This will include db.php from the lib directory

// Fetch the base URL from the database
$stmt = $pdo->prepare("SELECT value FROM app_config WHERE key_name = :key_name");
$stmt->execute(['key_name' => 'base_url']);
$base_url = $stmt->fetchColumn();

// Define BASE_URL constant without trimming
define('BASE_URL', $base_url . ''); // Optionally append a trailing slash

?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required Meta Tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="theme-color" content="#003366" />
		<link rel="manifest" href="<?php echo BASE_URL; ?>manifest.json" />
		<script>
		  //if browser support service worker
		  if ("serviceWorker" in navigator) {
			navigator.serviceWorker.register("sw.js");
		  }
		</script>
		
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <!-- Google Fonts Roboto -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap"
    />
    <!-- MDB -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/login/css/mdb.min.css" />

	 
    <!-- toastr CSS -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <!-- toastr CSS -->

        <title>Vetolut</title>
    </head>
    <body>


	<style>
	
	input, button, select, optgroup, textarea {
    margin: 0;
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    height: 45px;
	}
	
	.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    --mdb-nav-pills-link-active-bg: var(--mdb-primary-bg-subtle);
    --mdb-nav-pills-link-active-color: #096868;
    background-color: #e2ffff;
    color: var(--mdb-nav-pills-link-active-color);
	}
	
	.nav-pills .nav-link {
   
     text-transform: unset;
	}
	
	.justify-content-center {
    text-align: right!important;
	}
	.d-flex {
     display: flex unset!important;
	}
	
	a {
    color: #008080;
    text-decoration: none;
	}
	
	label{
		font-weight:bold;
	}
	
	.input-group>.form-control {
    min-height: calc(2.08rem + 2px);
    height: unset;
    padding-top: .27rem;
    padding-bottom: .27rem;
    transition: all .2s linear;
}
	  .fab {
    font-family: "Font Awesome 6 Brands";
    font-weight: 400;
    font-size: 28px;
}

.i {
    padding: 25px!important;
    border-radius: 25px!important;
}
#togglePassword i {
  font-size: 1.2rem;
  color: #000; /* Adjust the color as per your theme */
}


 
	
	.form-check-input[type=checkbox]:checked {
    background-image: none;
    background-color: #008080;
}
.form-check-input:checked[type=checkbox] {
    --mdb-form-check-bg-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e);
}
.form-check-input[type=checkbox] {
    border-radius: .25rem;
    margin-top: .19em;
    margin-right: 6px;
}

.form-check-input:checked {
    border-color: #008080;
}
	
	:root, [data-mdb-theme=light] {
    --mdb-font-roboto: "Roboto", sans-serif;
    --mdb-bg-opacity: 1;
    --mdb-text-hover-opacity: 0.8;
    --mdb-surface-color: #4f4f4f;
    --mdb-surface-color-rgb: 79, 79, 79;
    --mdb-surface-bg: #fff;
    --mdb-surface-inverted-color: #fff;
    --mdb-surface-inverted-color-rgb: 255, 255, 255;
    --mdb-surface-inverted-bg: #6d6d6d;
    --mdb-divider-color: #f5f5f5;
    --mdb-divider-blurry-color: hsl(0, 0%, 40%);
    --mdb-highlight-bg-color: #eeeeee;
    --mdb-scrollbar-rail-bg: #eeeeee;
    --mdb-scrollbar-thumb-bg: #9e9e9e;
    --mdb-picker-header-bg: #3b71ca;
    --mdb-timepicker-clock-face-bg: var(--mdb-secondary-bg);
    --mdb-sidenav-backdrop-opacity: 0.1;
    --mdb-input-focus-border-color: #008080;
    --mdb-input-focus-label-color: #008080;
    --mdb-form-control-border-color: #bdbdbd;
    --mdb-form-control-label-color: #757575;
    --mdb-form-control-disabled-bg: #e0e0e0;
    --mdb-box-shadow-color: #000;
    --mdb-box-shadow-color-rgb: 0, 0, 0;
    --mdb-stepper-mobile-bg: #fbfbfb;
}
	
	.btn-primary {
    --mdb-btn-bg: #003566;
    --mdb-btn-color: #fff;
    --mdb-btn-box-shadow: 0 4px 9px -4px #386bc0;
    --mdb-btn-hover-bg: #008080;
    --mdb-btn-hover-color: #fff;
    --mdb-btn-focus-bg: #008080;
    --mdb-btn-focus-color: #fff;
    --mdb-btn-active-bg: #9f9d9a;
    --mdb-btn-active-color: #fff;
    --mdb-btn-box-shadow-state: 0 8px 9px -4px rgba(56, 107, 192, 0.3), 0 4px 18px 0 rgba(56, 107, 192, 0.2);
}
.btn {
    --mdb-btn-padding-top: 0.625rem;
    --mdb-btn-padding-bottom: 0.5rem;
    --mdb-btn-border-width: 0;
    --mdb-btn-border-color: none;
    --mdb-btn-border-radius: 0.25rem;
    --mdb-btn-box-shadow: 0 4px 9px -4px rgba(var(--mdb-box-shadow-color-rgb), 0.35);
    --mdb-btn-hover-box-shadow: 0 8px 9px -4px rgba(var(--mdb-box-shadow-color-rgb), 0.15), 0 4px 18px 0 rgba(var(--mdb-box-shadow-color-rgb), 0.1);
    --mdb-btn-focus-box-shadow: 0 8px 9px -4px rgba(var(--mdb-box-shadow-color-rgb), 0.15), 0 4px 18px 0 rgba(var(--mdb-box-shadow-color-rgb), 0.1);
    --mdb-btn-active-box-shadow: 0 8px 9px -4px rgba(var(--mdb-box-shadow-color-rgb), 0.15), 0 4px 18px 0 rgba(var(--mdb-box-shadow-color-rgb), 0.1);
    padding-top: var(--mdb-btn-padding-top);
    padding-bottom: var(--mdb-btn-padding-bottom);
    text-transform: unset;
    vertical-align: bottom;
    border: 0;
    border-radius: var(--mdb-btn-border-radius);
    box-shadow: var(--mdb-btn-box-shadow);
}
	
	
logo{
	width: 80%!important;
    height: 90px!important;
}

.bg-body-tertiary {
    --mdb-bg-opacity: 1;
      background-color: unset !important;
}
	
	</style>