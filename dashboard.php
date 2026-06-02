<?php
session_start();
include 'config/db_connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Hira Rentals</title>
    <style>
        body{
            font-family: Arial;
            background: #f5f5f5;
            margin: 0;
        }
        .navbar{
            background: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
        }
        .navbar h2{
            color: #E8622A;
            margin: 0;
        }
        .container{
            padding: 30px;
        }
        .welcome{
            font-size: 24px;
            margin-bottom: 20px;
        }
        .cards{
            display: grid;
            grid-template-columns: repeat(4,1fr);
            gap: 15px;
        }
        .card{
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .card h3{
            color: #E8622A;
            font-size: 30px;
            margin: 0;
        }
        .logout{
            background: #E8622A;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Hira Rentals</h2>
        <div>
            <span>Welcome, <?php echo $name; ?></span>
            &nbsp;&nbsp;
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            Dashboard — 
            <?php 
                if($role == 'admin') echo "Admin";
                elseif($role == 'manager') echo "Property Manager";
                elseif($role == 'owner') echo "Owner";
                else echo "Tenant";
            ?>
        </div>

        <?php
        // Total Properties
        $prop = mysqli_query($conn, "SELECT COUNT(*) as total FROM properties");
        $prop = mysqli_fetch_assoc($prop);

        // Total Users
        $users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
        $users = mysqli_fetch_assoc($users);

        // Total Bookings
        $bookings = mysqli_query($conn, "SELECT COUNT(*) as total FROM rental_requests");
        $bookings = mysqli_fetch_assoc($bookings);

        // Total Maintenance
        $maintenance = mysqli_query($conn, "SELECT COUNT(*) as total FROM maintenance");
        $maintenance = mysqli_fetch_assoc($maintenance);
        ?>

        <div class="cards">
            <div class="card">
                <p>Total Properties</p>
                <h3><?php echo $prop['total']; ?></h3>
            </div>
            <div class="card">
                <p>Total Users</p>
                <h3><?php echo $users['total']; ?></h3>
            </div>
            <div class="card">
                <p>Total Bookings</p>
                <h3><?php echo $bookings['total']; ?></h3>
            </div>
            <div class="card">
                <p>Maintenance</p>
                <h3><?php echo $maintenance['total']; ?></h3>
            </div>
        </div>
    </div>
</body>
</html>