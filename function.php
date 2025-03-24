<?php
	include_once('config.php');
	
	function navbar(){
		echo'
			<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
				<div class="container">
					<a class="navbar-brand fw-bold" href="index.php">Scribea</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarContent">
						<ul class="navbar-nav ms-auto">
							<li class="nav-item">
								<a href="book_return.php" class="nav-link d-flex align-items-center">
									<i class="bi bi-book me-2"></i>
									<span>Rented Books</span>
								</a>
							</li>
							<li class="nav-item">
								<a href="cart.php" class="nav-link d-flex align-items-center">
									<i class="bi bi-cart me-2"></i>
									<span>Cart</span>
								</a>
							</li>
							<li class="nav-item">
								<a href="community.php" class="nav-link d-flex align-items-center">
									<i class="bi bi-people me-2"></i>
									<span>Community</span>
								</a>
							</li>
							<li class="nav-item">
								<a href="user.php" class="nav-link d-flex align-items-center">
									<i class="bi bi-person-circle me-2"></i>
									<span class="d-lg-none">Profile</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		';
	}
?>