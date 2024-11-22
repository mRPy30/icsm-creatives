<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "ICSM Contact"; ?>
    </title>


    <!---CSS--->
    <link rel="stylesheet" href="../css/homepage.css">

    <!--CSS FRAMEWORK-->


    <!--ICON LINKS-->
    <script src="https://kit.fontawesome.com/11a4f2cc62.js" crossorigin="anonymous"></script>

    <!--FONT LINKS-->
    <link
        href="https://fonts.googleapis.com/css2?family=Abel&family=Inter:wght@400;800&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet"><!--ICON LINKS-->
    <link rel="stylesheet" href="font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

</head>

<body>
    <!-----Navbar------->
    <?php include '../homepage/navbar.php'; ?>


    <div class="contact-container">
        <div class="text-section">
            <h1>Let's be friends!</h1>
            <p>Choosing a photographer can be a big decision.<br>
                <b>At ICSM Creatives,</b> we’re more than just a team – we’re passionate storytellers ready to bring
                your
                vision to life. Let’s work together to create memories you’ll treasure forever. We’d love to hear from
                you and help you plan your dream session!"
            </p>
        </div>

        <div class="image-section">
            <img src="..\picture\team.jpg" alt="Group Photo">
        </div>

        <div class="form-section">
            <div class="contact-info">
                <p>Send Us a Note. We'd love to hear from you</p>
                <p><strong>Email:</strong> icsmcreatives@gmail.com</p>
                <p><strong>Phone Number:</strong> 0999999999</p>
            </div>
            <div class="contact-fillup">
                <h2>Contact:</h2>
                <form action="send_email.php" method="post">
                    <label for="name">Name: <span class="required-asterisk">*</span></label>
                    <input type="text" id="name" name="name" placeholder="First Name, Last Name" required>

                    <label for="email">Email: <span class="required-asterisk">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Email" required>

                    <label for="phone">Phone Number: <span class="required-asterisk">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>

                    <label for="message">Message: <span class="required-asterisk">*</span></label>
                    <textarea id="message" name="message" placeholder="Message" required></textarea>

                    <button type="submit">Send</button>
                </form>
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

        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>


        <script>
            document.getElementById('contactForm').addEventListener('submit', function (event) {
                const nameField = document.getElementById('name');
                const emailField = document.getElementById('email');
                const phoneField = document.getElementById('phone');
                const messageField = document.getElementById('message');

                // Custom messages
                if (nameField.value === "") {
                    nameField.setCustomValidity("Please enter your name.");
                } else {
                    nameField.setCustomValidity("");
                }

                if (emailField.value === "") {
                    emailField.setCustomValidity("Please enter a valid email address.");
                } else {
                    emailField.setCustomValidity("");
                }

                if (phoneField.value === "") {
                    phoneField.setCustomValidity("Please enter your phone number.");
                } else {
                    phoneField.setCustomValidity("");
                }

                if (messageField.value === "") {
                    messageField.setCustomValidity("Please enter your message.");
                } else {
                    messageField.setCustomValidity("");
                }
            });
        </script>

</body>

</html>