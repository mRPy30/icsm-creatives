<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Outdoor Adventure | ICSM Creatives"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/homepage.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    <!-----Navbar------->
    <?php include '../homepage/navbar.php'; ?>

</head>

<body>

    <section class="container-service">
        <div class="wrapper">
            <div class="service-content">
                <div class="service-details">
                    <h2>OUTDOOR ADVENTURE</h2>
                    <p>Every birthday is a special moment to celebrate life and growth. Let us capture your joy and
                        excitement as you create new memories. Because it’s never just another birthday.</p>
                </div>

                <div class="gallery-item">
                    <img src="../picture/outdoorr1.jpg" alt="Team-Picture">
                    <img src="../picture/adventure-events.jpg" alt="Team-Picture">
                </div>

                <div class="service-tips">
                    <h3>Helpful Tips</h3>

                    <div class="service-item">
                        <div class="service-item_question">
                            Reschedule Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>We understand that plans can change. If you need to reschedule your session, please
                                contact us at least 48 hours in advance. We’ll do our best to find a new date that works
                                for you, depending on availability. Rescheduling within this time frame helps us keep
                                our schedule organized and fair for all clients.
                            </p>

                        </div>
                    </div>

                    <div class="service-item">
                        <div class="service-item_question">
                            Cancellation Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>If you must cancel your booking, please notify us at least 48 hours in advance. Deposits
                                are non-refundable, but if you reschedule within 30 days, we’ll apply your deposit
                                toward the new date. This policy helps us manage our calendar efficiently and ensures
                                that each client receives the best possible service.
                            </p>

                        </div>
                    </div>


                    <div class="service-item">
                        <div class="service-item_question">
                            Delivery Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>Our goal is to deliver high-quality photos and videos within 3-7 days after your event.
                                We’ll keep you updated on our progress and notify you of any unexpected delays. Fast
                                delivery without sacrificing quality is our priority, so you can relive your special
                                moments as soon as possible. Thank you for choosing ICSM Creatives!
                            </p>

                        </div>
                    </div>

                </div>
            </div>

            <div class="right-service">
                <div class="service-booking">
                    <div class="header-service">
                        <h3>Adventure</h3>
                        <div class="price-start">
                            <p>START FROM</p>
                            <h3>4,500</h3>
                        </div>
                    </div>
                    <div class="available-services">
                        <h4>Available Services</h4>
                        <ul>
                            <li>Outdoor Photo Only</li>
                            <li>Outdoor Photo and Video</li>
                            <li>Outdoor Video Highlights Only</li>
                        </ul>
                        <div class="form-group">
                            <label for="event_location">Where shall we capture your memories?</label>
                            <div class="input-with-icon">
                                <i class="fa-solid fa-location-dot"></i>
                                <select id="event_location" name="event_location" required>
                                    <option class="placeholder-location" value="" disabled selected>Choose a location
                                    </option>
                                    <!-- Placeholder -->
                                    <option>Imus</option>
                                    <option>Bacoor</option>
                                    <option>Kawit</option>
                                    <option>Dasmarinas</option>
                                    <option>Tagaytay</option>
                                    <option>Batangas</option>
                                    <option>Manila</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn-book" onclick="location.href='../client/login.php?event=Birthday'">Start
                            Booking
                            Now</button>
                    </div>
                </div>
                <div class="promo-container">
                    <div class="promo-content">
                        <div class="promo-title"> ALL-IN-ONE Package 20% OFF!</div>
                        <div class="promo-description">
                            Get <b>20% OFF</b> when you purchase everything!
                        </div>
                        <div class="promo-cta">Book Now and SAVE BIG!</div>
                        <div class="offer-banner">
                            <span class="main-text">Don't miss out on this exclusive offer</span>.<br>
                            <span class="limited-time">Limited time only!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="why-choose">
        <div class="choose-us-section">
            <h2>Why Choose Us?</h2>
            <div class="features-grid">
                <div class="feature-box">
                    <div class="choose-us-icon"><img src="../picture/peace-of-mind.png" alt="Budget Friendly Icon">
                    </div>
                    <div class="choose-us-box-right">
                        <h3>Relax and Have Fun</h3>
                        <p>Our photographers will capture all the best moments of your special event. All you need to do
                            is enjoy you’re special day!
                        </p>
                    </div>
                </div>

                <div class="feature-box">
                    <div class="choose-us-icon"><img src="../picture/folder.png" alt="Budget friendly">
                    </div>
                    <div class="choose-us-box-right">
                        <h3>Budget Friendly</h3>
                        <p>We got you affordable packages that fit your budget while still delivering amazing results.
                        </p>
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
                        <h3>High Quality Shots</h3>
                        <p>Get the high-quality photos and video that will make you smile every time you look at them.
                        </p>
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

    <script>
        document.querySelectorAll('.service-item_question').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const arrow = header.querySelector('.arrow');

                content.classList.toggle('active');
                arrow.classList.toggle('active');

                document.querySelectorAll('.service-item_answer').forEach(otherContent => {
                    if (otherContent !== content && otherContent.classList.contains('active')) {
                        otherContent.classList.remove('active');
                        otherContent.previousElementSibling.querySelector('.arrow').classList.remove('active');
                    }
                });
            });
        });

    </script>


</body>

</html>