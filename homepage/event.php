<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo "Hire Photographers with your sweet memories events | ICSM Creatives"; ?>
    </title>
    <link rel="stylesheet" href="../css/homepage.css">
    
</head>
<body>
    <!-----Navbar------->
    <?php include '../homepage/navbar.php'; ?>
    <div class="title_page">
        <h1>Book Now to Capture Your Best Moments</h1>
        <p>Select an event type for photography or videography services.</p>
    </div>
    <main class="events-container">
        <div class="event-card" onclick="location.href='events/birthday.php?event=Birthday'">
            <img src="../picture/birthday1.jpg" alt="Birthday Event">
            <h3>Birthday</h3>
            <p>Capture the special moments of your birthday celebration.</p>
        </div>
        <div class="event-card" onclick="location.href='events/marriage.php?event=Wedding'">
            <img src="../picture/marriage1.jpg" alt="Marriage Event">
            <h3>Marriage</h3>
            <p>Make your wedding day unforgettable with our photography and videography.</p>
        </div>
        <div class="event-card" onclick="location.href='events/graduation.php?event=Graduation'">
            <img src="../picture/graduation1.jpg" alt="Graduation Event">
            <h3>Graduation</h3>
            <p>Celebrate your academic achievements with a memorable photoshoot.</p>
        </div>
        <div class="event-card" onclick="location.href='events/christening.php?event=Christening'">
            <img src="../picture/christening1.jpg" alt="Christening">
            <h3>Christening</h3>
            <p>We capture sweet memories for your baby Christening.</p>
        </div>
        <div class="event-card" onclick="location.href='events/adventure.php?event=Outdoors Adventures'">
            <img src="../picture/outdoor.png" alt="Outdoor Adventures">
            <h3>Outdoor Adventures</h3>
            <p>We capture the best moments of your outdoor adventures.</p>
        </div>
        <div class="event-card" onclick="location.href='events/family-portrait.php?event=Family Portraits'">
            <img src="../picture/family.jpg" alt="Family Portraits">
            <h3>Family Portraits</h3>
            <p>Create lifelong memories with a beautiful family portrait session.</p>
        </div>
        <div class="event-card" onclick="location.href='events/corporate.php?event=Corporate'">
            <img src="../picture/corporate1.jpg" alt="Corpo">
            <h3>Corporate</h3>
            <p>Celebrate big event photoshoot.</p>
        </div>
        <div class="event-card" onclick="location.href='events/maternity.php?event=Maternity'">
            <img src="../picture/maternity.png" alt="Corpo">
            <h3>Maternity</h3>
            <p>Capture sweet maternity memories.</p>
        </div>
        
    </main>
</body>
</html>
