<?php
date_default_timezone_set('Europe/Istanbul');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

use App\Kernel;

ini_set('max_execution_time', 9999999);
ini_set('memory_limit', '2048M');
ini_set('post_max_size', '2048M');
ini_set('upload_max_filesize', '2048M');
ini_set('max_input_vars', '10000');
ini_set('max_input_time', '9999999');

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
