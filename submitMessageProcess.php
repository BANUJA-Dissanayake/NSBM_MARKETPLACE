<?php
include "connection.php";

header("Content-Type: text/plain");

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$topic = trim($_POST["topic"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "") {
    echo "Please enter your name.";
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Please enter a valid email address.";
    exit;
}
if ($topic === "") {
    echo "Please choose a topic.";
    exit;
}
if ($message === "") {
    echo "Please write a message.";
    exit;
}

$nameEscaped = Database::escape($name);
$emailEscaped = Database::escape($email);
$topicEscaped = Database::escape($topic);
$messageEscaped = Database::escape($message);

Database::iud("INSERT INTO messages (name, email, topic, message, is_read, created_at)
                VALUES ('$nameEscaped', '$emailEscaped', '$topicEscaped', '$messageEscaped', 0, NOW())");

echo "success";
