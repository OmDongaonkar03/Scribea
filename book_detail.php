<?php
	session_set_cookie_params(3600,"/");
	
	//db connection
	include_once('function.php');
	
	$user_email = $_SESSION['user'];
	
	$userid = mysqli_query($conn,"SELECT `session_id` FROM `user` WHERE `email` = '$user_email'");
	$user_id_data = mysqli_fetch_assoc($userid);
	$session_id = session_id();
	if($user_id_data['session_id'] != $session_id){
		header("location:login-signup.php");
	}
	
	// Get book details
    if(isset($_GET['book_name'])) {
        $book_name = $_GET['book_name'];
        $sql = mysqli_query($conn, "SELECT * FROM `books` WHERE `name` = '$book_name'");
        $book = mysqli_fetch_assoc($sql);
    }else{
		header('location:index.php');
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Handle rent book action
		if(isset($_POST['rent_book'])) {
			$book_id = $_POST['book_id'];
			$book_name = $_POST['book_name'];
			$price = $_POST['book_price'];
			
			date_default_timezone_set("Asia/Kolkata");
			$current_time = date("Y/m/d");
			
			$date = new DateTime($current_time);
			$date->modify('+7 days');
			$last_date = $date->format('Y/m/d');
			
			$verify = mysqli_query($conn,"SELECT * FROM `rented` WHERE `Book_name` = '$book_name'");
			if(mysqli_num_rows($verify) == 0){
				mysqli_query($conn, "UPDATE books SET stock = stock - 1 WHERE `no` = '$book_id' AND stock > 0");
				
				mysqli_query($conn, "INSERT INTO rented (Book_name, User_email, price, rent_date,last_return_date) VALUES ('$book_name', '$user_email', '$price','$current_time','$last_date')");
				
				header("Location: book_return.php");
				exit();
			}else{
				echo "<script>alert('Already rented')</script>";
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        .book-details-container {
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-radius: 15px;
            overflow: hidden;
        }
        .book-image-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            transition: transform 0.3s ease-in-out;
        }
        .book-image-container:hover {
            transform: scale(1.02);
        }
        .book-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            border-radius: 10px;
            transition: box-shadow 0.3s ease-in-out;
        }
        .book-image:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-info {
            padding: 2rem;
        }
        .book-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        .book-meta {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            transition: background-color 0.3s ease-in-out;
        }
        .book-meta:hover {
            background-color: #e9ecef;
        }
        .book-author, .book-publication {
            color: #6c757d;
            font-size: 1rem;
        }
        .book-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .book-stock {
            font-weight: 600;
            color: #198754;
        }
        .book-action-buttons {
            display: flex;
            gap: 1rem;
        }
        .btn-rent, .btn-details {
            flex-grow: 1;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-rent {
            background-color: #0d6efd;
            color: white;
            border: 2px solid #0d6efd;
        }
        .btn-rent:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-details {
            background-color: white;
            color: #0d6efd;
            border: 2px solid #0d6efd;
        }
        .btn-details:hover {
            background-color: #0d6efd;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .book-description {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            transition: background-color 0.3s ease-in-out;
        }
        .book-description:hover {
            background-color: #e9ecef;
        }
        @media (max-width: 767.98px) {
            .book-image-container {
                height: 400px;
            }
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-secondary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            opacity: 0.65;
        }
		.review-section {
            margin-top: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 2rem;
        }
        .review-form-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .review-item {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }
        .review-item:hover {
            background-color: #f8f9fa;
        }
        .review-author {
            font-weight: 600;
            color: #0d6efd;
        }
        .review-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .review-text {
            color: #495057;
            margin-top: 0.5rem;
        }
        .btn-submit-review {
            background-color: #198754;
            color: white;
            padding: 0.75rem 1.5rem;
        }
        .btn-submit-review:hover {
            background-color: #157347;
            color: white;
        }
    </style>
</head>
<body>
	<!-- navbar -->
	<?php navbar() ?>
	
    <div class="container py-5 justify-content-center align-items-center">
        <div class="book-details-container">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="book-image-container p-4">
                        <img src="<?php echo $book['image']; ?>" class="book-image shadow" alt="Book Cover">
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="book-info">
                        <h1 class="book-title"><?php echo $book['name']; ?></h1>
                        
                        <div class="book-meta">
                            <div class="row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <div class="book-author">
                                        <i class="fas fa-user-edit me-2"></i><?php echo $book['author']; ?>
                                    </div>
                                    <div class="book-publication">
                                        <i class="fas fa-calendar me-2"></i><?php echo $book['publish_year']; ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="book-price mb-2">$ <?php echo $book['price'];?> Per day</div>
                                    <div class="book-stock">
                                        <i class="fas fa-check-circle me-2"></i><?php echo $book['stock']; ?> available
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="book-description">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-book-open me-2"></i>Book Description
                            </h5>
                            <p class="text-muted mb-0">A classic book that has stood the test of time.</p>
                        </div>

                        <div class="book-action-buttons mt-4">
                            <?php if($book['stock'] > 0) { ?>
                                <form method="POST" style="flex-grow: 1;">
                                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                    <input type="hidden" name="book_name" value="<?php echo $book['name']; ?>">
                                    <input type="hidden" name="book_price" value="<?php echo $book['price']; ?>">
                                    <button type="submit" name="rent_book" class="btn btn-rent w-100">
                                        <i class="fas fa-shopping-cart me-2"></i>Rent Book
                                    </button>
                                </form>
                            <?php } else { ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times-circle me-2"></i>Out of Stock
                                </button>
                            <?php } ?>
                            <a href="index.php" class="btn btn-details">
                                <i class="fas fa-arrow-left me-2"></i>Back to Books
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	<div class="container py-4">
        <div class="review-section">
            <h3 class="mb-4">Customer Reviews</h3>
            
            <!-- Review Form -->
            <div class="review-form-card">
                <h5 class="mb-3">Write a Review</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <textarea class="form-control" rows="3" id="review" name="review" placeholder="Your Review..." required></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary" onclick="add_review()	">Submit Review</button>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="reviews-list" id="display_review">
				<?php
					$book_name = $_GET['book_name'];
					$avb_review = mysqli_query($conn,"SELECT * FROM `reviews` WHERE `book_name` = '$book_name'");
					if(mysqli_num_rows($avb_review)){
						while($avb_data = mysqli_fetch_assoc($avb_review)){
				?>
                <div class="review-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="review-author"><?php echo $avb_data['first_name'];?> <?php echo $avb_data['last_name'];?></span>
                        <span class="review-date"><?php echo $avb_data['date'];?></span>
                    </div>
                    <p class="review-text"><?php echo $avb_data['review'];?></p>
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