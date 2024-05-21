<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/auth/basic.
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
    echo "calling authenticate\r\n";
    authenticate();

} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Creating an Ably\AblyRest instance with API key.
 * https://ably.com/docs/auth/token#auth-url
 *
 * @return void
 */
function authenticate()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(['key' => $_ENV['ABLY_API_KEY']]);

    // Publishing a message to test..
    publishTest($rest, $channelName, 'greetings', 'from basic auth');
}

/**
 * Publishing a message.
 * https://ably.com/docs/getting-started/quickstart#step-4
 *
 * @param Ably\AblyRest $client  Ably Rest client
 * @param string        $channel Ably channel name
 * @param string        $name    Ably message name
 * @param string        $message Ably message
 *
 * @return null
 */
function publishTest(
    Ably\AblyRest $client,
    string $channel,
    string $name,
    string $message
) {
    $channel = $client->channels->get($channel);
    $channel->publish($name, $message);

    return null;
}