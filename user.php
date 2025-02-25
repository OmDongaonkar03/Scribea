<?php
	include_once('function.php');
	$user_email = $_SESSION['user'];
	
	$sql = mysqli_query($conn,"SELECT * FROM `user` WHERE `email` = '$user_email'");
	$data = mysqli_fetch_assoc($sql);
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		session_destroy();
		header("location:login-signup.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Library System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A5568;
            --secondary-color: #5A67D8;
            --background-color: #F7FAFC;
            --text-primary: #2D3748;
            --text-secondary: #4A5568;
        }
        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .profile-header {
            background-color: #CFF4FC;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            border: 4px solid white;
        }
        .profile-tabs .nav-link {
            background-color: #CFF4FC;
            font-weight: 600;
        }
        .profile-tabs .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .book-card {
            transition: transform 0.3s ease;
        }
        .book-card:hover {
            transform: scale(1.05);
        }
        .book-card img {
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php navbar();?>

    <div class="profile-container">
        <div class="profile-header">
            <div>
                <h1 class="mb-2"><?php echo $data['first_name'];?> <?php echo $data['last_name'];?></h1>
                <p class="mb-0"><?php echo $data['email'];?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills nav-fill mb-4 profile-tabs gap-2" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#profile" data-bs-toggle="tab">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#borrowedBooks" data-bs-toggle="tab">Currently Borrowed Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#readingHistory" data-bs-toggle="tab">Returned Books</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane active" id="profile">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Personal Information</h4>
                                <p><strong>Full Name:</strong> <?php echo $data['first_name'];?> <?php echo $data['last_name'];?></p>
                                <p><strong>Email:</strong> <?php echo $data['email'];?></p>
                            </div>
                            <div class="col-md-6">
								<!-- Total Borrowed -->
								<?php
									$T_borrow = mysqli_query($conn,"SELECT `no` FROM `rented` WHERE `User_email` = '$user_email'");
									$T_borrow_num = mysqli_num_rows($T_borrow);
								?>
                                <h4 class="mb-3">Library Statistics</h4>
                                <p><strong>Total Books Borrowed:</strong> <?php echo $T_borrow_num; ?></p>
								
								<!-- Currenty Borrowed -->
								<?php
									$C_borrow = mysqli_query($conn,"SELECT `no` FROM `rented` WHERE `User_email` = '$user_email' AND `return_date` = ''");
									$C_borrow_num = mysqli_num_rows($C_borrow);
								?>
                                <p><strong>Currently Borrowed:</strong> <?php echo $C_borrow_num; ?></p>
                            </div>
                        </div>
						<a href="landingpage.php" class="text-decoration-none">
							<div class="btn btn-danger p-2" style="min-width:120px;">
								logout
							</div>
						</a>
                    </div>

                    <!-- Borrowed Books Tab -->
                    <div class="tab-pane" id="borrowedBooks">
						<div class="row row-cols-1 row-cols-md-4 g-4">
							<?php
							$b_books = mysqli_query($conn, "SELECT * FROM `rented` WHERE `User_email` = '$user_email' AND `return_date` = ''");
							if(mysqli_num_rows($b_books) > 0){
								while($b_data = mysqli_fetch_assoc($b_books)){
							?>
								<div class="col">
									<div class="card book-card h-100">
										<div class="card-body">
											<h5 class="card-title"><?php echo $b_data['Book_name'];?></h5>
											<p class="card-text">Due: <?php echo $b_data['last_return_date'];?></p>
										</div>
									</div>
								</div>
							<?php
								}
							}
							?>
						</div>
					</div>
                    <!-- Reading History Tab -->
                    <div class="tab-pane" id="readingHistory">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Borrowed Date</th>
                                    <th>Returned Date</th>
                                </tr>
                            </thead>
                            <tbody>
								<?php
									$history_query = mysqli_query($conn,"SELECT `Book_name`,`rent_date`, `return_date` FROM `rented` WHERE `User_email` = '$user_email' AND `return_date` != ''");
									if(mysqli_num_rows($history_query)){
										while($history_data = mysqli_fetch_assoc($history_query)){	
								?>
                                <tr>
                                    <td><?php echo $history_data['Book_name']; ?></td>
                                    <td><?php echo $history_data['rent_date']; ?></td>
                                    <td><?php echo $history_data['return_date']; ?></td>
                                </tr>
								<?php
										}
									}
								?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>