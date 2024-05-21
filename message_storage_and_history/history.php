<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/storage-history/history.
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
    echo "\r\nRetrieve channel history\r\n";
    retrieveChannelHistory();
    echo "\r\nRetrieve presence history\r\n";
    retrievePresenceHistory();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Retrieve channel history.
 * https://ably.com/docs/storage-history/history#retrieve-channel
 *
 * @return void
 */
function retrieveChannelHistory()
{
    $rest = new Ably\AblyRest($_ENV['ABLY_API_KEY']);
    $channel = $rest->channels->get('channel1');
    $channel->publish('example', 'message data');
    $resultPage = $channel->history();
    $recentMessage = $resultPage->items[0];
    echo("Most recent message data: " . $recentMessage->data);
}

/**
 * Retrieve presence history.
 * https://ably.com/docs/storage-history/history#presence-history
 *
 * @return void
 */
function retrievePresenceHistory()
{
    $rest = new Ably\AblyRest($_ENV['ABLY_API_KEY']);
    $channel = $rest->channels->get('channel1');
    $eventsPage = $channel->presence->history();
    echo(count($eventsPage->items) . ' presence events received in first page');
    if (count($eventsPage->items) > 0 && $eventsPage.hasNext()) {
        $nextPage = $eventsPage->next();
        echo(count($nextPage->items) . ' presence events received in second page');
    }
}
