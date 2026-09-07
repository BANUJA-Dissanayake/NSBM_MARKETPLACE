<?php
session_start();
include "connection.php";
include "includes/session-check.php";

if (!isLoggedIn()) {
    echo json_encode(["error" => "Please log in first."]);
    exit;
}

$user_id = currentUserId();

$subtotal = 0;
$item_labels = [];
$cart_rs = Database::search("SELECT cart_items.quantity, cart_items.unit_price, listings.title
    FROM cart_items
    INNER JOIN carts ON cart_items.cart_id = carts.cart_id
    INNER JOIN listings ON cart_items.listing_id = listings.listing_id
    WHERE carts.user_id = $user_id");

if ($cart_rs) {
    while ($row = $cart_rs->fetch_assoc()) {
        $subtotal += (float) $row["quantity"] * (float) $row["unit_price"];
        $item_labels[] = $row["title"] . " x" . (int) $row["quantity"];
    }
}

$amount = number_format($subtotal, 2, '.', '');
$items_summary = implode(", ", $item_labels);

$user_rs = Database::search("SELECT email FROM `user` WHERE user_id = $user_id");
$user_email = $user_rs && $user_rs->num_rows > 0 ? $user_rs->fetch_assoc()["email"] : "";

$shipping_name = isset($_POST["shipping_name"]) ? trim($_POST["shipping_name"]) : "";
$shipping_phone = isset($_POST["shipping_phone"]) ? trim($_POST["shipping_phone"]) : "";
$shipping_address = isset($_POST["shipping_address"]) ? trim($_POST["shipping_address"]) : "";
$shipping_city = isset($_POST["shipping_city"]) ? trim($_POST["shipping_city"]) : "";

$name_parts = $shipping_name !== "" ? explode(" ", $shipping_name, 2) : [""];
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : "";
$merchant_id = "1224710"; // Replace with your actual merchant ID
$merchant_secret = "MjY2MzkyNDM1NDIwMTQ2Mzc0NTY1OTE3MjI4NTMyMTAyMDIxNTA3"; // Replace with your actual merchant secret
$currency = "LKR";
$order_id = uniqid(); // Generate a unique order ID for each transaction

$hash = strtoupper(
    md5(
        $merchant_id . 
        $order_id . 
        number_format($amount, 2, '.', '') . 
        $currency .  
        strtoupper(md5($merchant_secret)) 
    ) 
);
$array = [];

$array["merchant_id"] = $merchant_id;
$array["order_id"] = $order_id;
$array["amount"] = $amount;
$array["currency"] = $currency;
$array["hash"] = $hash;
$array["items"] = $items_summary;
$array["first_name"] = $first_name;
$array["last_name"] = $last_name;
$array["email"] = $user_email;
$array["phone"] = $shipping_phone;
$array["address"] = $shipping_address;
$array["city"] = $shipping_city;
$array["country"] = "Sri Lanka";
$array["delivery_address"] = $shipping_address;
$array["delivery_city"] = $shipping_city;

$jesonObj = json_encode($array);


echo $jesonObj;

?>