<?php
	include('function.php');
	session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Welcome</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 6rem 0;
        }
        
        .feature-card {
            transition: transform 0.2s;
            height: 100%;
            padding: 2rem;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        
        .cta-section {
            background-color: #f8f9fa;
            padding: 5rem 0;
        }
        
        .stats-item {
            text-align: center;
            padding: 2rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="landingpage.php">📚 Library System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About</a>
                    </li>
                    <li class="nav-item">
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="login-signup.php">Login/Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 mb-4">Discover Your Next Great Read</h1>
                    <p class="lead mb-4">Access thousands of books, manage your reading list, and connect with fellow readers.</p>
                    <div class="d-flex gap-3">
                        <a href="login-signup.php" class="btn btn-primary btn-lg">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1514593214839-ce1849100055?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Library" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Library</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-book feature-icon"></i>
                        <h3>Vast Collection</h3>
                        <p>Access thousands of books across multiple genres and categories.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-lightning feature-icon"></i>
                        <h3>Easy Management</h3>
                        <p>Keep track of your borrowed books and reading history effortlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-people feature-icon"></i>
                        <h3>Community</h3>
                        <p>Connect with other readers and share your literary journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">X+</div>
                        <div>Books Available</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">Y+</div>
                        <div>Active Members</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">Z+</div>
                        <div>Daily Visitors</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Start Reading?</h2>
            <p class="lead mb-4">Join our library today and discover a world of knowledge.</p>
            <a href="login-signup.php" class="btn btn-primary btn-lg">Sign Up Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2025 Library Management System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>