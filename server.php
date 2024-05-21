<?php
/**
 * Basic webserver for testing purposes within this project
 *
 * @category Testing
 * @package  AblyTest
 * @author   Greg Holmes <greg.holmes@ably.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version  1.0.0
 * @link     ''
 * @since    1.0.0
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

$client = new Ably\AblyRest(
    $_ENV['ABLY_API_KEY']
);

$app->get(
    '/auth',
    function (Request $request, Response $response, $args) use ($client) {
        try {
            $tokenRequest = $client->auth->createTokenRequest([]);
            $response->getBody()->write(json_encode($tokenRequest));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response
                ->getBody()
                ->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
);

$app->run();
