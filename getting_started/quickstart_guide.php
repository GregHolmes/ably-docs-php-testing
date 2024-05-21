<?php
/**
 * PHP manual testing suite for Ably docs:
 * https://ably.com/docs/getting-started/quickstart.
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
    $restApiKey = $_ENV['ABLY_API_KEY'];
    $channelName = 'docs';

    publishAMessage($restApiKey);

} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Publishing a message.
 * https://ably.com/docs/getting-started/quickstart#step-4
 *
 * @param string $restApiKey API key for authentication
 *
 * @return null
 */
function publishAMessage($restApiKey)
{
    $client = new Ably\AblyRest($restApiKey);
    $channel = $client->channels->get('quickstart');
    $channel->publish('greeting', 'hello!');

    return null;
}