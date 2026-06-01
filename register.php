<?php
include 'config/db_connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = md5($_POST['password']);

    $query = "INSERT INTO users 
              (first_name, last_name, email, phone, role, password) 
              VALUES 
              ('$first_name','$last_name','$email','$phone','$role','$password')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>
            alert('Registration Successful!');
            window.location='login.php';
        </script>";
    } else {
        echo "<script>alert('Email already exists!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Hira Rentals</title>
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
        .form-box{
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #ddd;
            width: 400px;
        }
        h2{
            text-align: center;
            margin-bottom: 20px;
        }
        .row{
            display: flex;
            gap: 10px;
        }
        input, select{
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button{
            width: 100%;
            padding: 12px;
            background: #E8622A;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        p{
            text-align: center;
            margin-top: 15px;
        }
        a{
            color: #E8622A;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Create Account</h2>
        <form method="POST">
            <div class="row">
                <input type="text" name="first_name" 
                       placeholder="First Name" required>
                <input type="text" name="last_name" 
                       placeholder="Last Name" required>
            </div>
            <input type="email" name="email" 
                   placeholder="your@email.com" required>
            <input type="text" name="phone" 
                   placeholder="+92 300 0000000" required>
            <select name="role">
                <option value="tenant">Tenant</option>
                <option value="owner">Owner</option>
                <option value="manager">Property Manager</option>
                <option value="admin">Admin</option>
            </select>
            <input type="password" name="password" 
                   placeholder="Create a strong password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have account? 
           <a href="login.php">Log In</a>
        </p>
    </div>
</body>
</html>