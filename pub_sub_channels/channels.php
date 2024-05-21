<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/channels/.
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
    echo "\r\ncreate or retrieve a channel with get\r\n";
    createOrRetrieveChannel();
    echo "\r\npublish a message to a channel\r\n";
    publishAMessage();
    echo "\r\nbatch publish\r\n";
    batchPublishAMessage();
    echo "\r\ncreate a filter expression\r\n";
    createAFilterExpression();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Create or retrieve a channel.
 * https://ably.com/docs/channels#create
 *
 * @return void
 */
function createOrRetrieveChannel()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $channel = $rest->channels->get('channelName');
}

/**
 * Publish a message to a channel.
 * https://ably.com/docs/channels#publish
 *
 * @return void
 */
function publishAMessage()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $channel = $rest->channels->get('lip-mac-cad');
    $channel->publish('example', 'message data');
}

/**
 * Batch publish messages to a channel.
 * https://ably.com/docs/channels#batch-publish
 *
 * @return void
 */
function batchPublishAMessage()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $content = ['channels' => ['test1', 'test2'], 'messages' => ['data' => 'myData']];
    $batchPublish = $rest->request('POST', '/messages', [], $content);

    echo('Success! status code was ' . $batchPublish->statusCode);
}

/**
 * Create a filter expression.
 * https://ably.com/docs/channels#filter-create
 *
 * @return void
 */
function createAFilterExpression()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $channel = $rest->channels->get('scoops-kiosk');
    $extras = [
        'headers' => [
            'flavor' => 'strawberry',
            'cost' => 35,
            'temp' => 3
        ]
    ];

    $message = new \Ably\Models\Message();
    $message->name = 'ice-cream';
    $message->data = 'test';
    $message->extras = $extras;

    $channel->publish($message);
}