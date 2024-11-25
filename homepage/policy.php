<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Corporate | ICSM Creatives"; ?>
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

    <div class="title_page">
        <h1>ICSM CREATIVES POLICIES</h1>
    </div>
    <main class="events-container">
        <div class="policy-container">
            <div class="policy-cards">
                <div class="policy-card">
                    <h2>Cancellation Policy</h2>
                    <ul>
                        <li>Clients may submit a request of cancellation<b> at least 96 hours (4 days) </b>before the
                            scheduled
                            date.</li>
                        <li>Cancellations requested <b>3 days or less </b>before the event date will not be accepted,
                            ensuring
                            adequate preparation for the event.</li>
                    </ul>
                </div>

                <div class="policy-card">
                    <h2>Reschedule Policy</h2>
                    <ul>
                        <li>Clients may request a Reschedule for their booking <b>at least 168 hours (7 days)</b> before
                            the
                            event date.</li>
                        <li>Rescheduling requests <b>within 6 days or less </b>before the event date will not be
                            allowed.</li>
                    </ul>
                </div>

                <div class="policy-card">
                    <h2>Refund Policy</h2>
                    <ul>
                        <li>Clients may request a refund submitting a request <b>at least 48 hours </b>after
                            cancellation.</li>
                        <li>100% Refunds will only be processed under the following conditions:</li>
                    </ul>
                    <div class="refund-details">
                        <h3>Refundable Conditions:</h3>
                        <ul>
                            <li>Natural disasters that prevent the event from proceeding</li>
                            <li>Health emergencies with valid medical proof submission</li>
                            <li>Incorrect payments (e.g., overpayments or system errors)</li>
                        </ul>
                        <h3>Non-Refundable:</h3>
                        <ul>
                            <li>Any payments made as a 50% deposit are non-refundable</li>
                        </ul>
                        <h3>Processing Time:</h3>
                        <ul>
                            <li>Refunds will be processed within 48 business hours after the request has been approved
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="policy-card">
                    <h2>Delivery Policy</h2>
                    <ul>
                        <li>The photos and videos will be delivered to your personalized gallery <b>within 7 days</b>
                            after
                            the event date. Our team ensures timely delivery while maintaining the highest quality
                            standards for your cherished memories.</li>

                    </ul>
                </div>

            </div>
        </div>

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


        const carousel = document.querySelector('.gallery-item');
        const leftBtn = document.querySelector('.left-btn');
        const rightBtn = document.querySelector('.right-btn');
        let scrollAmount = 0;
        const scrollStep = 320; // Adjust based on the image width + gap

        leftBtn.addEventListener('click', () => {
            scrollAmount -= scrollStep;
            if (scrollAmount < 0) {
                scrollAmount = 0; // Prevent scrolling beyond the first image
            }
            carousel.style.transform = `translateX(-${scrollAmount}px)`;
        });

        rightBtn.addEventListener('click', () => {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            scrollAmount += scrollStep;
            if (scrollAmount > maxScroll) {
                scrollAmount = maxScroll; // Prevent scrolling beyond the last image
            }
            carousel.style.transform = `translateX(-${scrollAmount}px)`;
        });


    </script>


</body>

</html>