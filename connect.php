<?php
include_once('function.php');

// Sanitize and validate session user
$user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user'] ?? ''), ENT_QUOTES, 'UTF-8');
$work = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['param'] ?? ''), ENT_QUOTES, 'UTF-8');

// Searchbar on index page
if ($work == 'search') {
    if (isset($_GET['input'])) {
        $input = mysqli_real_escape_string($conn, $_GET['input']);
        $sql_query = sprintf("SELECT * FROM `books` WHERE `name` LIKE '%%%s%%' OR `author` LIKE '%%%s%%'",
            mysqli_real_escape_string($conn, $input),
            mysqli_real_escape_string($conn, $input)
        );
        $sql = mysqli_query($conn, $sql_query);
    
        echo '<div class="row g-4" id="display">';
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                echo '
                    <div class="col-md-3">
                        <a class="text-decoration-none" href="book_detail.php?book_name=' . urlencode(htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8')) . '">
                            <div class="card book-card">
                                <img src="' . htmlspecialchars($data["image"], ENT_QUOTES, 'UTF-8') . '" class="card-img-top book-cover" alt="Book Image">
                                <div class="card-body">
                                    <h5 class="card-title">' . htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8') . '</h5>
                                    <p class="card-text text-muted">' . htmlspecialchars($data["author"], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p class="card-text text-muted">' . htmlspecialchars($data["publish_year"], ENT_QUOTES, 'UTF-8') . '</p>
                                    <form method="POST">
                                        <input type="hidden" name="book_name" value="' . htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8') . '">
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
if ($work == 'remove') {
    if (isset($_GET['bookid'])) {
        $bookId = mysqli_real_escape_string($conn, $_GET['bookid']);
        $delete_query = sprintf("DELETE FROM `cart` WHERE `user_email` = '%s' AND `no` = '%s'",
            mysqli_real_escape_string($conn, $user_email),
            mysqli_real_escape_string($conn, $bookId)
        );
        $delete = mysqli_query($conn, $delete_query);
        
        $sql_query = sprintf("SELECT * FROM `cart` WHERE `user_email` = '%s'",
            mysqli_real_escape_string($conn, $user_email)
        );
        $sql = mysqli_query($conn, $sql_query);
        
        echo '<div class="col-12" id="display"><h2 class="mb-4">Your Cart</h2>';
        $total_price = 0;
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $total_price += floatval($data['price']);
                echo '
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="' . htmlspecialchars($data['book_img'], ENT_QUOTES, 'UTF-8') . '" alt="Book" class="rounded me-3" style="width: 80px; height: 120px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-1">' . htmlspecialchars($data['book_name'], ENT_QUOTES, 'UTF-8') . '</h5>
                                    <p class="text-muted mb-0">' . htmlspecialchars($data['book_author'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p class="text-muted mb-0">$' . htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8') . ' Per day</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger me-2" onclick="remove(' . htmlspecialchars($data['no'], ENT_QUOTES, 'UTF-8') . ')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>';
            }
        }
        echo '
            <div class="cart-total">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Books</span>
                    <span>' . htmlspecialchars(mysqli_num_rows($sql), ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Rental Fee</span>
                    <span>$' . htmlspecialchars(number_format($total_price, 2), ENT_QUOTES, 'UTF-8') . ' Per day</span>
                </div>
            </div>
        </div>';
    }
}

// Add review on Books
if ($work == 'review_add') {
    $user_name_query = sprintf("SELECT `first_name`, `last_name` FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $user_name_result = mysqli_query($conn, $user_name_query);
    $user_name_data = mysqli_fetch_assoc($user_name_result);
    
    $user_first_name = htmlspecialchars($user_name_data['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $user_last_name = htmlspecialchars($user_name_data['last_name'] ?? '', ENT_QUOTES, 'UTF-8');
    
    $valid = true;
    $book_name = mysqli_real_escape_string($conn, $_GET['book_name'] ?? '');
    $review = mysqli_real_escape_string($conn, $_GET['review'] ?? '');
    
    if (!preg_match('/^[A-Za-z0-9@$. ]+$/', $review)) {
        $valid = false;
    }
    
    echo '<div class="reviews-list" id="display_review">';
    if ($valid) {
        date_default_timezone_set("Asia/Kolkata");
        $current_date = date("Y/m/d");
        
        $add_review_query = sprintf("INSERT INTO `reviews` (`book_name`, `user_email`, `first_name`, `last_name`, `review`, `date`) 
            VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
            mysqli_real_escape_string($conn, $book_name),
            mysqli_real_escape_string($conn, $user_email),
            mysqli_real_escape_string($conn, $user_first_name),
            mysqli_real_escape_string($conn, $user_last_name),
            mysqli_real_escape_string($conn, $review),
            mysqli_real_escape_string($conn, $current_date)
        );
        $add_review = mysqli_query($conn, $add_review_query);
    }
    
    $avb_review_query = sprintf("SELECT * FROM `reviews` WHERE `book_name` = '%s'",
        mysqli_real_escape_string($conn, $book_name)
    );
    $avb_review = mysqli_query($conn, $avb_review_query);
    
    if (mysqli_num_rows($avb_review)) {
        while ($avb_data = mysqli_fetch_assoc($avb_review)) {
            echo '
            <div class="review-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="review-author">' . htmlspecialchars($avb_data["first_name"] . " " . $avb_data["last_name"], ENT_QUOTES, 'UTF-8') . '</span>
                    <span class="review-date">' . htmlspecialchars($avb_data["date"], ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <p class="review-text">' . htmlspecialchars($avb_data["review"], ENT_QUOTES, 'UTF-8') . '</p>
            </div>';
        }
    }
    echo '</div>';
}

// Filter books
if ($work == 'filter') {
    $filter = mysqli_real_escape_string($conn, $_GET['input'] ?? '');
    
    if ($filter == 'all') {
        $sql = mysqli_query($conn, "SELECT * FROM `books`");
    } else {
        $sql_query = sprintf("SELECT * FROM `books` WHERE `category` = '%s'",
            mysqli_real_escape_string($conn, $filter)
        );
        $sql = mysqli_query($conn, $sql_query);
    }
    
    echo '<div class="row g-4" id="display">';
    if (mysqli_num_rows($sql) > 0) {
        while ($data = mysqli_fetch_assoc($sql)) {
            echo '
                <div class="col-md-3">
                    <a class="text-decoration-none" href="book_detail.php?book_name=' . urlencode(htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8')) . '">
                        <div class="card book-card">
                            <img src="' . htmlspecialchars($data["image"], ENT_QUOTES, 'UTF-8') . '" class="card-img-top book-cover" alt="Book Image">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8') . '</h5>
                                <p class="card-text text-muted">' . htmlspecialchars($data["author"], ENT_QUOTES, 'UTF-8') . '</p>
                                <p class="card-text text-muted">' . htmlspecialchars($data["category"], ENT_QUOTES, 'UTF-8') . '</p>
                                <p class="card-text text-muted">' . htmlspecialchars($data["publish_year"], ENT_QUOTES, 'UTF-8') . '</p>
                                <form method="POST">
                                    <input type="hidden" name="book_name" value="' . htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8') . '">
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

// Fetch community chats
if ($work == 'chats') {
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');
    
    $user_sql_query = sprintf("SELECT * FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $user_sql = mysqli_query($conn, $user_sql_query);
    $user_data = mysqli_fetch_assoc($user_sql);
    
    $community_status = $user_data[$category] ?? 'NOT JOINED';
    
    if ($community_status == 'NOT JOINED') {
        echo '
        <div class="join-community-container">
            <div class="join-community-box">
                <h4>Join the ' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . ' Community</h4>
                <p>You haven\'t joined this community yet. Join to start chatting and connecting with other members!</p>
                <button onclick="joinCommunity(\'' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '\')" class="btn btn-primary">
                    Join Community
                </button>
            </div>
        </div>';
    } else {
        $fetch_chats_query = sprintf("SELECT * FROM `community_chats` WHERE `category` = '%s' AND `clear` = 'NO'",
            mysqli_real_escape_string($conn, $category)
        );
        $fetch_chats_sql = mysqli_query($conn, $fetch_chats_query);
        
        if (mysqli_num_rows($fetch_chats_sql) > 0) {
            while ($fetch_chats = mysqli_fetch_assoc($fetch_chats_sql)) {
                if ($fetch_chats['useremail'] == $user_email) {
                    echo 
                    '<div class="message sent">
                        <div class="message-wrapper">
                            <small class="message-sender">' . htmlspecialchars($fetch_chats['username'], ENT_QUOTES, 'UTF-8') . '</small>
                            <div class="message-content">' . htmlspecialchars($fetch_chats['chat'], ENT_QUOTES, 'UTF-8') . '</div>
                            <div class="delete-icon" data-message-id="' . htmlspecialchars($fetch_chats['no'], ENT_QUOTES, 'UTF-8') . '">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </div>';
                } else {
                    echo 
                    '<div class="message received">
                        <div class="message-wrapper">
                            <small class="message-sender">' . htmlspecialchars($fetch_chats['username'], ENT_QUOTES, 'UTF-8') . '</small>
                            <div class="message-content">' . htmlspecialchars($fetch_chats['chat'], ENT_QUOTES, 'UTF-8') . '</div>
                        </div>
                    </div>';
                }
            }
        } else {
            echo '<div class="no-messages">No messages in this community yet.</div>';
        }
    }
}

// Send message
if ($work == 'sendmsg') {
    $useremail = mysqli_real_escape_string($conn, $_GET['useremail'] ?? '');
    $username = mysqli_real_escape_string($conn, $_GET['username'] ?? '');
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');
    $msg = mysqli_real_escape_string($conn, $_GET['msg'] ?? '');
    
    if ($useremail && $username && $category && $msg) {
        $insert_msg_query = sprintf("INSERT INTO `community_chats` (`useremail`, `username`, `category`, `chat`) 
            VALUES ('%s', '%s', '%s', '%s')",
            mysqli_real_escape_string($conn, $useremail),
            mysqli_real_escape_string($conn, $username),
            mysqli_real_escape_string($conn, $category),
            mysqli_real_escape_string($conn, $msg)
        );
        $insert_msg_sql = mysqli_query($conn, $insert_msg_query);
    }
}

// Join community
if ($work == 'join_community') {
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');
    
    if ($category) {
        $update_sql_query = sprintf("UPDATE `user` SET `%s` = 'JOINED' WHERE `email` = '%s'",
            mysqli_real_escape_string($conn, $category),
            mysqli_real_escape_string($conn, $user_email)
        );
        $update_sql = mysqli_query($conn, $update_sql_query);
        
        $update_member_query = sprintf("UPDATE `community` SET `members` = `members` + 1 WHERE `category` = '%s'",
            mysqli_real_escape_string($conn, $category)
        );
        $update_member = mysqli_query($conn, $update_member_query);
    }
}

// Delete message
if ($work == 'delete_msg') {
    $num = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
    if ($num) {
        $delete_query = sprintf("UPDATE `community_chats` SET `clear` = 'YES' WHERE `no` = '%s'",
            mysqli_real_escape_string($conn, $num)
        );
        mysqli_query($conn, $delete_query);
    }
}
?>