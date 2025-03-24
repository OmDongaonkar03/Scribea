<?php
	include('function.php');
	session_destroy();
	
	$ipaddress = $_SERVER['REMOTE_ADDR'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribea - Welcome</title>
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
            line-height: 1.7;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark-text);
            margin: 0 10px;
            position: relative;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            padding: 8rem 0;
            border-radius: 0 0 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section:before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
            top: -150px;
            right: -100px;
        }
        
        .hero-section:after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
            bottom: -100px;
            left: -50px;
        }
        
        .display-4 {
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 1.5rem;
        }
        
        .lead {
            font-size: 1.2rem;
            color: var(--light-text);
            margin-bottom: 2rem;
            font-weight: 300;
        }
        
        .hero-img {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transform: perspective(1000px) rotateY(-5deg);
            transition: all 0.5s;
        }
        
        .hero-img:hover {
            transform: perspective(1000px) rotateY(0deg);
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 40px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .feature-card {
            transition: all 0.3s;
            height: 100%;
            padding: 3rem 2rem;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border-color: rgba(67, 97, 238, 0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            background: rgba(67, 97, 238, 0.1);
            height: 100px;
            width: 100px;
            line-height: 100px;
            border-radius: 50%;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .feature-card:hover .feature-icon {
            background: var(--primary-color);
            color: white;
            transform: rotateY(360deg);
        }
        
        .feature-card h3 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .feature-card p {
            color: var(--light-text);
            font-size: 0.95rem;
        }
        
        .stats-section {
            background-color: white;
            padding: 5rem 0;
            position: relative;
        }
        
        .stats-section:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, #f8f9fa 0%, white 100%);
        }
        
        .stats-item {
            text-align: center;
            padding: 2.5rem;
            transition: all 0.3s;
            border-radius: 20px;
        }
        
        .stats-item:hover {
            background: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }
        
        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
        }
        
        .stats-text {
            color: var(--light-text);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .cta-section {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            padding: 6rem 0;
            border-radius: 100px 0 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            top: -100px;
            left: -100px;
        }
        
        .cta-section:after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            bottom: -75px;
            right: -75px;
        }
        
        .cta-section h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .cta-section .btn {
            background-color: white;
            color: var(--primary-color);
            border: none;
            font-weight: 600;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        
        .cta-section .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        footer {
            background-color: #f8f9fa;
            padding: 4rem 0 2rem;
            color: var(--light-text);
        }
        
        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .footer-text {
            color: var(--light-text);
            margin-bottom: 2rem;
        }
        
        .social-icons {
            margin-bottom: 2rem;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: white;
            color: var(--primary-color);
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        .footer-links h5 {
            color: var(--dark-text);
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--light-text);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }
        
        .copyright {
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 20px;
            margin-top: 40px;
            color: var(--light-text);
        }
        
        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Media Queries */
        @media (max-width: 992px) {
            .hero-section {
                padding: 6rem 0;
                border-radius: 0 0 0 50px;
            }
            
            .cta-section {
                border-radius: 50px 0 50px 0;
            }
            
            .hero-img {
                margin-top: 3rem;
                transform: none;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                text-align: center;
                border-radius: 0;
            }
            
            .cta-section {
                border-radius: 0;
            }
            
            .stats-item {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand" href="landingpage.php">Scribea</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="login-signup.php">Login/Sign Up</a>
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
                    <h1 class="display-4">Discover Your Next Great Read</h1>
                    <p class="lead">Access thousands of books, manage your reading list, and connect with fellow readers in our vibrant community.</p>
                    <div class="d-flex gap-3">
                        <a href="login-signup.php" class="btn btn-primary btn-lg">Get Started</a>
                        <a href="aboutus.php" class="btn btn-outline-primary btn-lg">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1514593214839-ce1849100055?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Library" class="img-fluid rounded hero-img float-animation">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 mt-5">
        <div class="container">
            <h2 class="text-center section-title">Why Choose Our Library</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-book feature-icon"></i>
                        <h3>Vast Collection</h3>
                        <p>Access thousands of books across multiple genres and categories, from classics to contemporary bestsellers.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-lightning feature-icon"></i>
                        <h3>Easy Management</h3>
                        <p>Keep track of your borrowed books and reading history effortlessly with our intuitive dashboard.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-people feature-icon"></i>
                        <h3>Community</h3>
                        <p>Connect with other readers, join book clubs, and share your literary journey with like-minded individuals.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">10K+</div>
                        <div class="stats-text">Books Available</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">5K+</div>
                        <div class="stats-text">Active Members</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-item">
                        <div class="stats-number">500+</div>
                        <div class="stats-text">Daily Visitors</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <h2 class="text-center section-title">What Our Members Say</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="p-4 bg-white rounded-4 shadow-sm text-center">
                                    <i class="bi bi-quote fs-1 text-primary opacity-25"></i>
                                    <p class="my-4 fs-5 fst-italic">"This library management system has transformed how I read and discover new books. The interface is intuitive and the recommendations are spot on!"</p>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="ms-3">
                                            <h5 class="mb-1">Sarah Johnson</h5>
                                            <p class="mb-0 text-muted">Book Enthusiast</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="p-4 bg-white rounded-4 shadow-sm text-center">
                                    <i class="bi bi-quote fs-1 text-primary opacity-25"></i>
                                    <p class="my-4 fs-5 fst-italic">"As someone who reads constantly, having a system that keeps track of my reading history and suggests new titles has been invaluable. Highly recommend!"</p>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="ms-3">
                                            <h5 class="mb-1">Michael Chen</h5>
                                            <p class="mb-0 text-muted">Literature Professor</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-primary rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-primary rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Start Reading?</h2>
            <p class="lead mb-4">Join our library today and discover a world of knowledge at your fingertips.</p>
            <a href="login-signup.php" class="btn btn-lg">Sign Up Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <a href="landingpage.php" class="footer-brand">Scribea</a>
                    <p class="footer-text">Your gateway to knowledge and imagination. Discover, learn, and grow with our vast collection of books.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Quick Links</h5>
                        <ul>
                            <li><a href="landingpage.php">Home</a></li>
                            <li><a href="aboutus.php">About</a></li>
                            <li><a href="login-signup.php">Login</a></li>
                            <li><a href="login-signup.php">Sign Up</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Resources</h5>
                        <ul>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Guidelines</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="footer-links">
                        <h5>Contact Us</h5>
                        <ul>
                            <li><i class="bi bi-geo-alt me-2"></i> 123 Library St., Knowledge City</li>
                            <li><i class="bi bi-envelope me-2"></i> info@librarysystem.com</li>
                            <li><i class="bi bi-telephone me-2"></i> (123) 456-7890</li>
                            <li><i class="bi bi-clock me-2"></i> Mon-Fri: 9AM-5PM</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center copyright">
                <p class="mb-0">© 2025 Library Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>
	<script>
		// Function to get user's location
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					let lat = position.coords.latitude;
					let long = position.coords.longitude;
	
					// Store lat and long in cookies
					document.cookie = "latitude=" + lat + "; path=/";
					document.cookie = "longitude=" + long + "; path=/";
	
					// Reload the page after setting cookies
					location.reload();
				});
			} else {
				alert("Geolocation is not supported by this browser.");
			}
		}
	
		// If the cookie is not set, fetch the location
		if (!document.cookie.includes("latitude") || !document.cookie.includes("longitude")) {
			getLocation();
		}
	</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>