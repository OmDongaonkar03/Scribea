<?php 
    session_set_cookie_params(3600, "/");
    include_once('function.php');
    
    $messages = []; // Array for toast messages
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user']), ENT_QUOTES, 'UTF-8');
    
    $userid_query = sprintf("SELECT `session_id` FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $userid = mysqli_query($conn, $userid_query);
    $user_id_data = mysqli_fetch_assoc($userid);

    // Browser check
    $session_id = session_id();
    if(!$user_id_data || $user_id_data['session_id'] != $session_id) {
        header("location:login-signup.php");
        exit();
    }
    
    // User details 
    $user_sql_query = sprintf("SELECT `username` FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $user_sql = mysqli_query($conn, $user_sql_query);
    $user_data = mysqli_fetch_assoc($user_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribea Community Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="uploads/scribea title logo.png" type="image/icon type">
<style>
/* Variables */
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #f72585;
    --light-bg: #f8f9fa;
    --dark-text: #2b2d42;
    --light-text: #8d99ae;
}

/* Base Styles */
* {
    box-sizing: border-box;
    transition: all 0.3s ease;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

/* Main Container */
.master-container {
    width: 95%;
    max-width: 1600px;
    height: 90vh;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    display: flex;
    overflow: hidden;
    margin: 1.5rem auto;
    position: relative;
}

/* Sidebar Styles */
.sidebar {
    width: 350px;
    background: white;
    border-right: 1px solid rgba(0,0,0,0.05);
    padding: 20px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
    z-index: 100;
}

.toggle-sidebar {
    display: none;
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 200;
    background: var(--primary-color);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    align-items: center;
    justify-content: center;
}

.close-sidebar {
    position: absolute;
    top: 7px;
    right: 10px;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--light-text);
    padding: 5px;
    z-index: 350;
}

/* Search Container */
.search-container {
    position: relative;
    margin-bottom: 20px;
    max-width: 330px;
}

.search-container input {
    width: 100%;
    padding: 12px 50px 12px 20px;
    border: 2px solid rgba(67, 97, 238, 0.1);
    border-radius: 30px;
    font-size: 14px;
}

.search-container i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
}

/* Communities List */
.communities-list {
    flex-grow: 1;
    overflow-y: auto;
    padding-right: 10px;
}

.community-item {
    display: flex;
    align-items: center;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.community-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(to right, rgba(67, 97, 238, 0.1), transparent);
    transition: width 0.3s ease;
}

.community-item:hover::before {
    width: 100%;
}

.community-item.active {
    background: linear-gradient(135deg, rgba(67, 97, 238, 0.05) 0%, rgba(67, 97, 238, 0.1) 100%);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
}

.community-item img {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    margin-right: 15px;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Chat Container */
.chat-container {
    flex-grow: 1;
    display: none;
    flex-direction: column;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

/* Chat Header */
.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: white;
}

.chat-header .community-info {
    display: flex;
    align-items: center;
}

.chat-header img {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    margin-right: 15px;
}

/* Community Dropdown */
.community-dropdown {
    display: none;
    position: relative;
}

.dropdown-toggle {
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    font-size: 16px;
    font-weight: 500;
    color: var(--dark-text);
    padding: 5px;
}

.dropdown-toggle img {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    margin-right: 10px;
}

.dropdown-toggle i {
    margin-left: 5px;
}

.dropdown-menu {
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 10px;
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 10px;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: rgba(67, 97, 238, 0.05);
}

.dropdown-item img {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    margin-right: 10px;
}

/* Chat Messages */
.chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
    background: rgba(255,255,255,0.7);
}

.message {
    margin-bottom: 15px;
}

.message.sent {
    display: flex;
    justify-content: flex-end;
}

.message.received {
    display: flex;
    justify-content: flex-start;
}

.message-wrapper {
    display: flex;
    flex-direction: column;
    max-width: 70%;
    position: relative;
    padding: 10px;
    border-radius: 15px;
}

.message.sent .message-wrapper {
    background-color: var(--primary-color);
    color: white;
}

.message.received .message-wrapper {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.message-sender {
    margin-bottom: 5px;
    font-size: 0.8em;
    color: var(--light-text);
}

.message.sent .message-sender {
    color: rgba(255,255,255,0.8);
}

.message-content {
    padding: 8px;
    position: relative;
    font-size: 0.95rem;
    word-break: break-word;
}

.delete-icon {
    position: absolute;
    top: 5px;
    right: 5px;
    color: #ff0000;
    cursor: pointer;
    display: none;
    font-size: 14px;
}

.message-wrapper:hover .delete-icon {
    display: block;
}

/* Chat Input */
.chat-input {
    display: flex;
    align-items: center;
    padding: 15px;
    background: white;
    border-top: 1px solid rgba(0,0,0,0.05);
    width: 100%;
    gap: 10px;
}

.chat-input input {
    flex-grow: 1;
    padding: 12px 20px;
    border: 2px solid rgba(67, 97, 238, 0.1);
    border-radius: 30px;
    font-size: 14px;
    min-width: 0;
}

.send-button {
    background: var(--primary-color);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    flex-shrink: 0;
}

.send-button:hover {
    transform: scale(1.05);
}

/* Emoji Components */
.emoji-container {
    flex-shrink: 0;
}

.emoji-toggle-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--primary-color);
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.emoji-picker {
    display: none;
    position: absolute;
    bottom: calc(100% + 10px);
    left: 0;
    width: 300px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 10px;
    z-index: 1000;
}

.emoji-picker.show {
    display: block;
}

.emoji-categories {
    display: flex;
    justify-content: space-around;
    margin-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.emoji-categories button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.emoji-categories button:hover {
    transform: scale(1.2);
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 5px;
    max-height: 200px;
    overflow-y: auto;
}

.emoji-grid button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.emoji-grid button:hover {
    transform: scale(1.2);
}

/* Toast Notifications */
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

/* Mobile Overlay */
.mobile-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 200;
}

.mobile-overlay.show {
    display: block;
}

/* Scrollbar Styling */
.communities-list::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar,
.emoji-grid::-webkit-scrollbar {
    width: 6px;
}

.communities-list::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track,
.emoji-grid::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
}

.communities-list::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb,
.emoji-grid::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .master-container {
        width: 98%;
        height: 85vh;
    }
    
    .sidebar {
        width: 300px;
    }
    
    .community-item img {
        width: 45px;
        height: 45px;
    }
    
    .chat-header img {
        width: 35px;
        height: 35px;
    }
}

@media (max-width: 992px) {
    .master-container {
        height: 80vh;
    }
    
    .sidebar {
        width: 250px;
        padding: 15px;
    }
    
    .community-item {
        padding: 12px;
    }
    
    .community-item img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }
    
    .search-container input {
        padding: 10px 40px 10px 15px;
        font-size: 13px;
    }
    
    .chat-header {
        padding: 10px 15px;
    }
    
    .chat-messages {
        padding: 15px;
    }
    
    .chat-input {
        padding: 10px;
    }
    
    .chat-input input {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .send-button {
        width: 40px;
        height: 40px;
    }
    
    .emoji-picker {
        width: 260px;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(5, 1fr);
        max-height: 180px;
    }
}

@media (max-width: 768px) {
    .master-container {
        height: calc(100vh - 60px);
        margin: 10px auto;
        border-radius: 15px;
        flex-direction: column;
    }
    
    .toggle-sidebar {
        display: flex;
    }
    
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 80%;
        max-width: 280px;
        height: 100%;
        transform: translateX(-100%);
        z-index: 300;
        box-shadow: 2px 0 15px rgba(0,0,0,0.1);
        padding: 15px;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .chat-container {
        width: 100%;
        height: 100%;
    }
    
    .chat-header {
        padding: 10px 60px 10px 15px;
        flex-wrap: wrap;
    }
    
    .community-info {
        flex: 1;
        min-width: 0;
    }
    
    .community-dropdown {
        display: block;
        width: 100%;
        margin-top: 10px;
    }
    
    .dropdown-toggle {
        width: 100%;
        justify-content: space-between;
        padding: 8px 10px;
        font-size: 14px;
    }
    
    .dropdown-menu {
        width: 100%;
        max-height: 250px;
    }
    
    .dropdown-item {
        padding: 8px;
    }
    
    .dropdown-item img {
        width: 35px;
        height: 35px;
        margin-right: 8px;
    }
    
    .header-actions {
        display: none;
    }
    
    .chat-messages {
        padding: 10px;
        height: calc(100% - 120px);
    }
    
    .message-wrapper {
        max-width: 80%;
        padding: 8px;
    }
    
    .chat-input {
        padding: 10px;
        flex-wrap: nowrap;
    }
    
    .emoji-toggle-btn {
        font-size: 1.2rem;
        padding: 5px;
    }
    
    .emoji-picker {
        width: 240px;
        left: -10px;
        bottom: calc(100% + 5px);
    }
    
    .emoji-grid {
        grid-template-columns: repeat(5, 1fr);
        max-height: 160px;
    }
    
    .toast-container {
        top: 10px;
        right: 10px;
        left: 10px;
    }
    
    .toast {
        min-width: unset;
        width: calc(100% - 20px);
        margin: 0 auto 10px;
    }
}

@media (max-width: 576px) {
    .master-container {
        width: 100%;
        height: calc(100vh - 20px);
        margin: 10px 0;
        border-radius: 0;
    }
    
    .sidebar {
        width: 90%;
        max-width: 260px;
        padding: 10px;
    }
    
    .search-container input {
        padding: 8px 35px 8px 12px;
        font-size: 12px;
    }
    
    .search-container i {
        right: 12px;
    }
    
    .community-item {
        padding: 10px;
    }
    
    .community-item img {
        width: 35px;
        height: 35px;
        margin-right: 8px;
    }
    
    .chat-header {
        padding: 8px 10px 8px 50px;
    }
    
    .chat-header img {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }
    
    .dropdown-toggle {
        font-size: 13px;
        padding: 6px 8px;
    }
    
    .dropdown-toggle img {
        width: 25px;
        height: 25px;
    }
    
    .dropdown-item {
        padding: 6px;
    }
    
    .dropdown-item img {
        width: 30px;
        height: 30px;
        margin-right: 6px;
    }
    
    .chat-messages {
        padding: 8px;
        height: calc(100% - 100px);
    }
    
    .message-wrapper {
        max-width: 85%;
        padding: 6px;
        font-size: 0.9rem;
    }
    
    .message-sender {
        font-size: 0.7em;
    }
    
    .chat-input {
        padding: 8px;
    }
    
    .chat-input input {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .send-button {
        width: 35px;
        height: 35px;
    }
    
    .emoji-toggle-btn {
        font-size: 1rem;
    }
    
    .emoji-picker {
        width: 200px;
        left: -20px;
        padding: 8px;
    }
    
    .emoji-categories button {
        font-size: 1.2rem;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
        max-height: 140px;
    }
    
    .emoji-grid button {
        font-size: 1.2rem;
    }
    
    .toast {
        font-size: 0.85rem;
        padding: 10px;
    }
    
    .toast-body {
        padding: 10px;
    }
}

@media (min-width: 769px) {
    .close-sidebar {
        display: none;
    }
}

@media (max-width: 400px) {
    .sidebar {
        width: 100%;
        max-width: none;
    }
    
    .chat-header {
        padding: 6px 10px 6px 45px;
    }
    
    .chat-messages {
        padding: 6px;
    }
    
    .chat-input {
        padding: 6px;
    }
    
    .emoji-picker {
        width: 180px;
        left: -10px;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(4, 1fr);
        max-height: 120px;
    }
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

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <?php navbar(); ?>
    
    <div class="master-container">
        <!-- Toggle Sidebar Button -->
        <button class="toggle-sidebar" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
			<button class="close-sidebar" id="closeSidebar">
				<i class="bi bi-x-lg"></i>
			</button>
            <div class="search-container">
                <input type="text" placeholder="Search communities..." id="searchCommunities">
                <i class="bi bi-search"></i>
            </div>
            
            <div class="communities-list" id="communitiesList">
                <?php
                    $category_sql = mysqli_query($conn, "SELECT * FROM `community`");
                    if (mysqli_num_rows($category_sql) > 0) {
                        while ($category = mysqli_fetch_assoc($category_sql)) {
                            $clean_image_path = htmlspecialchars(str_replace('\\', '/', $category['image']), ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="community-item" onclick="changecategory('<?php echo htmlspecialchars($category['category'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $clean_image_path; ?>', '<?php echo htmlspecialchars($category['members'], ENT_QUOTES, 'UTF-8'); ?>')">
                        <img src="<?php echo $clean_image_path; ?>" alt="<?php echo htmlspecialchars($category['category'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($category['category'], ENT_QUOTES, 'UTF-8'); ?></h6>
                            <small><?php echo htmlspecialchars($category['members'], ENT_QUOTES, 'UTF-8'); ?> members</small>
                        </div>
                    </div>
                <?php
                        }
                    } else {
                ?>
                    <div class="no-communities">
                        <p>No communities available.</p>
                    </div>
                <?php
                    }
                ?>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="chat-container" id="displaychats">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="community-info">
                    <img src="" id="selected_image" alt="">
                    <div>
                        <h5 class="mb-0" id="selected_category"></h5>
                        <small class="text-muted" id="selected_members"></small>
                    </div>
                </div>
                
                <div class="header-actions d-none d-md-block">
                    <button class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-people"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatmsg">
                <div class="no-messages text-center py-5">
                    <i class="bi bi-chat-dots" style="font-size: 3rem; color: var(--light-text);"></i>
                    <p class="mt-3">Select a community to start chatting!</p>
                </div>
            </div>

            <!-- Chat Input -->
            <div id="chatInputContainer" style="display: none;">
				<div class="chat-input">
						<div class="emoji-container">
							<button class="emoji-toggle-btn" onclick="toggleEmojiPicker()">
								<i class="bi bi-emoji-smile"></i>
							</button>
							<div class="emoji-picker" id="emojiPicker">
								<div class="emoji-categories">
									<button onclick="showEmojiCategory('smileys')">😀</button>
									<button onclick="showEmojiCategory('animals')">🐶</button>
									<button onclick="showEmojiCategory('food')">🍎</button>
									<button onclick="showEmojiCategory('activities')">⚽</button>
									<button onclick="showEmojiCategory('travel')">🌴</button>
								</div>
								<div class="emoji-grid" id="emojiGrid">
									<!-- Emojis will be dynamically inserted here -->
								</div>
							</div>
						</div>
					<input type="text" placeholder="Type your message..." id="msgtosend">
					<button class="send-button" onclick="sendmsg()">
						<i class="bi bi-send"></i>
					</button>
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
			
			// Check if community is selected
			const hasCommunity = localStorage.getItem('category');
			document.getElementById('chatInputContainer').style.display = hasCommunity ? 'flex' : 'none';
		
			// Sidebar functionality
			const toggleSidebarBtn = document.getElementById('toggleSidebar');
			const sidebar = document.getElementById('sidebar');
			const mobileOverlay = document.getElementById('mobileOverlay');
			const closeSidebarBtn = document.getElementById('closeSidebar');
		
			// Toggle sidebar
			toggleSidebarBtn.addEventListener('click', () => {
				sidebar.classList.toggle('show');
				mobileOverlay.classList.toggle('show');
			});
		
			// Close sidebar
			closeSidebarBtn.addEventListener('click', () => {
				sidebar.classList.remove('show');
				mobileOverlay.classList.remove('show');
			});
		
			// Close sidebar when clicking outside
			mobileOverlay.addEventListener('click', () => {
				sidebar.classList.remove('show');
				mobileOverlay.classList.remove('show');
			});
		
			// Community search functionality
			const searchInput = document.getElementById('searchCommunities');
			const communityItems = document.querySelectorAll('.community-item');
			
			searchInput.addEventListener('input', function() {
				const searchTerm = this.value.toLowerCase();
				communityItems.forEach(item => {
					const communityName = item.querySelector('h6').textContent.toLowerCase();
					item.style.display = communityName.includes(searchTerm) ? 'flex' : 'none';
				});
			});
			
			// Initialize existing community
			const savedCategory = localStorage.getItem('category');
			const savedImage = localStorage.getItem('image');
			const savedMembers = localStorage.getItem('members');
			
			if (savedCategory && savedImage && savedMembers) {
				document.getElementById('displaychats').style.display = 'flex';
				changecategory(savedCategory, savedImage, savedMembers);
			}
			
			// Auto-scroll to bottom of chat messages
			const chatMsgContainer = document.getElementById('chatmsg');
			if (chatMsgContainer) chatMsgContainer.scrollTop = chatMsgContainer.scrollHeight;
		});
		
		function changecategory(category, image, members) {
			// Save to localStorage
			localStorage.setItem("category", category);
			localStorage.setItem("image", image);
			localStorage.setItem("members", members);
			
			// Update display
			document.getElementById('selected_category').textContent = category; 
			document.getElementById('selected_image').src = image; 
			document.getElementById('selected_members').textContent = `${members} members`; 
			
			// Show chat container and input
			document.getElementById('displaychats').style.display = "flex";
			document.getElementById('chatInputContainer').style.display = "flex";
			
			// Highlight active community
			document.querySelectorAll('.community-item').forEach(item => {
				item.classList.toggle('active', item.querySelector('h6').textContent.trim() === category);
			});
			
			// Hide sidebar on mobile
			document.getElementById('sidebar').classList.remove('show');
			document.getElementById('mobileOverlay').classList.remove('show');
			
			// Load chats
			const chatMsgContainer = document.getElementById("chatmsg");
			const xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					chatMsgContainer.innerHTML = this.responseText;
					chatMsgContainer.scrollTop = chatMsgContainer.scrollHeight;
					startChatAutoUpdate(category);
				}
			};
			xhttp.open("GET", `connect.php?param=chats&category=${encodeURIComponent(category)}`, true);
			xhttp.send();
		}

        function startChatAutoUpdate(category) {
            if (window.chatUpdateInterval) {
                clearInterval(window.chatUpdateInterval);
            }
        
            window.chatUpdateInterval = setInterval(function() {
                let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const chatMsgContainer = document.getElementById("chatmsg");
                        const shouldScroll = chatMsgContainer.scrollTop + chatMsgContainer.clientHeight >= chatMsgContainer.scrollHeight - 100;
                        
                        chatMsgContainer.innerHTML = this.responseText;
                        
                        if (shouldScroll) {
                            chatMsgContainer.scrollTop = chatMsgContainer.scrollHeight;
                        }
                    }
                };
                xhttp.open("GET", "connect.php?param=chats&category=" + encodeURIComponent(category), true);
                xhttp.send();
            }, 5000);
        }
        
        function sanitizeInput(input) {
            input = input.replace(/<[^>]*>/g, '');
            input = input.trim();
            input = input.substring(0, 500);
            input = input.replace(/\s+/g, ' ');
            input = input.replace(/&/g, '').replace(/</g, '').replace(/>/g, '').replace(/"/g, '').replace(/'/g, '');
            return input;
        }
        
        function sendmsg() {
            let useremail = <?php echo json_encode($user_email); ?>;
            let username = <?php echo json_encode($user_data['username']); ?>;
            let category = localStorage.getItem('category');
            let msg = document.getElementById('msgtosend').value;
            
            msg = sanitizeInput(msg);
            
            if (!msg) {
                showToast('danger', 'Message cannot be empty.');
                return;
            }
            
            if (msg.length > 500) {
                showToast('danger', 'Message is too long. Maximum 500 characters.');
                return;
            }
            
            if (!category) {
                showToast('danger', 'Please select a community first.');
                return;
            }
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        document.getElementById('msgtosend').value = '';
                        showToast('success', 'Message sent successfully!');
                        if (category) {
                            startChatAutoUpdate(category); // Trigger update after sending
                        }
                    } else {
                        showToast('danger', 'Failed to send message. Please try again.');
                    }
                }
            };
            // Use GET to match the working version's backend expectation
            xhttp.open("GET", "connect.php?param=sendmsg&category=" + encodeURIComponent(category) + "&useremail=" + encodeURIComponent(useremail) + "&username=" + encodeURIComponent(username) + "&msg=" + encodeURIComponent(msg), true);
            xhttp.send();
        }
        
        function deleteMessage(messageId) {
            if (!confirm('Are you sure you want to delete this message?')) {
                return;
            }
            
            let category = localStorage.getItem('category');
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    showToast('success', 'Message deleted successfully!');
                    startChatAutoUpdate(category); // Refresh chat after deletion
                } else if (this.readyState == 4) {
                    showToast('danger', 'Failed to delete message. Please try again.');
                }
            };
            xhttp.open("GET", "connect.php?param=delete_msg&id=" + encodeURIComponent(messageId), true);
            xhttp.send();
        }
        
        function showToast(type, message) {
            const toastContainer = document.querySelector('.toast-container');
            
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.setAttribute('data-bs-autohide', 'true');
            toast.setAttribute('data-bs-delay', '5000');
            
            toast.innerHTML = `
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
        
        // Emoji picker functions
        function toggleEmojiPicker() {
            const emojiPicker = document.getElementById('emojiPicker');
            emojiPicker.classList.toggle('show');
            
            if (emojiPicker.classList.contains('show')) {
                showEmojiCategory('smileys');
                
                document.addEventListener('click', closeEmojiPickerOnOutsideClick);
            } else {
                document.removeEventListener('click', closeEmojiPickerOnOutsideClick);
            }
        }
        
        function closeEmojiPickerOnOutsideClick(event) {
            const emojiPicker = document.getElementById('emojiPicker');
            const emojiToggleBtn = document.querySelector('.emoji-toggle-btn');
            
            if (!emojiPicker.contains(event.target) && !emojiToggleBtn.contains(event.target)) {
                emojiPicker.classList.remove('show');
                document.removeEventListener('click', closeEmojiPickerOnOutsideClick);
            }
        }
        
        const emojiCategories = {
            smileys: ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '🥰', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤩', '🤔', '🤨', '😐', '😑', '😶', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '🥱', '😴', '😌', '😛', '😜', '😝', '🤤', '😒', '😓', '😔', '😕', '🙃', '🤑', '😲', '☹️', '🙁', '😖', '😞', '😟', '😤', '😢', '😭', '😦', '😧', '😨', '😩', '🤯', '😬', '😰', '😱', '🥵', '🥶', '😳', '🤪', '😵', '🥴', '😠', '😡', '🤬', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '😇', '🥳', '🥺', '🤠', '🤡', '🤥', '🤫', '🤭', '🧐', '🤓'],
            animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🦗', '🕷', '🕸', '🦂', '🐢', '🐍', '🦎', '🦖', '🦕', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊', '🐅', '🐆', '🦓', '🦍', '🦧', '🐘', '🦛', '🦏', '🐪', '🐫', '🦒', '🦘', '🐃', '🐂', '🐄', '🐎', '🐖', '🐏', '🐑', '🦙', '🐐', '🦌', '🐕', '🐩', '🦮', '🐕‍🦺', '🐈', '🐓', '🦃', '🦚', '🦜', '🦢', '🦩', '🐇', '🦝', '🦨', '🦡', '🦦', '🦥', '🐁', '🐀', '🐿', '🦔'],
            food: ['🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶', '🌽', '🥕', '🧄', '🧅', '🥔', '🍠', '🥐', '🥯', '🍞', '🥖', '🥨', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭', '🍔', '🍟', '🍕', '🥪', '🥙', '🧆', '🌮', '🌯', '🥗', '🥘', '🥫', '🍝', '🍜', '🍲', '🍛', '🍣', '🍱', '🥟', '🦪', '🍤', '🍙', '🍚', '🍘', '🍥', '🥠', '🥮', '🍢', '🍡', '🍧', '🍨', '🍦', '🥧', '🧁', '🍰', '🎂', '🍮', '🍭', '🍬', '🍫', '🍿', '🍩', '🍪', '🌰', '🥜', '🍯', '🥛', '🍼', '☕', '🍵', '🧃', '🥤', '🍶', '🍺', '🍻', '🥂', '🍷', '🥃', '🍸', '🍹', '🧉', '🍾', '🧊'],
            activities: ['⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '🥅', '⛳', '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸', '🥌', '🎿', '⛷', '🏂', '🪂', '🏋️', '🤼', '🤸', '⛹️', '🤺', '🤾', '🏌️', '🏇', '🧘', '🏄', '🏊', '🤽', '🚣', '🧗', '🚴', '🚵', '🎮', '🕹', '🎲', '🎯', '🎳', '🎭', '🎨', '🎬', '🎤', '🎧', '🎼', '🎹', '🥁', '🎷', '🎺', '🎸', '🪕', '🎻', '🎪', '🪐'],
            travel: ['🌎', '🌍', '🌏', '🗺', '🗾', '🧭', '🏔', '⛰', '🌋', '🗻', '🏕', '🏖', '🏜', '🏝', '🏞', '🏟', '🏛', '🏗', '🧱', '🏘', '🏚', '🏠', '🏡', '🏢', '🏣', '🏤', '🏥', '🏦', '🏨', '🏩', '🏪', '🏫', '🏬', '🏭', '🏯', '🏰', '💒', '🗼', '🗽', '⛪', '🕌', '🛕', '🕍', '⛩', '🕋', '⛲', '⛺', '🌁', '🌃', '🌄', '🌅', '🌆', '🌇', '🌉', '♨️', '🎠', '🎡', '🎢', '💈', '🎪', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗', '🚘', '🚙', '🚚', '🚛', '🚜', '🛴', '🛵', '🛺', '🚲', '🛑', '🦼', '🦽', '⚓', '⛵', '🛶', '🚤', '🛳', '⛴', '🛥', '🚢', '✈️', '🛩', '🛫', '🛬', '🪂', '💺', '🚁', '🚟', '🚠', '🚡', '�Satellite', '🚀']
        };
        
        function showEmojiCategory(category) {
            const emojiGrid = document.getElementById('emojiGrid');
            emojiGrid.innerHTML = '';
            
            emojiCategories[category].forEach(emoji => {
                const button = document.createElement('button');
                button.textContent = emoji;
                button.addEventListener('click', () => {
                    addEmoji(emoji);
                });
                emojiGrid.appendChild(button);
            });
        }
        
        function addEmoji(emoji) {
            const messageInput = document.getElementById('msgtosend');
            const startPos = messageInput.selectionStart;
            const endPos = messageInput.selectionEnd;
            
            messageInput.value = 
                messageInput.value.substring(0, startPos) + 
                emoji + 
                messageInput.value.substring(endPos);
            
            messageInput.selectionStart = messageInput.selectionEnd = startPos + emoji.length;
            messageInput.focus();
        }
        
        // Add keydown event listener for Enter key to send messages
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('msgtosend');
            messageInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    sendmsg();
                }
            });
        });
        
        // Handle click events for delete icons
        document.addEventListener('click', function(event) {
            const deleteIcon = event.target.closest('.delete-icon');
            if (deleteIcon) {
                const messageId = deleteIcon.getAttribute('data-message-id');
                deleteMessage(messageId);
                event.stopPropagation();
            }
        });
    </script>
</body>
</html>