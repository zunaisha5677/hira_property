<?php
session_start();
include 'config/db_connection.php';
include 'config/mail_config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Only insert a payment record the FIRST time this page loads after a real
// checkout (i.e. when payments.php set these session values). If the tenant
// refreshes this page, or opens it directly without paying, we don't insert
// again — this avoids duplicate "Paid" rows for the same payment.
if(isset($_SESSION['property_id']) && isset($_SESSION['amount'])){
    $property_id = $_SESSION['property_id'];
    $amount = $_SESSION['amount'];

    $insert_query = "INSERT INTO payments 
              (tenant_id, property_id, amount, status, payment_method) 
              VALUES 
              ('$user_id','$property_id','$amount','Paid','PayFast')";
    mysqli_query($conn, $insert_query);
    $payment_id = mysqli_insert_id($conn);

    // Fetch tenant + property details to send confirmation email
    $info_query = "SELECT u.first_name, u.email, pr.title as property_title
                   FROM users u
                   JOIN properties pr ON pr.id = '$property_id'
                   WHERE u.id = '$user_id'";
    $info_result = mysqli_query($conn, $info_query);

    if($info_result && mysqli_num_rows($info_result) > 0){
        $info = mysqli_fetch_assoc($info_result);
        send_payment_confirmation_email(
            $info['email'],
            $info['first_name'],
            $info['property_title'],
            $amount,
            $payment_id
        );
    }

    unset($_SESSION['property_id']);
    unset($_SESSION['amount']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful - Hira Rentals</title>
    <style>
        body{
            font-family: Arial;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card{
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 400px;
            border: 1px solid #eef2f5;
        }
        .tick{ font-size: 60px; margin-bottom: 20px; }
        h2{ color: #28a745; margin-bottom: 10px; }
        p{ color: #718096; margin-bottom: 30px; }
        .btn{
            display: inline-block;
            padding: 12px 30px;
            background: #E8622A;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }
        .btn:hover{ background: #c94d1a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="tick">✅</div>
        <h2>Payment Successful!</h2>
        <p>Your rent payment has been completed successfully. 
           Click the button below to go back to your dashboard.</p>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>