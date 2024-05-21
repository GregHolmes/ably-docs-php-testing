<?php
/**
 * PHP manual testing suite for Ably docs:
 * https://ably.com/docs/metadata-stats/stats.
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
    echo "\r\nApp-level statistics\r\n";
    appLevelStatistics();
} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * App-level statistics.
 * https://ably.com/docs/metadata-stats/stats#app
 *
 * @return void
 */
function appLevelStatistics()
{
    $rest = new Ably\AblyRest($_ENV['ABLY_API_KEY']);
    $resultPage = $rest->stats(['unit' => 'hour']);
    $thisHour = $resultPage->items[0];
    echo('Published this hour ' . $thisHour->inbound->all->all->count);
}
