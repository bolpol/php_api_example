<?php
// Turn off all error reporting
error_reporting(0);

// connect composer
require_once __DIR__ . '/vendor/autoload.php';
// connect needed files
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/DataBase.php";
require_once __DIR__ . "/UsersController.php";
require_once __DIR__ . "/WalletController.php";
require_once __DIR__ . "/API.php";

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

$api = new API();
#Инициализаруем новый экземпляр класса
$wallet = new WalletController((new DataBase())->db_config, $api);
$user = new UsersController((new DataBase())->db_config, $api);

/*$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
var_dump($uri);*/
// получить список
// /
if (isset($_GET['api']) && $_GET['api'] === 'wallet')
{

    /**
     * Создаем новую запись в базу
     */
    if( isset($_GET['create']) && empty($_GET['create']) &&

        isset($_POST['eth_wallet']) && !empty($_POST['eth_wallet']) &&
        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    ) {

        if($user->validateOnAdmin($user, $_POST['auth_token'], $_POST['nonce'])) {

            $user->incNonce("write", $_POST['nonce'], $_POST['auth_token']);

            $bitcoinECDSA = new BitcoinECDSA();
            $bitcoinECDSA->generateRandomPrivateKey();

            $bitcoinECDSA_2 = new BitcoinECDSA();
            $bitcoinECDSA_2->generateRandomPrivateKey();

            echo $wallet->create(
                $id_user = $_POST['eth_wallet'],
                $base_address = $bitcoinECDSA->getAddress(),
                $base_balance = 0,
                $priv_key_base_address = $bitcoinECDSA->getPrivateKey(),
                $bonus_address = $bitcoinECDSA_2->getAddress() ,
                $bonus_balance = (isset($_GET['bonus_balance']) && !empty($_GET['bonus_balance'])) ? (float) $_GET['bonus_balance'] : 0,
                $priv_key_bonus_address = $bitcoinECDSA_2->getPrivateKey()
            );
        }
        exit;
    }

    // get param - ethereum wallet
    if(isset($_GET['get_user_info']) && !empty($_GET['get_user_info'])) {
        echo $wallet->getInfoByUserId($_GET['get_user_info']);
        exit;
    }

    // get param - ethereum wallet
    if(isset($_GET['get_btc_address']) && !empty($_GET['get_btc_address'])) {
        echo $wallet->getBaseAddress($_GET['get_btc_address']);
        exit;
    }

    // get param - ethereum wallet
    if(isset($_GET['get_btc_balance']) && !empty($_GET['get_btc_balance'])) {
        echo $wallet->getBaseBalance($_GET['get_btc_balance']);
        exit;
    }

    // get param - ethereum wallet
    if(isset($_GET['get_bonus_address']) && !empty($_GET['get_bonus_address'])) {
        echo $wallet->getBonusAddress($_GET['get_bonus_address']);
        exit;
    }

    // get param - ethereum wallet
    if(isset($_GET['get_bonus_balance']) && !empty($_GET['get_bonus_balance'])) {
        echo $wallet->getBonusBalance($_GET['get_bonus_balance']);
        exit;
    }

    // get param - btc wallet
    if( isset($_GET['get_btc_priv_key']) && !empty($_GET['get_btc_priv_key']) &&

        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    )
    {
        if($user->validateOnAdmin($_POST['auth_token'], $_POST['nonce']))
        {
            echo $wallet->getPrivateKeyBaseAddress($_GET['get_btc_priv_key']);
        }
        exit;
    }

    // get param - bonus btc wallet
    if( isset($_GET['get_bonus_priv_key']) && !empty($_GET['get_bonus_priv_key']) &&

        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    )
    {
        if($user->validateOnAdmin($_POST['auth_token'], $_POST['nonce']))
        {
            echo $wallet->getPrivateKeyBonusAddress($_GET['get_bonus_priv_key']);
        }
        exit;
    }

    // get param - bonus wallet
    if( isset($_GET['delete_by_address']) && !empty($_GET['delete_by_address']) &&

        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    )
    {
        if($user->validateOnAdmin($_POST['auth_token'], $_POST['nonce']))
        {
            echo $wallet->getPrivateKeyBonusAddress($_GET['delete_by_address']);
        }
        exit;
    }

    // update bonus balance - bonus wallet
    if( isset($_GET['update_bonus_balance']) && empty($_GET['update_bonus_balance']) &&

        isset($_POST['eth_wallet']) && !empty($_POST['eth_wallet']) &&
        isset($_POST['new_balance']) && !empty($_POST['new_balance']) &&
        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    )
    {
        if($user->validateOnApi($_POST['auth_token'], $_POST['nonce']))
        {
            echo $wallet->updateBonusBalance($_POST['eth_wallet'], $_POST['new_balance']);
        }
        exit;
    }

}

// url: /api/user/update_user/auth_token=effwef540&id=1&nonce=1
elseif (isset($_GET['api']) && $_GET['api'] === 'user')
{

    if( isset($_GET['update_user']) && empty($_GET['update_user']) &&

        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce'])
    )
    {
        if($user->validateOnAdmin($_POST['auth_token'], $_POST['nonce']))
        {
            echo $user->updateKey($_POST['auth_token']);
        }
        exit;
    }

    if( isset($_GET['gen_user']) && empty($_GET['gen_user']) &&

        isset($_POST['auth_token']) && !empty($_POST['auth_token']) &&
        isset($_POST['nonce']) && !empty($_POST['nonce']) &&
        isset($_POST['mode']) && !empty($_POST['mode'])
    )
    {
        if($user->validateOnAdmin($_POST['auth_token'], $_POST['nonce']))
        {
            echo $user->generateUser($_POST['mode']);
        }
        exit;
    }

}
else
{
    require_once __DIR__ . "/404.php";
}

/*


$test = [];

$test['userId'] = "123456";
$test['nonce'] = 40;
$test['sharedKey'] = "someVerySecureString";
$test['hmacKey'] = hash_hmac ( 'sha256', $test['sharedKey'] , $test['nonce'] );
$test['hmac'] = hash_hmac ( 'sha256', $test['userId'] , $test['hmacKey'] );


$sso = [];
$sso['nonce'] = hash('sha1', rand() );
$sso['sharedKey'] = 'someVerySecureString';
$sso['hmacKey'] = hash_hmac ( 'sha256', $test['sharedKey'] , $test['nonce'] );
$sso['userId'] = '123456';
$sso['hmac'] = hash_hmac ( 'sha256', $test['userId'] , $test['hmacKey'] );

echo "<pre>";


function validateSSOToken($test, $sharedKey='someVerySecureString') {
    $isValid = false;
    //if ( isset( $_GET['id'] ) && isset( $_POST['nonce'] ) && isset( $_GET['hmac'] )) {
    $userId = $test['userId'];
    $nonce = $test['nonce'];
    $hmac = $test['hmac'];
    // Sign the key with the nonce to get the tmp key
    $hmacKey = hash_hmac ( 'sha256', $sharedKey, $nonce );
    // rebuild the string to sign
    $localHmac = hash_hmac ( 'sha256', $userId , $hmacKey );
    if(hash_equals($localHmac, $hmac)) {
        $isValid = true;
    }
    //}
    return $isValid;


}

var_dump(validateSSOToken($test));

*/

/*$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://alexbtcservice/validate' . $sso['url']
));
$result = curl_exec($curl);
var_dump($result);*/


