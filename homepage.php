<?php
include 'db_connect.php'; // Include the new database connection

// Fetch all approved items
try {
    $stmt = $pdo->prepare(
        "SELECT * FROM item_reports 
         WHERE report_status = 'approved' 
         ORDER BY item_datetime DESC"
    );
    $stmt->execute();
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate items into lost and found arrays
    $lost_items = [];
    $found_items = [];

    foreach ($all_items as $item) {
        if ($item['report_type'] == 'lost') {
            $lost_items[] = $item;
        } else {
            $found_items[] = $item;
        }
    }

} catch (PDOException $e) {
    echo "";
    $lost_items = [];
    $found_items = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RETRIEVE | TUPV Lost and Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="landing.css"> <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="homepage.css">
</head>
<body>

    <header class="main-header">
        <nav class="main-nav">
            <div class="logoContainer">
                <a href="landing.html" class="logoLink"> 
                    <img src="images/logo.png" alt="TUPV Retrieve Logo" class="logoImg">
                    <div class="headerText">
                        <span class="universityName">Retrieve TUPV</span>
                        <span class="universityMotto" id="current-date-time">Loading date...</span>
                    </div>
                </a>
            </div>
            
            <div class="navigationItems">
                <div class="navItem"><a href="homepage.php">Home</a></div>
                <div class="navItem"><a href="#" id="about-link">About</a></div>
                <div class="navItem"><a href="#" id="contact-link">Contact</a></div>
            </div>
        </nav>
    </header>
    
    <div class="page-wrapper">
        <aside class="sidebar collapsed" id="desktop-sidebar">
            <button class="sidebar-toggle" id="sidebar-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <nav class="sidebar-nav">
                <a href="homepage.php" class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard</span>
                </a>
                <a href="reportlost.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                    <span>Report Lost Item</span>
                </a>
                <a href="reportfound.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                    <span>Report Found Item</span>
                </a>
                <a href="profile.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Profile</span>
                </a>
            </nav>
        </aside>

        <div class="app-container" id="app-container">
            
            <nav class="mobile-nav">
                <a href="reportlost.php" class="nav-icon" title="Report Lost">
                    <img src="images/reportlost.png" alt="Report Lost">
                </a>
                <a href="reportfound.php" class="nav-icon" title="Report Found">
                     <img src="images/reportfound.png" alt="Report Found">
                </a>
                <a href="homepage.php" class="nav-icon active" title="Home"> <img src="images/home.png" alt="Home">
                </a>
                <a href="profile.php" class="nav-icon" title="Profile">
                    <img src="images/user.png" alt="Profile">
                </a>
            </nav>

            <div class="main-content">
                
                <div class="filter-controls">
                     <div class="category-filter-container">
                         <div class="select-wrapper">
                             <select id="category-filter-select">
                                 <option value="">All TUPV Categories</option>
                                 <option value="Electronics">Electronics</option>
                                 <option value="Books & Notes">Books & Notes</option>
                                 <option value="Clothing">Uniforms & Clothing</option>
                                 <option value="Accessories">Accessories & IDs</option>
                                 <option value="Other">Other</option>
                             </select>
                         </div>
                     </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" id="lost-btn">Looking For (Lost)</button>
                        <button class="filter-btn" id="found-btn">Turned In (Found)</button>
                    </div>
                </div>

                <div class="items-container">
                    
                    <div id="lost-items-list">
                        <?php if (empty($lost_items)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 40px; grid-column: 1 / -1;">No lost items reported on campus yet.</p>
                        <?php else: ?>
                            <?php foreach ($lost_items as $item): 
                                $item_name = htmlspecialchars($item['item_name_specific']);
                                if (empty($item_name)) {
                                    $item_name = htmlspecialchars($item['item_category']); 
                                }
                                $category = htmlspecialchars($item['item_category']);
                                $location = htmlspecialchars($item['item_location']);
                                $description = htmlspecialchars($item['item_description']);
                                $image_path = htmlspecialchars($item['image_path'] ?? ''); 
                                $date_time_obj = new DateTime($item['item_datetime']);
                                $date_reported = $date_time_obj->format('F j, Y \a\t g:i A'); 
                            ?>
<div class="item-card" 
     data-category="<?php echo $category; ?>"
     data-status="LOST ITEM"
     data-item-name="<?php echo $item_name; ?>"
     data-location="<?php echo $location; ?>"
     data-date="<?php echo $date_reported; ?>"
     data-description="<?php echo $description; ?>"
     data-image-src="<?php echo $image_path; ?>">

    <div class="item-details">
        <h3>LOST ITEM</h3>
        <p><strong>ITEM:</strong> <?php echo $item_name; ?></p>
                                        <p><strong>DESCRIPTION:</strong> <?php echo $description; ?></p>
                                        <p><strong>LAST SEEN:</strong> <?php echo $location; ?></p>
                                        <p><strong>DATE REPORTED:</strong> <?php echo $date_reported; ?></p>
                                    </div>
                                    <div class="item-image">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?php echo $image_path; ?>" alt="<?php echo $item_name; ?>">
                                        <?php else: ?>
                                            <div class="item-image no-image"><span>NO IMAGE</span></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="found-items-list" style="display: none;">
                        <?php if (empty($found_items)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 40px; grid-column: 1 / -1;">No found items reported on campus yet.</p>
                        <?php else: ?>
                            <?php foreach ($found_items as $item): 
                                $item_name = htmlspecialchars($item['item_name_specific']);
                                $category = htmlspecialchars($item['item_category']);
                                $location = htmlspecialchars($item['item_location']);
                                $description = htmlspecialchars($item['item_description']);
                                $image_path = htmlspecialchars($item['image_path'] ?? ''); 
                                $date_time_obj = new DateTime($item['item_datetime']);
                                $date_reported = $date_time_obj->format('F j, Y \a\t g:i A'); 
                            ?>
<div class="item-card" 
     data-category="<?php echo $category; ?>"
     data-status="FOUND ITEM"
     data-item-name="<?php echo $item_name; ?>"
     data-location="<?php echo $location; ?>"
     data-date="<?php echo $date_reported; ?>"
     data-description="<?php echo $description; ?>"
     data-image-src="<?php echo $image_path; ?>">

    <div class="item-details">
        <h3>FOUND ITEM</h3>
        <p><strong>ITEM:</strong> <?php echo $item_name; ?></p>
                                        <p><strong>DESCRIPTION:</strong> <?php echo $description; ?></p>
                                        <p><strong>FOUND AT:</strong> <?php echo $location; ?></p>
                                        <p><strong>DATE FOUND:</strong> <?php echo $date_reported; ?></p>
                                    </div>
                                    <div class="item-image">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?php echo $image_path; ?>" alt="<?php echo $item_name; ?>">
                                        <?php else: ?>
                                            <div class="item-image no-image"><span>NO IMAGE</span></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <section class="info-section">
                    <h2>How Retrieve Works for TUPV Students</h2>
                    <div class="steps-container">
                        <div class="step">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                            <h3>1. File a Report</h3>
                            <p>Lose something on campus? Post the details securely. Our system catalogues the entry for TUPV admin verification.</p>
                        </div>
                        <div class="step">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                            <h3>2. System Matching</h3>
                            <p>Once verified, it hits the feed. Our platform connects students who lost items with those who found them across the campus.</p>
                        </div>
                        <div class="step">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"></path><path d="m9 12 2 2 4-4"></path></svg>
                            <h3>3. Safe Retrieval</h3>
                            <p>Claim your recovered item safely at the designated TUPV Student Affairs office.</p>
                        </div>
                    </div>
                </section>
                </div>
        </div>
    </div>

    <footer>
        <div class="footer-row">
            <div class="footer-col">
                <h3 class="foot_logo_text">Retrieve | TUP Visayas</h3>
                <p>Your trusted campus partner in finding what's lost. We create a community of trust and support for all TUPV students and faculty.</p>
            </div>
            
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a id="footer-about-link">About Us</a></li>
                    <li><a id="footer-how-it-works-link">How It Works</a></li>
                    <li><a id="footer-faq-link">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contact & Legal</h3>
                <ul>
                    <li><a id="footer-contact-link">Contact Us</a></li>
                    <li><a id="footer-privacy-link">Privacy Policy</a></li>
                    <li><a id="footer-terms-link">Terms of Service</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Stay Connected</h3>
                <p>Follow us on social media for system updates and important TUPV announcements.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://x.com/" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://www.instagram.com/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <p class="copyright">&copy; 2025 Retrieve. All rights reserved. | Technological University of the Philippines Visayas</p>
    </footer>

    <div id="about-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>About Retrieve TUPV</h2>
            <p>Retrieve is a modern solution specifically built for the Technological University of the Philippines Visayas campus. Our platform connects people who have lost items with those who have found them, fostering a community of trust.</p>
        </div>
    </div>

    <div id="how-it-works-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>How It Works</h2>
            <p>Post details about your lost or found item securely. Our system catalogues the entry, and you can wait for TUPV administrative approval to finalize the match safely and transparently.</p>
        </div>
    </div>

    <div id="faq-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <h4>How do I report an item on campus?</h4>
                <p>Click on the "Report Lost Item" or "Report Found Item" button on your dashboard to securely fill out the necessary details.</p>
            </div>
            <div class="faq-item">
                <h4>What happens after I submit a report?</h4>
                <p>Your report is sent to a TUPV administrator for review. Once verified, it will be visible on the public feed to help facilitate a match.</p>
            </div>
        </div>
    </div>

    <div id="footer-contact-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>Contact TUPV Admin</h2>
            <p><strong>Email:</strong> support@retrieve.tupv.edu.ph</p>
            <p><strong>Phone:</strong> +63 994 300 8493</p>
        </div>
    </div>

    <div id="privacy-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>Privacy Policy</h2>
            <p>Your privacy is important to us. We only collect personal information when we truly need it to provide a service to you, collecting it by fair and lawful means.</p>
            <p>We do not share any personally identifying information publicly or with third-parties, except when required to by law or to facilitate a secure item return on campus.</p>
        </div>
    </div>

    <div id="terms-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2>Terms of Service</h2>
            <p>By accessing this website, you are agreeing to be bound by these terms of service and agree that you are responsible for compliance with TUPV rules and local laws.</p>
            <p>You may not use the materials for any commercial purpose, or attempt to reverse engineer any software contained on the platform.</p>
        </div>
    </div>

    <div id="item-details-modal" class="modal-overlay">
        <div class="modal-content item-details-modal-content">
            <button class="close-modal" id="details-modal-close">&times;</button>
            
            <div class="details-modal-image">
                <img id="modal-item-image" src="" alt="Item Image">
            </div>
            
            <div class="details-modal-info">
                <h3 id="modal-item-status"></h3>
                <p><strong>ITEM:</strong> <span id="modal-item-name"></span></p>
                <p><strong>CATEGORY:</strong> <span id="modal-item-category"></span></p>
                <p><strong>LOCATION:</strong> <span id="modal-item-location"></span></p>
                <p><strong>DATE:</strong> <span id="modal-item-date"></span></p>
                <p><strong>DESCRIPTION:</strong></p>
                <p id="modal-item-description"></p>
            </div>
        </div>
    </div>

    <script>
        // --- Date/Time Script ---
        function updateDateTime() {
            const dateTimeElement = document.getElementById('current-date-time');
            if(dateTimeElement) {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                dateTimeElement.textContent = now.toLocaleDateString('en-US', options);
            }
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // --- Guest Routing ---
        document.addEventListener('DOMContentLoaded', () => {
            const userType = sessionStorage.getItem('userType');
            if (userType === 'guest') {
                const guestLinks = ['reportlost.php', 'reportfound.php', 'profile.php'];
                guestLinks.forEach(link => {
                    document.querySelectorAll(`a[href="${link}"]`).forEach(el => el.href = 'guestprofile.html');
                });
            }
        });

        // --- Sidebar Toggle ---
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('desktop-sidebar');
        if(sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        // --- Modal Logic (Unified) ---
        const modals = {
            'about-link': 'about-modal',
            'footer-about-link': 'about-modal',
            'footer-how-it-works-link': 'how-it-works-modal',
            'footer-faq-link': 'faq-modal',
            'contact-link': 'footer-contact-modal',
            'footer-contact-link': 'footer-contact-modal',
            'footer-privacy-link': 'privacy-modal',
            'footer-terms-link': 'terms-modal'
        };

        Object.keys(modals).forEach(linkId => {
            const linkElement = document.getElementById(linkId);
            if(linkElement) {
                linkElement.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.getElementById(modals[linkId]).classList.add('show');
                });
            }
        });

        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.modal-overlay').classList.remove('show');
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('show');
            }
        });

        // --- Item Details Modal Data Injection ---
        const detailsModal = document.getElementById('item-details-modal');
        const modalImage = document.getElementById('modal-item-image');
        
        function openItemModal(card) {
            document.getElementById('modal-item-status').textContent = card.dataset.status;
            document.getElementById('modal-item-name').textContent = card.dataset.itemName;
            document.getElementById('modal-item-category').textContent = card.dataset.category;
            document.getElementById('modal-item-location').textContent = card.dataset.location;
            document.getElementById('modal-item-date').textContent = card.dataset.date;
            document.getElementById('modal-item-description').textContent = card.dataset.description;
            
            const imageSrc = card.dataset.imageSrc;
            if (imageSrc && imageSrc !== '') {
                modalImage.src = imageSrc;
                document.querySelector('.details-modal-image').style.display = 'block';
            } else {
                document.querySelector('.details-modal-image').style.display = 'none'; 
            }
            
            document.getElementById('modal-item-status').style.color = card.dataset.status === 'LOST ITEM' ? 'var(--accent-amber)' : 'var(--primary-blue)';
            detailsModal.classList.add('show');
        }

        document.getElementById('lost-items-list').addEventListener('click', (e) => {
            const card = e.target.closest('.item-card');
            if (card) openItemModal(card);
        });

        document.getElementById('found-items-list').addEventListener('click', (e) => {
            const card = e.target.closest('.item-card');
            if (card) openItemModal(card);
        });

        // --- Filtering Logic ---
        const lostBtn = document.getElementById('lost-btn');
        const foundBtn = document.getElementById('found-btn');
        const categoryFilterSelect = document.getElementById('category-filter-select');
        const lostItemsList = document.getElementById('lost-items-list');
        const foundItemsList = document.getElementById('found-items-list');
        
        function applyFilters(categoryFilter) {
            const currentList = lostBtn.classList.contains('active') ? lostItemsList : foundItemsList;
            const allCards = currentList.querySelectorAll('.item-card');

            allCards.forEach(card => {
                const cardCategory = card.dataset.category;
                const isInCategory = !categoryFilter || categoryFilter === "" || cardCategory === categoryFilter;
                card.style.display = isInCategory ? 'flex' : 'none';
            });
        }

        lostBtn.addEventListener('click', () => {
            lostBtn.classList.add('active');
            foundBtn.classList.remove('active');
            lostItemsList.style.display = ''; 
            foundItemsList.style.display = 'none'; 
            applyFilters(categoryFilterSelect.value); 
        });

        foundBtn.addEventListener('click', () => {
            foundBtn.classList.add('active');
            lostBtn.classList.remove('active');
            foundItemsList.style.display = ''; 
            lostItemsList.style.display = 'none'; 
            applyFilters(categoryFilterSelect.value); 
        });

        categoryFilterSelect.addEventListener('change', () => applyFilters(categoryFilterSelect.value));
        
        const params = new URLSearchParams(window.location.search);
        const category = params.get('category');
        if (category) {
            categoryFilterSelect.value = category;
            applyFilters(category);
        }
    </script>
</body>
</html>