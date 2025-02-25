<?php
include_once('function.php');
$user_email = $_SESSION['user'];

$work = $_GET['param'];

//searchbar on index page
if ($work == 'search') {
	if(isset($_GET['input'])){
		$input = $_GET['input'];
		$sql = mysqli_query($conn, "SELECT * FROM books WHERE name LIKE '%$input%' OR author LIKE '%$input%'");
	
		echo '<div class="row g-4" id="display">';
			if (mysqli_num_rows($sql) > 0) {
				while ($data = mysqli_fetch_assoc($sql)) {
					echo '
						<div class="col-md-3">
							<a class="text-decoration-none" href="book_detail.php?book_name=' . $data["name"] . '">
								<div class="card book-card">
									<img src="' . $data["image"] . '" class="card-img-top book-cover" alt="Book Image">
									<div class="card-body">
										<h5 class="card-title">' . $data["name"] . '</h5>
										<p class="card-text text-muted">' . $data["author"] . '</p>
										<p class="card-text text-muted">' . $data["publish_year"] . '</p>
										<form method="POST">
											<input type="hidden" name="book_name" value="' . $data["name"] . '">
											<button type="submit" class="btn btn-primary p-2" name="atc">Add To Cart</button>
										</form>
									</div>
								</div>
							</a>
						</div>';
				}
			} else {
				echo '<div><h2>No Book FOUND</h2></div>';
			}
		echo '</div>';
	}
}

// Remove product from Cart
if($work == 'remove'){
    if(isset($_GET['bookid'])){
        $bookId = $_GET['bookid'];
        $delete = mysqli_query($conn,"DELETE FROM `cart` WHERE `user_email` = '$user_email' AND `no` = '$bookId'");
        $sql = mysqli_query($conn,"SELECT * FROM `cart`");
        echo
        '<div class="col-12" id="display">
            <h2 class="mb-4">Your Cart</h2>';
        $total_price = 0;
        if(mysqli_num_rows($sql) > 0){
            while($data = mysqli_fetch_assoc($sql)){
                $total_price += $data['price'];
                echo'
                <div class="card mb-4">
                <a href="book_detail.php?book_name=' . $data['book_name'] . '" class="text-decoration-none">
                    <div class="card-body">
                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="' . $data['book_img']. '" alt="Book" class="rounded me-3" style="width: 80px; height: 120px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-1">' . $data['book_name'] .'</h5>
                                    <p class="text-muted mb-0">' . $data['book_author'] .'</p>
                                    <p class="text-muted mb-0">$' . $data['price'] .' Per day</p>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-sm btn-danger me-2" name="atc_remove" onclick="remove(' . $data['no'] .')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </a>
            </div>';
            }
        }
        echo'
            <div class="cart-total">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Books</span>
                    <span>' . mysqli_num_rows($sql) . '</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Rental Fee</span>
                    <span>$' . $total_price.' Per day</span>
                </div>
            </div>
        </div>';
    }
}

//add review on Books
if($work == 'review_add'){
	$user_name_query = mysqli_query($conn,"SELECT `first_name`, `last_name` FROM `user` WHERE `email` = '$user_email'");
	$user_name_data = mysqli_fetch_assoc($user_name_query);
	
	$user_first_name = $user_name_data['first_name'];
	$user_last_name = $user_name_data['last_name'];
	
	$book_name = $_GET['book_name'];
	$review = $_GET['review'];
	
	date_default_timezone_set("Asia/Kolkata");
    $current_date = date("Y/m/d");
	
	$add_review = mysqli_query($conn,"INSERT INTO `reviews`(`book_name`,`user_email`, `first_name`, `last_name`, `review`, `date`) VALUES ('$book_name','$user_email','$user_first_name','$user_last_name','$review','$current_date')");
	
	//result
	echo'
	<div class="reviews-list" id="display_review">';
	$avb_review = mysqli_query($conn,"SELECT * FROM `reviews` WHERE `book_name` = '$book_name'");
	if(mysqli_num_rows($avb_review)){
		while($avb_data = mysqli_fetch_assoc($avb_review)){
			echo'
			<div class="review-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="review-author">'. $avb_data["first_name"]." " .$avb_data["last_name"].'</span>
                    <span class="review-date">' . $avb_data["date"] .'</span>
                </div>
                <p class="review-text">'. $avb_data["review"] .'</p>
            </div>
			';
		}
	}
}

?>
