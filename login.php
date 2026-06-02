<?php
session_start();
include 'config/db_connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $query = "SELECT * FROM users 
              WHERE email='$email' 
              AND password='$password' 
              AND role='$role'";
    
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];
        
        echo "<script>
            alert('Login Successful!');
            window.location='dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Invalid Email or Password!');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Hira Rentals</title>
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
            width: 380px;
        }
        h2{
            text-align: center;
            margin-bottom: 20px;
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
        <h2>Log In</h2>
        <form method="POST">
            <input type="email" name="email" 
                   placeholder="your@email.com" required>
            <input type="password" name="password" 
                   placeholder="Password" required>
            <select name="role">
                <option value="tenant">Tenant</option>
                <option value="owner">Owner</option>
                <option value="manager">Property Manager</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Log In</button>
        </form>
        <p>No account? 
           <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>