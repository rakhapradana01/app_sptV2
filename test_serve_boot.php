<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reflector = new ReflectionClass(get_class($kernel));
$method = $reflector->getMethod('getArtisan');
$method->setAccessible(true);
$artisan = $method->invoke($kernel);

$command = $artisan->find('serve');
echo "Command class: " . get_class($command) . "\n";
echo "Is instance of Illuminate\Console\Command: " . ($command instanceof \Illuminate\Console\Command ? 'YES' : 'NO') . "\n";
if (method_exists($command, 'getLaravel')) {
    echo "Laravel container on command: " . ($command->getLaravel() ? 'YES' : 'NO') . "\n";
} else {
    echo "getLaravel method does not exist!\n";
}
