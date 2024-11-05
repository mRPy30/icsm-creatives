<?php
include '../backend/logout.php';
include '../backend/dbcon.php';
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
        <?php echo "Welcome to ICSM!"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!---Date picker--->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    

</head>

<body>
    <!-----Navbar------->
    <?php include '../client/navbar.php'; ?>

    <main class="main-content">
        <section class="container">
            
        </section>
        <section class="booking-status">
        <div class="status-content">
            <?php
                $statuses = ['Pending', 'Accepted', 'Declined', 'Completed'];
                $currentDate = date('Y-m-d');

                foreach ($statuses as $status) {
                    $headingText = $status == 'Accepted' ? 'Upcoming Events' : "{$status} Events";

                    echo "<div class='status-panel" . ($status == 'Accepted' ? ' active' : '') . "' id='{$status}-panel'>";
                    echo "<h3>{$headingText}</h3>";

                    if ($status == 'Accepted') {
                        echo "<p>Here are the moments you`ve captured with ICSM Creatives.</p>";
                    } 
                
                    echo "<div class='event-container' id='events-{$status}'>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                }
                ?>
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