<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Booking Succesful | ICSM Creatives"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/homepage.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
</head>

<style>
    /* General reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #FAF6F2;
    }

    .success-message-container {
        text-align: center;
        padding: 30px;
        width: 55%;
    }

    .icon {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
        font-size: 140px;
        color: #b3875a;
        animation: fadeInScale 0.4s ease-out forwards;
    }

    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.5);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .success-message-container h2 {
        font-size: 30px;
        color: #242424;
        margin-bottom: 25px;
        font-weight: bold;
    }

    .success-message-container p {
        font: normal 18px / normal 'poppins';
        color: #242424;
        margin-bottom: 55px;
    }

    .back-button {
        display: inline-block;
        background-color: #b3875a;
        color: #fff;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .back-button:hover {
        background-color: #946d4a;
    }
</style>



<body>
    <div class="success-message-container">
        <div class="icon">
            <i class="fa-regular fa-circle-check"></i>
        </div>
        <h2>Booking Request Successfully Submitted</h2>
        <p>Thank you for choosing ICSM Creatives! We have received your booking request and will review it shortly. Our
            team will be in touch soon to confirm the details.</p>
        <a href="../client/booking.php" class="back-button">Back to Booking</a>
    </div>
</body>

</html>