<?php
session_start();
// Connection
include '../backend/dbcon.php';


// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Calculate total expenses
$sqlTotalExpenses = "SELECT SUM(amount) AS totalExpenses FROM expenses";
$resultTotalExpenses = $conn->query($sqlTotalExpenses);

if ($resultTotalExpenses->num_rows > 0) {
    $rowTotalExpenses = $resultTotalExpenses->fetch_assoc();
    $totalExpenses = $rowTotalExpenses['totalExpenses'];
} else {
    $totalExpenses = 0;
}


// Calculate total accepted revenue
$sqlAcceptedRevenue = "SELECT SUM(total_cost) AS totalAcceptedRevenue FROM booking WHERE status = 'Accepted'";
$resultAcceptedRevenue = $conn->query($sqlAcceptedRevenue);

if ($resultAcceptedRevenue->num_rows > 0) {
    $rowAcceptedRevenue = $resultAcceptedRevenue->fetch_assoc();
    $totalAcceptedRevenue = $rowAcceptedRevenue['totalAcceptedRevenue'];
} else {
    $totalAcceptedRevenue = 0;
}


// Calculate total bookings per year from 2023
$sqlBookingsPerYear = "SELECT YEAR(created_at) AS year, COUNT(*) AS totalBookings 
                       FROM booking 
                       WHERE YEAR(created_at) >= 2023 
                       GROUP BY YEAR(created_at)";
$resultBookingsPerYear = $conn->query($sqlBookingsPerYear);

$bookingsPerYear = array();
if ($resultBookingsPerYear->num_rows > 0) {
    while ($row = $resultBookingsPerYear->fetch_assoc()) {
        $bookingsPerYear[$row['year']] = $row['totalBookings'];
    }
} else {
    $bookingsPerYear = [];
}

// Bookings Per Year Starting from 2023
$sqlBookingsPerYear = "SELECT YEAR(created_at) AS year, COUNT(*) AS totalBookings 
                       FROM booking 
                       WHERE YEAR(created_at) >= 2023 
                       GROUP BY YEAR(created_at)";
$resultBookingsPerYear = $conn->query($sqlBookingsPerYear);

$bookingsPerYear = [];
while ($row = $resultBookingsPerYear->fetch_assoc()) {
    $bookingsPerYear[$row['year']] = $row['totalBookings'];
}


$sqlExpenses = "SELECT * FROM expenses";
$resultExpenses = $conn->query($sqlExpenses);

$expensesData = array();
if ($resultExpenses->num_rows > 0) {
    while ($row = $resultExpenses->fetch_assoc()) {
        $expensesData[] = $row;
    }
}


// Fetch the top services by frequency
$sqlTopServices = "
    SELECT sp.service_name, COUNT(b.service_package) AS totalBookings
    FROM booking b
    JOIN services sp ON b.service_package = sp.serviceID
    GROUP BY sp.service_name
    ORDER BY totalBookings DESC
    LIMIT 10"; // Limit to top 10 services

$resultTopServices = $conn->query($sqlTopServices);

$serviceNames = [];
$totalBookings = [];

if ($resultTopServices->num_rows > 0) {
    while ($row = $resultTopServices->fetch_assoc()) {
        $serviceNames[] = $row['service_name'];
        $totalBookings[] = $row['totalBookings'];
    }
}

// Calculate number of clients registered via Google or Website
$sqlClientRegistration = "
    SELECT 
        COUNT(CASE WHEN google_id IS NOT NULL THEN 1 END) AS googleCount,
        COUNT(CASE WHEN google_id IS NULL THEN 1 END) AS websiteCount
    FROM client";
$resultClientRegistration = $conn->query($sqlClientRegistration);

if ($resultClientRegistration->num_rows > 0) {
    $rowClientRegistration = $resultClientRegistration->fetch_assoc();
    $googleCount = $rowClientRegistration['googleCount'];
    $websiteCount = $rowClientRegistration['websiteCount'];
} else {
    $googleCount = 0;
    $websiteCount = 0;
}

// Fetch bookings by event location over time
$sqlEventLocationBookings = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        eventLocation, 
        COUNT(*) AS bookingCount
    FROM booking
    WHERE YEAR(created_at) >= 2024
    GROUP BY month, eventLocation
    ORDER BY month, eventLocation
";
$resultEventLocationBookings = $conn->query($sqlEventLocationBookings);

$eventLocationBookingsData = [];
$uniqueLocations = [];

if ($resultEventLocationBookings->num_rows > 0) {
    while ($row = $resultEventLocationBookings->fetch_assoc()) {
        $month = $row['month'];
        $location = $row['eventLocation'];
        $count = $row['bookingCount'];

        if (!in_array($location, $uniqueLocations)) {
            $uniqueLocations[] = $location;
        }

        if (!isset($eventLocationBookingsData[$location])) {
            $eventLocationBookingsData[$location] = [];
        }
        $eventLocationBookingsData[$location][$month] = $count;
    }
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
        <?php echo "Admin | Analytics"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    
</head>
    
<body>
    

<section class="layout-admin">
    <button class="download-receipt" id="download-analytics">Download Reports</button>        
    <!-- Middle Section -->
    <div class="top-analytics">
        <div class="left-analytics">
            <div class="container-items">
                <div class="analytics-item">
                    <div class="analytics-item-content" >
                        <p>Expenses</p>
                        <div class="metric-container">
                            <h2>₱ <?php echo number_format($totalExpenses, 0); ?></h2>
                        </div>
                    </div>
                    <div class="icon-container-client">
                        <i class="fas fa-peso-sign" style="font-size: 36px; color: #FCF6F6;"></i>
                    </div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-item-content">
                        <p>Overall Revenue</p>
                        <div class="metric-container">
                            <h2>₱ <?php echo number_format($totalAcceptedRevenue, 0); ?></h2>
                        </div>
                    </div>
                    <div class="icon-container-client">
                        <i class="fas fa-money-bill-trend-up" style="font-size: 36px; color: #FCF6F6;"></i>
                    </div>
                </div>
            </div>
            <div class="total-bookings">
                <div class="top-book">
                    <h4>Total Bookings</h4>
                </div>
                <table class="tbls-bookings">
                    <thead>
                        <tr>
                            <th>YEAR</th>
                            <th>NO. OF BOOKING</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookingsPerYear as $year => $totalBookings) : ?>
                            <tr>
                                <td><?php echo $year; ?></td>
                                <td><?php echo $totalBookings; ?></td>
                            </tr>
                        <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="services-bar-graph">
            <div class="top-book">
                <h4>Total Services Bar Graph</h4>
            </div>
            <canvas id="servicesBarGraph"></canvas>
        </div>
    </div>
</section>

    <section class="container-bottom">
        <div class="tbl-admin">
            <div class="top-book">
                <h4>Expenses</h4>
                <div class="buttons-admin">
                    <button id="download-btn"><i class="fa-regular fa-file"></i> Download</button>
                    <button class="btn-admin"><i class="fa-solid fa-plus"></i>  Add New</button>
                </div>    
            </div>
            <div class="tbl-container">
                <table class="header-table">
                    <thead>
                        <tr>
                            <th>Expenses ID</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expensesData)) : ?>
                            <tr>
                                <td colspan="6">No expenses found</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($expensesData as $expense) : ?>
                                <tr>
                                    <td><?php echo $expense['expensesID']; ?></td>
                                    <td><?php echo date('F j, Y', strtotime($expense['date'])); ?></td>
                                    <td><?php echo $expense['category']; ?></td>
                                    <td><?php echo $expense['description']; ?></td>
                                    <td><?php echo '₱ ' . number_format($expense['amount'], 2); ?></td>
                                    <td>
                                        <form method="post" action="../backend/expenses.php">
                                            <input type="hidden" name="expensesID" value="<?php echo $expense['expensesID']; ?>">
                                            <button type="submit" name="delete">Delete</button>
                                        </form>                                   
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="client-pie-chart">
            <div class="top-book">
                <h4>Total Client Pie Chart</h4>
            </div>
            <canvas id="clientPieChart"></canvas>
        </div>
            <!-- Add New Expense Popup -->
        <div id="popup" class="popup-admin">
            <div class="form-container">
                <span class="close" onclick="hidePopup()">&times;</span>
                <form id="expensesForm" method="post">
                    <!-- Form Fields -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category-exp" class="form-control">
                            <option value="Travel">Travel</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Food">Food</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description-exp" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="text" name="amount" id="amount-exp" class="form-control" oninput="formatAmount()">
                    </div>
                    <button class="submit-btn" type="button" onclick="addExpenses()">Add Report</button>
                </form>
            </div>
        </div>

        <!-- Delete Expense Confirmation Popup -->
        <div id="deleteExpensePopup" class="popupDelete">
            <div class="popup">
                <p>Do you want to delete this expense report?</p>
                <button onclick="hideDeletePopup()">No</button>
                <button onclick="confirmDelete()">Yes</button>
            </div>
        </div>
    </section>

    <section class="container-bottom">
    <div class="event-location-line-graph">
            <div class="top-book">
                <h4>Bookings by Event Location</h4>
            </div>
            <canvas id="eventLocationLineGraph"></canvas>
        </div>
    </section>

    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>   

    <script>

        // Function to generate full analytics report PDF
        function generateFullAnalyticsReport() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const logoPath = '../picture/logo.png';
        const img = new Image();
        img.src = logoPath;

        img.onload = function() {
        // Add logo
        doc.addImage(img, 'PNG', 10, 10, 40, 20);

        // Set up document properties
        doc.setFontSize(18);
        doc.text('ICSM Creatives Reports', doc.internal.pageSize.getWidth() / 2, 40, { align: 'center' });

        let yPosition = 60;

        // Key Metrics Section
        doc.setFontSize(14);
        doc.text('Key Metrics', 14, yPosition);
        yPosition += 10;

        doc.setFontSize(10);
        doc.text(`Total Expenses: Php. ${document.querySelector('.analytics-item:first-child h2').textContent}`, 14, yPosition);
        yPosition += 7;
        doc.text(`Overall Revenue: Php. ${document.querySelector('.analytics-item:nth-child(2) h2').textContent}`, 14, yPosition);
        yPosition += 15;

        // Bookings Per Year
        doc.setFontSize(14);
        doc.text('Bookings Per Year', 14, yPosition);
        yPosition += 10;

        const bookingsTable = document.querySelector('.tbls-bookings');
        const bookingsData = Array.from(bookingsTable.querySelectorAll('tbody tr')).map(row => [
            row.querySelector('td:first-child').textContent,
            row.querySelector('td:last-child').textContent
        ]);

        doc.autoTable({
            startY: yPosition,
            head: [['Year', 'Number of Bookings']],
            body: bookingsData,
            headStyles: {
                fillColor: [66, 66, 66],
                textColor: 255,
                fontSize: 10,
                fontStyle: 'bold',
            }
        });

        // Top Services Chart (convert to table)
        yPosition = doc.autoTable.previous.finalY + 15;
        doc.setFontSize(14);
        doc.text('Top Services', 14, yPosition);
        yPosition += 10;

        const topServicesData = serviceNames.map((service, index) => [
            service,
            totalBookings[index]
        ]);

        doc.autoTable({
            startY: yPosition,
            head: [['Service', 'Total Bookings']],
            body: topServicesData,
            headStyles: {
                fillColor: [66, 66, 66],
                textColor: 255,
                fontSize: 10,
                fontStyle: 'bold',
            }
        });

        // Client Registration Pie Chart Data
        yPosition = doc.autoTable.previous.finalY + 15;
        doc.setFontSize(14);
        doc.text('Client Registration Methods', 14, yPosition);
        yPosition += 10;

        doc.autoTable({
            startY: yPosition,
            head: [['Registration Method', 'Count']],
            body: [
                ['Registered via Google', googleCount],
                ['Registered via Website', websiteCount]
            ],
            margin: { bottom: 0 }, // Reduce bottom margin
            headStyles: {
                fillColor: [66, 66, 66],
                textColor: 255,
                fontSize: 10,
                fontStyle: 'bold',
            }
        });


        // Add Event Location Line Graph Data
        yPosition = doc.autoTable.previous.finalY + 15;
        doc.setFontSize(14);
        doc.text('Bookings by Event Location', 14, yPosition);
        yPosition += 10;

        const eventLocationData = months.map((month, index) => {
            return [
                month,
                ...uniqueLocations.map(location => 
                    datasets.find(ds => ds.label === location).data[index].toString()
                )
            ];
        });

        doc.autoTable({
            head: [['Month', ...uniqueLocations]],
            body: eventLocationData,
            startY: yPosition,
            styles: {
                fontSize: 9,
                cellPadding: 3,
            },
            headStyles: {
                fillColor: [66, 66, 66],
                textColor: 255,
                fontSize: 10,
                fontStyle: 'bold',
            }
        });

        yPosition = doc.autoTable.previous.finalY + 15;
        doc.setFontSize(10);
        doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, yPosition);
        // Save the document
        doc.save(`Analytics_Report_${new Date().toISOString().split('T')[0]}.pdf`);
    };
}

function generateExpensesReport() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');

    // Set up document properties
    doc.setFontSize(18);
    doc.text('Expenses Report', 14, 22);
    doc.setFontSize(10);
    doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 30);

    // Extract expenses data from the table
    const expensesTable = document.querySelector('.header-table');
    const expensesData = Array.from(expensesTable.querySelectorAll('tbody tr'))
        .map(row => [
            row.querySelector('td:nth-child(1)').textContent,
            row.querySelector('td:nth-child(2)').textContent,
            row.querySelector('td:nth-child(3)').textContent,
            row.querySelector('td:nth-child(4)').textContent,
            row.querySelector('td:nth-child(5)').textContent
        ]);

    // Add table with expenses
    doc.autoTable({
        startY: 40,
        head: [['Expenses ID', 'Date', 'Category', 'Description', 'Amount']],
        body: expensesData,
        theme: 'striped',
        headStyles: {
            fillColor: [66, 66, 66],
            textColor: 255,
            fontSize: 10,
            fontStyle: 'bold',
        }
    });

    // Add summary at the bottom
    const totalExpenses = document.querySelector('.analytics-item h2').textContent;
    doc.setFontSize(12);
    doc.text(`Total Expenses: ${totalExpenses}`, 14, doc.autoTable.previous.finalY + 15);

    // Save the document
    doc.save(`Expenses_Report_${new Date().toISOString().split('T')[0]}.pdf`);
}

// Add event listeners to the existing download buttons
document.addEventListener('DOMContentLoaded', function() {
    const fullReportDownloadBtn = document.getElementById('download-analytics');
    const expensesDownloadBtn = document.getElementById('download-btn');

    if (fullReportDownloadBtn) {
        fullReportDownloadBtn.addEventListener('click', generateFullAnalyticsReport);
    }

    if (expensesDownloadBtn) {
        expensesDownloadBtn.addEventListener('click', generateExpensesReport);
    }
});


// Event Location Bookings Line Graph
var eventLocationData = <?php echo json_encode($eventLocationBookingsData); ?>;
var uniqueLocations = <?php echo json_encode($uniqueLocations); ?>;

// Prepare data for Chart.js
var months = Object.keys(Object.values(eventLocationData)[0] || {}).sort();
var datasets = uniqueLocations.map(location => ({
    label: location,
    data: months.map(month => eventLocationData[location][month] || 0),
    fill: false,
    borderColor: getRandomColor(), // You'll need to add this function
    tension: 0.1
}));

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

var ctx3 = document.getElementById('eventLocationLineGraph').getContext('2d');
var eventLocationLineGraph = new Chart(ctx3, {
    type: 'line',
    data: {
        labels: months,
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Bookings'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Month'
                }
            }
        }
    }
});

        // Function to show the popup
        function showPopup() {
            var popup = document.getElementById("popup");
            popup.style.display = "block";
        }

        function hidePopup() {
            var popup = document.getElementById("popup");
            popup.style.display = "none";
        }

        document.addEventListener('DOMContentLoaded', function () {
            var addButton = document.querySelector('.btn-admin');
            addButton.addEventListener('click', function () {
                showPopup();
            });

            var closeButton = document.querySelector('.close');
            closeButton.addEventListener('click', function () {
                hidePopup();
            });

            window.addEventListener('click', function (event) {
                var popup = document.getElementById("popup");
                if (event.target == popup) {
                    hidePopup();
                }
            });
        });

    function addExpenses() {
        var form = document.getElementById("expensesForm");
        var formData = new FormData(form);

        // Remove formatting from the amount before sending it to the server
        var amountInput = document.getElementById("amount-exp");
        formData.set("amount", removeFormatting(amountInput.value));

        // Add the selected category to the form data
        var categorySelect = document.getElementById("category-exp");
        formData.set("category", categorySelect.value);

        // Add additional data to the form data
        formData.append("action", "add_expenses");

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../backend/expenses.php", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                // Handle success, e.g., show a success message or redirect
                hidePopup();
                location.reload(); // You may customize this based on your needs
            } else {
                // Handle errors
                alert("Error: " + xhr.responseText);
            }
        };
        xhr.send(formData);
    }

    function removeFormatting(amount) {
        // Remove currency symbol, comma, and period
        return parseFloat(amount.replace(/[^\d]/g, ''));
    }

    
    // Data for the Top Services Bar Graph
        var serviceNames = <?php echo json_encode($serviceNames); ?>;
        var totalBookings = <?php echo json_encode($totalBookings); ?>;

        // Initialize Top Services Bar Graph
        var ctxTopServices = document.getElementById('servicesBarGraph').getContext('2d');
        var servicesBarGraph = new Chart(ctxTopServices, {
            type: 'bar',
            data: {
                labels: serviceNames,
                datasets: [{
                    label: 'Top Services by Bookings',
                    data: totalBookings,
                    backgroundColor: '#BC8759',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


         // Client registration data
         var googleCount = <?php echo json_encode($googleCount); ?>;
            var websiteCount = <?php echo json_encode($websiteCount); ?>;

           // Create the client registration pie chart
        var ctx2 = document.getElementById('clientPieChart').getContext('2d');
        var clientPieChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Registered via Google', 'Registered via Website'],
                datasets: [{
                    label: 'Client Registration Method',
                    data: [googleCount, websiteCount],
                    backgroundColor: ['#BC8759', '#444444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        var inactivityTimeout = 900; s

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; 
            }, inactivityTimeout * 1000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });


        
    </script>
</body>
</html>