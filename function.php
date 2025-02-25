<?php
	include_once('config.php');
	
	function navbar(){
		echo'
			<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
				<div class="container">
					<a class="navbar-brand fw-bold" href="index.php">📚 Library System</a>
					<div class="ms-auto d-flex align-items-center">
						<a href="book_return.php" class="me-4 text-decoration-none text-dark d-flex align-items-center">
							<i class="bi bi-book me-2"></i>
							<span>Rented Books</span>
						</a>
						<a href="cart.php" class="me-4 text-decoration-none text-dark d-flex align-items-center">
							<i class="bi bi-cart me-2"></i>
							<span>Cart</span>
						</a>
						<a href="user.php" class="text-decoration-none text-dark">
							<i class="bi bi-person-circle"></i>
						</a>
					</div>
				</div>
			</nav>
		';
	}
?>