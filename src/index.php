<?php

namespace MySimpleApi;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

if (PHP_SAPI == 'cli-server') {
	// To help the built-in PHP dev server, check if the request was actually for
	// something which should probably be served as a static file
	$url  = parse_url($_SERVER['REQUEST_URI']);
	$file = __DIR__ . $url['path'];
	if (is_file($file)) {
		return false;
	}
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$app = new \Slim\App();

// Routes

/**
 * @api {get} /apartments Request list of apartment entities
 * @apiParam {String} [page] Page >= 0
 * @apiParam {String} [limit] Items per page
 * @apiParam {String} [filterLime1] Filters columns 'line1'
 * @apiParam {String} [filterEmail] Filters columns 'email'
 * @apiGroup Apartment
 * @apiName GetApartmentList
 */
$app->get('/apartments', function (Request $request, Response $response, $args) {

	// sanitise query params
	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0;
	$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? $_GET['limit'] : 20;
	$filter = [];
	if (isset($_GET['filterLine1']) && is_string($_GET['filterLine1']) && strlen($_GET['filterLine1']) > 0) {
		$filter['line1'] = $_GET['filterLine1'];
	};
	if (isset($_GET['filterEmail']) && is_string($_GET['filterEmail']) && strlen($_GET['filterEmail']) > 0) {
		$filter['email'] = $_GET['filterEmail'];
	};

	// get list from database
	$list = ApartmentService::getInstance()->getList($page, $limit, $filter);

	// convert to REST format
	$body = [
		"apartments" => $list,
		"meta" => [
			"page" => $page,
			"limit" => $limit,
			"filter" => $filter
		]
	];

	$response->getBody()->write(json_encode($body));
	$response->withHeader('Content-Type', 'application/json');
	return $response;
});

/**
 * @api {get} /apartments/:apartmentId Request apartment information
 * @apiGroup Apartment
 * @apiName GetSingleApartment
 * @apiParam {Number} apartmentId Unique apartment id
 */
$app->get('/apartments/{apartmentId}', function (Request $request, Response $response, $args) {
	// get item from database
	$item = ApartmentService::getInstance()->getItem($request->getAttribute('apartmentId'));

	// check token, if exists
	if (isset($_GET['token']) && is_string($_GET['token'])) {
		if ($item['token'] !== $_GET['token']) {
			$response = $response->withStatus(403);
			$response->getBody()->write("You cannot edit this apartment.");
			return $response;
		}
	}

	// convert to REST format
	$body = [
		"apartment" => $item
	];

	$response->getBody()->write(json_encode($body));
	$response->withHeader('Content-Type', 'application/json');
	return $response;
});

/**
 * @api {post} /apartments Crete a new apartment
 * @apiGroup Apartment
 * @apiParam {String} [key] value
 * @apiName CreateSingleApartment
 */
$app->post('/apartments', function (Request $request, Response $response, $args) {

	// add item to database
	$data = $request->getParsedBody();
	ApartmentService::getInstance()->addItem($data);
	$response = $response->withStatus(201);
	return $response;
});

/**
 * @api {put} /apartments/:apartmentId Request apartment information
 * @apiGroup Apartment
 * @apiName UpdateSingleApartment
 * @apiParam {Number} apartmentId Unique apartment id
 */
$app->put('/apartments/{apartmentId}', function (Request $request, Response $response, $args) {

	$data = $request->getParsedBody();
	$item = ApartmentService::getInstance()->getItem($request->getAttribute('apartmentId'));

	// check token
	$token = isset($_GET['token']) && is_string($_GET['token']) ? $_GET['token'] : false;
	if ($token !== $item['token']) {
		$response = $response->withStatus(403);
		$response->getBody()->write("You cannot edit this apartment.");
		return $response;
	}

	// update apartment
	ApartmentService::getInstance()->updateItem($item['id'], $data);
	$response = $response->withStatus(204);
	return $response;
});

// Enable CORS
$app->add(function ($req, $res, $next) {
	/* @var Response $response */
	$response = $next($req, $res);
	return $response
		->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
		->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
// Run app
$app->run();
