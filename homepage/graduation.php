<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Graduation | ICSM Creatives"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/homepage.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    <!-----Navbar------->
    <?php include '../homepage/navbar.php'; ?>

    <!--Google Map Api-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPzSHoxlFDTzFpBHyAKuQAMCadJu0x1Wo&libraries=places"></script>



</head>

<body>

    <section class="container-service">
        <div class="wrapper">
            <div class="service-content">
                <div class="service-details">
                    <h2>Graduation</h2>
                    <p>Graduation is more than a milestone—it's a celebration of hard work, growth, and new beginnings.
                        At ICSM Creatives, we honor this achievement by capturing the pride, joy, and excitement of your
                        graduation day. Let us create stunning images that celebrate your journey and mark this
                        unforgettable chapter in your life. Together, we’ll preserve memories that inspire you for a
                        lifetime.
                    </p>
                </div>

                <div class="gallery-carousel">
                    <button class="carousel-btn left-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <div class="gallery-item">
                        <img src="../picture/graduation1.jpg" alt="Picture">
                        <img src="../picture/graddd.jpg" alt="Celebration">
                        <img src="../picture/graduation2.jpg" alt="picture">
                        <img src="../picture/graduation5.jpg" alt="picture">
                        <img src="../picture/graduation4.jpg" alt="picture">
                        <img src="../picture/graduation3.jpg" alt="picture">
                        <img src="../picture/graduation6.jpg" alt="picture">
                    </div>
                    <button class="carousel-btn right-btn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>

                <div class="see-more-photos">
                    <a href="graduation-gallery.php" target="_blank">See More Photos</a>
                </div>

                <div class="service-tips">
                    <h3>Helpful Tips</h3>

                    <div class="service-item">
                        <div class="service-item_question">
                            Reschedule Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>You may request to reschedule their booking <b>at least 168 hours (7 days)</b> before
                                the
                                event date. Rescheduling requests made <b>within 6 days or less </b>of the event date
                                will not be accepted. <a href="policy.php" class="learn-more-link">Learn
                                    more</a>.
                            </p>

                        </div>
                    </div>

                    <div class="service-item">
                        <div class="service-item_question">
                            Cancellation Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>You may submit a request of cancellation <b>at least 96 hours (4 days)</b> before the
                                scheduled date.
                                Cancellations requested <b>3 days or less before </b>the event date will not be
                                accepted,
                                ensuring adequate preparation for the event. <a href="policy.php"
                                    class="learn-more-link">Learn more</a>.
                            </p>

                        </div>
                    </div>


                    <div class="service-item">
                        <div class="service-item_question">
                            Delivery Policy
                            <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                        <div class="service-item_answer">
                            <p>Your photos and videos will be delivered to your personalized gallery <b>within 7 days
                                </b>after
                                the event date. Our team ensures timely delivery while maintaining the highest quality
                                standards for your cherished memories.
                                <a href="policy.php" class="learn-more-link">Learn more</a>.
                            </p>

                        </div>
                    </div>

                </div>
            </div>

            <div class="right-service">
                <div class="service-booking">
                    <div class="header-service">
                        <h3>Graduation</h3>
                        <div class="price-start">
                            <p>START FROM</p>
                            <h3>4,500</h3>
                        </div>
                    </div>
                    <div class="available-services">
                        <h4>Available Services</h4>
                        <ul>
                            <li>Graduation Photoshoot Onlyy</li>
                            <li>Graduation Video Highlights Only</li>
                            <li>Graduation Photoshoot and Video</li>
                        </ul>
                        <div class="form-group">
                            <label for="event_location">Where shall we capture your memories?</label>
                            <div class="input-with-icon">
                                <i class="fa-solid fa-location-dot"></i>
                                <input type="text" id="event_location" name="event_location" placeholder="Input your event location" required autocomplete="off">
                            </div>
                            <!-- Add container for Google Map -->
                            <div id="google-map-container" "></div>
                        </div>
                        <button class="btn-book" onclick="redirectToLogin()">Start Booking Now</button>

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
        function redirectToLogin() {
    const location = document.getElementById('event_location').value;
    window.location.href = `../client/login.php?event=Wedding&location=${encodeURIComponent(location)}`;
}
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

        document.addEventListener('DOMContentLoaded', function() {
        const locationInput = document.getElementById('event_location');
        const googleMapContainer = document.getElementById('google-map-container');

        // Function to load Google Maps for any input location
        function loadGoogleMap(location) {
            if (!location) return;
            googleMapContainer.style.display = 'block';
            
            // Create an iframe for Google Maps
            const mapFrame = document.createElement('iframe');
            mapFrame.width = '100%';
            mapFrame.height = '300';
            mapFrame.style.border = '0';
            mapFrame.loading = 'lazy';
            mapFrame.allowFullscreen = true;
            
            // Construct Google Maps embed URL
            const mapUrl = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAPzSHoxlFDTzFpBHyAKuQAMCadJu0x1Wo&q=${encodeURIComponent(location + ', Philippines')}`;
            mapFrame.src = mapUrl;
            
            // Clear previous map and add new one
            googleMapContainer.innerHTML = '';
            googleMapContainer.appendChild(mapFrame);
        }

        // Event listener for location input
        locationInput.addEventListener('input', function() {
            const inputLocation = this.value.trim();
            
            if (inputLocation.length > 2) {
                clearTimeout(this.mapLoadTimeout);
                this.mapLoadTimeout = setTimeout(() => {
                    loadGoogleMap(inputLocation);
                }, 500);
            }
        });

        // Load map when input loses focus
        locationInput.addEventListener('blur', function() {
            const inputLocation = this.value.trim();
            if (inputLocation) {
                loadGoogleMap(inputLocation);
            }
        });
    });

    </script>


</body>

</html>