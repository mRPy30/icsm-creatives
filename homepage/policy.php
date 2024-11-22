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
        <p>Select an event type for photography or videography services.</p>
    </div>
    <main class="events-container">
        <div class="">

        </div>
    </main>


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