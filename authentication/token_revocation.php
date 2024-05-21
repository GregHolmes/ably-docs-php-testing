<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/auth/recovation.
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
    echo "calling revokeByClientId\r\n";
    revokebyClientId();
    echo "\r\ncalling revokeByRevocationKey\r\n";
    revokeByRevocationKey();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Revoke a token using the clientId as the identifier.
 * https://ably.com/docs/auth/revocation#client-id
 *
 * @return void
 */
function revokebyClientId()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $tokenDetails = $rest->auth->requestToken(
        ['clientId' => 'client@example.com']
    );

    $requestBody = ['targets' => ['clientId:client@example.com']];

    $apiKeyName = $_ENV['ABLY_API_KEY_NAME'];
    $response = $rest->request(
        'POST',
        "/keys/$apiKeyName/revokeTokens",
        [],
        $requestBody
    );

    if (!$response->success) {
        echo('An error occurred; err = ' . $response->errorMessage);
    } else {
        echo('Success! status code was ' . strval($response->statusCode));
    }
}

/**
 * Revoke a token using the revocationKey as the identifier.
 * https://ably.com/docs/auth/revocation#revocation-key
 *
 * @return void
 */
function revokeByRevocationKey()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $tokenDetails = $rest->auth->requestToken(
        ['clientId' => 'client@example.com']
    );

    $requestBody = ['targets' => ['revocationKey:users.group1@example.com']];

    $apiKeyName = $_ENV['ABLY_API_KEY_NAME'];
    $response = $rest->request(
        'POST',
        "/keys/$apiKeyName/revokeTokens",
        [],
        $requestBody
    );

    if (!$response->success) {
        echo('An error occurred; err = ' . $response->errorMessage);
    } else {
        echo('Success! status code was ' . strval($response->statusCode));
    }
}