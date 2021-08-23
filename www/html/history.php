<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'purchase.php';

session_start();

if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$token = get_csrf_token();

if(is_admin($user)){
    $histories = get_histories($db);
}else{
    $histories = get_histories($db, $user['user_id']);
}

header('X-FRAME-OPTIONS: DENY');
include_once '../view/history_view.php';