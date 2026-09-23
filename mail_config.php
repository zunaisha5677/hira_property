<?php
// ============================================
// Hira Property - Email (OTP) Sending Config
// ============================================
// Talks directly to Gmail's SMTP server
// (no external library / PHPMailer needed)

// ---- Gmail credentials ----
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'zunaisha5677@gmail.com');
define('SMTP_PASSWORD', 'oikxvabeqqajnexw'); // App Password (no spaces)
define('SMTP_FROM_EMAIL', 'zunaisha5677@gmail.com');
define('SMTP_FROM_NAME', 'Hira Property');

// Helper: read one response line from the SMTP server
function smtp_read_response($socket){
    $data = "";
    while($str = fgets($socket, 515)){
        $data .= $str;
        if(substr($str, 3, 1) == " ") break;
    }
    return $data;
}

// Sends the OTP verification email
// Returns: true on success, false on failure
function send_otp_email($to_email, $otp_code){
    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if(!$socket){
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    smtp_read_response($socket);

    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);

    fputs($socket, "STARTTLS\r\n");
    smtp_read_response($socket);

    if(!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)){
        fclose($socket);
        return false;
    }

    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);

    fputs($socket, "AUTH LOGIN\r\n");
    smtp_read_response($socket);

    fputs($socket, base64_encode(SMTP_USERNAME) . "\r\n");
    smtp_read_response($socket);

    fputs($socket, base64_encode(SMTP_PASSWORD) . "\r\n");
    $auth_response = smtp_read_response($socket);

    if(substr($auth_response, 0, 3) != "235"){
        error_log("SMTP auth failed: $auth_response");
        fclose($socket);
        return false;
    }

    fputs($socket, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
    smtp_read_response($socket);

    fputs($socket, "RCPT TO: <" . $to_email . ">\r\n");
    smtp_read_response($socket);

    fputs($socket, "DATA\r\n");
    smtp_read_response($socket);

    $subject = "Your Hira Property Verification Code";
    $body  = "Assalam-o-Alaikum,\r\n\r\n";
    $body .= "Your verification code (OTP) is: $otp_code\r\n\r\n";
    $body .= "This code will expire in 10 minutes.\r\n";
    $body .= "If you did not request this, please ignore this email.\r\n\r\n";
    $body .= "- Hira Property Management Services";

    $message  = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $message .= "To: <" . $to_email . ">\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "\r\n" . $body . "\r\n.\r\n";

    fputs($socket, $message);
    $send_response = smtp_read_response($socket);

    fputs($socket, "QUIT\r\n");
    smtp_read_response($socket);
    fclose($socket);

    // "250" means the email was accepted successfully
    return (strpos($send_response, "250") !== false);
}

// General purpose email sender (used for the contact form)
// Sets Reply-To as the visitor's email so replies go straight to them
function send_contact_email($visitor_name, $visitor_email, $subject_line, $message_body){
    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if(!$socket){
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    smtp_read_response($socket);
    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "STARTTLS\r\n");
    smtp_read_response($socket);

    if(!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)){
        fclose($socket);
        return false;
    }

    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "AUTH LOGIN\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_USERNAME) . "\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_PASSWORD) . "\r\n");
    $auth_response = smtp_read_response($socket);

    if(substr($auth_response, 0, 3) != "235"){
        fclose($socket);
        return false;
    }

    $to_email = SMTP_FROM_EMAIL; // goes to the Hira Property team inbox

    fputs($socket, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "RCPT TO: <" . $to_email . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "DATA\r\n");
    smtp_read_response($socket);

    $subject = "Website Contact: " . $subject_line;
    $body  = "New message from Hira Property website contact form:\r\n\r\n";
    $body .= "Name: $visitor_name\r\n";
    $body .= "Email: $visitor_email\r\n";
    $body .= "Subject: $subject_line\r\n\r\n";
    $body .= "Message:\r\n$message_body\r\n";

    $message  = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $message .= "Reply-To: $visitor_name <$visitor_email>\r\n";
    $message .= "To: <" . $to_email . ">\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "\r\n" . $body . "\r\n.\r\n";

    fputs($socket, $message);
    $send_response = smtp_read_response($socket);
    fputs($socket, "QUIT\r\n");
    smtp_read_response($socket);
    fclose($socket);

    return (strpos($send_response, "250") !== false);
}

// Sent to a tenant when their booking (rental request) gets approved by the manager.
// Returns: true on success, false on failure
function send_booking_confirmation_email($to_email, $tenant_first_name, $property_title, $property_location, $property_price){
    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if(!$socket){
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    smtp_read_response($socket);
    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "STARTTLS\r\n");
    smtp_read_response($socket);

    if(!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)){
        fclose($socket);
        return false;
    }

    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "AUTH LOGIN\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_USERNAME) . "\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_PASSWORD) . "\r\n");
    $auth_response = smtp_read_response($socket);

    if(substr($auth_response, 0, 3) != "235"){
        error_log("SMTP auth failed: $auth_response");
        fclose($socket);
        return false;
    }

    fputs($socket, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "RCPT TO: <" . $to_email . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "DATA\r\n");
    smtp_read_response($socket);

    $subject = "Your Booking Request Has Been Approved!";
    $body  = "Assalam-o-Alaikum $tenant_first_name,\r\n\r\n";
    $body .= "Good news! Your booking request has been approved by the property manager.\r\n\r\n";
    $body .= "Property: $property_title\r\n";
    $body .= "Location: $property_location\r\n";
    $body .= "Rent: PKR " . number_format($property_price) . "/month\r\n\r\n";
    $body .= "The manager will now prepare your lease contract. You can check its status\r\n";
    $body .= "anytime under 'My Contract' in your dashboard, and sign it once it's ready.\r\n\r\n";
    $body .= "- Hira Property Management Services";

    $message  = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $message .= "To: <" . $to_email . ">\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "\r\n" . $body . "\r\n.\r\n";

    fputs($socket, $message);
    $send_response = smtp_read_response($socket);

    fputs($socket, "QUIT\r\n");
    smtp_read_response($socket);
    fclose($socket);

    return (strpos($send_response, "250") !== false);
}

// Sent to a tenant right after their rent payment is successfully recorded (PayFast).
// Returns: true on success, false on failure
function send_payment_confirmation_email($to_email, $tenant_first_name, $property_title, $amount_paid, $payment_id){
    $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if(!$socket){
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    smtp_read_response($socket);
    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "STARTTLS\r\n");
    smtp_read_response($socket);

    if(!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)){
        fclose($socket);
        return false;
    }

    fputs($socket, "EHLO localhost\r\n");
    smtp_read_response($socket);
    fputs($socket, "AUTH LOGIN\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_USERNAME) . "\r\n");
    smtp_read_response($socket);
    fputs($socket, base64_encode(SMTP_PASSWORD) . "\r\n");
    $auth_response = smtp_read_response($socket);

    if(substr($auth_response, 0, 3) != "235"){
        error_log("SMTP auth failed: $auth_response");
        fclose($socket);
        return false;
    }

    fputs($socket, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "RCPT TO: <" . $to_email . ">\r\n");
    smtp_read_response($socket);
    fputs($socket, "DATA\r\n");
    smtp_read_response($socket);

    $subject = "Payment Received - Hira Property";
    $body  = "Assalam-o-Alaikum $tenant_first_name,\r\n\r\n";
    $body .= "We have successfully received your rent payment. Here are the details:\r\n\r\n";
    $body .= "Payment ID: #$payment_id\r\n";
    $body .= "Property: $property_title\r\n";
    $body .= "Amount Paid: PKR " . number_format($amount_paid) . "\r\n";
    $body .= "Payment Method: PayFast\r\n";
    $body .= "Status: Paid\r\n\r\n";
    $body .= "Thank you for your timely payment. You can view this in your\r\n";
    $body .= "'Payment History' section anytime from your dashboard.\r\n\r\n";
    $body .= "- Hira Property Management Services";

    $message  = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $message .= "To: <" . $to_email . ">\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "\r\n" . $body . "\r\n.\r\n";

    fputs($socket, $message);
    $send_response = smtp_read_response($socket);

    fputs($socket, "QUIT\r\n");
    smtp_read_response($socket);
    fclose($socket);

    return (strpos($send_response, "250") !== false);
}
?>
