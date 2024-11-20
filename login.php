<?php
session_start();
include("../koneksi.php"); 

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username di database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
        }

        .login-wrapper {
            display: flex;
            width: 800px;
            max-width: 90%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .login-image {
            width: 50%;
            background: url('../image/OIP.jpeg') no-repeat center;
            background-size: cover;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px;
            width: 50%;
        }

        .login-container h2 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 20px;
        }

        .login-container h2 i {
            color: #ffc107;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #ddd;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.7);
            color: #333;
            outline: none;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .login-container input:focus {
            transform: scale(1.02);
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.8);
        }

        .login-container button {
            background: #2193b0;
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background: #6dd5ed;
        }

        .login-container .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 0.9rem;
            color: #fff;
        }

        .login-container .footer a {
            color: #2193b0;
            text-decoration: none;
            font-weight: 600;
        }

        .login-container .footer a:hover {
            text-decoration: underline;
        }

        /* Media query untuk layar kecil */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                width: 95%;
            }

            .login-image {
                width: 100%;
                height: 200px;
            }

            .login-container {
                width: 100%;
                padding: 20px;
            }

            .login-container h2 {
                font-size: 1.5rem;
            }

            .login-container input[type="text"],
            .login-container input[type="password"] {
                padding: 10px;
                font-size: 0.9rem;
            }

            .login-container button {
                padding: 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-image"></div>
        <div class="login-container">
            <h2><i class="fas fa-store"></i> Coba Mart</h2>
            <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
