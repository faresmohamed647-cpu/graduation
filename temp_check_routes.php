<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(Illuminate\Support\Facades\Route::getRoutes() as $r){
    $uri = $r->uri();
    if(str_contains($uri, 'parent') || str_contains($uri, 'driver') || str_contains($uri, 'admin')){
        echo $uri . ' [' . implode('|',$r->methods()) . '] => ' . $r->getActionName() . PHP_EOL;
    }
}
