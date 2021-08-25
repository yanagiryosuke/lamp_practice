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

$carts = get_user_carts($db, $user['user_id']);
$token = get_post('token');

if(is_valid_csrf_token($token)){
  unset($_SESSION['csrf_token']);
} else {
  set_error('外部から攻撃を受けました。ログアウトしてください。');
  unset($_SESSION['csrf_token']);
  redirect_to(CART_URL);
}

if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
}

if(insert_purchase($db, $user['user_id'], $carts) === false){
  set_error('購入履歴を追加できませんでした。');
  redirect_to(HOME_URL);
}

$total_price = sum_carts($carts);

header('X-FRAME-OPTIONS: DENY');
include_once '../view/finish_view.php';