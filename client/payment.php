<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that booking data and total cost are set
    $_SESSION['booking'] = array_merge($_SESSION['booking'] ?? [], $_POST); // Merge new data with existing
    $total_cost = array_sum($_POST['services'] ?? []);
    $_SESSION['total_cost'] = $total_cost;
}

if (!isset($_SESSION['booking']) || !isset($_SESSION['total_cost'])) {
    die("Booking data or total cost is missing. Please complete the previous steps.");
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
        <?php echo "Payment"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

</head>

<body>
    <!-----Navbar------->
    <?php include '../client/navbar.php'; ?>

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
                </div>
            </div>
        </section>
            <section class="booking-feed">
                <div class="content">
                    <div class="fillup-book">
                        <div class="form-book">
                            <div class="top-book">
                                <div class="title">
                                    <h3>Event Services</h3>
                                </div>
                                <div class="steps">
                                    <div class="circle active">
                                        <h4>1</h4>
                                    </div>
                                    <div class="progress-line"></div>
                                    <div class="circle">
                                        <h4>2</h4>
                                    </div>
                                    <div class="progress-line"></div>
                                    <div class="circle">
                                        <h4>3</h4>
                                    </div>
                                </div>
                            </div>
                            <form class="form-fillup needs-validation" action="../backend/confirm_booking.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">    
                                    <h2>Step 3: Payment</h2>
                                    <h3>Event Summary:</h3>
                                    <p>Date: <?php echo htmlspecialchars($_SESSION['booking']['event_date'] ?? 'Not provided'); ?></p>
                                    <p>Time: <?php echo htmlspecialchars($_SESSION['booking']['event_time'] ?? 'Not provided'); ?></p>
                                    <p>Type: <?php echo htmlspecialchars($_SESSION['booking']['type_of_event'] ?? 'Not provided'); ?></p>
                                    <p>Name: <?php echo htmlspecialchars($_SESSION['booking']['title_event'] ?? 'Not provided'); ?></p>
                                    <p>Location: <?php echo htmlspecialchars($_SESSION['booking']['event_location'] ?? 'Not provided'); ?></p>
                                    <h3>Breakdown of Costs:</h3>
                                    <div id="total-cost">Total Cost: PHP <?php echo htmlspecialchars($_SESSION['total_cost'] ?? 0); ?></div>
                                    <label for="paymentProof">Upload Payment Proof:</label>
                                    <input type="file" id="paymentProof" name="payment_proof" required><br>
                                    <div class="buttons-book">
                                        <button id="next" type="submit">Request Booking</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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