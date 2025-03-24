<?php
    session_set_cookie_params(3600,"/");
    
    include_once('function.php');
    
    $messages = []; // Array for toast messages
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user']), ENT_QUOTES, 'UTF-8');
    
    // Fetch all books in cart with sanitized query
    $sql_query = sprintf("SELECT * FROM `cart` WHERE `user_email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $sql = mysqli_query($conn, $sql_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Library Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
			background-color: #fcfcfc;
			color: var(--dark-text);
			line-height: 1.7;
		}
		
		.cart-container {
			max-width: 900px;
			margin: 2rem auto;
			padding: 0 15px;
		}
		
		.cart-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 2rem;
		}
		
		.cart-header h2 {
			font-family: 'Playfair Display', serif;
			font-size: 2.5rem;
			color: var(--dark-text);
			position: relative;
			padding-bottom: 15px;
		}
		
		.cart-header h2::after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			width: 70px;
			height: 3px;
			background: var(--primary-color);
		}
		
		.cart-item {
			background-color: white;
			border-radius: 15px;
			box-shadow: 0 10px 20px rgba(0,0,0,0.05);
			margin-bottom: 1.5rem;
			transition: all 0.3s ease;
			overflow: hidden;
		}
		
		.cart-item:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 30px rgba(0,0,0,0.1);
		}
		
		.cart-item .card-body {
			display: flex;
			align-items: center;
			padding: 1.5rem;
		}
		
		.cart-item img {
			width: 120px;
			height: 180px;
			object-fit: cover;
			border-radius: 10px;
			margin-right: 1.5rem;
			box-shadow: 0 5px 15px rgba(0,0,0,0.1);
		}
		
		.cart-item-details {
			flex-grow: 1;
		}
		
		.cart-item-details h5 {
			font-size: 1.25rem;
			font-weight: 600;
			margin-bottom: 0.5rem;
			color: var(--dark-text);
		}
		
		.cart-item-details p {
			color: var(--light-text);
			margin-bottom: 0.25rem;
		}
		
		.remove-btn {
			background-color: var(--accent-color);
			border: none;
			color: white;
			border-radius: 50%;
			width: 40px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.3s;
		}
		
		.remove-btn:hover {
			background-color: #ff4d9a;
			transform: scale(1.1);
		}
		
		.cart-total {
			background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
			color: white;
			border-radius: 15px;
			padding: 1.5rem;
			box-shadow: 0 15px 30px rgba(67, 97, 238, 0.2);
		}
		
		.cart-total .d-flex {
			margin-bottom: 1rem;
		}
		
		.cart-total span:first-child {
			opacity: 0.8;
		}
		
		.cart-total span:last-child {
			font-weight: 600;
		}
		
		.empty-cart {
			text-align: center;
			padding: 3rem;
			background-color: white;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(0,0,0,0.05);
		}
		
		.empty-cart i {
			font-size: 4rem;
			color: var(--light-text);
			margin-bottom: 1.5rem;
		}
		
		.empty-cart h3 {
			color: var(--light-text);
			font-size: 1.8rem;
		}
		
		@media (max-width: 768px) {
			.cart-item .card-body {
				flex-direction: column;
				text-align: center;
			}
		
			.cart-item img {
				margin-right: 0;
				margin-bottom: 1rem;
				width: 200px;
				height: 300px;
			}
		
			.remove-btn {
				margin-top: 1rem;
			}
		
			.cart-header h2 {
				font-size: 2rem;
			}
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

    <!-- Cart Section -->
    <div class="container cart-container">
        <div class="row">
            <div class="col-12" id="display">
                <h2 class="mb-4">Your Cart</h2>
                
                <!-- Cart Items -->
                <?php 
                    $total_price = 0;
                    if(mysqli_num_rows($sql) > 0) {
                        while($data = mysqli_fetch_assoc($sql)) {
                            $total_price += floatval($data['price']); // Ensure price is treated as a number
                ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($data['book_img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Book" class="rounded me-3" style="width: 80px; height: 120px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($data['book_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($data['book_author'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-muted mb-0">$<?php echo htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8'); ?> Per day</p>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-danger me-2" onclick="remove(<?php echo htmlspecialchars($data['no'], ENT_QUOTES, 'UTF-8'); ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php 
                        }
                    } else {
                ?>
                <div class="empty-cart">
                    <i class="bi bi-cart-x mb-3 d-block"></i>
                    <h3>Your Cart is Empty</h3>
                    <p class="text-muted">Add some books to start your reading journey!</p>
                    <a href="index.php" class="btn btn-primary">Browse Books</a>
                </div>
                <?php 
                    }
                ?>

                <!-- Cart Total -->
                <?php if(mysqli_num_rows($sql) > 0) { ?>
                <div class="cart-total">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Books</span>
                        <span><?php echo htmlspecialchars(mysqli_num_rows($sql), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Rental Fee</span>
                        <span>$<?php echo htmlspecialchars(number_format($total_price, 2), ENT_QUOTES, 'UTF-8'); ?> Per day</span>
                    </div>
                </div>
                <?php } ?>
            </div>
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

        function remove(id) {
            let bookid = encodeURIComponent(id);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "connect.php?param=remove&bookid=" + bookid, true);
            xhttp.send();
        }
    </script>
</body>
</html>