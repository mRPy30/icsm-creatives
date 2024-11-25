<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Birthday | ICSM Creatives"; ?>
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
        <h1>Birthday Memories</h1>
    </div>
    <main class="events-container">
        <div class="birthday-gallery-wrapper">
            <!----Line 1--->
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthdayyy.jpg" alt="Birthday Celebration 2">
            </div>
            <div class="birthday-gallery-item landscape">
                <img src="../picture/birthday2.jpg" alt="Birthday Celebration 1">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/bornday.jpg" alt="Birthday Celebration 3">
            </div>

            <!----Line 2---->
            <div class="birthday-gallery-item portrait">
                <img src="../picture/7birthday.jpg" alt="Birthday Celebration 3">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthdaygallery1.jpg" alt="Birthday Celebration 5">
            </div>
            <div class="birthday-gallery-item landscape">
                <img src="../picture/birthday15.jpg" alt="Birthday Celebration 4">
            </div>
            <!----Line 3---->
            <div class="birthday-gallery-item landscape">
                <img src="../picture/debut1.jpg" alt="Birthday Celebration 6">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/18birthday.jpg" alt="Birthday Celebration 5">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthday11.jpg" alt="Birthday Celebration 5">
            </div>
            <!----Line 4---->
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthday13.jpg" alt="Birthday Celebration 5">
            </div>
            <div class="birthday-gallery-item landscape">
                <img src="../picture/bday.jpg" alt="Birthday Celebration 6">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthdaygallery.jpg" alt="Birthday Celebration 5">
            </div>
            <!----Line 5---->
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthdayyyy.jpg" alt="Birthday Celebration 5">
            </div>
            <div class="birthday-gallery-item portrait">
                <img src="../picture/birthday6.jpg" alt="Birthday Celebration 5">
            </div>
            <div class="birthday-gallery-item landscape">
                <img src="../picture/birthday77.jpg" alt="Birthday Celebration 6">
            </div>

        </div>



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

        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>


    </main>

    <div class="view-image">
        <span class="close-btn">&times;</span>
        <span class="nav-btn prev-btn">&#10094;</span>
        <span class="nav-btn next-btn">&#10095;</span>
        <img src="" alt="Viewed Image">
    </div>

</body>
<script>
        const viewContainer = document.querySelector('.view-image');
        const viewImage = viewContainer.querySelector('img');
        const closeBtn = document.querySelector('.close-btn');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const galleryImages = document.querySelectorAll('.birthday-gallery-item img');
        let currentIndex = 0;

        // Convert NodeList to Array for easier navigation
        const imagesArray = Array.from(galleryImages);

        // Open image view
        galleryImages.forEach((img, index) => {
            img.addEventListener('click', () => {
                currentIndex = index;
                updateViewImage();
                viewContainer.classList.add('active');
            });
        });

        // Close image view
        closeBtn.addEventListener('click', () => {
            viewContainer.classList.remove('active');
        });

        // Close on click outside image
        viewContainer.addEventListener('click', (e) => {
            if (e.target === viewContainer) {
                viewContainer.classList.remove('active');
            }
        });

        // Navigate to previous image
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex - 1 + imagesArray.length) % imagesArray.length;
            updateViewImage();
        });

        // Navigate to next image
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex + 1) % imagesArray.length;
            updateViewImage();
        });

        // Update viewed image
        function updateViewImage() {
            const currentImg = imagesArray[currentIndex];
            viewImage.src = currentImg.src;
            viewImage.alt = currentImg.alt;
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (viewContainer.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    prevBtn.click();
                } else if (e.key === 'ArrowRight') {
                    nextBtn.click();
                } else if (e.key === 'Escape') {
                    closeBtn.click();
                }
            }
        });
    </script>
</html>