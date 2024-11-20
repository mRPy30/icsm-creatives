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
                    <img src="../picture/coverpage1.jpg" alt="coverpage">
                    <img src="../picture/christening1.jpg" alt="coverpage">
                    <img src="../picture/corporate.jpg" alt="coverpage">
                    <img src="../picture/coverpage5.jpg" alt="coverpage">
                    <img src="../picture/family.jpg" alt="coverpage">
                </div>
                <div class="Center-text">
                    <h2>Capture Every Precious Moments, Through our Lenses </h2>
                    <p>Customize Your Package with Flexible Options and Budget-Friendly Add-Ons That Fits Your
                        Unique
                        Style and Budget.</p>
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
                <div class="milestone-card" onclick="location.href='birthday.php'">
                    <a href="birthday.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Birthdays:</h3>
                    <p>Every year is a reason to celebrate.</p>
                    <img src="../picture/cake.png" alt="Birthday Cake">
                </div>

                <div class="milestone-card" onclick="location.href='wedding.php'">
                    <a href="marriage.php" class="icon-link"> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Wedding:</h3>
                    <p>Capturing every step of your journey to love.</p>
                    <img src="../picture/marriage-ring.png" alt="Marriage">
                </div>

                <div class="milestone-card" onclick="location.href='family-portrait.php'">
                    <a href="family-portrait.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Family Portraits:</h3>
                    <p>Highlight your growing family.</p>
                    <img src="../picture/family.png" alt="Family Portraits">
                </div>

                <div class="milestone-card" onclick="location.href='graduation.php'">
                    <a href="graduation.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Graduation:</h3>
                    <p>Capture your academic achievements.</p>
                    <img src="../picture/graduation.png" alt="Graduation">
                </div>

                <div class="milestone-card" onclick="location.href='christening.php'">
                    <a href="christening.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Baby:</h3>
                    <p>Capture the precious moments of your baby's early days.</p>
                    <img src="../picture/baby.png" alt="Baby">
                </div>

                <div class="milestone-card" onclick="location.href='corporate.php'">
                    <a href="corporate.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Corporate Events:</h3>
                    <p>Showcase your business milestones and team achievements.</p>
                    <img src="../picture/business.png" alt="Corporate Events">
                </div>

                <div class="milestone-card" onclick="location.href='adventure.php'">
                    <a href="adventure.php" class="icon-link">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <h3>Outdoor Adventures:</h3>
                    <p>Capture the thrill of your outdoor experiences.</p>
                    <img src="../picture/adventure.png" alt="Outdoor Adventures">
                </div>

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
                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/folder.png" alt="Budget Friendly Icon">
                        </div>
                        <div class="choose-us-box-right">
                            <h3>Budget Friendly</h3>
                            <p>We believe great photography and videography should be accessible to everyone. That’s
                                why
                                we offer affordable packages that fit your budget while still delivering amazing
                                results.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/hassle-free.png" alt="Hassle Free Icon">
                        </div>
                        <div class="choose-us-box-right">
                            <h3>Hassle-Free Booking</h3>
                            <p>Simply select a package and a photographer will be assigned to you shortly.</p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/fast-delivery.png" alt="Fast Delivery Icon">
                        </div>
                        <div class="choose-us-box-right">
                            <h3>Fast Delivery</h3>
                            <p>You won’t have to wait long to relive your special moments. We ensure quick delivery
                                of
                                your
                                photos and videos, so you can start enjoying and sharing them as soon as possible.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/camera.png" alt="Great Photographers Icon">
                        </div>
                        <div class="choose-us-box-right">
                            <h3>Great Photographers</h3>
                            <p>Our photographers and videographers are passionate about capturing your story. They
                                know
                                how to make you feel comfortable and bring out the best in every shot.</p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/picture.png" alt="Great Photos Icon"></div>
                        <div class="choose-us-box-right">
                            <h3>Great Photos</h3>
                            <p>We don’t just take pictures—we capture emotions, details, and memories that will last
                                a
                                lifetime. Our high-quality photos will make you smile every time you look at them.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="choose-us-icon"><img src="../picture/peace-of-mind.png" alt="Peace of Mind Icon">
                        </div>
                        <div class="choose-us-box-right">
                            <h3>Peace of Mind</h3>
                            <p>When you book with us, you can relax knowing everything is in good hands. We take
                                care of
                                all the details, so you can focus on enjoying your day while we capture every
                                special
                                moment.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>





        <section class="how-it-works">
            <div class="how-it-works__container">
                <h2 class="how-it-works__title">How it Works?</h2>

                <div class="how-it-works__steps-wrapper">

                    <div class="how-it-works__step-box">
                        <div class="how-it-works__step-content">
                            <span class="how-it-works__step-number">01</span>
                            <h3 class="how-it-works__step-heading">Book</h3>
                            <p class="how-it-works__step-text">We don't just take pictures—we capture emotions,
                                stories,
                                and memories that will last a lifetime. Our high-quality photos will make you smile
                                every time you look at them.</p>
                        </div>
                    </div>

                    <div class="how-it-works__step-box">
                        <div class="how-it-works__step-content">
                            <span class="how-it-works__step-number">02</span>
                            <h3 class="how-it-works__step-heading">Plan</h3>
                            <p class="how-it-works__step-text">We don't just take pictures—we capture emotions,
                                stories,
                                and memories that will last a lifetime. Our high-quality photos will make you smile
                                every time you look at them.</p>
                        </div>
                    </div>

                    <div class="how-it-works__step-box">
                        <div class="how-it-works__step-content">
                            <span class="how-it-works__step-number">03</span>
                            <h3 class="how-it-works__step-heading">Shoot</h3>
                            <p class="how-it-works__step-text">We don't just take pictures—we capture emotions,
                                stories,
                                and memories that will last a lifetime. Our high-quality photos will make you smile
                                every time you look at them.</p>
                        </div>
                    </div>

                    <div class="how-it-works__step-box">
                        <div class="how-it-works__step-content">
                            <span class="how-it-works__step-number">04</span>
                            <h3 class="how-it-works__step-heading">Download</h3>
                            <p class="how-it-works__step-text">We don't just take pictures—we capture emotions,
                                stories,
                                and memories that will last a lifetime. Our high-quality photos will make you smile
                                every time you look at them.</p>
                        </div>
                    </div>
                </div>

                <div class="how-it-works__cta">
                    <a href="#" class="how-it-works__link">Learn how it works! →</a>
                </div>
            </div>
        </section>




        <section class="faq-section">
            <div class="faq-section__container">
                <!-- Left Side - Image and Title -->
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
                    <!-- FAQ Item 1 -->
                    <div class="faq-section__item">
                        <div class="faq-section_question">
                            What types of photography do you do?
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="faq-section_answer">
                            <p>Hiring a photographer is a great idea because they capture important moments with
                                professional quality and a creative touch. Whether it's a special event, personal
                                milestone,
                                or business need, photographers have the skills, equipment, and expertise to produce
                                stunning images that tell your story or enhance your brand. Plus, they handle all
                                the
                                details, from planning and shooting to editing, saving you time and ensuring you get
                                beautiful, high-quality photos that you'll cherish or can use to boost your
                                business.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="faq-section__item">
                        <div class="faq-section_question">
                            Why should I hire a ICSM Creatives ?
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="faq-section_answer">
                            <p>Hiring a photographer is a great idea because they capture important moments with
                                professional quality and a creative touch. Whether it's a special event, personal
                                milestone,
                                or business need, photographers have the skills, equipment, and expertise to produce
                                stunning images that tell your story or enhance your brand. Plus, they handle all
                                the
                                details, from planning and shooting to editing, saving you time and ensuring you get
                                beautiful, high-quality photos that you'll cherish or can use to boost your
                                business.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="faq-section__item">
                        <div class="faq-section_question">
                            What's your cancellation/rescheduling policy?
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="faq-section_answer">
                            <p>Hiring a photographer is a great idea because they capture important moments with
                                professional quality and a creative touch. Whether it's a special event, personal
                                milestone,
                                or business need, photographers have the skills, equipment, and expertise to produce
                                stunning images that tell your story or enhance your brand. Plus, they handle all
                                the
                                details, from planning and shooting to editing, saving you time and ensuring you get
                                beautiful, high-quality photos that you'll cherish or can use to boost your
                                business.
                            </p>

                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="faq-section__item">
                        <div class="faq-section_question">
                            Are my payment refundable?
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="faq-section_answer">
                            <p>Hiring a photographer is a great idea because they capture important moments with
                                professional quality and a creative touch. Whether it's a special event, personal
                                milestone,
                                or business need, photographers have the skills, equipment, and expertise to produce
                                stunning images that tell your story or enhance your brand. Plus, they handle all
                                the
                                details, from planning and shooting to editing, saving you time and ensuring you get
                                beautiful, high-quality photos that you'll cherish or can use to boost your
                                business.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="faq-section__item">
                        <div class="faq-section_question">
                            Do you have props?
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="faq-section_answer">
                            <p>Hiring a photographer is a great idea because they capture important moments with
                                professional quality and a creative touch. Whether it's a special event, personal
                                milestone,
                                or business need, photographers have the skills, equipment, and expertise to produce
                                stunning images that tell your story or enhance your brand. Plus, they handle all
                                the
                                details, from planning and shooting to editing, saving you time and ensuring you get
                                beautiful, high-quality photos that you'll cherish or can use to boost your
                                business.
                            </p>

                        </div>
                    </div>

                </div>
            </div>
        </section>




        <section class="ms-testimonials">
            <h2 class="ms-testimonials-title">What our clients say about us.</h2>
            <div class="ms-testimonials-wrapper">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
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
                            <a href="../homepage/events.php"><button>Inquire about your date </button></a>
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
            const portfolioSection = document.querySelector('.milestone-gallery');

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