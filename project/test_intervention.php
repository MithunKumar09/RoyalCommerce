<?php
require 'vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$image = $manager->read('../../assets/images/thumbnails/173086523535Ifn9IA.jpg');

// List all public methods
$reflection = new ReflectionClass($image);
$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

echo "Available Image Methods:\n";
echo "========================\n\n";

foreach ($methods as $method) {
    if ($method->getDeclaringClass()->getName() !== 'ReflectionMethod') {
        echo $method->getName() . "\n";
    }
}

echo "\n\nImage Info:\n";
echo "Width: " . $image->width() . "\n";
echo "Height: " . $image->height() . "\n";

// Test encode method
try {
    $encoded = $image->encode('jpeg', quality: 80);
    echo "\nEncode method works!\n";
    echo "Encoded type: " . get_class($encoded) . "\n";
} catch (Exception $e) {
    echo "\nEncode error: " . $e->getMessage() . "\n";
}

// Test webp method
try {
    $webp = $image->toWebp(quality: 80);
    echo "\nWebP method works!\n";
    echo "WebP type: " . get_class($webp) . "\n";
} catch (Exception $e) {
    echo "\nWebP error: " . $e->getMessage() . "\n";
}

// Test save method
try {
    $image->save('test-output.jpg', quality: 80);
    echo "\nSave method works!\n";
} catch (Exception $e) {
    echo "\nSave error: " . $e->getMessage() . "\n";
}
?>
