<?php
session_start();
include '../backend/dbcon.php'; 

// Fetch the selected event from step 1 (booking.php)
if (!isset($_SESSION['booking'])) {
    header("Location: booking.php"); // Redirect to step 1 if no booking data found
    exit();
}

$type_of_event = $_SESSION['booking']['type_of_event'];

// Fetch all services included for the selected event type
$sql = "SELECT s.serviceID, s.service_name, s.price 
        FROM services s
        JOIN services es ON s.serviceID = es.serviceID
        JOIN event e ON es.eventID = e.eventID
        WHERE e.eventName = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $type_of_event);
$stmt->execute();
$result = $stmt->get_result();
$services = [];

while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

$stmt->close();
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
        <?php echo "Choose Your Package"; ?>
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
                            <h2>Services for <?php echo htmlspecialchars($type_of_event); ?></h2>

                            <?php if (!empty($services)): ?>
                                <form action="payment.php" method="POST">
                                    <div class="services-list">
                                        <?php foreach ($services as $service): ?>
                                            <div class="service-item">
                                                <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                                <p>Price: PHP <?php echo htmlspecialchars($service['price']); ?></p>
                                                <label>
                                                    <input type="checkbox" name="services[]" value="<?php echo htmlspecialchars($service['serviceID']); ?>">
                                                    Select this service
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                            <?php else: ?>
                                <p>No services available for this event.</p>
                            <?php endif; ?>
                            
                            <h3>Additional Services:</h3>
                            <label for="photographers">Additional Photographers:</label>
                            <input type="checkbox" id="photographers" name="services[]" value="2000" onclick="calculateTotal()"> PHP 2000<br>
                            
                            <label for="video">Teaser Video:</label>
                            <input type="checkbox" id="video" name="services[]" value="1500" onclick="calculateTotal()"> PHP 1500<br>
                            
                            <label for="invitation">Virtual Invitation:</label>
                            <input type="checkbox" id="invitation" name="services[]" value="500" onclick="calculateTotal()"> PHP 500<br>
                            
                            <div id="total-cost">Total Cost: PHP 0</div>
                            
                            <button type="submit" id="next">Next</button>
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
<script>
    function calculateTotal() {
    let total = 0;
    document.querySelectorAll('input[name="services[]"]:checked').forEach((service) => {
        total += parseFloat(service.value);
    });
    document.getElementById('total-cost').textContent = "Total Cost: PHP " + total;
}

</script>
</html>