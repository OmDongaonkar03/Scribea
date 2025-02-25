<?php
	session_set_cookie_params(3600,"/");
	
	//db connection
	include_once('function.php');
	
	//function to calculate ppd
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
	
	$user_email = $_SESSION['user'];
	
	$userid = mysqli_query($conn,"SELECT `session_id` FROM `user` WHERE `email` = '$user_email'");
	$user_id_data = mysqli_fetch_assoc($userid);
	$session_id = session_id();
	if($user_id_data['session_id'] != $session_id){
		header("location:login-signup.php");
	}
	
	$rented_books = mysqli_query($conn, "SELECT rented.*, books.image FROM rented LEFT JOIN books ON rented.Book_name = books.name WHERE rented.User_email = '$user_email' AND `return_date` = ''");
	
	// Handle return book
	if(isset($_POST['return_book'])) {
		$rent_id = $_POST['rent_id'];
		$book_name = $_POST['book_name'];
		$user_email = $_SESSION['user'];
		
		date_default_timezone_set("Asia/Kolkata");
		$current_time = date("Y/m/d");
		
		$rental_query = mysqli_query($conn, "SELECT rent_date, price FROM rented WHERE Book_name = '$book_name' AND User_email = '$user_email' AND return_date = ''");
		$rental_data = mysqli_fetch_assoc($rental_query);
		
		$fee_data = calculateRentalFee($rental_data['rent_date'], $current_time, $rental_data['price']);
		$total_fee = $fee_data;
		
		mysqli_query($conn, "UPDATE rented SET return_date = '$current_time', total_fee = $total_fee WHERE Book_name = '$book_name' AND User_email = '$user_email' AND return_date = ''");
		
		mysqli_query($conn, "UPDATE books SET stock = stock + 1 WHERE name = '$book_name'");
		
		$_SESSION['return_fee'] = $total_fee;
		header("Location: book_return.php");
		exit();
	}
	
	// Calculate current total fee
	$total_current_fee = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Books - Library Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        .return-container {
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .book-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-image {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .book-details {
            flex-grow: 1;
        }
        .return-status {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .btn-return {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-return:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
        }
        .overdue-badge {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .return-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .page-title {
            color: #212529;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
            font-size: 1.75rem;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .due-date {
            font-weight: 600;
            color: #198754;
        }
        .overdue {
            color: #dc3545;
        }
        @media (min-width: 768px) {
            .return-container {
                padding: 2rem;
            }
            .book-card {
                flex-direction: row;
                align-items: center;
            }
            .book-image {
                margin-right: 1.5rem;
                margin-bottom: 0;
            }
            .btn-return {
                width: auto;
                margin-top: 0;
            }
            .page-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
	<!-- navbar -->
	<?php navbar() ?>
	
    <div class="container py-4 py-md-5">
        <div class="return-container">
            <h2 class="page-title">My Rented Books</h2>
            
            <?php 
            if(mysqli_num_rows($rented_books) > 0) {
                while($book = mysqli_fetch_assoc($rented_books)) { 
                    
                    date_default_timezone_set("Asia/Kolkata");
                    $current_time = date("Y/m/d");
                    $fee_data = calculateRentalFee($book['rent_date'], $current_time, $book['price']);
                    $current_fee = $fee_data;
                    $total_current_fee += $current_fee;
					
            ?>
                <div class="book-card">
                    <img src="<?php echo $book['image']; ?>" class="book-image" alt="Book Cover">
                    <div class="book-details">
                        <h4 class="mb-2 h5"><?php echo $book['Book_name']; ?></h4>
                        <p class="text-muted mb-2">Price: $<?php echo $book['price'];?> Per day</p>
                        <p class="text-muted mb-2">Book rented on: <?php echo $book['rent_date']; ?></p>
                        <p class="text-muted mb-2">Last date to return: <?php echo $book['last_return_date']; ?></p>
                        <p class="text-muted mb-2">Current fee: $<?php echo $current_fee; ?></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="rent_id" value="<?php echo $book['no']; ?>">
                        <input type="hidden" name="book_name" value="<?php echo $book['Book_name']; ?>">
                        <button type="submit" name="return_book" class="btn btn-return">
                            <i class="fas fa-undo-alt me-2"></i>Return Book
                        </button>
                    </form>
                </div>
            <?php 
                }
            } else {
            ?>
                <div class="alert alert-info">
                    You haven't rented any books yet.
                </div>
            <?php
            }
            ?>
			<div class="d-flex justify-content-center" style="color:red">Note : After last day of return if book not returned then price per day will be doubled.</div>
            <div class="return-summary">
                <h5 class="mb-3">Return Summary</h5>
                <div class="summary-item">
                    <span>Total Books Rented</span>
                    <strong><?php echo mysqli_num_rows($rented_books); ?></strong>
                </div>
                <div class="summary-item">
                    <span>Total Current Fees</span>
                    <strong>$<?php echo $total_current_fee; ?></strong>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>