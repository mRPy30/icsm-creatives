<style>
    /***** Homepage Navbar *****/

    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }

    .cover-content-style {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    .cover-content-style .nav-links li a {
        color: #fcf6f6 !important;
    }

    .cover-content-style .logo img {
        content: url('../picture/logoDark.png');
    }
    .cover-content-style .register button{
        background-color: #fcf6f6;
        color: #1c1c1d;
    }

    .cover-content-style .nav-item a:hover {
        color: #C2BE63 !important;
        transition: all 0.3s;
    }
    
    .header-section {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 5% 0px 5%;
        position: fixed;
        background: #fcf6f6;
        z-index: 9999;
        box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.2);
    }

    .header-section .logo {
        width: 10%;
    }

    .header-section .logo img {
        width: clamp(100%, 5%, 20%);
        height: 11%;
        cursor: pointer;
        margin-top: 5px;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 20px 50px; 
    }
  
    .nav-links li {
      padding: 0;
      display: inline-block;
    }

    .nav-links li a {
      color: #1c1c1c;
      font: normal 600 17px/normal 'Poppins';
      cursor: pointer;
      letter-spacing: 1px;
      text-decoration: none;
    }
    .nav-links li a.active {
        color: #C2BE63;
    }

    .nav-links li a:hover {
        color: #C2BE63;
        transition: all 0.3s;
    }

    .nav-item {
        list-style: none;
        display: inline-block;
        margin: 5px 40px 5px 10px;
    }

    .register button {
        width: 100px;
        height: 34px;
        left: 95vw;
        font: normal 500 15px/normal 'Poppins';
        filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
        display: inline-block;
        cursor: pointer;
        background: #1C1C1D;
        border-radius: 25px;
        padding: 3px 17px;
        transition: background-color 0.3s;
        color: #FCF6F6;
        border: none;
    }

    .register button:hover {
        background-color: #454548;
        color: #fcfcfc;
    }

    .hamburger-menu {
        display: none;
        font-size: 24px;
        cursor: pointer;
        color: #1c1c1d;
    }

    .hamburger-menu:focus{
        background: #1c1c1c;
    }

    .rotate {
        transform: rotate(90deg);
        transition: transform 0.5s ease;
    }

@media screen and (max-width: 768px) {
    .nav-links {
      display: none;
    }
  
    .register {
      display: none;
    }
  
    .hamburger-menu {
      display: block;
      animation: rotateMenu 0.5s ease-in-out forwards;
    }


    .cover-content-style .nav-links li a {
      color: #1c1c1c !important;
    }

    .cover-content-style .hamburger-menu{
        color: #fbfbfb !important;
    }

    .nav-active {
      display: flex;
      flex-direction: column;
      position: absolute;
      background-color: #fbfbfb;
      top: 30px;
      left: 0;
      right: 0;
      text-align: center;
    }
  
    .nav-active li {
      margin: 10px 0;
    }
    
    }


    
</style>

<body>
    <header class="header-section">
        <div class="logo">
            <a href="../homepage/homepage.php">
                <img src="../picture/logo.png" alt="logo">
            </a>
        </div>
        <ul class="nav-links">
            <li>
                <a href="homepage.php">Home</a>
            </li>
            <li>
                <a href="portfolio.php">Portfolio</a>
            </li>
            <li>
                <a href="services.php">Services</a>
            </li>
            <li>
                <a href="about.php">About</a>
            </li>
            <li>
                <a href="contact.php">Contact</a>
            </li>
        </ul>
        <a class="register" href="../client/register.php"><button>Register</button></a>
        <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
    </header>

    <script>
        const hamburgerMenu = document.querySelector('.hamburger-menu');
        const navLinks = document.querySelector('.nav-links');

        function toggleMenu() {
            hamburgerMenu.classList.toggle('rotate');
            navLinks.classList.toggle('nav-active');
        }
        
    </script>
</body>

</html>