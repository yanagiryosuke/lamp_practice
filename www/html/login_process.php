<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

$name = get_post('name');
$password = get_post('password');
$token = get_post('token');

$db = get_db_connect();

if(is_valid_csrf_token($token)) {
  unset($_SESSION['csrf_token']);
} else {
  set_error('外部から攻撃を受けました。');
  unset($_SESSION['csrf_token']);
  redirect_to(LOGIN_URL);
}

$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
redirect_to(HOME_URL);