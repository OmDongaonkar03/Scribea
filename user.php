<?php
    session_set_cookie_params(3600, "/");
    
    include_once('function.php');
    
    $messages = []; // Array for toast messages
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    $sql_query = sprintf("SELECT * FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $sql = mysqli_query($conn, $sql_query);
    $data = mysqli_fetch_assoc($sql);
    
    if (!$data) {
        $messages[] = ['type' => 'danger', 'text' => 'User not found. Please log in again.'];
        session_destroy();
        header("location:login-signup.php");
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_destroy();
        $messages[] = ['type' => 'success', 'text' => 'Logged out successfully!'];
        header("location:landingpage.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Scribea</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            color: var(--dark-text);
            background-color: #fcfcfc;
            line-height: 1.7;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #d9e4f5 0%, #f5e3e6 100%);
            padding: 3rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .profile-header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
        }
        
        .profile-header::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(247, 37, 133, 0.05);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-tabs .nav-link {
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s;
            color: var(--dark-text);
            margin-right: 10px;
            border: 1px solid var(--light-text);
        }
        
        .profile-tabs .nav-link.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .profile-tabs .nav-link:hover:not(.active) {
            background-color: var(--light-bg);
            transform: translateY(-2px);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .book-card {
            height: 100%;
            transition: transform 0.3s ease;
        }
        
        .book-card:hover {
            transform: scale(1.03);
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            color: var(--dark-text);
            font-size: 2rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-danger:hover {
            background-color: #e01f6f;
            border-color: #e01f6f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
        }
        
        .info-card {
            background-color: white;
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
        }
        
        .info-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .info-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
        }
        .center-content {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%; /* Ensure the parent container has a defined height */
}
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--light-bg);
            border-bottom: none;
            font-weight: 600;
            color: var(--dark-text);
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-card {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        @media (max-width: 767px) {
            .profile-header {
                padding: 2rem 1rem;
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .profile-tabs .nav-link {
                margin-bottom: 10px;
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

    <?php navbar(); ?>

    <div class="profile-container">
        <div class="profile-header">
            <div>
                <h1 class="mb-2"><?php echo htmlspecialchars($data['first_name'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($data['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="mb-0"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <div class="card animate-card">
            <div class="card-body">
                <ul class="nav nav-pills mb-4 profile-tabs" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#profile" data-bs-toggle="tab"><i class="bi bi-person me-2"></i>Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#borrowedBooks" data-bs-toggle="tab"><i class="bi bi-book me-2"></i>Currently Borrowed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#readingHistory" data-bs-toggle="tab"><i class="bi bi-clock-history me-2"></i>Return History</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane active" id="profile">
                        <h3 class="section-title">My Profile</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-card animate-card" style="animation-delay: 0.1s">
                                    <div class="info-icon">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <h4 class="info-title">Personal Information</h4>
                                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($data['first_name'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($data['last_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $T_borrow_query = sprintf("SELECT `no` FROM `rented` WHERE `User_email` = '%s'",
                                        mysqli_real_escape_string($conn, $user_email)
                                    );
                                    $T_borrow = mysqli_query($conn, $T_borrow_query);
                                    $T_borrow_num = mysqli_num_rows($T_borrow);
                                    
                                    $C_borrow_query = sprintf("SELECT `no` FROM `rented` WHERE `User_email` = '%s' AND `return_date` = ''",
                                        mysqli_real_escape_string($conn, $user_email)
                                    );
                                    $C_borrow = mysqli_query($conn, $C_borrow_query);
                                    $C_borrow_num = mysqli_num_rows($C_borrow);
                                ?>
                                <div class="info-card animate-card" style="animation-delay: 0.2s">
                                    <div class="info-icon">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </div>
                                    <h4 class="info-title">Library Statistics</h4>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="mb-0"><strong>Total Books Borrowed:</strong></p>
                                        <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($T_borrow_num, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0"><strong>Currently Borrowed:</strong></p>
                                        <span class="badge bg-info rounded-pill"><?php echo htmlspecialchars($C_borrow_num, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 animate-card" style="animation-delay: 0.3s">
                            <form method="POST">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Borrowed Books Tab -->
                    <div class="tab-pane" id="borrowedBooks">
                        <h3 class="section-title">Currently Borrowed Books</h3>
                        <div class="row row-cols-1 row-cols-md-4 g-4">
                            <?php
                            $b_books_query = sprintf("SELECT * FROM `rented` WHERE `User_email` = '%s' AND `return_date` = ''",
                                mysqli_real_escape_string($conn, $user_email)
                            );
                            $b_books = mysqli_query($conn, $b_books_query);
                            if (mysqli_num_rows($b_books) > 0) {
                                $delay = 0.1;
                                while ($b_data = mysqli_fetch_assoc($b_books)) {
                                    $delay += 0.1;
                            ?>
                                <div class="col animate-card" style="animation-delay: <?php echo $delay; ?>s">
                                    <div class="card book-card h-100">
                                        <div class="card-body">
                                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Due Soon</span>
                                            <h5 class="card-title mb-3"><?php echo htmlspecialchars($b_data['Book_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bi bi-calendar-check me-2 text-primary"></i>
                                                <p class="card-text mb-0">Due: <?php echo htmlspecialchars($b_data['last_return_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                }
                            } else {
                            ?>
                                <div class="col-12 center-content">
                                    <div class="text-center py-5">
                                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3">No Books Currently Borrowed</h4>
                                        <p class="text-muted">Browse our collection and find your next read!</p>
                                        <a href="index.php" class="btn btn-primary mt-2">
                                            <i class="bi bi-search me-2"></i>Explore Books
                                        </a>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Reading History Tab -->
                    <div class="tab-pane" id="readingHistory">
                        <h3 class="section-title">Reading History</h3>
                        <?php
                            $history_query = sprintf("SELECT `Book_name`, `rent_date`, `return_date` FROM `rented` WHERE `User_email` = '%s' AND `return_date` != ''",
                                mysqli_real_escape_string($conn, $user_email)
                            );
                            $history_result = mysqli_query($conn, $history_query);
                            if (mysqli_num_rows($history_result) > 0) {
                        ?>
                        <div class="table-responsive animate-card">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Borrowed Date</th>
                                        <th>Returned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        while ($history_data = mysqli_fetch_assoc($history_result)) {    
                                    ?>
                                    <tr>
                                        <td><i class="bi bi-book me-2 text-primary"></i><?php echo htmlspecialchars($history_data['Book_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><i class="bi bi-calendar3 me-2 text-success"></i><?php echo htmlspecialchars($history_data['rent_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><i class="bi bi-calendar-check me-2 text-info"></i><?php echo htmlspecialchars($history_data['return_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                            } else {
                        ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Reading History</h4>
                            <p class="text-muted">Your returned books will appear here.</p>
                        </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize toasts
            const toastElList = document.querySelectorAll('.toast');
            toastElList.forEach(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        });
    </script>
</body>
</html>