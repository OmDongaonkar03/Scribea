<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Scribea</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="icon" href="uploads/scribea title logo.png" type="image/icon type">
    <style>
        .about-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 4rem 0;
        }
        
        .team-member {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 1rem;
            object-fit: cover;
        }
        
        .contact-section {
            background-color: #f8f9fa;
            padding: 4rem 0;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .contact-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="landingpage.php">Scribea</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="login-signup.php">Login/Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Header -->
    <section class="about-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="mb-4">About Our Library</h1>
                    <p class="lead">A digital platform designed to streamline book management and enhance the reading experience for our community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="text-center mb-4">Our Mission</h2>
                    <p class="text-center">To provide an efficient and user-friendly library management system that makes accessing and managing books easier for everyone in our community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">What We Offer</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 mb-3">Book Management</h3>
                            <p class="card-text">Digital catalog system for easy book tracking and management.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 mb-3">User Accounts</h3>
                            <p class="card-text">Personal accounts to manage borrowed books and reading history.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 mb-3">Search System</h3>
                            <p class="card-text">Easy-to-use search functionality to find books by title or author.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-section">
        <div class="container">
            <h2 class="text-center mb-4">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <h3 class="h5 mb-1">Email</h3>
                            <p class="mb-0">contact@librarysystem.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h3 class="h5 mb-1">Phone</h3>
                            <p class="mb-0">(XYZ) ABC-ABCD</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-clock"></i>
                        <div>
                            <h3 class="h5 mb-1">Support Hours</h3>
                            <p class="mb-0">Monday - Friday: 9:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2025 Scribea. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>