<?php
    //db connection
	include_once('function.php');

    // Add book request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $name = $_POST['name'];
        $author = $_POST['author'];
        $year = $_POST['year'];
        $stock = $_POST['stock'];
        $ppd = $_POST['ppd'];
        $uploaddir = 'uploads/';

		$image = $uploaddir . basename($_FILES['productImage']['name']);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $image);
		
        $sql = mysqli_query($conn, "INSERT INTO `books`(`name`, `author`, `image`, `publish_year`,`price`,`stock`) VALUES ('$name', '$author', '$image', '$year','$ppd','$stock')");
        if ($sql) {
            echo "<script>alert('Upload successful');</script>";
        } else {
            echo "<script>alert('Database error: " . mysqli_error($conn) . "');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Book</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            background-color: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .main-content {
            padding: 2rem;
        }
        .preview-image {
            max-width: 200px;
            max-height: 300px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-3">
                <h4 class="text-center mb-4">Admin Panel</h4>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">Add New Book</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Add New Book</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="addBookForm" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Left Column - Book Details -->
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="bookName" class="form-label">Book Name *</label>
                                            <input type="text" class="form-control" id="bookName" name="name" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="author" class="form-label">Author *</label>
                                            <input type="text" class="form-control" id="author" name="author" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="publishYear" class="form-label">Publish Year *</label>
                                            <input type="number" class="form-control" id="publishYear" name="year" min="1800" max="2025" required>
                                        </div>
										<div class="mb-3">
                                            <label for="publishYear" class="form-label">Quantity/Stock *</label>
                                            <input type="number" class="form-control" id="publishYear" name="stock" min="1800" max="2025" required>
                                        </div>
										<div class="mb-3">
                                            <label for="publishYear" class="form-label">Price Per Day *</label>
                                            <input type="number" class="form-control" id="publishYear" name="ppd" min="1800" max="2025" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="bookImage" class="form-label">Book Cover Image *</label>
                                            <input type="file" class="form-control" id="bookImage" name="productImage" accept="image/*" required onchange="previewImage(event)">
                                        </div>
                                    </div>

                                    <!-- Right Column - Image Preview -->
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5 class="mb-3">Cover Preview</h5>
                                            <img id="imagePreview" src="/api/placeholder/200/300" 
                                                 class="preview-image mb-3" alt="Book cover preview">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-light">Clear Form</button>
                                    <button type="submit" class="btn btn-primary">Add Book</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('imagePreview');
                preview.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>