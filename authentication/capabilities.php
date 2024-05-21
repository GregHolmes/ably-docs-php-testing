<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/auth/capabilities.
 *
 * @category Testing
 * @package  AblyTest
 * @author   Greg Holmes <greg.holmes@ably.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version  1.0.0
 * @link     ''
 * @since    1.0.0
 */

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    // Commented this out because it won't work,
    // there are no defined capabilities in the example.
    // echo "calling capabilities\r\n";
    // capabilities();
    echo "\r\ncalling requestToken without capabilities\r\n";
    tokenWithoutCapabilities();
    echo "\r\ncalling requestToken with capabilities\r\n";
    tokenIntersectionCapabilities();
    echo "\r\ncalling requestToken with incompatible capabilities\r\n";
    tokenIncompatibleCapabilities();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Create a token with capabilities.
 * https://ably.com/docs/auth/capabilities#capabilities-token
 *
 * @return void
 */
function capabilities()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $capability = [];

    $tokenParams = [
        'clientId' => 'client@example.com',
        'capability' => json_encode($capability)
    ];
    $tokenRequest = $rest->auth->requestToken($tokenParams);
}

/**
 * Create a token without capabilities.
 * https://ably.com/docs/auth/capabilities#ably-token-all
 *
 * @return void
 */
function tokenWithoutCapabilities()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $tokenParams = [
        'clientId' => 'client@example.com',
        'ttl' => 3600 * 1000, // ms
    ];
    $tokenRequest = $rest->auth->requestToken($tokenParams);

    var_dump($tokenRequest);
}

/**
 * Create a token without capabilities.
 * https://ably.com/docs/auth/capabilities#ably-token-intersection
 *
 * @return void
 */
function tokenIntersectionCapabilities()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    /**
     * API key capabilities:
     * {
     *   "chat:*": ["publish", "subscribe", "presence"],
     *   "status": ["subscribe", "history"],
     *   "alerts": ["subscribe"]
     * }
     */

    // Token request that specifies capabilities:
    $capabilities = [
        "chat:bob" => ["subscribe"],  // only "subscribe" intersects
        "status" => ["*"],  // "*" intersects with "subscribe"
        "secret" => ["publish", "subscribe"]  // key does not have access to "secret" channel
    ];

    $tokenDetails = $rest
        ->auth
        ->requestToken(
            ['capability' => json_encode($capabilities)]
        );

    /**
     * Resulting token capabilities:
     * {
     *   "chat:bob": ["subscribe"],
     *   "status": ["subscribe", "history"]
     * }
     */

    var_dump($tokenDetails);
}

/**
 * Create a token without capabilities.
 * https://ably.com/docs/auth/capabilities#ably-token-error
 *
 * @return void
 */
function tokenIncompatibleCapabilities()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    /**
     * API key capabilities:
     * {
     *   "chat": ["*"]
     * }
     */

    // Token request that specifies capabilities:
    $tokenDetails = $rest
        ->auth
        ->requestToken(
            ['capability' => json_encode(['status' => ['*']])]
        );

    var_dump($tokenDetails);
}
