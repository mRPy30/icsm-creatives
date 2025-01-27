<?php
// Connection
include '../backend/dbcon.php';

// Fetch feedback data with joins to get client name and event name
$query = "SELECT f.*, c.name as client_name, e.eventName as event_name, b.title_event as booking_event 
          FROM feedback f
          LEFT JOIN client c ON f.clientID = c.clientID
          LEFT JOIN booking b ON f.bookingId = b.bookingId
          LEFT JOIN event e ON b.eventID = e.eventID
          WHERE f.status = 'Posted'
          ORDER BY f.feedback_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Online Event Booking | ICSM Creatives"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/homepage.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

</head>

<body>
    <!-----Navbar------->
    <?php include '../homepage/navbar.php'; ?>

    <main class="main-content">
        <section class="coverpage">
            <div class="cover-content">
                <div class="carousel">
                    <?php
                    include '../backend/dbcon.php';
                    $query = "SELECT picture FROM homepage_carousel WHERE is_active = 1";
                    $result = mysqli_query($conn, $query);
        
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $picture = base64_encode($row['picture']); 
                            echo "<img src='data:image/jpeg;base64,$picture' alt='Carousel Image'>";
                        }
                    } else {
                        echo "<p>No active carousel images available at the moment.</p>";
                    }
                
                    mysqli_close($conn);
                    ?>
                </div>
                <div class="Center-text">
                    <?php
                        include '../backend/dbcon.php';

                        $query = "SELECT heading, subheading FROM homepage_cover_text WHERE is_active = 1 LIMIT 1";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $heading = htmlspecialchars($row['heading']); 
                            $subheading = htmlspecialchars($row['subheading']); 
                        
                            echo "<h2>$heading</h2>";
                            echo "<p>$subheading</p>";
                        } else {
                            echo "HELLO CLIENT!";
                        }
                    
                        mysqli_close($conn);
                    ?>
                    <button id="book-now-btn" class="btn-cover">Book Now</button>
                </div>
                <div class="carousel-page-numbers">
                    <span class="page-number-text">1/5</span>
                </div>
                <div class="horizontal-line"></div>
            </div>
        </section>

        <section class="milestone-gallery">
            <div class="milestone-header">
                <h2>Capture for Every Milestone</h2>
                <p>Make every occasion unforgettable—start creating your perfect moment today.</p>
            </div>
            <div class="milestone-grid">
                <?php
                include '../backend/dbcon.php';
                $query = "SELECT eventName, milestone_img, milestone_text FROM event WHERE milestone_img IS NOT NULL AND milestone_text IS NOT NULL";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='milestone-card' onclick=\"location.href='{$row['eventName']}.php'\">
                            <a href='{$row['eventName']}.php' class='icon-link'>
                                <i class='fa-solid fa-arrow-right'></i>
                            </a>
                            <h3>{$row['eventName']}</h3>
                            <p>{$row['milestone_text']}</p>
                            <img src='data:image/jpeg;base64," . base64_encode($row['milestone_img']) . "' alt='{$row['eventName']}'>
                          </div>";
                }
                ?>
                <div class="milestone-card" onclick="location.href='event.php'">
                    <a href="event.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Browse Other Occasions:</h3>
                    <p>For every moment, we are here.</p>
                </div>
            </div>
        </section>

        <section class="why-choose">
            <div class="choose-us-section">
                <h2>Why Choose Us?</h2>
                <div class="features-grid">
                <?php
                    // Database connection
                    include '../backend/dbcon.php'; // Adjust path if necessary

                    $query = "SELECT icon, title, description FROM homepage_choose_us WHERE is_active = 1";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $icon = base64_encode($row['icon']); 
                            $title = htmlspecialchars($row['title']); 
                            $description = htmlspecialchars($row['description']); 
                        
                            echo "
                            <div class='feature-box'>
                                <div class='choose-us-icon'>
                                    <img src='data:image/png;base64,$icon' alt='$title Icon'>
                                </div>
                                <div class='choose-us-box-right'>
                                    <h3>$title</h3>
                                    <p>$description</p>
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<p>No features available at the moment. Please check back later.</p>";
                    }
                
                    mysqli_close($conn);
                ?>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <div class="how-it-works__container">
                <h2 class="how-it-works__title">How it Works?</h2>

                <div class="how-it-works__steps-wrapper">
                <?php
                include '../backend/dbcon.php';

                // Fetch the steps from the database
                $query = "SELECT step_number, heading, description FROM homepage_instruction WHERE is_active = 1 ORDER BY step_number ASC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "
                    <div class='how-it-works__step-box'>
                        <div class='how-it-works__step-content'>
                            <span class='how-it-works__step-number'>" . str_pad($row['step_number'], 2, '0', STR_PAD_LEFT) . "</span>
                            <h3 class='how-it-works__step-heading'>{$row['heading']}</h3>
                            <p class='how-it-works__step-text'>{$row['description']}</p>
                        </div>
                    </div>";
                }
                ?>
                </div>
            </div>
        </section>

        <section class="faq-section">
            <div class="faq-section__container">
                <div class="faq-section__left">
                    <h2 class="faq-section__title">
                        <span>Have</span>
                        <span>Questions?</span>
                    </h2>
                    <div class="faq-section__image">
                        <img src="../picture/faq-image.png" alt="FAQ Illustration">
                    </div>
                </div>
                <!-- Right Side - Accordion -->
                    <div class="faq-section__right">
                        <?php
                        include '../backend/dbcon.php';

                        // Fetch FAQ items from the database
                        $query = "SELECT question, answer FROM homepage_faq WHERE is_active = 1 ORDER BY faqID ASC";
                        $result = mysqli_query($conn, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='faq-section__item'>
                                <div class='faq-section_question'>
                                    {$row['question']}
                                    <span class='arrow'><i class='fa-solid fa-angle-down'></i></span>
                                </div>
                                <div class='faq-section_answer'>
                                    <p>{$row['answer']}</p>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="ms-testimonials">
            <h2 class="ms-testimonials-title">What our clients say about us.</h2>
            <div class="ms-testimonials-wrapper">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="ms-testimonial">
                        <i class="fa-solid fa-quote-left"></i>
                        <p class="ms-testimonial-quote">
                            <?php echo htmlspecialchars($row['feedback_description']); ?>
                        </p>
                        <p class="ms-testimonial-author">
                            <?php echo htmlspecialchars($row['client_name']); ?>
                        </p>
                        <div class="ms-testimonial-stars">
                            <?php
                            $rating = intval($row['rating']);
                            for ($i = 1; $i <= $rating; $i++) {
                                echo '<i class="fa-solid fa-star"></i>';
                            }
                            for ($i = $rating + 1; $i <= 5; $i++) {
                                echo '<i class="fa-solid fa-star empty-star"></i>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="about">
            <div class="about-page">
                <div class="about-left-content">
                    <div class="about-title">
                        <h1>Our Mission</h1>
                        <h5>is to make Life Memorable</h5>
                    </div>
                    <div class="about-description">
                        <p><B>We capture any occasion, easy and fast.</B><br>
                            Our days are shaped by moments; joyful moments, big important life achievements, but
                            also
                            the
                            ordinary, everyday moments. Anything could be special when you do it with the people
                            closest
                            to
                            you. But sometimes, these moments pass you by.<br><br>
                            Looking forward to work with you.</p>
                    </div>
                    <div class="more-button">
                        <a href="../homepage/about.php"><button>About Us</button></a>
                    </div>
                </div>


                <div class="about-right-content">
                    <div class="image1">
                        <img src="../picture/team.jpg" alt="Team-Picture">
                    </div>
                    <div class="image2">
                        <img src="../picture/behind-the-cam.jpg" alt="Behind-the-cam">
                    </div>
                </div>
            </div>
        </section>

        <section class="call-to-attention">
            <div class="banner-homepage">
                <div class="banner-image">
                    <img src="../picture/CTAcover.jpg" alt="coverpage">
                </div>
                <div class="banner-content">
                    <div class="banner-inner-content">
                        <h1>Let's make something incredible together</h1>
                        <div class="CTA-button">
                            <a href="../homepage/event.php"><button>Inquire about your date </button></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="footer-page">
            <div class="footer">
                <div class="footer-row">
                    <ul class="footer-left-link">
                        <li><a href="../client/login.php">Login</a></li>
                        <li><a href="../homepage/about.php">About</a></li>
                        <li><a href="../homepage/events.php">Offer Events</a></li>
                        <li><a href="../homepage/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="vertical-line-left"></div>
                <div class="footer-center-content">

                    <div class="footer-center">
                        <h6>About ICSM Creatives</h6>
                        <p>We are dedicated to serving women of color in an underrepresented bridal market. All
                            brides
                            will find inspiration on our blog, in our digital publication, on our social circuit and
                            at
                            our national bridal events.</p>

                        <div class="social-meadia-links">
                            <h6>Connect with us</h6>
                            <div class="icons">
                                <a class="facebook" href="https://www.facebook.com/icsmcreatives" target="_blank"><i
                                        class="fa-brands fa-facebook"></i>
                                </a>
                                <a class="mail" href="https://www.facebook.com/cvsuimusofficialpage" target="_blank"><i
                                        class="fa-solid fa-envelope"></i>
                                </a>
                                <a class="instagram" href="https://www.instagram.com/icsmcreatives">
                                    <i class=" fa-brands fa-instagram"></i>
                                </a>
                                <a class="tiktok" href="https://www.tiktok.com/@icsm.creatives">
                                    <i class="fa-brands fa-tiktok"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vertical-line-right"></div>
                <div class="footer-logo">
                    <a href="../homepage/homepage.php">
                        <img src="../picture/logo.png" alt="logo">
                    </a>
                </div>
            </div>
        </section>

        <section class="going-back">
            <div class="arrow-up-button back-to-top-hidden">
                <button class="back-to-top" onclick="scrollToTop()"><i class="fas fa-arrow-up"></i></button>
            </div>
        </section>

        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>
    </main>



    <script>

        document.querySelectorAll('.faq-section_question').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const arrow = header.querySelector('.arrow');

                content.classList.toggle('active');
                arrow.classList.toggle('active');

                document.querySelectorAll('.faq-section_answer').forEach(otherContent => {
                    if (otherContent !== content && otherContent.classList.contains('active')) {
                        otherContent.classList.remove('active');
                        otherContent.previousElementSibling.querySelector('.arrow').classList.remove('active');
                    }
                });
            });
        });

        const images = document.querySelectorAll('.carousel img');
        const pageNumbers = document.querySelectorAll('.carousel-page-numbers .page-number');
        let idx = 0;

        function showImage(index) {
            images.forEach((image) => {
                image.style.display = 'none';
            });

            images[index].style.display = 'block';
            idx = index;

            // Update active page number
            pageNumbers.forEach((pageNumber, i) => {
                if (i === index) {
                    pageNumber.classList.add('active');
                } else {
                    pageNumber.classList.remove('active');
                }
            });

            updatePageNumberText(index);
        }

        function nextImage() {
            idx = (idx + 1) % images.length;
            showImage(idx);
        }

        function updatePageNumberText(index) {
            const currentPage = index + 1;
            const totalPages = images.length;
            const pageNumberText = document.querySelector('.page-number-text');
            pageNumberText.textContent = currentPage + '/' + totalPages;
        }

        setInterval(nextImage, 3000); // Change image every 5 seconds (adjust this value as needed)

        // Add click event listeners to page numbers
        pageNumbers.forEach((pageNumber, index) => {
            pageNumber.addEventListener('click', () => {
                showImage(index);
            });
        });



        //Arrow up button
        document.addEventListener('DOMContentLoaded', function () {
            const arrowButton = document.querySelector('.arrow-up-button');
            const coverpage = document.querySelector('.coverpage');

            window.addEventListener('scroll', function () {
                const coverpageRect = coverpage.getBoundingClientRect();

                if (coverpageRect.bottom <= 0) {
                    arrowButton.classList.remove('back-to-top-hidden');
                } else {
                    arrowButton.classList.add('back-to-top-hidden');
                }
            });
        });

        function scrollToTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const headerSection = document.querySelector('.header-section');
            const coverContent = document.querySelector('.cover-content');
            const portfolioSection = document.querySelector('.horizontal-line');

            function handleScroll() {
                const coverContentRect = coverContent.getBoundingClientRect();
                const portfolioRect = portfolioSection.getBoundingClientRect();

                if (coverContentRect.bottom > 0 && portfolioRect.top > 0) {
                    // If in cover-content, change navbar style
                    headerSection.classList.add('cover-content-style');
                } else {
                    // If outside cover-content, revert navbar style
                    headerSection.classList.remove('cover-content-style');
                }
            }

            // Initial check on page load
            handleScroll();

            // Listen for scroll events
            window.addEventListener('scroll', handleScroll);
        });


        //event
        document.getElementById("book-now-btn").addEventListener("click", function () {
            window.location.href = "../homepage/event.php";
        });

        //register
        document.getElementById("register").addEventListener("click", function () {
            window.location.href = "../client/register.php";
        });


    </script>
</body>

</html>