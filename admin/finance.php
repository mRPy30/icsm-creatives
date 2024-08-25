<?php 
session_start();
//Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];


$sqlRevenue = "SELECT SUM(totalRevenue) AS totalRevenue FROM revenue"; 
$resultRevenue = $conn->query($sqlRevenue);

if ($resultRevenue->num_rows > 0) {
    $rowRevenue = $resultRevenue->fetch_assoc();
    $totalRevenue = $rowRevenue['totalRevenue'];
} else {
    $totalRevenue = 0;
}

$sqlExp = "SELECT SUM(amount) AS amount FROM expenses"; 
$resultExp = $conn->query($sqlExp);

if ($resultExp->num_rows > 0) {
    $rowExp = $resultExp->fetch_assoc();
    $totalExp = $rowExp['amount'];
} else {
    $totalExp = 0;
}

$sqlExpenses = "SELECT date, category FROM expenses";
$resultExpenses = $conn->query($sqlExpenses);

$sqlPackages = "SELECT * FROM package";
$resultPackages = $conn->query($sqlPackages);
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
        <?php echo "Admin | Finance"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    
    <style>
        body {
            overflow-y: auto;
        }       
    </style>
    
</head>
    
<body>

    <section class="finance">
        <div class="mid-fin">
            <div class="left-finance">
                <div class="top">
                    <div class="fin-box">
                    <h4>₱ <?php echo number_format($totalExp); ?></h4>
                        <p>Expenses</p>
                    </div>
                    <div class="fin-box">
                    <h4>₱ <?php echo number_format($totalRevenue); ?></h4>
                        <p>Overall Revenue</p>
                    </div>
                </div>
                <div class="bottom">
                    <div class="projects">
                        <h4>Total Finish Project</h4>
                        <table class="header-table">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>No. of Projects</th>
                                </tr>
                            </thread>
                        </table>
                        <div class="data-container">
                            <table class="data-table">
                                <tbody>
                                    <tr>
                                        <td>2023</td>
                                        <td>4</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-finance">
                <div class="reports">
                    <h4>Expenses Reports</h4>
                    <table class="header-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                            </tr>
                        </thread>
                    </table>
                    <div class="data-container">
                        <table class="data-table">
                            <tbody>
                                <?php
                                while ($rowExpense = $resultExpenses->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . date('F j, Y', strtotime($rowExpense['date'])); "</td>";
                                    echo "<td>" . $rowExpense['category'] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="bot-button">
                        <button>View More</button>
                    </div>    
                </div>
            </div>
        </div>
            <div class="packages">
                <div class="pack-box">
                    <div class="top">
                        <h4>Packages Prices</h4>
                        <button class="add-button"><i class="fa-solid fa-plus"></i> Add New</button>
                    </div>
                    <div class="finance-tbl">
                        <table class="header-table">
                            <thead>
                                <tr>
                                    <th>Details</th>
                                    <th>Category</th>
                                    <th>Package Name</th>
                                    <th>Price Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="data-container">
                            <table class="data-table">
                            <tbody>
                                <?php if ($resultPackages->num_rows > 0) : ?>
                                    <?php while ($rowPackage = $resultPackages->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo $rowPackage['packageDetails']; ?></td>
                                            <td><?php echo $rowPackage['packageCategory']; ?></td>
                                            <td><?php echo $rowPackage['packageName']; ?></td>
                                            <td><?php echo '₱ ' . number_format($rowPackage['packagePrice'], 2); ?></td>
                                            <td>
                                                <form method="post" action="../backend/package.php" id="packageForm">
                                                    <input type="hidden" name="packageId" value="<?php echo $rowPackage['packageId']; ?>">
                                                    <input type="hidden" name="editPackageDetails" value="<?php echo $rowPackage['packageDetails']; ?>">
                                                    <button type="button" name="edit" onclick="editPackage(<?php echo $rowPackage['packageId']; ?>)">Edit</button>
                                                    <button type="submit" name="delete">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">No packages found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            </table>
                        </div>
                    </div>               
                </div>
            </div>
            <!-- Popup -->
            <div id="popup" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="hidePopup()">&times;</span>
                    <div class="form-container">
                        <form method="post" action="../backend/package.php">
                            <input type="hidden" name="submitPackage" value="1"> <!-- Add this line -->
                            <input type="hidden" name="tableName" value="package"> <!-- Specify the table name -->
                            <div class="form-group">
                                <label for="details">Package Details</label>
                                <input type="text" name="packageDetails" id="details" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="category">Package Category</label>
                                <input type="text" name="packageCategory" id="category" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="packageName">Package Name</label>
                                <input type="text" name="packageName" id="packageName" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="amount">Price Amount</label>
                                <div class="input-group">
                                    <input type="text" name="packagePrice" id="amount" class="form-control" oninput="formatAmount()">
                                </div>
                            </div>
                            <button type="submit" class="btn-save-event">Add Package Price</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>  

</body>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const viewMoreButton = document.querySelector('button');

        viewMoreButton.addEventListener('click', function () {
            window.location.href = '../admin/expenses.php';
        });

        const addButton = document.querySelector('.add-button');
        const popup = document.getElementById('popup');

        addButton.addEventListener('click', function () {
            popup.style.display = 'block';
        });
    });

    function hidePopup() {
        const popup = document.getElementById('popup');
        popup.style.display = 'none';
    }

    document.addEventListener('mousemove', function () {
        clearTimeout(checkInactivity);
        checkInactivity();
    });

    document.addEventListener('keypress', function () {
        clearTimeout(checkInactivity);
        checkInactivity();
    });

    function formatAmount() {
        var amountInput = document.getElementById('amount');
        var amountValue = amountInput.value.replace(/[^\d.]/g, '');
        var numericValue = parseFloat(amountValue);
        if (!isNaN(numericValue)) {
            var formattedAmount = '₱' + numericValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            amountInput.value = formattedAmount;
        } else {
            amountInput.value = '₱';
        }
    }

    document.getElementById('amount').value = '₱';

    function updatePackage(packageId) {
        const packageForm = document.getElementById('packageForm');
        packageForm.action = '../backend/package.php';
        packageForm.method = 'post';

        // Add an input field to indicate the update action
        const updateInput = document.createElement('input');
        updateInput.type = 'hidden';
        updateInput.name = 'updatePackage';
        updateInput.value = '1';

        // Add packageId to the form
        const packageIdInput = document.createElement('input');
        packageIdInput.type = 'hidden';
        packageIdInput.name = 'packageId';
        packageIdInput.value = packageId;

        // Append the input fields to the form
        packageForm.appendChild(updateInput);
        packageForm.appendChild(packageIdInput);

        // Submit the form
        packageForm.submit();
    }

    function editPackage(packageId) {
        showEditPopup();
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../backend/package.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var packageDetails = JSON.parse(xhr.responseText);
            
                document.getElementById('details').value = packageDetails.packageDetails;
                document.getElementById('category').value = packageDetails.packageCategory;
                document.getElementById('packageName').value = packageDetails.packageName;
                document.getElementById('amount').value = '₱' + parseFloat(packageDetails.packagePrice).toFixed(2);
            
                const submitButton = document.querySelector('.btn-save-event');
                submitButton.textContent = 'Update Package Ratings';
                submitButton.removeEventListener('click', addPackage);
                submitButton.addEventListener('click', function () {
                    updatePackage(packageId);
                });
            }
        };
        xhr.send("edit=1&packageId=" + packageId);
    }

    // Function to show the edit popup
    function showEditPopup() {
        const popup = document.getElementById('popup');
        popup.style.display = 'block';
    }

</script>


</html>