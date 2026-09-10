<?php
session_start();
include 'config/db_connection.php';
include 'config/mail_config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $role       = $_POST['role'];
    $password_raw = $_POST['password'];

    if (!preg_match('/[A-Z]/', $password_raw) || 
        !preg_match('/[a-z]/', $password_raw) || 
        !preg_match('/[0-9]/', $password_raw) || 
        strlen($password_raw) < 8) {
        echo "<script>alert('Weak password! At least 8 characters, one uppercase letter and one number required.'); window.history.back();</script>";
        exit();
    }

    if($role != 'tenant' && $role != 'owner'){
        echo "<script>alert('Invalid role selected!'); window.history.back();</script>";
        exit();
    }

    // ---- Duplicate email check (prepared statement) ----
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if(mysqli_stmt_num_rows($check_stmt) > 0){
        echo "<script>alert('This email is already registered! Please use a different email or login.'); window.history.back();</script>";
        exit();
    }
    mysqli_stmt_close($check_stmt);

    // ---- Secure password hash ----
    $password = password_hash($password_raw, PASSWORD_DEFAULT);
    $otp_code = rand(100000, 999999);
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // ---- Insert (prepared statement) ----
    $stmt = mysqli_prepare($conn, "INSERT INTO users 
              (first_name, last_name, email, phone, role, password, otp_code, otp_expiry, is_verified) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");

    if(!$stmt){
        echo "<script>alert('Registration failed: " . mysqli_error($conn) . "');</script>";
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt, "ssssssss",
        $first_name, $last_name, $email, $phone, $role, $password, $otp_code, $otp_expiry
    );

    if(mysqli_stmt_execute($stmt)){
        $email_sent = send_otp_email($email, $otp_code);
        $_SESSION['pending_verification_email'] = $email;

        if($email_sent){
            echo "<script>
                alert('Registration successful! Please check your email for OTP.');
                window.location='verify_otp.php';
            </script>";
        } else {
            echo "<script>
                alert('Account created! Email could not be sent. Use Resend OTP on next page.');
                window.location='verify_otp.php';
            </script>";
        }
    } else {
        echo "<script>alert('Registration failed: " . mysqli_stmt_error($stmt) . "');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hira Property</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy:#1B2340;
            --navy-deep:#12162A;
            --paper:#FAF8F4;
            --panel:#FFFFFF;
            --ember:#E8622A;
            --ember-dark:#c94d1a;
            --text:#221F1C;
            --muted:#726C63;
            --line:#EDE8DE;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body{
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            padding: 30px 0;
        }
        body::after{
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(18,22,42,0.55) 0%, rgba(18,22,42,0.88) 100%);
            z-index: 0;
        }
        .top-brand{
            position: fixed;
            top: 22px; left: 30px;
            z-index: 10;
            display: flex; align-items: center; gap: 9px;
            text-decoration: none;
        }
        .top-brand img{
            height: 28px; width: 28px;
            object-fit: cover; border-radius: 6px;
        }
        .top-brand span{
            font-family: 'Playfair Display', serif;
            font-size: 16px; font-weight: 700; color: #fff;
        }
        .top-brand span em{ color: var(--ember); font-style: normal; }

        /* Back to Home - dark version */
        .back-home{
            position: fixed; top: 25px; right: 28px;
            color: #fff; text-decoration: none;
            font-size: 12px; font-weight: 600; z-index: 10;
            background: var(--navy-deep);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 7px 16px; border-radius: 20px; transition: all 0.2s;
        }
        .back-home:hover{ background: var(--ember); border-color: var(--ember); }

        /* CARD */
        .form-card{
            background: var(--panel);
            width: 440px;
            border-radius: 16px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            position: relative; z-index: 1;
            overflow: hidden;
            border: 1px solid var(--line);
        }

        /* CARD HEADER - slim, navy + ember accent */
        .card-header{
            background: var(--navy);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 3px solid var(--ember);
        }
        .header-logo{
            width: 36px; height: 36px;
            flex-shrink: 0;
            background: rgba(232,98,42,0.18);
            border: 1.5px solid rgba(232,98,42,0.5);
            border-radius: 50%;
            display: flex; align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .header-logo img{
            width: 100%; height: 100%;
            object-fit: cover; border-radius: 50%;
        }
        .header-text{ text-align: left; }
        .card-header h2{
            font-family: 'Playfair Display', serif;
            font-size: 17px; font-weight: 700;
            color: #fff; line-height: 1.2;
        }
        .card-header h2 em{ color: var(--ember); font-style: normal; }
        .card-header p{
            font-size: 10.5px; color: #B9BCCB; letter-spacing: 0.3px;
        }

        /* CARD BODY */
        .card-body{ padding: 24px 32px 26px; max-height: 78vh; overflow-y: auto; }
        .row{ display: flex; gap: 12px; }
        .row .field-wrap{ flex: 1; }
        .field-wrap{ margin-bottom: 13px; }
        .field-label{
            font-size: 11px; font-weight: 600;
            color: var(--text); margin-bottom: 5px; display: block;
        }
        input, select{
            width: 100%; padding: 10px 13px;
            border: 1px solid var(--line); border-radius: 7px;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            color: var(--text); background: var(--paper);
            transition: border-color 0.2s; box-sizing: border-box;
        }
        input:focus, select:focus{
            outline: none; border-color: var(--ember); background: #fff;
        }
        .password-wrapper{ position: relative; }
        .password-wrapper input{ padding-right: 40px; }
        .eye-icon{
            position: absolute; right: 11px; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            font-size: 16px; color: #999; user-select: none;
        }
        #password-err{
            font-size: 11.5px; display: block;
            margin-top: 4px; font-weight: 600;
        }
        .btn-register{
            width: 100%; padding: 12px;
            background: var(--ember);
            color: #fff; border: none; border-radius: 7px;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            font-size: 13px; font-weight: 600;
            margin-top: 6px; transition: all 0.2s;
        }
        .btn-register:hover{
            background: var(--ember-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232,98,42,0.35);
        }
        .btn-register:disabled{ background: #ccc; cursor: not-allowed; transform: none; box-shadow: none; }
        .divider{
            text-align: center; color: #ccc;
            font-size: 11px; margin: 14px 0; position: relative;
        }
        .divider::before, .divider::after{
            content: ''; position: absolute; top: 50%;
            width: 40%; height: 1px; background: var(--line);
        }
        .divider::before{ left: 0; }
        .divider::after{ right: 0; }
        .login-link{
            text-align: center; font-size: 12px; color: var(--muted);
        }
        .login-link a{
            color: var(--ember); font-weight: 600; text-decoration: none;
        }
        .login-link a:hover{ text-decoration: underline; }

        /* CARD FOOTER */
        .card-footer{
            background: var(--paper);
            border-top: 1px solid var(--line);
            padding: 13px 32px;
            display: flex;
        }
        .feat-item{
            flex: 1; text-align: center;
            padding: 0 4px;
            border-right: 1px solid var(--line);
        }
        .feat-item:last-child{ border-right: none; }
        .feat-icon{ font-size: 15px; margin-bottom: 3px; }
        .feat-text{ font-size: 9.5px; color: var(--muted); line-height: 1.4; }

        @media(max-width: 460px){
            .form-card{ width: 92%; }
            .card-body{ padding: 20px; }
            .card-header{ padding: 14px 20px; }
        }
    </style>
</head>
<body>

    <a href="index.php" class="top-brand">
        <img src="assets/logo.png" alt="Hira Property" onerror="if(!this.dataset.fb){this.dataset.fb=1;this.src='assets/logo.png.jpeg';}else{this.style.display='none';}">
        <span>Hira <em>Property</em></span>
    </a>

    <a href="index.php" class="back-home">← Back to Home</a>

    <div class="form-card">

        <!-- SLIM NAVY HEADER -->
        <div class="card-header">
            <div class="header-logo">
                <img src="assets/logo.png" alt="Hira Property"
                     onerror="if(!this.dataset.fb){this.dataset.fb=1;this.src='assets/logo.png.jpeg';}else{this.style.display='none';}">
            </div>
            <div class="header-text">
                <h2>Create <em>Account</em></h2>
                <p>Join Hira Property - District Gujrat</p>
            </div>
        </div>

        <!-- WHITE BODY -->
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="field-wrap">
                        <label class="field-label">First Name</label>
                        <input type="text" name="first_name" placeholder="Ali" required>
                    </div>
                    <div class="field-wrap">
                        <label class="field-label">Last Name</label>
                        <input type="text" name="last_name" placeholder="Ahmed" required>
                    </div>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Email Address</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Phone Number</label>
                    <input type="text" name="phone" placeholder="+92 300 0000000" required>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Register As</label>
                    <select name="role">
                        <option value="tenant">🏡 Tenant - Looking to rent</option>
                        <option value="owner">🏠 Owner - Have property to rent</option>
                    </select>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" 
                               placeholder="Create a strong password" 
                               required onkeyup="checkPasswordStrength();">
                        <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
                    </div>
                    <span id="password-err"></span>
                </div>

                <button type="submit" id="register-btn" class="btn-register">
                    Create Account →
                </button>
            </form>

            <div class="divider">or</div>

            <div class="login-link">
                Already have an account? 
                <a href="login.php">Sign In</a>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="card-footer">
            <div class="feat-item">
                <div class="feat-icon">🔒</div>
                <div class="feat-text">Secure</div>
            </div>
            <div class="feat-item">
                <div class="feat-icon">📧</div>
                <div class="feat-text">Verified</div>
            </div>
            <div class="feat-item">
                <div class="feat-icon">🏠</div>
                <div class="feat-text">30+ Props</div>
            </div>
            <div class="feat-item">
                <div class="feat-icon">📍</div>
                <div class="feat-text">Gujrat</div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(id){
        var input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function checkPasswordStrength(){
        var password = document.getElementById('password').value;
        var errorSpan = document.getElementById('password-err');
        var submitBtn = document.getElementById('register-btn');
        var strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if(password.length === 0){
            errorSpan.innerHTML = "";
            submitBtn.disabled = false;
            return;
        }
        if(!strongRegex.test(password)){
            errorSpan.style.color = "red";
            errorSpan.innerHTML = "❌ At least 8 characters, 1 uppercase, 1 lowercase, 1 number.";
            submitBtn.disabled = true;
        } else {
            errorSpan.style.color = "green";
            errorSpan.innerHTML = "✅ Strong Password!";
            submitBtn.disabled = false;
        }
    }
    </script>
</body>
</html>
