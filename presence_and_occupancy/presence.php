<?php
/**
 * PHP manual testing suite for Ably docs:
 * https://ably.com/docs/presence-occupancy/presence.
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
    echo "\r\nRetrieve presence members\r\n";
    retrievePresenceMembers();
    echo "\r\nBatch presence\r\n";
    batchPresence();
    echo "\r\nHandling batch presence responses\r\n";
    handlingBatchPresenceResponses();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Retrieve presence members.
 * https://ably.com/docs/presence-occupancy/presence#retrieve-members
 *
 * @return void
 */
function retrievePresenceMembers()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $channel = $rest->channels->get('channelName');

    $membersPage = $channel->presence->get();
    echo(count($membersPage->items) . ' presence members in first page');

    if ($membersPage->hasNext()) {
        $nextPage = $membersPage.next();
    }
}

/**
 * Batch presence members.
 * https://ably.com/docs/presence-occupancy/presence#batch
 *
 * @return void
 */
function batchPresence()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $content = ['channel' => 'channel1,channel2'];
    $presenceSets = $rest->request('GET', '/presence', $content);

    if (!$presenceSets->success) {
        echo('An error occurred; err = ' . $presenceSets->errorMessage);
    } else {
        echo('Success! status code was ' . strval($presenceSets->statusCode));
    }
}

/**
 * Handling batch presence responses.
 * https://ably.com/docs/presence-occupancy/presence#batch-response
 *
 * @return void
 */
function handlingBatchPresenceResponses()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );
    $content = ['channel' => 'channel1,channel2'];
    $presenceSets = $rest->request('GET', '/presence', $content);
    echo "Is a success? $presenceSets->success\r\n";
    echo "Has an error code? $presenceSets->errorCode\r\n";

    if ($presenceSets->success) {
        // If complete success
        for ($i = 0; $i < count($presenceSets->items); $i++) {
            // Each presenceSets item will be roughly of the style:
            /*
                {
                    'channel': 'channel1',
                    'presence': [
                    { 'action': 1, 'clientId': 'CLIENT1' },
                    { 'action': 1, 'clientId': 'CLIENT2' }
                    ]
                }
            */
        }
    } elseif ($presenceSets->errorCode === 40020) {
        // If partial success
        for ($i = 0; $i < count($presenceSets->items[0]->batchResponse); $i++) {
            // Each batchResponse item will either be the same as success if it succeeded, or:
            /*
            {
                'channel': 'channel1',
                'error': {
                'code': 40160,
                'message': 'ERROR_MESSAGE',
                'statusCode': 401
                }
            }
            */
        }
    } else {
        // If failed, check why
        var_dump($presenceSets->errorCode . ', ' . $presenceSets->errorMessage);
    }
}