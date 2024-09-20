<?php
// Connection
include '../backend/dbcon.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $hashedPassword = md5($password);

        $query = "SELECT id FROM client WHERE email = '$email' AND password = '$hashedPassword'";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $matchedRows = mysqli_num_rows($result);

        if ($matchedRows > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row['id'];

            $_SESSION['name'] = $email;
            $_SESSION['id'] = $id;

            $redirect_url = "../client/booking.php?id=$id";

            echo '<style>
                body { overflow: hidden; }
                .loading-overlay {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    z-index: 10000;
                }
                .loading-circle {
                    display: inline-block;
                    width: 40px;
                    height: 40px;
                    border: 7px solid #E1DE8F;
                    border-radius: 50%;
                    border-top: 5px solid transparent;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>';
            echo '<div class="loading-overlay">
                    <div class="loading-circle"></div>
                  </div>';

            echo '<script>
                setTimeout(function() {
                    window.location.href = "' . $redirect_url . '";
                }, 2000);
            </script>';
            exit();
        } else {
            header("Location: ../homepage.php?login_error=true");
            exit();
        }
    }
}
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
                    <img src="../picture/prenup.jpg" alt="coverpage">
                    <img src="../picture/girls.jpg" alt="coverpage">
                    <img src="../picture/self.jpg" alt="coverpage">
                    <img src="../picture/wedding.jpg" alt="coverpage">
                </div>
                <div class="text">
                    <h2>Capture every precious moment through our lenses </h2>
                    <p>Get expert photographers and amazing photos, and <br>videos, starting from just PHP 2,500.</p>
                    <button id="book-now-btn" class="btn-cover">Book Now</button>
                </div>
                <div class="carousel-page-numbers">
                    <span class="page-number-text">1/5</span>
                </div>
                <div class="horizontal-line"></div>
            </div>
        </section>

        

        <section class="portfolio">
            <!-- Portfolio content here -->
            <div class="portfolio-title">
                <hr class="horizontal-line1">
                <h2>Explore Portfolio</h2>
                <hr class="horizontal-line2">
            </div>

            <div class="box-section">
                <!-- Portfolio items -->
                <div class="box1">
                    <div class="box4">
                        <div class="portfolio-content1">
                            <a href="portfolio.php">
                                <img src="../picture/wed.jpg" alt="Wedding-Engagement">
                            </a>
                            <h2>Wedding + Engagement</h2>

                        </div>
                    </div>
                </div>
                <div class="box2">
                    <div class="box5">
                        <div class="portfolio-content2">
                            <a href="portfolio.php">
                                <img src="../picture/birthday.jpg" alt="Birthday">
                            </a>
                            <h2>Birthday</h2>
                        </div>
                    </div>
                </div>
                <div class="box3">
                    <div class="box6">
                        <div class="portfolio-content3">
                            <a href="portfolio.php">
                                <img src="../picture/portrait.jpg" alt="Portrait">
                            </a>
                            <h2>Portrait + Family</h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="services">
            <div class="services-title">
                <h2>Check out these special deals!</h2>
                <h6>The ICSM price ratings</h6>
            </div>
            <div class="services-box-section">
                <!-- services price items -->
                <div class="service-box" style="padding-bottom:130px;">
                    <div class="package-name">
                        <h6>Package A</h6>
                        <p>Photoshoot</p>
                    </div>
                    <div class="price">
                        <h2>₱ 2,500</h2>
                    </div>
                    <div class="feature">
                        <h4>Things what you get:</h4>
                    </div>
                    <div class="price-details">
                        <div class="top">
                            <i class="fa-solid fa-check"></i>
                            <p>1 Hour Photoshoot</p>
                        </div>
                        <div class="bottom">
                            <i class="fa-solid fa-check"></i>
                            <p>100 pieces photo edited</p>
                        </div>
                    </div>
                </div>
                <div class="service-box" style="padding-bottom:130px;">
                    <div class="package-name">
                        <h6>Package B</h6>
                        <p>Video</p>
                    </div>
                    <div class="price">
                        <h2>₱ 30,000</h2>
                    </div>
                    <div class="feature">
                        <h4>Things what you get:</h4>
                    </div>
                    <div class="price-details">
                        <div class="top">
                            <i class="fa-solid fa-check"></i>
                            <p>Same day Edit</p>
                        </div>
                        <div class="bottom">
                            <i class="fa-solid fa-check"></i>
                            <p>10 minutes max video duration</p>
                        </div>
                    </div>
                </div>
                <div class="service-box">
                    <div class="package-name">
                        <h6>Package C</h6>
                        <p>Wedding Package</p>
                    </div>
                    <div class="price">
                        <h2>₱ 50,000</h2>
                    </div>
                    <div class="feature">
                        <h4>Things what you get:</h4>
                    </div>
                    <div class="price-details">
                        <div class="top">
                            <i class="fa-solid fa-check"></i>
                            <p>Same day Edit</p>
                        </div>
                        <div class="top">
                            <i class="fa-solid fa-check"></i>
                            <p>Unlimited Photoshoot</p>
                        </div>
                        <div class="bottom">
                            <i class="fa-solid fa-check"></i>
                            <p>Prenup Photoshoot</p>
                        </div>
                        <div class="bottom">
                            <i class="fa-solid fa-check"></i>
                            <p>Prenup Video
                        </div>
                        <div class="bottom">
                            <i class="fa-solid fa-check"></i>
                            <p>Video Highlights</p>
                        </div>
                    </div>
                </div>
            </div>
            <button class="button-services-section" id="view-more-btn">View More</button>
        </section>

        <section class="review">
            <div class="review-part">
                <div class="review-left">
                    <img src="../picture/review.jpg" alt="">
                </div>
                <div class="review-right">
                    <div class="testimonial">
                        <h5> Testimonial</h5>
                        <h4>"Hands down one of our best decisions in our wedding planning
                            process!"</h4>

                        <p>"Where do I even begin with these two!? Such amazing photographers -
                            so amazing for our
                            wedding. They are amazing at communicating their vision and aligning
                            it with yours, and
                            make sure it comes alive on your wedding day. They were patient and
                            fun and personable,
                            and felt more like friends than photographers on the big day. Their
                            images are striking,
                            being able to capture both beautiful posed shots as well as
                            heartfelt candids. Truly
                            amazing photos and we feel so lucky that they were there to capture
                            such a great and
                            memorable day."<br><br>
                            <i>- Kathryn Bernardo & Daniel Padilla </i>
                        </p>
                        <div class="testimonial-arrow"><i class="fa-solid fa-arrow-left"></i>
                            <i class="fa-solid fa-arrow-right" style="margin-left:10px;"></i>
                        </div>
                    </div>
                </div>
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
                            Our days are shaped by moments; joyful moments, big important life achievements, but also
                            the
                            ordinary, everyday moments. Anything could be special when you do it with the people closest
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
                            <a href="../homepage/booking.php"><button>Inquire about your date </button></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="footer-page">
            <div class="footer">
                <div class="footer-row">
                    <ul class="footer-left-link">
                        <li><a href="../login.php">Login</a></li>
                        <li><a href="../about.php">About</a></li>
                        <li><a href="../portfolio.php">Portfolio</a></li>
                        <li><a href="../review.php">Testimonial</a></li>
                        <li><a href="../contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="vertical-line-left"></div>
                <div class="footer-center-content">

                    <div class="footer-center">
                        <h6>About ICSM Creatives</h6>
                        <p>We are dedicated to serving women of color in an underrepresented bridal market. All brides
                            will find inspiration on our blog, in our digital publication, on our social circuit and at
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

        <div id="login_modal" class="modal">
            <div class="modal-content">
                <i class="fa-solid fa-xmark"></i>                
                <h2>Login</h2>
                <form class="form-fillup" method="POST" onsubmit="return validateForm()">
                    <input type="text" class="form" placeholder="Enter your Email" name="email" required>
                    <input type="password" class="form" placeholder="Enter your Password" name="password" id="password" required oninput="checkPasswordStrength(this)">
                        <div id="popup" class="popup">
                            <p id="popup-message"></p>
                        </div>
                    <button class="btn btn-lg btn-block btn-success" type="submit" name="submit" value="Submit"
                        style="height: 7vh;">Login</button>
                    <a href="#">Forget Password?</a>
                </form>
                <p style="margin-top: 5%;">Don’t have any account? <span id="register">Register now</span></p>
            </div>
        </div>
    </main>

    

    <script>

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
            const portfolioSection = document.querySelector('.portfolio');

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
        document.getElementById("book-now-btn").addEventListener("click", function() {
            window.location.href = "../homepage/event.php";
        });

        //register
        document.getElementById("register").addEventListener("click", function() {
            window.location.href = "../client/register.php";
        });

    </script>
</body>

</html>