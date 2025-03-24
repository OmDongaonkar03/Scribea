<?php
    session_set_cookie_params(3600,"/");
    
    //db connection
    include_once('function.php');
    
    $messages = [];
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user']), ENT_QUOTES, 'UTF-8');
    
    $userid_query = sprintf("SELECT `session_id` FROM `user` WHERE `email` = '%s'", 
        mysqli_real_escape_string($conn, $user_email)
    );
    $userid = mysqli_query($conn, $userid_query);
    $user_id_data = mysqli_fetch_assoc($userid);
    $session_id = session_id();
    if($user_id_data['session_id'] != $session_id){
        header("location:login-signup.php");
        exit();
    }
    
    // Get book details with sanitization
    if(isset($_GET['book_name'])) {
        $book_name = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['book_name']), ENT_QUOTES, 'UTF-8');
        $sql = sprintf("SELECT * FROM `books` WHERE `name` = '%s'",
            mysqli_real_escape_string($conn, $book_name)
        );
        $result = mysqli_query($conn, $sql);
        $book = mysqli_fetch_assoc($result);
        if(!$book) {
            header('location:index.php');
            exit();
        }
    } else {
        header('location:index.php');
        exit();
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Handle rent book action
        if(isset($_POST['rent_book'])) {
            $book_id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['book_id']), ENT_QUOTES, 'UTF-8');
            $book_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['book_name']), ENT_QUOTES, 'UTF-8');
            $price = filter_var($_POST['book_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            date_default_timezone_set("Asia/Kolkata");
            $current_time = date("Y/m/d");
            
            $date = new DateTime($current_time);
            $date->modify('+7 days');
            $last_date = $date->format('Y/m/d');
            
            $verify_query = sprintf("SELECT * FROM `rented` WHERE `Book_name` = '%s' AND `User_email` = '%s'",
                mysqli_real_escape_string($conn, $book_name),
                mysqli_real_escape_string($conn, $user_email)
            );
            $verify = mysqli_query($conn, $verify_query);
            
            if(mysqli_num_rows($verify) == 0){
                $update_query = sprintf("UPDATE books SET stock = stock - 1 WHERE `no` = '%s' AND stock > 0",
                    mysqli_real_escape_string($conn, $book_id)
                );
                mysqli_query($conn, $update_query);
                
                $insert_query = sprintf("INSERT INTO rented (Book_name, User_email, price, rent_date, last_return_date) VALUES ('%s', '%s', '%s', '%s', '%s')",
                    mysqli_real_escape_string($conn, $book_name),
                    mysqli_real_escape_string($conn, $user_email),
                    mysqli_real_escape_string($conn, $price),
                    mysqli_real_escape_string($conn, $current_time),
                    mysqli_real_escape_string($conn, $last_date)
                );
                mysqli_query($conn, $insert_query);
                
                header("Location: book_return.php");
                exit();
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Already rented'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribea - <?php echo $book_name?></title>
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
        
        .book-header {
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            padding: 4rem 0;
            border-radius: 0 0 50% 50% / 4%;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .book-header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
        }
        
        .book-header::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
        }
        
        .book-details-container {
            background: white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-radius: 20px;
            overflow: hidden;
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }
        
        .book-image-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--light-bg);
            transition: transform 0.3s ease-in-out;
        }
        
        .book-image {
            max-height: 400px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            transition: transform 0.5s ease;
        }
        
        .book-image-container:hover .book-image {
            transform: scale(1.03);
        }
        
        .book-info {
            padding: 2.5rem;
        }
        
        .book-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .book-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .book-meta {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .book-meta:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .book-author, .book-publication {
            display: flex;
            align-items: center;
            color: var(--light-text);
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .book-author i, .book-publication i {
            color: var(--primary-color);
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .book-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }
        
        .book-stock {
            font-weight: 600;
            color: #198754;
            display: flex;
            align-items: center;
        }
        
        .book-stock i {
            margin-right: 0.5rem;
        }
        
        .book-action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-rent, .btn-details {
            flex-grow: 1;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-rent {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-rent:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }
        
        .btn-details {
            background-color: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-details:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            opacity: 1;
            color: white;
            border-radius: 50px;
            padding: 1rem 1.5rem;
        }
        
        .btn-secondary:disabled {
            background-color: #6c757d;
            opacity: 0.7;
        }
        
        .book-description {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 15px;
            margin-top: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .book-description:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .book-description h5 {
            color: var(--dark-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .book-description h5 i {
            color: var(--primary-color);
            margin-right: 0.75rem;
        }
        
        .review-section {
            margin-top: 3rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;
        }
        
        .review-section h3 {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: var(--dark-text);
        }
        
        .review-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .review-form-card {
            background: var(--light-bg);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .review-form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .review-form-card h5 {
            color: var(--dark-text);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }
        
        .review-item {
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .review-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--primary-color);
        }
        
        .review-author {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .review-date {
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .review-text {
            color: var(--dark-text);
            margin-top: 1rem;
            line-height: 1.7;
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
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .animate-delay-1 {
            animation-delay: 0.2s;
        }
        
        .animate-delay-2 {
            animation-delay: 0.4s;
        }
        
        .animate-delay-3 {
            animation-delay: 0.6s;
        }
        
        /* Enhanced Responsive Styles */
        @media (max-width: 991.98px) {
            .book-title {
                font-size: 2.2rem;
            }
            
            .book-price {
                font-size: 1.5rem;
            }
            
            .book-action-buttons {
                flex-direction: row;
            }
            
            .book-details-container {
                margin-top: -60px;
            }
        }
        
        @media (max-width: 767.98px) {
            .book-image-container {
                height: auto;
                max-height: 350px;
                padding: 1.5rem;
            }
            
            .book-header {
                padding: 2.5rem 0;
                border-radius: 0 0 30% 30% / 4%;
            }
            
            .book-title {
                font-size: 1.8rem;
            }
            
            .book-info {
                padding: 1.5rem;
            }
            
            .book-details-container {
                margin-top: -40px;
                border-radius: 15px;
            }
            
            .review-section {
                padding: 1.5rem;
                margin-top: 2rem;
            }
            
            .review-form-card {
                padding: 1.5rem;
            }
            
            .book-action-buttons {
                flex-direction: column;
            }
            
            .btn-rent, .btn-details {
                width: 100%;
                padding: 0.75rem 1rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .book-header {
                padding: 2rem 0;
            }
            
            .book-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .book-meta {
                padding: 1rem;
            }
            
            .book-price {
                font-size: 1.3rem;
            }
            
            .book-description h5 {
                font-size: 1.1rem;
            }
            
            .book-author, .book-publication {
                font-size: 0.9rem;
            }
            
            .review-section h3 {
                font-size: 1.5rem;
            }
            
            .review-form-card h5 {
                font-size: 1.2rem;
            }
            
            .review-item {
                padding: 1rem;
            }
            
            .review-author {
                font-size: 1rem;
            }
            
            .review-date {
                font-size: 0.8rem;
            }
            
            .toast {
                min-width: 250px;
            }
            
            .book-action-buttons {
                margin-top: 1.5rem;
                gap: 0.75rem;
            }
            
            .book-details-container {
                margin-top: -30px;
            }
            
            /* Fix for review author and date on small screens */
            .review-item .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .review-item .review-date {
                margin-top: 0.25rem;
            }
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

    <!-- navbar -->
    <?php navbar() ?>
    
    <!-- Book Header -->
    <div class="book-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 text-center">
                    <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container px-4 px-md-3">
        <div class="book-details-container animate-fade">
            <div class="row g-0">
                <div class="col-12 col-md-5">
                    <div class="book-image-container">
                        <img src="<?php echo htmlspecialchars($book['image'], ENT_QUOTES, 'UTF-8'); ?>" class="book-image" alt="<?php echo htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8'); ?> Cover">
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="book-info">
                        <h1 class="book-title"><?php echo htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                        
                        <div class="book-meta animate-fade animate-delay-1">
                            <div class="row">
                                <div class="col-6 col-sm-6 mb-3 mb-sm-0">
                                    <div class="book-author mb-2">
                                        <i class="bi bi-person"></i>
                                        <span><?php echo htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="book-publication">
                                        <i class="bi bi-calendar3"></i>
                                        <span><?php echo htmlspecialchars($book['publish_year'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 text-end">
                                    <div class="book-price mb-2">$<?php echo htmlspecialchars($book['price'], ENT_QUOTES, 'UTF-8'); ?>/day</div>
                                    <div class="book-stock">
                                        <i class="bi bi-check-circle"></i>
                                        <span><?php echo htmlspecialchars($book['stock'], ENT_QUOTES, 'UTF-8'); ?> available</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="book-description animate-fade animate-delay-2">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-book"></i>
                                Book Description
                            </h5>
                            <p class="text-muted mb-0">A classic book that has stood the test of time.</p>
                        </div>

                        <div class="book-action-buttons animate-fade animate-delay-3">
                            <?php if($book['stock'] > 0) { ?>
                                <form method="POST" style="flex-grow: 1;">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['no'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($book['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="book_price" value="<?php echo htmlspecialchars($book['price'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" name="rent_book" class="btn btn-rent w-100">
                                        <i class="bi bi-bag-check me-2"></i>Rent Book
                                    </button>
                                </form>
                            <?php } else { ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-x-circle me-2"></i>Out of Stock
                                </button>
                            <?php } ?>
                            <a href="index.php" class="btn btn-details">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container px-4 px-md-3 py-5">
        <div class="review-section animate-fade animate-delay-3">
            <h3>Customer Reviews</h3>
            
            <!-- Review Form -->
            <div class="review-form-card">
                <h5 class="mb-3">Write a Review</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <textarea class="form-control" rows="3" id="review" name="review" placeholder="Share your thoughts about this book..." required></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary" onclick="add_review()">
                            <i class="bi bi-send me-2"></i>Submit Review
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="reviews-list" id="display_review">
                <?php
                    $book_name = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['book_name']), ENT_QUOTES, 'UTF-8');
                    $avb_review_query = sprintf("SELECT * FROM `reviews` WHERE `book_name` = '%s'",
                        mysqli_real_escape_string($conn, $book_name)
                    );
                    $avb_review = mysqli_query($conn, $avb_review_query);
                    if(mysqli_num_rows($avb_review)){
                        while($avb_data = mysqli_fetch_assoc($avb_review)){
                ?>
                <div class="review-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="review-author"><?php echo htmlspecialchars($avb_data['first_name'], ENT_QUOTES, 'UTF-8');?> <?php echo htmlspecialchars($avb_data['last_name'], ENT_QUOTES, 'UTF-8');?></span>
                        <span class="review-date"><i class="bi bi-calendar-date me-1"></i><?php echo htmlspecialchars($avb_data['date'], ENT_QUOTES, 'UTF-8');?></span>
                    </div>
                    <p class="review-text"><?php echo htmlspecialchars($avb_data['review'], ENT_QUOTES, 'UTF-8');?></p>
                </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
		document.addEventListener('DOMContentLoaded', () => {
            const toastElList = document.querySelectorAll('.toast');
            const toastList = [...toastElList].map(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                return toast;
            });
        });
		
        function add_review() {
            let User_review = document.getElementById('review').value;
            let bookname = "<?php echo $_GET['book_name']; ?>";
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display_review").innerHTML = this.responseText;
                }
            };
            let param = "review_add";
            xhttp.open("GET", "connect.php?param=" + param + "&review=" + User_review + "&book_name=" + bookname, true);
            xhttp.send();
            document.getElementById('review').value = '';
        }
    </script>
</body>
</html>