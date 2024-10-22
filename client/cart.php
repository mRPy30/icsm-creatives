<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title><?php echo "Payment"; ?></title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">
</head>

<body>
    <?php include '../client/navbar.php'; ?>

    <main class="main-content">
        <section class="container">
            <div class="text-header">
                <h2>My Cart</h2>
                <p>Your add to Cart can book again</p>
            </div>
        </section>
        <section class="cart-section">
            <div class="cart-content">

            </div>
        </section>
    </main>
</body>
</html>
