<?php
	session_set_cookie_params(3600,"/");
	
	//db connection
	include_once('config.php');
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['signup'])) {
			$first_name = $_POST['fname'];
			$last_name = $_POST['lname'];
			$email = $_POST['email'];
			$password = $_POST['pass'];
			$session_id = session_id();
			$email_check = mysqli_query($conn, "SELECT * FROM `user` WHERE email = '$email'");
			if (mysqli_num_rows($email_check) > 0) {
				echo "<script>alert('Email already registered');</script>";
			} else {
				$sql = mysqli_query($conn,"INSERT INTO `user` (`first_name`, `last_name`, `email`, `pass`,`session_id`) VALUES ('$first_name', '$last_name', '$email', '$password', '$session_id')");
				
				if ($sql) {
					$_SESSION['user'] = $email;
					echo "<script>alert('Signup successful');</script>";
					header("Location: index.php");
				} else {
					echo "<script>alert('Signup failed');</script>";
				}
			}
		}
		if(isset($_POST['login'])){	
			$uemail = $_POST['uemail'];
			$upass = $_POST['upass'];
			
			$check = mysqli_query($conn,"SELECT * FROM `user` WHERE `email` = '$uemail' AND `pass` = '$upass';");
			if(mysqli_num_rows($check) > 0){
				$session_id = session_id();
				mysqli_query($conn,"UPDATE `user` SET `session_id`='$session_id' WHERE `email` = '$uemail';");
				$_SESSION['user'] = $uemail;
				header("location:index.php");
			}else{
				echo "<script>alert(Invalid Details)</script>";
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --bg-color: #f4f4f6;
            --text-color: #333;
        }
        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
        }
        .auth-container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            transition: all 0.6s ease;
        }
        .auth-form {
            padding: 40px;
            transition: transform 0.6s ease;
        }
        .form-control {
            border-radius: 25px;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52,152,219,0.25);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .toggle-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            text-decoration: underline;
            margin-top: 15px;
        }
        .toggle-btn:hover {
            color: #2980b9;
        }
        .form-section {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        .form-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <div class="auth-form">
            <div id="signup-section" class="form-section active">
                <h2 class="text-center mb-4">Create Account</h2>
                <form id="signup-form" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name= "fname" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lname" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="pass" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="signup">Sign Up</button>
                    </div>
                    <div class="text-center">
                        <button type="button" class="toggle-btn" id="login-toggle">Already have an account?</button>
                    </div>
                </form>
            </div>

            <div id="login-section" class="form-section">
                <h2 class="text-center mb-4">Library Login</h2>
                <form id="login-form" method="POST">
                    <div class="mb-3">
                        <label class="form-label" >Email address</label>
                        <input type="email" class="form-control" name="uemail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" name="password">Password</label>
                        <input type="password" class="form-control" name="upass" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="login">Login</button>
                    </div>
                    <div class="text-center">
                        <button type="button" class="toggle-btn" id="signup-toggle">Create an account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const signupSection = document.getElementById('signup-section');
            const loginSection = document.getElementById('login-section');
            const loginToggle = document.getElementById('login-toggle');
            const signupToggle = document.getElementById('signup-toggle');

            function switchToLogin() {
                signupSection.classList.remove('active');
                loginSection.classList.add('active');
            }

            function switchToSignup() {
                loginSection.classList.remove('active');
                signupSection.classList.add('active');
            }

            loginToggle.addEventListener('click', switchToLogin);
            signupToggle.addEventListener('click', switchToSignup);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>