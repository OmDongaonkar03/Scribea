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
    
    // Function to calculate price per day (remains unchanged as it processes sanitized data)
    function calculateRentalFee($rent_date, $return_date, $price_per_day) {
        $start_time = strtotime($rent_date);
        $end_time = strtotime($return_date);
        
        $days = ceil(($end_time - $start_time) / (60 * 60 * 24));
        $days = max(1, $days);
        
        $total_cost = 0;
        
        if ($days <= 7) {
            $total_cost = $days * $price_per_day;
        } else {
            $first_week_cost = 7 * $price_per_day;
            $extra_days = $days - 7;
            $extra_days_cost = $extra_days * ($price_per_day * 2);
            $total_cost = $first_week_cost + $extra_days_cost;
        }
        
        return $total_cost;
    }
    
    // Fetch rented books with sanitized query
    $rented_books_query = sprintf("SELECT rented.*, books.image FROM rented LEFT JOIN books ON rented.Book_name = books.name WHERE rented.User_email = '%s' AND `return_date` = ''",
        mysqli_real_escape_string($conn, $user_email)
    );
    $rented_books = mysqli_query($conn, $rented_books_query);
    
    // Handle return book
    if(isset($_POST['return_book'])) {
        $rent_id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['rent_id']), ENT_QUOTES, 'UTF-8');
        $book_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['book_name']), ENT_QUOTES, 'UTF-8');
        $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user']), ENT_QUOTES, 'UTF-8');
        
        date_default_timezone_set("Asia/Kolkata");
        $current_time = date("Y/m/d");
        
        $rental_query = sprintf("SELECT rent_date, price FROM rented WHERE Book_name = '%s' AND User_email = '%s' AND return_date = ''",
            mysqli_real_escape_string($conn, $book_name),
            mysqli_real_escape_string($conn, $user_email)
        );
        $rental_query_result = mysqli_query($conn, $rental_query);
        $rental_data = mysqli_fetch_assoc($rental_query_result);
        
        if($rental_data) {
            $fee_data = calculateRentalFee($rental_data['rent_date'], $current_time, $rental_data['price']);
            $total_fee = $fee_data;
            
            $update_rented_query = sprintf("UPDATE rented SET return_date = '%s', total_fee = '%s' WHERE Book_name = '%s' AND User_email = '%s' AND return_date = ''",
                mysqli_real_escape_string($conn, $current_time),
                mysqli_real_escape_string($conn, $total_fee),
                mysqli_real_escape_string($conn, $book_name),
                mysqli_real_escape_string($conn, $user_email)
            );
            mysqli_query($conn, $update_rented_query);
            
            $update_books_query = sprintf("UPDATE books SET stock = stock + 1 WHERE name = '%s'",
                mysqli_real_escape_string($conn, $book_name)
            );
            mysqli_query($conn, $update_books_query);
            
            $messages[] = ['type' => 'success', 'text' => 'Book returned successfully'];
            $_SESSION['return_fee'] = $total_fee;
            header("Location: book_return.php");
            exit();
        } else {
            $messages[] = ['type' => 'danger', 'text' => 'Error returning book'];
        }
    }
    
    if(isset($_POST['read'])) {
        $rent_id_view = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['rent_id_view']), ENT_QUOTES, 'UTF-8');
        $book_pdf_query = sprintf("SELECT `pdf` FROM `books` WHERE `name` = '%s'",
            mysqli_real_escape_string($conn, $rent_id_view)
        );
        $book_pdf_sql = mysqli_query($conn, $book_pdf_query);
        $book_pdf_data = mysqli_fetch_assoc($book_pdf_sql);
        
        if($book_pdf_data) {
            $book_pdf = htmlspecialchars($book_pdf_data['pdf'], ENT_QUOTES, 'UTF-8');
            $_SESSION['book'] = $book_pdf;
            header("location: pdf.php?bookName=" . urlencode($book_pdf));
            exit();
        } else {
            $messages[] = ['type' => 'danger', 'text' => 'Book PDF not found'];
        }
    }
    
    // Calculate current total fee
    $total_current_fee = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribea - Return Books</title>
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
        
        .header-container {
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            padding: 5rem 0 6rem;
            border-radius: 0 0 50% 50% / 4%;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .header-container::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
        }
        
        .header-container::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
        }
        
        .page-title {
            font-size: 3rem;
            color: var(--dark-text);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
            text-align: center;
            margin-bottom: 0;
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
            margin-bottom: 1.5rem;
            background: white;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .book-image {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .book-details {
            flex-grow: 1;
        }
        
        .book-details h4 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-text);
        }
        
        .text-muted {
            color: var(--light-text) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .return-summary {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .animate-card {
            animation: fadeInUp 0.5s ease forwards;
        }
        
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
        
        .warning-note {
            background-color: #feecf0;
            border-left: 4px solid var(--accent-color);
            padding: 1rem;
            border-radius: 0 10px 10px 0;
            margin-top: 1.5rem;
            font-weight: 500;
            color: #e63946;
        }
        
        .no-books {
            padding: 3rem;
            text-align: center;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }
        
        .no-books h2 {
            color: var(--light-text);
            font-size: 1.8rem;
        }
        
        .no-books i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--light-text);
        }
        
        .current-fee {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
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
		
        @media (min-width: 768px) {
            .book-card {
                flex-direction: row;
                align-items: center;
            }
            
            .book-image {
                margin-right: 1.5rem;
                margin-bottom: 0;
            }
            
            .book-actions {
                display: flex;
                flex-shrink: 0;
            }
            
            .btn-primary {
                margin-top: 0;
                margin-left: 0.5rem;
            }
            
            .btn-primary:first-child {
                margin-left: 0;
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
    
    <!-- Header Section -->
    <div class="header-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="page-title">My Rented Books</h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">Current Rentals</h2>
                
                <?php 
                if(mysqli_num_rows($rented_books) > 0) {
                    $delay = 0;
                    while($book = mysqli_fetch_assoc($rented_books)) { 
                        $delay += 0.1;
                        
                        date_default_timezone_set("Asia/Kolkata");
                        $current_time = date("Y/m/d");
                        $fee_data = calculateRentalFee($book['rent_date'], $current_time, $book['price']);
                        $current_fee = $fee_data;
                        $total_current_fee += $current_fee;
                ?>
                <div class="book-card animate-card" style="animation-delay: <?php echo $delay; ?>s">
                    <img src="<?php echo htmlspecialchars($book['image'], ENT_QUOTES, 'UTF-8'); ?>" class="book-image" alt="Book Cover">
                    <div class="book-details">
                        <h4><?php echo htmlspecialchars($book['Book_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="text-muted mb-2">
                            <i class="bi bi-tag me-2"></i>
                            Price: $<?php echo htmlspecialchars($book['price'], ENT_QUOTES, 'UTF-8'); ?> Per day
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-check me-2"></i>
                            Book rented on: <?php echo htmlspecialchars($book['rent_date'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-x me-2"></i>
                            Last date to return: <?php echo htmlspecialchars($book['last_return_date'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p class="current-fee mb-0">
                            <i class="bi bi-cash-stack me-2"></i>
                            Current fee: $<?php echo htmlspecialchars($current_fee, ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                    <div class="book-actions">
                        <form method="POST" class="d-flex flex-column flex-md-row">
                            <input type="hidden" name="rent_id" value="<?php echo htmlspecialchars($book['no'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($book['Book_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="return_book" class="btn btn-primary">
                                <i class="bi bi-arrow-return-left me-2"></i>Return Book
                            </button>
                            <button type="submit" name="read" class="btn btn-primary ms-md-2 mt-2 mt-md-0">
                                <input type="hidden" name="rent_id_view" value="<?php echo htmlspecialchars($book['Book_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="bi bi-eye me-2"></i>View Book
                            </button>
                        </form>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                    <div class="no-books">
                        <i class="bi bi-book mb-3 d-block"></i>
                        <h2>No Rented Books</h2>
                        <p class="text-muted">You haven't rented any books yet. Visit our collection to find your next read.</p>
                        <a href="index.php" class="btn btn-primary mt-3" style="max-height:50px">
                            <i class="bi bi-search me-2" style="font-size:18px;"></i>Browse Books
                        </a>
                    </div>
                <?php
                }
                ?>
                
                <?php if(mysqli_num_rows($rented_books) > 0) { ?>
                <div class="warning-note">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Note: After last day of return if book not returned then price per day will be doubled. (price is just for example purpose)
                </div>
                
                <div class="return-summary">
                    <h3 class="mb-4">Return Summary</h3>
                    <div class="summary-item">
                        <span>Total Books Rented</span>
                        <strong><?php echo htmlspecialchars(mysqli_num_rows($rented_books), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Total Current Fees</span>
                        <strong class="text-primary">$<?php echo htmlspecialchars($total_current_fee, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>
                <?php } ?>
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
    </script>
</body>
</html>