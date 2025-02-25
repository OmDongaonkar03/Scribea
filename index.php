<?php
	session_set_cookie_params(3600,"/");
	
	//db connection
	include_once('function.php');
	
	//user session
	$user_email = $_SESSION['user'];
	
	$userid = mysqli_query($conn,"SELECT `session_id` FROM `user` WHERE `email` = '$user_email'");
	$user_id_data = mysqli_fetch_assoc($userid);
	
	//browser check
	$session_id = session_id();
	if($user_id_data['session_id'] != $session_id){
		header("location:login-signup.php");
	}
	
	$sql = mysqli_query($conn,"SELECT * FROM `books`");
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atc'])){
		$name = $_POST['book_name'];
		$verify = mysqli_query($conn,"SELECT * FROM `cart` WHERE `book_name` = '$name'");
		
		if(mysqli_num_rows($verify) == 0){
			$sql = mysqli_query($conn,"SELECT * FROM `books` WHERE `name` = '$name'");
			$data = mysqli_fetch_assoc($sql);
			
			$cart = mysqli_query($conn,"INSERT INTO `cart`(`user_email`, `book_id`, `book_name`,`book_author`,`book_img`,`price`) VALUES ('$user_email','$data[no]','$data[name]','$data[author]','$data[image]','$data[price]')");
			if($cart){
				header('location:cart.php');
				exit();
			}else{
				echo "<script>alert('Something Went Wrong!')</script>";
			}
		}else{
			echo "<script>alert('Already in cart!');</script>";
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
        .book-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .search-container {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 3rem 0;
        }
        .book-cover {
            height: 500px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
	<?php navbar() ?>

    <!-- Search Section -->
	<form method="GET">
		<div class="search-container">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-8">
						<h1 class="text-center mb-4">Find Your Next Book</h1>
							<div class="input-group mb-3">
								<input type="text" class="form-control form-control-lg" name ="search" id="searchInput" placeholder="Search by title, author">
							</div>
					</div>
				</div>
			</div>
		</div>
	</form>               
	
    <!-- Books Section -->
	<div class="container py-5">
		<h2 class="mb-4">Books</h2>
		<div class="row g-4" id="display">
			<?php if(mysqli_num_rows($sql) > 0){
					while($data = mysqli_fetch_assoc($sql)){ ?>
				<div class="col-md-3">
					<a class="text-decoration-none" href="book_detail.php?book_name=<?php echo $data['name']; ?>">
						<div class="card book-card">
							<img src="<?php echo $data['image'] ;?>" class="card-img-top book-cover" alt="Book 1">
							<div class="card-body">
								<h5 class="card-title"><?php echo $data['name']; ?></h5>
								<p class="card-text text-muted"><?php echo $data['author'] ; ?></p>
								<p class="card-text text-muted"><?php echo $data['publish_year'] ; ?></p>
								
								<form method="POST">
									<input type="hidden" name="book_name" value="<?php echo $data['name']; ?>">
									<button type="submit" class="btn btn-primary p-2" name="atc">Add To Cart</button>
								</form>
							</div>
						</div>
					</a>
				</div>
			<?php
					}
				}else{    
			?>
				<div>
					<h2>No Book FOUND</h2>
				</div>
			<?php
				}
			?>
		</div>
	</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
	<script>
		document.getElementById('searchInput').addEventListener('keyup',search);

		function search() {
			let word = document.getElementById("searchInput").value;
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("display").innerHTML = this.responseText;
				}
			};
			let param = "search";
			xhttp.open("GET","connect.php?param="+param+"&input="+word,true);
			xhttp.send();
		}
	</script>
</body>
</html>