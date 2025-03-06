<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = 'verify@vetolutz.devdigitalz.com'; // SMTP username
    $mail->Password = '@Vetolutz2024'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    // Recipient details
    $mail->setFrom('amoskid1996@gmail.com', 'Your Name');
    $mail->addAddress('codedlabdev@gmail.com', 'Recipient Name'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a sample <b>HTML</b> email sent using PHPMailer.';
    $mail->AltBody = 'This is a sample plain-text email body for non-HTML email clients.';

    $mail->send();
    echo 'Message has been sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
