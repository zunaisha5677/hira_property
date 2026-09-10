<?php
session_start();
include 'config/db_connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $role = $_POST['role'];

    // ---- Fetch user by email + role (prepared statement) ----
    $stmt = mysqli_prepare($conn, 
        "SELECT * FROM users WHERE email = ? AND role = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result && mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);

        // ---- Verify password (supports new hashed passwords) ----
        if(password_verify($password_raw, $user['password'])){

            if($user['is_verified'] == 1){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['first_name'];
                $_SESSION['role'] = $user['role'];

                echo "<script>
                    alert('Login Successful!');
                    window.location='dashboard.php';
                </script>";
            } else {
                $_SESSION['pending_verification_email'] = $email;
                echo "<script>
                    alert('Please verify your email first!');
                    window.location='verify_otp.php';
                </script>";
            }
        } else {
            echo "<script>alert('Invalid Email or Password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid Email or Password!');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hira Property</title>
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
        .back-home{
            position: fixed; top: 25px; right: 28px;
            color: rgba(255,255,255,0.6); text-decoration: none;
            font-size: 12px; z-index: 10;
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 14px; border-radius: 20px; transition: all 0.2s;
        }
        .back-home:hover{ color: #fff; border-color: var(--ember); }

        /* CARD */
        .form-card{
            background: var(--panel);
            width: 400px;
            border-radius: 16px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            position: relative; z-index: 1;
            overflow: hidden;
            border: 1px solid var(--line);
        }

        /* CARD HEADER — slim, navy + ember accent */
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
        .card-header h2 em{
            color: var(--ember);
            font-style: normal;
        }
        .card-header p{
            font-size: 10.5px;
            color: #B9BCCB;
            letter-spacing: 0.3px;
        }

        /* CARD BODY */
        .card-body{ padding: 24px 32px 26px; }
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
        .btn-login{
            width: 100%; padding: 12px;
            background: var(--ember);
            color: #fff; border: none; border-radius: 7px;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            font-size: 13px; font-weight: 600;
            margin-top: 6px; transition: all 0.2s;
        }
        .btn-login:hover{
            background: var(--ember-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232,98,42,0.35);
        }
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
        .register-link{
            text-align: center; font-size: 12px; color: var(--muted);
        }
        .register-link a{
            color: var(--ember); font-weight: 600; text-decoration: none;
        }
        .register-link a:hover{ text-decoration: underline; }

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
                <h2>Hira <em>Property</em></h2>
                <p>Rental Management System — District Gujrat</p>
            </div>
        </div>

        <!-- WHITE BODY -->
        <div class="card-body">
            <form method="POST">
                <div class="field-wrap">
                    <label class="field-label">Email Address</label>
                    <input type="email" name="email"
                           placeholder="your@email.com" required>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password"
                               id="login_password"
                               placeholder="Enter your password" required>
                        <span class="eye-icon"
                              onclick="togglePassword('login_password')">
                            👁️
                        </span>
                    </div>
                </div>

                <div class="field-wrap">
                    <label class="field-label">Login As</label>
                    <select name="role">
                        <option value="tenant">🏡 Tenant</option>
                        <option value="owner">🏠 Property Owner</option>
                        <option value="manager">👔 Property Manager</option>
                        <option value="admin">⚙️ Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn-login">
                    Login →
                </button>
            </form>

            <div class="divider">or</div>

            <div class="register-link">
                Don't have an account?
                <a href="register.php">Register here</a>
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
    </script>

</body>
</html>
