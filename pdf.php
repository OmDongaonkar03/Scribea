<?php
    session_set_cookie_params(3600, "/");
    
    include('function.php');
    
    $messages = []; // Array for toast messages
    
    // Sanitize and validate session user
    $user_email = htmlspecialchars(mysqli_real_escape_string($conn, $_SESSION['user'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    $userid_query = sprintf("SELECT `session_id` FROM `user` WHERE `email` = '%s'",
        mysqli_real_escape_string($conn, $user_email)
    );
    $userid = mysqli_query($conn, $userid_query);
    $user_id_data = mysqli_fetch_assoc($userid);
    
    // Browser check
    $session_id = session_id();
    if (!$user_id_data || $user_id_data['session_id'] != $session_id) {
        header("location:login-signup.php");
        exit();
    }
    
    // Sanitize book parameters
    $book_a = htmlspecialchars($_SESSION['book'] ?? '', ENT_QUOTES, 'UTF-8');
    $book_b = htmlspecialchars($_GET['bookName'] ?? '', ENT_QUOTES, 'UTF-8');
    
    if ($book_a != $book_b) {
        $messages[] = ['type' => 'danger', 'text' => 'Invalid book access attempt'];
        header('location:landingpage.php');
        exit();
    }
    
    // Validate book path (basic check for security)
    if (!file_exists($book_a) || pathinfo($book_a, PATHINFO_EXTENSION) !== 'pdf') {
        $messages[] = ['type' => 'danger', 'text' => 'Invalid or inaccessible PDF file'];
        header('location:landingpage.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Room | Scribea</title>
    <link rel="icon" href="uploads/scribea title logo.png" type="image/icon type">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400&family=Roboto:wght@300;400;500&family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js"></script>
    <style>
        :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #f72585;
        --background-color: #fcfcfc;
        --text-color: #2b2d42;
        --light-text: #8d99ae;
        --page-color: #fff;
        --shadow-color: rgba(0,0,0,0.1);
    }
    
    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Poppins', sans-serif;
        line-height: 1.7;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    h1, h2, h3, h4, h5 {
        font-family: 'Playfair Display', serif;
    }
    
    .content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .main-content {
        flex: 1;
        position: relative;
    }
    
    .reader-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px 15px;
        width: 100%;
    }
    
    .pdf-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 20px var(--shadow-color);
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .book-title {
        margin: 0;
        font-weight: 500;
        font-size: clamp(1.2rem, 3vw, 1.5rem);
        font-family: 'Playfair Display', serif;
        word-break: break-word;
        max-width: 100%;
    }
    
    .reader-tools {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .pdf-container {
        width: 100%;
        margin: 0 auto;
        text-align: center;
        position: relative;
        background-color: var(--page-color);
        border-radius: 0 0 15px 15px;
        box-shadow: 0 10px 20px var(--shadow-color);
        padding: 20px 0;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .pdf-canvas-container {
        width: 100%;
        overflow-x: auto;
        display: flex;
        justify-content: center;
        padding: 0 10px;
    }
    
    .pdf-canvas {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        background-color: white;
        transition: transform 0.2s;
    }
    
    .pdf-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
    }
    
    .page-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin: 25px 0 15px;
        padding: 10px;
        border-radius: 30px;
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 20px var(--shadow-color);
        position: relative;
        z-index: 20;
        flex-wrap: wrap;
    }
    
    .page-control-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 30px;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.9rem;
    }
    
    .page-control-btn:hover {
        background-color: var(--secondary-color);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        transform: translateY(-2px);
    }
    
    .page-control-btn:active {
        transform: translateY(0);
    }
    
    .page-info {
        font-weight: 500;
        font-size: 14px;
        color: var(--text-color);
        padding: 8px 15px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .page-num, .page-count {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .reading-progress-container {
        width: 100%;
        height: 6px;
        background-color: rgba(0,0,0,0.1);
        border-radius: 3px;
        margin-top: 20px;
        overflow: hidden;
    }
    
    .reading-progress {
        height: 100%;
        background-color: var(--accent-color);
        border-radius: 3px;
        transition: width 0.3s ease;
        width: 0%;
    }
    
    .reading-mode-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        background-color: var(--primary-color);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    .reading-mode-toggle:hover {
        background-color: var(--secondary-color);
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .reading-statistics {
        background-color: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 10px 20px var(--shadow-color);
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .stat-item {
        text-align: center;
        flex: 1;
        min-width: calc(33.333% - 15px);
        padding: 10px;
        background-color: rgba(67, 97, 238, 0.05);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .stat-item:hover {
        background-color: rgba(67, 97, 238, 0.1);
        transform: translateY(-3px);
    }
    
    .stat-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: var(--light-text);
        margin-top: 5px;
    }
    
    .focus-mode {
        background-color: #f8f9fa;
    }
    
    .focus-mode .pdf-container {
        max-width: 800px;
        background-color: #f8f8f8;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }
    
    #error-message {
        color: var(--accent-color);
        text-align: center;
        margin: 20px;
        display: none;
        background-color: rgba(255, 235, 238, 0.9);
        padding: 15px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(247, 37, 133, 0.2);
    }
    
    .reading-tip {
        position: fixed;
        bottom: 100px;
        right: 30px;
        background-color: var(--primary-color);
        color: white;
        padding: 15px;
        border-radius: 10px;
        max-width: 300px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 1001;
        animation: fadeIn 0.5s;
    }
    
    .reading-tip p {
        margin: 10px 0 0;
    }
    
    .reading-tip button {
        background: none;
        border: none;
        color: white;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.2rem;
        cursor: pointer;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Dark mode features */
    .dark-mode {
        --background-color: #1a1a1a;
        --page-color: #2a2a2a;
        --text-color: #e0e0e0;
        --shadow-color: rgba(0,0,0,0.25);
    }
    
    .dark-mode .pdf-canvas {
        filter: brightness(0.85) contrast(1.1);
    }
    
    .dark-mode .reading-statistics {
        background-color: #2a2a2a;
    }
    
    .dark-mode .stat-item {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .dark-mode .stat-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .dark-mode .page-info {
        background-color: rgba(42, 42, 42, 0.9);
        color: #e0e0e0;
    }
    
    .dark-mode .page-controls {
        background-color: rgba(42, 42, 42, 0.9);
    }
    
    /* Responsive design */
    @media (max-width: 992px) {
        .reader-container {
            padding: 15px 10px;
        }
        
        .pdf-header {
            padding: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .pdf-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .reader-tools {
            width: 100%;
            justify-content: flex-start;
        }
        
        .page-controls {
            flex-direction: row;
            padding: 8px;
            gap: 10px;
        }
        
        .page-control-btn {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        .page-info {
            padding: 6px 12px;
            font-size: 0.85rem;
            order: 0;
            width: 100%;
        }
        
        .reading-mode-toggle {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
        
        .reading-statistics {
            padding: 15px;
        }
        
        .stat-item {
            min-width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .book-title {
            font-size: 1.2rem;
        }
        
        .pdf-header {
            padding: 12px;
        }
        
        .reader-tools {
            justify-content: space-between;
        }
        
        .page-controls {
            margin: 15px 0 10px;
            flex-direction: column;
            width: 100%;
        }
        
        .page-control-btn {
            width: 100%;
            justify-content: center;
        }
        
        .reading-tip {
            left: 15px;
            right: 15px;
            bottom: 80px;
            max-width: none;
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

    <div class="content-wrapper">
        <div class="main-content">
            <div class="reader-container">
                <div class="pdf-header">
                    <h2 class="book-title"><?php echo htmlspecialchars(basename($book_a, '.pdf'), ENT_QUOTES, 'UTF-8'); ?></h2>
                    <div class="reader-tools">
                        <button id="zoom-in" class="btn btn-sm btn-light">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button id="zoom-out" class="btn btn-sm btn-light">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <button id="toggle-focus" class="btn btn-sm btn-light">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="pdf-container">
                    <div class="pdf-canvas-container">
                        <canvas id="pdf-canvas" class="pdf-canvas"></canvas>
                    </div>
                    <div class="pdf-overlay"></div>
                    
                    <div class="page-controls">
                        <div id="page-info" class="page-info">
                            Page <span id="page-num" class="page-num">1</span> of <span id="page-count" class="page-count">?</span>
                        </div>
                        <button id="prev" class="page-control-btn">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <button id="next" class="page-control-btn">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="reading-progress-container">
                        <div class="reading-progress" id="reading-progress"></div>
                    </div>
                </div>
                
                <div id="error-message" class="alert alert-danger mt-4" style="display: none;"></div>
                
                <div class="reading-statistics">
                    <div class="stat-item">
                        <div class="stat-value" id="time-spent">0:00</div>
                        <div class="stat-label">Time Spent</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="reading-pace">0</div>
                        <div class="stat-label">Pages/Hour</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="completion">0%</div>
                        <div class="stat-label">Completion</div>
                    </div>
                </div>
            </div>
        </div>
        
        <button id="reading-mode-toggle" class="reading-mode-toggle">
            <i class="bi bi-moon"></i>
        </button>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-4">
        <div class="container text-center">
            <p class="mb-0">© 2025 Scribea. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Set the worker source
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
        
        // Prevent right-click
        document.addEventListener('contextmenu', event => event.preventDefault());
        
        // Prevent keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey && (event.key === 's' || event.key === 'p')) || 
                (event.ctrlKey && event.shiftKey && event.key === 's')) {
                event.preventDefault();
            }
        });
        
        // PDF.js initialization
        const pdfUrl = <?php echo json_encode($book_a); ?>; // Securely pass sanitized PHP variable
        let pdfDoc = null;
        let pageNum = 1;
        let pageCount = 0;
        let scale = 1.5;
        const canvas = document.getElementById("pdf-canvas");
        const ctx = canvas.getContext("2d");
        const errorMessage = document.getElementById("error-message");
        const readingProgress = document.getElementById("reading-progress");
        const timeSpentEl = document.getElementById("time-spent");
        const readingPaceEl = document.getElementById("reading-pace");
        const completionEl = document.getElementById("completion");
        
        // Reading statistics
        let startTime = Date.now();
        let pageChanges = 0;
        let focusMode = false;
        let darkMode = false;
        
        // Adjust scale based on device width
        function adjustInitialScale() {
            if (window.innerWidth < 768) {
                scale = 1.2;
            } else if (window.innerWidth < 480) {
                scale = 1.0;
            }
        }
        
        adjustInitialScale();
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const newScale = window.innerWidth < 768 ? (window.innerWidth < 480 ? 1.0 : 1.2) : 1.5;
            if (newScale !== scale) {
                scale = newScale;
                renderPage(pageNum);
            }
        });
        
        // Load PDF with error handling
        pdfjsLib.getDocument(pdfUrl).promise.then(doc => {
            pdfDoc = doc;
            pageCount = doc.numPages;
            document.getElementById('page-count').textContent = pageCount;
            renderPage(pageNum);
            updateStatistics();
            updateProgress();
        }).catch(error => {
            console.error("Error loading PDF:", error);
            showToast('danger', 'Failed to load the PDF. Please ensure the file is accessible.');
            errorMessage.style.display = "block";
            errorMessage.innerHTML = `<i class="bi bi-exclamation-triangle"></i> Failed to load the PDF. Please try again later.`;
        });
        
        // Render page function
        function renderPage(num) {
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: scale });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                const overlay = document.querySelector('.pdf-overlay');
                overlay.style.width = viewport.width + 'px';
                overlay.style.height = viewport.height + 'px';
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                page.render(renderContext);
                document.getElementById('page-num').textContent = num;
                updateProgress();
            }).catch(error => {
                showToast('danger', 'Error rendering page: ' + error.message);
            });
        }
        
        // Update progress bar
        function updateProgress() {
            const percentage = (pageNum / pageCount) * 100;
            readingProgress.style.width = `${percentage}%`;
            completionEl.textContent = `${Math.round(percentage)}%`;
        }
        
        // Update reading statistics
        function updateStatistics() {
            setInterval(() => {
                const timeElapsed = Math.floor((Date.now() - startTime) / 1000);
                const minutes = Math.floor(timeElapsed / 60);
                const seconds = timeElapsed % 60;
                timeSpentEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (pageChanges > 0 && timeElapsed > 0) {
                    const pagesPerHour = Math.round((pageChanges * 3600) / timeElapsed);
                    readingPaceEl.textContent = pagesPerHour;
                }
            }, 1000);
        }
        
        // Page navigation
        document.getElementById('prev').addEventListener('click', () => {
            if (pageNum <= 1) return;
            pageNum--;
            renderPage(pageNum);
            pageChanges++;
        });
        
        document.getElementById('next').addEventListener('click', () => {
            if (pageNum >= pageCount) return;
            pageNum++;
            renderPage(pageNum);
            pageChanges++;
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') {
                if (pageNum >= pageCount) return;
                pageNum++;
                renderPage(pageNum);
                pageChanges++;
            } else if (e.key === 'ArrowLeft') {
                if (pageNum <= 1) return;
                pageNum--;
                renderPage(pageNum);
                pageChanges++;
            }
        });
        
        // Zoom controls
        document.getElementById('zoom-in').addEventListener('click', () => {
            if (scale >= 2.5) return;
            scale += 0.2;
            renderPage(pageNum);
        });
        
        document.getElementById('zoom-out').addEventListener('click', () => {
            if (scale <= 0.5) return;
            scale -= 0.2;
            renderPage(pageNum);
        });
        
        // Focus mode toggle
        document.getElementById('toggle-focus').addEventListener('click', () => {
            document.body.classList.toggle('focus-mode');
            focusMode = !focusMode;
            const icon = document.querySelector('#toggle-focus i');
            icon.className = focusMode ? 'bi bi-eye-slash' : 'bi bi-eye';
            showToast('success', focusMode ? 'Focus mode enabled' : 'Focus mode disabled');
        });
        
        // Dark mode toggle
        document.getElementById('reading-mode-toggle').addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            darkMode = !darkMode;
            const icon = document.querySelector('#reading-mode-toggle i');
            icon.className = darkMode ? 'bi bi-sun' : 'bi bi-moon';
            showToast('success', darkMode ? 'Dark mode enabled' : 'Dark mode disabled');
        });
        
        // Auto-save last page read
        function saveReadingProgress() {
            localStorage.setItem(`${pdfUrl}_lastPage`, pageNum);
        }
        
        function loadReadingProgress() {
            const lastPage = localStorage.getItem(`${pdfUrl}_lastPage`);
            if (lastPage) {
                pageNum = parseInt(lastPage);
                renderPage(pageNum);
            }
        }
        
        window.addEventListener('beforeunload', saveReadingProgress);
        window.addEventListener('load', () => {
            loadReadingProgress();
            // Initialize toasts
            const toastElList = document.querySelectorAll('.toast');
            toastElList.forEach(toastEl => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        });
        
        // Inactivity timer for reading tips
        let inactivityTimer;
        const readingTips = [
            "Try the 20-20-20 rule: Every 20 minutes, look at something 20 feet away for 20 seconds.",
            "Remember to blink regularly to prevent eye strain.",
            "Adjust the zoom for comfortable reading.",
            "Try dark mode for nighttime reading.",
            "Taking regular short breaks improves comprehension.",
            "Use the focus mode to minimize distractions."
        ];
        
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(showReadingTip, 5 * 60 * 1000); // 5 minutes
        }
        
        function showReadingTip() {
            const tip = readingTips[Math.floor(Math.random() * readingTips.length)];
            const tipElement = document.createElement('div');
            tipElement.className = 'reading-tip';
            tipElement.innerHTML = `
                <i class="bi bi-lightbulb"></i> Reading Tip
                <p>${tip}</p>
                <button>×</button>
            `;
            
            document.body.appendChild(tipElement);
            
            tipElement.querySelector('button').addEventListener('click', () => {
                document.body.removeChild(tipElement);
            });
            
            setTimeout(() => {
                if (document.body.contains(tipElement)) {
                    document.body.removeChild(tipElement);
                }
            }, 10000);
            
            resetInactivityTimer();
        }
        
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart', 'touchmove'].forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });
        
        // Touch swipe navigation
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const minSwipeDistance = 50;
            if (touchEndX < touchStartX - minSwipeDistance) {
                if (pageNum >= pageCount) return;
                pageNum++;
                renderPage(pageNum);
                pageChanges++;
            }
            if (touchEndX > touchStartX + minSwipeDistance) {
                if (pageNum <= 1) return;
                pageNum--;
                renderPage(pageNum);
                pageChanges++;
            }
        }
        
        // Toast function
        function showToast(type, text) {
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
                    ${text}
                </div>
            `;
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
        
        resetInactivityTimer();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>