<?php

$urlParsed = parse_url($_SERVER['REQUEST_URI']);
$path = $urlParsed['path' ?? ''];

if ($path == '/emails') {
    if ($_GET['inbox'] == 'jane@example.org') {
        if (isset($_GET['subject']) && $_GET['subject'] == 'Your credentials') {
            echo file_get_contents("emails-1-result-2nd.json");
        } else {
            echo file_get_contents("emails-1-result.json");
        }
    } else {
        echo file_get_contents("emails-no-result.json");
    }
} elseif ($path == '/emails/9bf63f3b-4e4e-46a0-8c7d-html') {
    echo file_get_contents("email-body.html");
} elseif ($path == '/emails/9bf63f3b-4e4e-46a0-8c7d-txt') {
    echo file_get_contents("email-body.txt");
}
