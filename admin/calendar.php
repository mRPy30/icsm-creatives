<?php
session_start();
// Connection
include '../backend/dbcon.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form data exists
    if (isset($_POST['event_name'], $_POST['event_start_date'], $_POST['event_end_date'])) {
        $event_name = $_POST['event_name'];
        $event_start_date = $_POST['event_start_date'];
        $event_end_date = $_POST['event_end_date'];

        // Modify the query to insert data into the schedule table
        $insert_query = "INSERT INTO `schedule` (`schedName`, `schedStart`, `schedEnd`) 
                         VALUES ('$event_name', '$event_start_date', '$event_end_date')";

        if (mysqli_query($conn, $insert_query)) {
            $data = array(
                'status' => true,
                'msg' => 'Event added successfully!'
            );
        } else {
            $data = array(
                'status' => false,
                'msg' => 'Sorry, Event not added.'
            );
        }
        echo json_encode($data);
        exit();
    } else {
        $data = array(
            'status' => false,
            'msg' => 'Invalid form data!'
        );
        echo json_encode($data);
        exit();
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
        <?php echo "Admin | Calendar"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />

    <!----css---->
    <style>
        body {
            overflow-y: hidden;
        }       
    </style>
</head>
    
<body>

       
    <!--  navbar and sidebar-->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 

	<main class="calendar">
        <div class="calendar-header">
            <button id="addScheduleButton" class="add-schedule-button"><i class="fa-solid fa-plus"></i> Add Schedule</button>
            <div id="calendar" class="event_management"></div>
        </div>
    </main>


    <!-- Popup -->
    <div id="event_entry_modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="img-container">
                <div class="form-group">
                    <label for="event_name">Event name</label>
                    <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Enter your event name">
                </div>
                <div class="form-group">
                    <label for="event_start_date">Event start</label>
                    <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date">
                </div>
                <div class="form-group">
                    <label for="event_end_date">Event end</label>
                    <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date">
                </div>
                <button id="saveEventButton" class="btn-save-event">Save</button>
            </div>
        </div>
    </div>
<!-- End popup dialog box -->

    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- JS for full calendar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <!----css---->
<script>
        $(document).ready(function() {
            display_events();

            function display_events() {
                $.ajax({
                    url: '../backend/display_event.php',
                    dataType: 'json',
                    success: function(response) {
                        var events = [];
                        if (response.status) {
                            $.each(response.data, function(i, item) {
                                events.push({
                                    event_id: item.event_id,
                                    title: item.title,
                                    start: item.start,
                                    end: item.end,
                                    color: item.color,
                                    url: item.url
                                });
                            });
                            initializeCalendar(events);
                        } else {
                            alert('No events to display!');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching events: " + error);
                    }
                });
            }

            function initializeCalendar(events) {
                var calendar = $('#calendar').fullCalendar({
                    defaultView: 'month',
                    timeZone: 'local',
                    editable: true,
                    selectable: true,
                    selectHelper: true,
                    events: events,
                    select: function(start, end) {
                        // Open the modal for adding events when a day is clicked
                        $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
                        $('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
                        $('#event_entry_modal').show();
                    },
                    eventRender: function(event, element, view) {
                        element.bind('click', function() {
                            alert(event.event_id);
                        });
                    }
                });
            }

            // Save Event button click handler
            $("#saveEventButton").on("click", function() {
        save_event();
    });

    function save_event() {
        var event_name = $("#event_name").val();
        var event_start_date = $("#event_start_date").val();
        var event_end_date = $("#event_end_date").val();

        if (event_name === "" || event_start_date === "" || event_end_date === "") {
            alert("Please enter all required details.");
            return false;
        }

        $.ajax({
            url: "calendar.php",
            type: "POST",
            dataType: 'json',
            data: {
                event_name: event_name,
                event_start_date: event_start_date,
                event_end_date: event_end_date
            },
            success: function(response) {
                $('#event_entry_modal').hide();
                if (response.status === true) {
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert("Error occurred while processing the request.");
            }
        });

        return false;
    }
            // Event handlers for modal display
            $("#addScheduleButton").on("click", function() {
                $("#event_entry_modal").show();
            });

            $(".close").on("click", function() {
                $("#event_entry_modal").hide();
            });

            $(window).on("click", function(event) {
                if (event.target.id === "event_entry_modal") {
                    $("#event_entry_modal").hide();
                }
            });
        });

        // Add the following script to periodically check for inactivity and logout
        var inactivityTimeout = 900; // 15 minutes in seconds

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; // Replace 'logout.php' with the actual logout page
            }, inactivityTimeout * 1000);
        }

        // Start checking for inactivity when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        // Reset the inactivity timer when there's user activity
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