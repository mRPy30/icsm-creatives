<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'])) {
    $section = $_POST['section'];

    switch ($section) {
        case 'addPhoto':
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                $photo = file_get_contents($_FILES['photo']['tmp_name']);
                $photoEscaped = mysqli_real_escape_string($conn, $photo);

                $query = "INSERT INTO homepage_carousel (picture, is_active) VALUES ('$photoEscaped', 0)";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "Photo added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Error uploading photo.";
            }
            break;

        case 'addText':
            if (isset($_POST['heading'], $_POST['subheading'])) {
                $heading = mysqli_real_escape_string($conn, $_POST['heading']);
                $subheading = mysqli_real_escape_string($conn, $_POST['subheading']);

                $query = "INSERT INTO homepage_cover_text (heading, subheading, is_active) VALUES ('$heading', '$subheading', 0)";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "Text added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Invalid input for Cover Text.";
            }
            break;

        case 'addFaq':
            if (isset($_POST['question'], $_POST['answer'])) {
                $question = mysqli_real_escape_string($conn, $_POST['question']);
                $answer = mysqli_real_escape_string($conn, $_POST['answer']);

                $query = "INSERT INTO homepage_faq (question, answer) VALUES ('$question', '$answer')";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "FAQ added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Invalid input for FAQ.";
            }
            break;

        case 'addChooseUs':
            if (isset($_POST['title'], $_POST['description'], $_FILES['icon'])) {
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $icon = file_get_contents($_FILES['icon']['tmp_name']);
                $iconEscaped = mysqli_real_escape_string($conn, $icon);

                $query = "INSERT INTO homepage_choose_us (icon, title, description) VALUES ('$iconEscaped', '$title', '$description')";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "Choose Us entry added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Invalid input for Choose Us.";
            }
            break;

        case 'addInstruction':
            if (isset($_POST['step_number'], $_POST['heading'], $_POST['description'])) {
                $stepNumber = mysqli_real_escape_string($conn, $_POST['step_number']);
                $heading = mysqli_real_escape_string($conn, $_POST['heading']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);

                $query = "INSERT INTO homepage_instruction (step_number, heading, description) VALUES ('$stepNumber', '$heading', '$description')";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "Instruction added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Invalid input for Instruction.";
            }
            break;

        default:
            $_SESSION['error'] = "Invalid section specified.";
    }

    header('Location: ../admin/control.php');
    exit();
}
?>
