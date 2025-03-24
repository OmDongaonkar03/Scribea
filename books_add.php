<?php
    //db connection
    include_once('function.php');

    $messages = []; // Array for toast messages

    // Add book request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and escape inputs
        $name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['name']), ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['author']), ENT_QUOTES, 'UTF-8');
        $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
        $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
        $ppd = filter_var($_POST['ppd'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $uploaddir = 'uploads/';

        // Validate inputs
        $validation = true;
        
        if(empty($name) || !preg_match('/^[A-Za-z0-9\s\-\.\(\)]+$/', $name)) {
            $messages[] = ['type' => 'danger', 'text' => 'Invalid book name format'];
            $validation = false;
        }
        if(empty($author) || !preg_match('/^[A-Za-z\s\-\.]+$/', $author)) {
            $messages[] = ['type' => 'danger', 'text' => 'Invalid author name format'];
            $validation = false;
        }
        if($year < 1000 || $year > 2025) {
            $messages[] = ['type' => 'danger', 'text' => 'Publish year must be between 1000 and 2025'];
            $validation = false;
        }
        if($stock < 10 || $stock > 10000) {
            $messages[] = ['type' => 'danger', 'text' => 'Stock must be between 10 and 10000'];
            $validation = false;
        }
        if($ppd < 0 || $ppd > 200) {
            $messages[] = ['type' => 'danger', 'text' => 'Price per day must be between 0 and 200'];
            $validation = false;
        }

        // Handle file uploads securely
        $image = '';
        $pdf = '';
        if(isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
            $image_extension = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));
            $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array($image_extension, $allowed_image_types)) {
                $image = $uploaddir . uniqid() . '.' . $image_extension;
                move_uploaded_file($_FILES['productImage']['tmp_name'], $image);
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Invalid image file type. Only JPG, PNG, GIF allowed'];
                $validation = false;
            }
        } else {
            $messages[] = ['type' => 'danger', 'text' => 'Image upload failed'];
            $validation = false;
        }

        if(isset($_FILES['bookpdf']) && $_FILES['bookpdf']['error'] == 0) {
            $pdf_extension = strtolower(pathinfo($_FILES['bookpdf']['name'], PATHINFO_EXTENSION));
            if($pdf_extension === 'pdf') {
                $pdf = $uploaddir . uniqid() . '.pdf';
                move_uploaded_file($_FILES['bookpdf']['tmp_name'], $pdf);
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Only PDF files are allowed for book upload'];
                $validation = false;
            }
        } else {
            $messages[] = ['type' => 'danger', 'text' => 'PDF upload failed'];
            $validation = false;
        }

        if($validation) {
            $insert_query = sprintf("INSERT INTO `books` (`name`, `author`, `image`, `pdf`, `publish_year`, `price`, `stock`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                mysqli_real_escape_string($conn, $name),
                mysqli_real_escape_string($conn, $author),
                mysqli_real_escape_string($conn, $image),
                mysqli_real_escape_string($conn, $pdf),
                mysqli_real_escape_string($conn, $year),
                mysqli_real_escape_string($conn, $ppd),
                mysqli_real_escape_string($conn, $stock)
            );
            
            $sql = mysqli_query($conn, $insert_query);
            
            if ($sql) {
                $messages[] = ['type' => 'success', 'text' => 'Upload successful'];
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Database error: ' . mysqli_error($conn)];
            }
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
    <link rel="icon" href="uploads/scribea title logo.png" type="image/icon type">
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
                                            <input type="number" class="form-control" id="publishYear" name="year" min="1000" max="2025" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Quantity/Stock *</label>
                                            <input type="number" class="form-control" id="stock" name="stock" min="10" max="10000" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ppd" class="form-label">Price Per Day *</label>
                                            <input type="number" class="form-control" id="ppd" name="ppd" min="0" max="200" step="0.01" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="bookImage" class="form-label">Book Cover Image *</label>
                                            <input type="file" class="form-control" id="bookImage" name="productImage" accept="image/*" required onchange="previewImage(event)">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="bookpdf" class="form-label">Book PDF (only .pdf extension) *</label>
                                            <input type="file" class="form-control" id="bookpdf" name="bookpdf" accept=".pdf" required>
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
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize and show toasts
            const toastElList = document.querySelectorAll('.toast');
            const toastList = [...toastElList].map(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                return toast;
            });
        });

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