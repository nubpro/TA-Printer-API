<?php
header('Access-Control-Allow-Origin: *');
ini_set('default_socket_timeout', 2);
 
// Turn SSL verification off (Sorry!)
stream_context_set_default( [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/printerstatus/all', function (Request $request, Response $response, array $args) {
	
	$printerList = array(
		"APU RS1 (Print Shop)" => "0.0.0.0",
		"APU RS3 (TechCentre)" => "0.0.0.0",
	);
	
	$result = [];
	
	foreach($printerList as $name => $url) {
		$printer = new Printer($name, $url);
		$result[] = $printer->getStatus();
	}
	
	return jsonResult(0, $result);
});

function jsonResult($err, $data) {
	$arr = array('error' => $err, 'result' => $data);
	return json_encode($arr, JSON_UNESCAPED_SLASHES);
}	


$app->run();
