<?php
/**
 * PHP manual testing suite for Ably docs: https://ably.com/docs/auth/token.
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
    echo "calling authUrl\r\n";
    authUrl();
    echo "calling authCallback\r\n";
    authCallback();
    echo "calling authOptions\r\n";
    authOptions();
    echo "calling AblyToken\r\n";
    ablyToken();
    echo "calling tokenRequest\r\n";
    tokenRequest();
    echo "\r\ncreating JWT using apiKey\r\n";
    jwtUsingApiKey();
    echo "\r\ncalling generateJWT\r\n";
    generateJWT();

} catch (\Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

/**
 * Creating an Ably\AblyRest instance with authUrl.
 * https://ably.com/docs/auth/token#auth-url
 *
 * @return void
 */
function authUrl()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(['authUrl' => $_ENV['ABLY_AUTH_URL'] . '/auth']);

    // Publishing a message to test..
    publishTest($rest, $channelName, 'greetings', 'from authUrl');
}

/**
 * Creating an Ably\AblyRest instance with authCallback.
 * https://ably.com/docs/auth/token#auth-callback
 *
 * @return void
 */
function authCallback()
{
    $channelName = 'docs';

    $rest = new Ably\AblyRest(
        [
            'authCallback' => function () {
                $url = $_ENV['ABLY_AUTH_URL'] . '/auth';
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);

                $tokenRequestData = json_decode($response, true);
                $tokenRequest = new Ably\Models\TokenRequest($tokenRequestData);

                return $tokenRequest;
            },
        ]
    );

    // Publishing a message to test..
    publishTest($rest, $channelName, 'greetings', 'from authCallback');
}

/**
 * Creating an Ably\AblyRest instance with authCallback.
 * https://ably.com/auth/token#auth-options
 *
 * @return void
 */
function authOptions()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        [
            'authUrl' => $_ENV['ABLY_AUTH_URL'] . '/auth',
            'authMethod' => 'GET',
            'authParams' => [ 'param1' => 'value1', 'test' => 1, 'ttl' => 720000 ],
            'authHeaders' => [ 'h1: header1', 'h2: header3'],
        ]
    );

    // Publishing a message to test..
    publishTest($rest, $channelName, 'greetings', 'from authOptions');
}

/**
 * Creating an Ably TokenRequest with requestToken.
 * https://ably.com/auth/token#token-request
 *
 * @return void
 */
function tokenRequest()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $tokenRequest = $rest->auth->createTokenRequest(
        ['clientId' => 'client@example.com']
    );

    var_dump($tokenRequest);
}

/**
 * Creating an Ably Token with requestToken.
 * https://ably.com/auth/token#ably-token
 *
 * @return void
 */
function ablyToken()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        ['authUrl' => $_ENV['ABLY_AUTH_URL'] . '/auth']
    );

    $tokenDetails = $rest->auth->requestToken(
        ['clientId' => 'client@example.com']
    );

    $client = new Ably\AblyRest(['tokenDetails' => $tokenDetails]);

    // Publishing a message to test..
    publishTest($client, $channelName, 'greetings', 'from ablyToken');
}

/**
 * Creating an JWT using apiKey.
 * https://ably.com/auth/token#standard
 *
 * @return void
 */
function jwtUsingApiKey()
{
    $header = [
        'typ' => 'JWT',
        'alg' => 'HS256',
        'kid' => '{{API_KEY_NAME}}'
    ];

    $currentTime = time();

    $claims = [
        'iat' => $currentTime,
        'exp' => $currentTime + 3600,
        'x-ably-capability' => '{\"*\":[\"*\"]}'
    ];

    $base64Header = base64_encode(json_encode($header));
    $base64Claims = base64_encode(json_encode($claims));

    $signature = hash_hmac('sha256', $base64Header . '.' . $base64Claims, '{{API_KEY_SECRET}}', true);

    $jwt = $base64Header . '.' . $base64Claims . '.' . $signature;

    echo $jwt;
}

/**
 * Creating an Ably Token with requestToken.
 * https://ably.com/auth/token#embedded
 *
 * @return void
 */
function generateJWT()
{
    $channelName = 'docs';
    $rest = new Ably\AblyRest(
        ['key' => $_ENV['ABLY_API_KEY']]
    );

    $tokenDetails = $rest->auth->requestToken(
        ['clientId' => 'client@example.com']
    );

    $header = [
        'typ' => 'JWT',
        'alg' => 'HS256',
        'x-ably-token' => $tokenDetails->token
    ];

    $currentTime = time();

    $claims = [
        'exp' => $currentTime + 3600
    ];

    $base64Header = base64_encode(json_encode($header));
    $base64Claims = base64_encode(json_encode($claims));

    $secret = 'YOUR_SECRET';
    $signature = hash_hmac('sha256', $base64Header . '.' . $base64Claims, $secret, true);
    $base64Signature = base64_encode($signature);

    $jwt = $base64Header . '.' . $base64Claims . '.' . $base64Signature;

    echo $jwt;
}

/**
 * Publishing a message.
 * https://ably.com/docs/getting-started/quickstart#step-4
 *
 * @param Ably\AblyRest $client  Ably Rest client
 * @param string        $channel Ably channel name
 * @param string        $name    Ably message name
 * @param string        $message Ably
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

/**
 * Function to make a GET request to a predetermined auth URL.
 * Which will return a json encoded token request
 *
 * @return Ably\Models\TokenRequest
 */
function getToken()
{
    $tokenRequestUrl = $_ENV['ABLY_AUTH_URL'] . '/auth';

    $tokenRequestJson = file_get_contents($tokenRequestUrl);
    $tokenRequestData = json_decode($tokenRequestJson, true);

    $tokenRequest = new Ably\Models\TokenRequest($tokenRequestData);

    return $tokenRequest;
}