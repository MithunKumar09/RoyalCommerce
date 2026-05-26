<?php
header('Content-Type: text/css; charset=UTF-8');

$color = isset($_GET['color']) ? preg_replace('/[^a-fA-F0-9]/', '', $_GET['color']) : '424a4d';
$headerColor = isset($_GET['header_color']) ? strtolower($_GET['header_color']) : 'light';

if (strlen($color) !== 6) {
    $color = '424a4d';
}

$textColor = '#180207';
$white = '#ffffff';
$dark = '#180207';
$yellow = '#ffb800';
$lightRed = '#ff4c3b';

echo ":root {\n";
echo "  --blue-color: #{$color};\n";
echo "  --text-color: {$textColor};\n";
echo "  --white-color: {$white};\n";
echo "  --dark-color: {$dark};\n";
echo "  --yellow-color: {$yellow};\n";
echo "  --light-red-color: {$lightRed};\n";
echo "}\n";

// Optional header color hint (light/dark)
if ($headerColor === 'dark') {
    echo ".header-section .header-top, .header-section .info-bar { background-color: #111; }\n";
    echo ".header-section .header-top a, .header-section .info-bar a { color: {$white}; }\n";
}
