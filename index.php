<?php
    session_set_cookie_params(3600,"/");
    
    //db connection
    include_once('function.php');
    
    $messages = []; // Array for toast messages
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user']), ENT_QUOTES, 'UTF-8');
    
    $userid_query = sprintf("SELECT `session_id` FROM `user` WHERE `email` = '%s'", 
        mysqli_real_escape_string($conn, $user_email)
    );
    $userid = mysqli_query($conn, $userid_query);
    $user_id_data = mysqli_fetch_assoc($userid);
    
    // Browser check
    $session_id = session_id();
    if($user_id_data['session_id'] != $session_id){
        header("location:login-signup.php");
        exit();
    }
    
    $sql = mysqli_query($conn, "SELECT * FROM `books`");
    
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atc'])) {
        $name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['book_name']), ENT_QUOTES, 'UTF-8');
        
        $verify_query = sprintf("SELECT * FROM `cart` WHERE `book_name` = '%s' AND `user_email` = '%s'",
            mysqli_real_escape_string($conn, $name),
            mysqli_real_escape_string($conn, $user_email)
        );
        $verify = mysqli_query($conn, $verify_query);
        
        if(mysqli_num_rows($verify) == 0) {
            $book_query = sprintf("SELECT * FROM `books` WHERE `name` = '%s'",
                mysqli_real_escape_string($conn, $name)
            );
            $sql = mysqli_query($conn, $book_query);
            $data = mysqli_fetch_assoc($sql);
            
            if($data) {
                $insert_query = sprintf("INSERT INTO `cart` (`user_email`, `book_id`, `book_name`, `book_author`, `book_img`, `price`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                    mysqli_real_escape_string($conn, $user_email),
                    mysqli_real_escape_string($conn, $data['no']),
                    mysqli_real_escape_string($conn, $data['name']),
                    mysqli_real_escape_string($conn, $data['author']),
                    mysqli_real_escape_string($conn, $data['image']),
                    mysqli_real_escape_string($conn, $data['price'])
                );
                $cart = mysqli_query($conn, $insert_query);
                
                if($cart) {
                    $messages[] = ['type' => 'success', 'text' => 'Book added to cart successfully'];
                    header('location:cart.php');
                    exit();
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'Something went wrong while adding to cart'];
                }
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Book not found'];
            }
        } else {
            $messages[] = ['type' => 'danger', 'text' => 'Book already in cart'];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
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
            background-color: #fcfcfc;
            line-height: 1.7;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
        }
        
        .search-container {
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            padding: 5rem 0 6rem;
            border-radius: 0 0 50% 50% / 4%;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .search-container::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
        }
        
        .search-container::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
        }
        
        .search-title {
            font-size: 3rem;
            margin-bottom: 2rem;
            color: var(--dark-text);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
        }
        
        .input-group {
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-radius: 50px;
            overflow: hidden;
        }
        
        .form-control {
            border: none;
            padding: 15px 25px;
            font-size: 1.1rem;
            border-radius: 50px 0 0 50px !important;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .input-group-text {
            background-color: var(--primary-color);
            border: none;
            color: white;
            border-radius: 0 50px 50px 0 !important;
            padding: 0 25px;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            color: var(--dark-text);
            font-size: 2.2rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .book-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
        }
        
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .book-card .card-img-top {
            height: 350px;
            object-fit: cover;
        }
        
        .book-card .card-body {
            padding: 1.5rem;
        }
        
        .book-card .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8rem;
        }
        
        .book-card .card-text {
            color: var(--light-text);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--accent-color);
            padding: 8px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }
        
        .author-info {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .author-info i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }
        
        .year-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .year-info i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }
        
        .price-tag {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .no-results {
            padding: 3rem;
            text-align: center;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .no-results h2 {
            color: var(--light-text);
            font-size: 1.8rem;
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--light-text);
        }
        
        a.text-decoration-none {
            color: inherit;
        }
        
        @media (max-width: 767px) {
            .book-card .card-img-top {
                height: 300px;
            }
            
            .search-container {
                padding: 3rem 0 4rem;
                border-radius: 0 0 25% 25% / 3%;
            }
            
            .search-title {
                font-size: 2.2rem;
            }
            
            .section-title {
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
        
        .animate-card {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .filters-section {
            margin-bottom: 2rem;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 30px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--light-text);
            color: var(--dark-text);
            background-color: white;
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Toast Container Styles */
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
                    <?php echo htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Navbar -->
    <?php navbar() ?>

    <!-- Search Section -->
    <form method="GET">
        <div class="search-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h1 class="search-title">Discover Your Next Book</h1>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" name="search" id="searchInput" placeholder="Search by title, author or genre...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Books Section -->
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Explore Our Collection</h2>
            <div class="d-flex align-items-center">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                <span class="fw-medium">View</span>
            </div>
        </div>
        
        <div class="filters-section">
            <button class="filter-btn active" id="all" onclick="filter(this)">All</button>
            <button class="filter-btn" id="Fiction" onclick="filter(this)">Fiction</button>
            <button class="filter-btn" id="Non-Fiction" onclick="filter(this)">Non-Fiction</button>
            <button class="filter-btn" id="Biography" onclick="filter(this)">Biography</button>
            <button class="filter-btn" id="Fantasy" onclick="filter(this)">Fantasy</button>
        </div>
        
        <div class="row g-4" id="display">
            <?php if(mysqli_num_rows($sql) > 0){
                    $delay = 0;
                    while($data = mysqli_fetch_assoc($sql)){ 
                        $delay += 0.1;
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 animate-card" style="animation-delay: <?php echo $delay; ?>s">
                    <div class="book-card">
                        <a class="text-decoration-none" href="book_detail.php?book_name=<?php echo urlencode(htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8')); ?>">
                            <span class="badge">Book</span>
                            <img src="<?php echo htmlspecialchars($data['image'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <div class="author-info">
                                    <i class="bi bi-person"></i>
                                    <p class="card-text text-muted mb-0"><?php echo htmlspecialchars($data['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="author-info">
                                    <i class="bi bi-folder"></i>
                                    <p class="card-text text-muted mb-0"><?php echo htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="year-info">
                                    <i class="bi bi-calendar3"></i>
                                    <p class="card-text text-muted mb-0"><?php echo htmlspecialchars($data['publish_year'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <?php if(isset($data['price'])) { ?>
                                <div class="price-tag">$<?php echo htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php } ?>
                        </a>
                        <form method="POST">
                            <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-primary" name="atc">
                                <i class="bi bi-cart-plus me-2"></i>Add To Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                    }
                } else {    
            ?>
                <div class="col-12">
                    <div class="no-results">
                        <i class="bi bi-search mb-3 d-block"></i>
                        <h2>No Books Found</h2>
                        <p class="text-muted">Try adjusting your search to find what you're looking for.</p>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize and show toasts
            const toastElList = document.querySelectorAll('.toast');
            const toastList = [...toastElList].map(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                return toast;
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', search);

        function search() {
            let word = document.getElementById("searchInput").value;
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "search";
            xhttp.open("GET", "connect.php?param=" + param + "&input=" + encodeURIComponent(word), true);
            xhttp.send();
        }
        
        // Filter buttons functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        function filter(element) {
            let filter = element.id;
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "filter";
            xhttp.open("GET", "connect.php?param=" + param + "&input=" + encodeURIComponent(filter), true);
            xhttp.send();
        }
    </script>
</body>
</html>