<?php
$time = new DateTime('now', new DateTimeZone('Asia/Manila'));

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 864000);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(864000);

session_start();

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'lstc'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token'
    )
);

spl_autoload_register(function($class) 
{
    require_once dirname(__DIR__) . '/classes/' . $class . '.php';
});

require_once dirname(__DIR__) . '/functions/sanitize.php';
if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) 
{
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

    if ($hashCheck->count()) 
    {
        $user = new User($hashCheck->first()->user_id);
        $user->login();
    }
}