<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

function get_history_by_purchase_id($db, $purchase_id) {
    $sql = "
        SELECT
            purchase_histories.purchase_id,
            user_id,
            purchase_histories.created,
            SUM(purchase_details.item_price * purchase_details.item_amount) AS total_price
        FROM
            purchase_histories
        JOIN
            purchase_details
        ON
            purchase_histories.purchase_id = purchase_details.purchase_id
        WHERE
            purchase_histories.purchase_id = ?
        GROUP BY
            purchase_details.purchase_id
    ";
    $params = array($purchase_id);
    return fetch_all_query($db, $sql, $params);
}

function get_histories($db, $user_id=null) {
    if($user_id === null){
        $where = '';
    }else{
        $where = 'WHERE purchase_histories.user_id = ?';
    }
    $sql = "
        SELECT
            purchase_histories.purchase_id,
            user_id,
            purchase_histories.created,
            SUM(purchase_details.item_price * purchase_details.item_amount) AS total_price
        FROM
            purchase_histories
        JOIN
            purchase_details
        ON
            purchase_histories.purchase_id = purchase_details.purchase_id
        {$where}
        GROUP BY
            purchase_details.purchase_id
    ";
    if($user_id === null){
        $params = null;
    }else{
        $params = array($user_id);
    }
    return fetch_all_query($db, $sql, $params);
}

function get_details($db, $purchase_id) {
    $sql = "
        SELECT
            purchase_id,
            item_name,
            item_price,
            item_amount,
            SUM(item_price * item_amount) AS total_price,
            created
        FROM
            purchase_details
        WHERE
            purchase_id = ?
        GROUP BY
            detail_id

    ";
    $params = array($purchase_id);
    return fetch_all_query($db, $sql, $params);
}

function insert_history($db, $user_id) {
    $sql = "
        INSERT INTO
            purchase_histories(
                user_id
            )
        VALUES(:user_id);
    ";
    $params = array(':user_id' => $user_id);
    return execute_query($db, $sql, $params);
}

function insert_detail($db, $purchase_id, $item_id, $item_name, $item_price, $item_amount) {
    $sql = "
        INSERT INTO
            purchase_details(
                purchase_id,
                item_id,
                item_name,
                item_price,
                item_amount
            )
        VALUES(:purchase_id, :item_id, :item_name, :item_price, :item_amount);
    ";
    $params = array(':purchase_id' => $purchase_id, ':item_id' => $item_id, ':item_name' => $item_name, ':item_price' => $item_price, ':item_amount' => $item_amount);
    return execute_query($db, $sql, $params);
}

function insert_purchase($db, $user_id, $carts) {
    $db->beginTransaction();
    insert_history($db, $user_id);
    $sql = "
        SELECT
            LAST_INSERT_ID() as purchase_id;
    ";
    $purchase_id = fetch_query($db, $sql);
    foreach($carts as $cart) {
      insert_detail($db, $purchase_id['purchase_id'], $cart['item_id'], $cart['name'], $cart['price'], $cart['amount']);
    }
    $db->commit();
    return true;
    $db->rollback();
    return false;
}