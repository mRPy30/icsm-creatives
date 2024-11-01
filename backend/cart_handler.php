<?php
session_start();

class CartHandler {
    private $clientId;
    
    public function __construct() {
        // Generate or retrieve client ID from cookie
        if (!isset($_COOKIE['client_id'])) {
            $this->clientId = uniqid('client_', true);
            setcookie('client_id', $this->clientId, time() + (86400 * 30), "/"); // 30 days expiry
        } else {
            $this->clientId = $_COOKIE['client_id'];
        }
        
        // Initialize cart from localStorage if session is empty
        if (!isset($_SESSION['cart']) && isset($_COOKIE['cart_' . $this->clientId])) {
            $_SESSION['cart'] = json_decode($_COOKIE['cart_' . $this->clientId], true);
        }
    }
    
    public function addToCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $bookingId = uniqid();
        $timestamp = time();
        $expiryTime = $timestamp + (24 * 60 * 60); // 24 hours
        
        $cartItem = [
            'id' => $bookingId,
            'event_type' => $_SESSION['booking']['type_of_event'] ?? '',
            'title_event' => $_SESSION['booking']['title_event'] ?? '',
            'event_date' => $_SESSION['booking']['event_date'] ?? '',
            'start_time' => $_SESSION['booking']['start_time'] ?? '',
            'end_time' => $_SESSION['booking']['end_time'] ?? '',
            'location' => $_SESSION['booking']['event_location'] ?? '',
            'services' => $_SESSION['service_ids'] ?? [],
            'additional_services' => $_SESSION['additional_services'] ?? [],
            'total_cost' => $_SESSION['total_cost'] ?? 0,
            'expiry_time' => $expiryTime,
            'client_id' => $this->clientId
        ];
        
        $_SESSION['cart'][$bookingId] = $cartItem;
        
        // Store in cookie
        $this->updateCartCookie();
        
        return ['success' => true, 'booking_id' => $bookingId];
    }
    
    private function updateCartCookie() {
        setcookie(
            'cart_' . $this->clientId,
            json_encode($_SESSION['cart']),
            time() + (86400 * 30), // 30 days
            "/"
        );
    }
    
    public function getCart() {
        return $_SESSION['cart'] ?? [];
    }
    
    public function removeExpiredItems() {
        if (!isset($_SESSION['cart'])) {
            return;
        }
        
        $currentTime = time();
        foreach ($_SESSION['cart'] as $bookingId => $item) {
            if ($item['expiry_time'] < $currentTime) {
                unset($_SESSION['cart'][$bookingId]);
            }
        }
        
        $this->updateCartCookie();
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartHandler = new CartHandler();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_to_cart':
                $result = $cartHandler->addToCart();
                echo json_encode($result);
                break;
        }
    }
    exit;
}
?>