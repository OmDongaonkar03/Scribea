<?php
	include_once('function.php');
	$user_email = $_SESSION['user']; //user email
	
	//fetch all books in cart
	$sql = mysqli_query($conn,"SELECT * FROM `cart`");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Library Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .cart-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .cart-item {
            transition: all 0.3s ease;
        }
        .cart-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .cart-total {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php navbar() ?>

    <!-- Cart Section -->
    <div class="container cart-container">
        <div class="row">
            <div class="col-12" id="display">
                <h2 class="mb-4">Your Cart</h2>
                
                <!-- Cart Items -->
				<?php 
					$total_price = 0;
					if(mysqli_num_rows($sql) > 0){
						while($data = mysqli_fetch_assoc($sql)){
						$total_price += $data['price'];
				?>
                <div class="card mb-4">
					<!--<a href="book_detail.php?book_name=<?php// echo $data['book_name'];?>" class="text-decoration-none">-->
						<div class="card-body">
							<div class="cart-item d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
								<div class="d-flex align-items-center">
									<img src="<?php echo $data['book_img'];?>" alt="Book" class="rounded me-3" style="width: 80px; height: 120px; object-fit: cover;">
									<div>
										<h5 class="mb-1"><?php echo $data['book_name']; ?></h5>
										<p class="text-muted mb-0"><?php echo $data['book_author']; ?></p>
										<p class="text-muted mb-0">$<?php echo $data['price']; ?> Per day</p>
									</div>
								</div>
								
								<button type="submit" class="btn btn-sm btn-danger me-2" name="atc_remove" onclick="remove(<?php echo $data['no']; ?>)">
									<i class="bi bi-trash"></i>
								</button>
							</div>
						</div>
					<!-- </a> -->
                </div>
				<?php 
						}
					}
				?>
                <!-- Cart Total -->
                <div class="cart-total">
					<div class="d-flex justify-content-between mb-2">
                        <span>Total Books</span>
                        <span><?php echo mysqli_num_rows($sql); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Rental Fee</span>
                        <span>$<?php echo $total_price; ?> Per day</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
	<script>
		function remove(id){
			let bookid = id;
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("display").innerHTML = this.responseText;
				}
			};
			let param = "remove";
			xhttp.open("GET","connect.php?param="+param+"&bookid="+bookid,true);
			xhttp.send();
		}
	</script>
</body>
</html>