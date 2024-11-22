<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Hire Photographers with your sweet memories events | ICSM Creatives"; ?>
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

    <div class="title_page">
        <h1>Book Now to Capture Your Best Moments</h1>
        <p>Select an event type for photography or videography services.</p>
    </div>
    <main class="events-container">
        <div class="event-card" onclick="location.href='birthday.php?event=Birthday'">
            <img src="../picture/birthday1.jpg" alt="Birthday Event">
            <h3>Birthday</h3>
            <p>Capture the special moments of your birthday celebration.</p>
        </div>
        <div class="event-card" onclick="location.href='wedding.php?event=Wedding'">
            <img src="../picture/marriage1.jpg" alt="Wedding Event">
            <h3>Wedding</h3>
            <p>Make your wedding day unforgettable with our photography and videography.</p>
        </div>
        <div class="event-card" onclick="location.href= 'graduation.php?event=Graduation'">
            <img src="../picture/graduation1.jpg" alt="Graduation Event">
            <h3>Graduation</h3>
            <p>Celebrate your academic achievements with a memorable photoshoot.</p>
        </div>
        <div class="event-card" onclick="location.href='christening.php?event=Christening'">
            <img src="../picture/christening1.jpg" alt="Christening">
            <h3>Christening</h3>
            <p>We capture sweet memories for your baby Christening.</p>
        </div>
        <div class="event-card" onclick="location.href='adventure.php?event=Outdoors Adventures'">
            <img src="../picture/outdoor.png" alt="Outdoor Adventures">
            <h3>Outdoor Adventures</h3>
            <p>We capture the best moments of your outdoor adventures.</p>
        </div>
        <div class="event-card" onclick="location.href='family-portrait.php?event=Family Portraits'">
            <img src="../picture/family.jpg" alt="Family Portraits">
            <h3>Family Portraits</h3>
            <p>Create lifelong memories with a beautiful family portrait session.</p>
        </div>
        <div class="event-card" onclick="location.href='corporate.php?event=Corporate'">
            <img src="../picture/corporate1.jpg" alt="Corpo">
            <h3>Corporate</h3>
            <p>Celebrate big event photoshoot.</p>
        </div>
        <div class="event-card" onclick="location.href='maternity.php?event=Maternity'">
            <img src="../picture/genderreveal4.jpg" alt="Corpo">
            <h3>Maternity</h3>
            <p>Capture sweet maternity memories.</p>
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
</body>

</html>