<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
	
    return $response;
});

$app->get('/printer', function (Request $request, Response $response, array $args) {
    $handle = fopen("tray.html", "r", true);
	$matchStr = "infoIn=";
	
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			$pos = strpos($line, $matchStr);
			if ($pos > 0) {
				$data = substr($line, strlen($matchStr) + $pos, -3);
				break;
			}
		}
		fclose($handle);
	}
	
	if (isset($data)) {
		$data = str_replace("'", "\"", $data);
		$data = json_decode($data);
	}

	return json_encode($data);
});
