<?php
/**
 * PHP manual testing suite for Ably docs:
 * https://ably.com/docs/auth/identified-clients.
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
    echo "calling tokenAuth\r\n";
    tokenAuth();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Revoke a token using the clientId as the identifier.
 * https://ably.com/docs/auth/identified-clients#token
 *
 * @return void
 */
function tokenAuth()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $tokenRequest = $rest->auth->createTokenRequest(['clientId' => 'Bob']);
    // ... issue the TokenRequest to a client ...
}