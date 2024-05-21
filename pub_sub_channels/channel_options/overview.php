<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/channels/options.
 *
 * @category Testing
 * @package  AblyTest
 * @author   Greg Holmes <greg.holmes@ably.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version  1.0.0
 * @link     ''
 * @since    1.0.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

try {
    echo "\r\nCreate a channel with options\r\n";
    createChannelWithOptions();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Create a channel with options.
 * https://ably.com/docs/channels/options#get
 *
 * @return void
 */
function createChannelWithOptions()
{
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $key = Ably\Utils\Crypto::generateRandomKey();
    $channelOpts = ['cipher' => ['key' => $key]];
    $channel = $rest->channels->get('abs-yes-cam', $channelOpts);
}