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

$token = get_post('token');
$purchase_id = get_post('purchase_id');

if(is_valid_csrf_token($token)){
    unset($_SESSION['csrf_token']);
  } else {
    set_error('外部から攻撃を受けました。ログアウトしてください。');
    unset($_SESSION['csrf_token']);
    redirect_to(HISTORY_URL);
  }

if(is_admin($user)){
    $histories = get_history_by_purchase_id($db, $purchase_id);
    $details = get_details($db, $purchase_id);
}else{
    $histories = get_history_by_purchase_id($db, $purchase_id);
    $details = get_details($db, $purchase_id);
}

header('X-FRAME-OPTIONS: DENY');
include_once '../view/purchase_detail_view.php';