<?php
    session_set_cookie_params(3600,"/");
    
    include_once('config.php');
    
    $messages = [];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['signup'])) {
            $first_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['fname']), ENT_QUOTES, 'UTF-8');
            $last_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['lname']), ENT_QUOTES, 'UTF-8');
            $username = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['username']), ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['pass']), ENT_QUOTES, 'UTF-8');
            
            $validation = true;
            
            if(!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)){
                $messages[] = ['type' => 'danger', 'text' => 'Please enter a valid email address'];
                $validation = false;
            }
            if(!preg_match('/^[A-Za-z\s]+$/', $first_name)){
                $messages[] = ['type' => 'danger', 'text' => 'First name should only contain letters'];
                $validation = false;
            }
            if(!preg_match('/^[A-Za-z\s]+$/', $last_name)){
                $messages[] = ['type' => 'danger', 'text' => 'Last name should only contain letters'];
                $validation = false;
            }
            if(!preg_match('/^[A-Za-z][A-Za-z0-9\.\-\_\s]{2,19}$/', $username)){
                $messages[] = ['type' => 'danger', 'text' => 'Invalid username format'];
                $validation = false;
            }
            if(strlen($password) < 6){
                $messages[] = ['type' => 'danger', 'text' => 'Password should be at least 6 characters'];
                $validation = false;
            }
            
            if($validation == true){
                $session_id = session_id();
                
                $email_check_query = sprintf("SELECT * FROM `user` WHERE email = '%s' OR username = '%s'", 
                    mysqli_real_escape_string($conn, $email),
                    mysqli_real_escape_string($conn, $username)
                );
                
                $email_check = mysqli_query($conn, $email_check_query);
                
                if (mysqli_num_rows($email_check) > 0) {
                    $messages[] = ['type' => 'danger', 'text' => 'Email or username already registered'];
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $insert_query = sprintf("INSERT INTO `user` (`first_name`, `last_name`, `username`, `email`, `pass`, `session_id`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                        mysqli_real_escape_string($conn, $first_name),
                        mysqli_real_escape_string($conn, $last_name),
                        mysqli_real_escape_string($conn, $username),
                        mysqli_real_escape_string($conn, $email),
                        mysqli_real_escape_string($conn, $hashed_password),
                        mysqli_real_escape_string($conn, $session_id)
                    );
                    
                    $sql = mysqli_query($conn, $insert_query);
                    
                    if ($sql) {
                        $_SESSION['user'] = $email;
                        $messages[] = ['type' => 'success', 'text' => 'Signup successful'];
                        header("Location:index.php");
                        exit();
                    } else {
                        $messages[] = ['type' => 'danger', 'text' => 'Signup failed'];
                    }
                }
            }
        }
        if(isset($_POST['login'])){    
            $uemail = filter_var($_POST['uemail'], FILTER_SANITIZE_EMAIL);
            $upass = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['upass']), ENT_QUOTES, 'UTF-8');
            
            $login_valid = true;
            
            if(!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $uemail)){
                $messages[] = ['type' => 'danger', 'text' => 'Please enter a valid email address'];
                $login_valid = false;
            }
            
            if($login_valid == true){
                $check_query = sprintf("SELECT * FROM `user` WHERE `email` = '%s'",
                    mysqli_real_escape_string($conn, $uemail)
                );
                
                $check = mysqli_query($conn, $check_query);
                
                if(mysqli_num_rows($check) > 0){
                    $user = mysqli_fetch_assoc($check);
                    if(password_verify($upass, $user['pass'])) {
                        $session_id = session_id();
                        
                        $update_query = sprintf("UPDATE `user` SET `session_id`= '%s' WHERE `email` = '%s'",
                            mysqli_real_escape_string($conn, $session_id),
                            mysqli_real_escape_string($conn, $uemail)
                        );
                        
                        mysqli_query($conn, $update_query);
                        
                        $_SESSION['user'] = $uemail;
                        header("location:index.php");
                        exit();
                    } else {
                        $messages[] = ['type' => 'danger', 'text' => 'Invalid login details'];
                    }
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'Invalid login details'];
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribea</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="uploads/scribea title logo.png" type="image/icon type">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #f72585;
            --light-bg: #f8f9fa;
            --dark-text: #2b2d42;
            --light-text: #8d99ae;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark-text);
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            line-height: 1.7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
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
        
        .auth-container::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
            z-index: 0;
        }
        
        .auth-container::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
            z-index: 0;
        }
        
        .auth-form {
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        
        .auth-title {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            color: var(--dark-text);
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .auth-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            border: 1px solid #e1e5eb;
            background-color: var(--light-bg);
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
            background-color: white;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .toggle-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            margin-top: 1.5rem;
            transition: color 0.3s;
            display: block;
            width: 100%;
            text-align: center;
        }
        
        .toggle-btn:hover {
            color: var(--secondary-color);
            text-decoration: underline;
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
        
        .input-group-text {
            background-color: var(--primary-color);
            border: none;
            color: white;
            border-radius: 0 50px 50px 0;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            color: var(--light-text);
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 1rem;
        }
        
		.toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .toast {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            min-width: 300px;
        }

        .toast-header {
            border-radius: 10px 10px 0 0;
            font-weight: 500;
        }

        .toast-body {
            padding: 15px;
            font-size: 0.95rem;
        }
		
        @media (max-width: 576px) {
            .auth-form {
                padding: 30px 20px;
            }
            
            .auth-title {
                font-size: 1.8rem;
            }
        }
        
		
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-form {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .logo i {
            font-size: 2.5rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
	<!-- Toast Container -->
    <div class="toast-container">
        <?php foreach ($messages as $message): ?>
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header bg-<?php echo $message['type']; ?> text-white">
                    <strong class="me-auto"><?php echo $message['type'] === 'success' ? 'Success' : 'Error'; ?></strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo $message['text']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
	
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="auth-container animate-form">
                    <div class="auth-form">
                        <div id="signup-section" class="form-section active">
                            <div class="logo">
                                <i class="bi bi-book"></i>
                            </div>
                            <h2 class="auth-title">Create Your Account</h2>
                            <form id="signup-form" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <div class="mb-3 position-relative">
                                            <input type="text" class="form-control" name="fname" required>
                                            <i class="bi bi-person input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <div class="mb-3 position-relative">
                                            <input type="text" class="form-control" name="lname" required>
                                            <i class="bi bi-person input-icon"></i>
                                        </div>
                                    </div>
                                </div>
								<div class="mb-3">
                                    <label class="form-label">Set a Username</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control" name="username" required>
                                        <i class="bi bi-user input-icon"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="position-relative">
                                        <input type="email" class="form-control" name="email" required>
                                        <i class="bi bi-envelope input-icon"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" name="pass" required>
                                        <i class="bi bi-lock input-icon"></i>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" name="signup">
                                        <i class="bi bi-person-plus me-2"></i>Sign Up
                                    </button>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="toggle-btn" id="login-toggle">Already have an account? Log In</button>
                                </div>
                            </form>
                        </div>

                        <div id="login-section" class="form-section">
                            <div class="logo">
                                <i class="bi bi-book"></i>
                            </div>
                            <h2 class="auth-title">Welcome Back</h2>
                            <form id="login-form" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="position-relative">
                                        <input type="email" class="form-control" name="uemail" required>
                                        <i class="bi bi-envelope input-icon"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" name="upass" required>
                                        <i class="bi bi-lock input-icon"></i>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" name="login">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                    </button>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="toggle-btn" id="signup-toggle">New user? Create an account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
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
			
            const toastElList = document.querySelectorAll('.toast');
            const toastList = [...toastElList].map(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                return toast;
            });
        });
		
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>