<?php
// Your Infobip credentials
define('INFOBIP_API_KEY', 'e69df00ad60a62539172286acf5c53b2-c561d54e-5ab7-4abc-9263-ba5cf877bcd4');
define('INFOBIP_BASE_URL', '2mx8w6.api.infobip.com');
define('INFOBIP_SENDER_ID', '447491163443');

/**
 * Send SMS using Infobip API
 * 
 * @param string $phoneNumber Recipient's phone number
 * @param string $message Message content
 * @return bool Returns true if successful, false otherwise
 */
function sendSMS($phoneNumber, $message) {
    // Format phone number (assuming Philippines format)
    $phoneNumber = formatPhoneNumber($phoneNumber);
    
    // Prepare the request payload
    $payload = [
        'messages' => [
            [
                'from' => INFOBIP_SENDER_ID,
                'destinations' => [
                    ['to' => $phoneNumber]
                ],
                'text' => $message
            ]
        ]
    ];

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . INFOBIP_BASE_URL . '/sms/2/text/advanced', // Added https://
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Authorization: App ' . INFOBIP_API_KEY,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
    ]);

    // Execute cURL request
    $response = curl_exec($curl);
    $err = curl_error($curl);

    // Close cURL session
    curl_close($curl);

    // Return false if there was an error
    if ($err) {
        error_log("Infobip SMS Error: " . $err);
        return false;
    }

    return true;
}

/**
 * Format phone number to international format
 * 
 * @param string $phoneNumber Phone number to format
 * @return string Formatted phone number
 */
function formatPhoneNumber($phoneNumber) {
    // Remove any non-numeric characters
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // Add Philippines country code if not present
    if (substr($phoneNumber, 0, 2) !== '63') {
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '63' . substr($phoneNumber, 1);
        } else {
            $phoneNumber = '63' . $phoneNumber;
        }
    }
    
    return $phoneNumber;
}

// Handle SMS sending
if (isset($_POST['send_sms'])) {
    $phoneNumber = $_POST['cellphone-number'];
    $message = $_POST['message'];
    
    if (sendSMS($phoneNumber, $message)) {
        echo "<script>
                alert('SMS sent successfully!');
                window.location.href = '../admin/booking.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to send SMS. Please try again.');
                window.location.href = '../admin/booking.php';
              </script>";
    }
    exit();
}
?>