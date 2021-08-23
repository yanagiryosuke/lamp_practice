<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

function get_history($db, $user_id) {
    $sql = "
        SELECT
            purchase_id,
            user_id,
            created
        FROM
            purchase_histories
        WHERE
            user_id = :user_id
    ";
    $params = array(':user_id' => $user_id);
    return fetch_all_query($db, $sql, $params);
}

function get_history_by_purchase_id($db, $purchase_id) {
    $sql = "
        SELECT
            purchase_id,
            user_id,
            created
        FROM
            purchase_histories
        WHERE
            purchase_id = :purchase_id
    ";
    $params = array(':purchase_id' => $purchase_id);
    return fetch_all_query($db, $sql, $params);
}

function get_histories($db) {
    $sql = "
        SELECT
            purchase_id,
            user_id,
            created
        FROM
            purchase_histories
    ";
    return fetch_all_query($db, $sql);
}

function get_detail($db, $histories) {
    $detail = array();
    foreach($histories as $history){
        $sql = "
            SELECT
                purchase_id,
                item_name,
                item_price,
                item_amount
            FROM
                purchase_details
            WHERE
                purchase_id = :purchase_id
        ";
        $params = array(':purchase_id' => $history['purchase_id']);
        $detail[] = fetch_all_query($db, $sql, $params);
    }    
    return $detail;
}

function get_detail_by_purchase_id($db, $purchase_id) {
    $sql = "
        SELECT
            detail_id,
            purchase_id,
            item_name,
            item_price,
            item_amount
        FROM
            purchase_details
        WHERE
            purchase_id = :purchase_id
    ";
    $params = array(':purchase_id' => $purchase_id);
    return fetch_all_query($db, $sql, $params);
}

function get_details($db) {
    $sql = "
        SELECT
            purchase_id,
            item_name,
            item_price,
            item_amount,
            created
        FROM
            purchase_details
    ";
    return fetch_all_query($db, $sql);
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

function history_total_price($db){
    $sql = "
        SELECT
            purchase_id,
            SUM(item_price * item_amount) AS total_price
        FROM
            purchase_details
        GROUP BY
            purchase_id;
    ";
    $total_price = fetch_all_query($db, $sql);
    return $total_price;
}

function detail_total_price($db){
    $sql = "
        SELECT
            detail_id,
            purchase_id,
            SUM(item_price * item_amount) AS total_price
        FROM
            purchase_details
        GROUP BY
            detail_id;
    ";
    $total_price = fetch_all_query($db, $sql);
    return $total_price;
}

function add_history_total_price($db, $histories){
    $total_price = history_total_price($db);
    foreach($histories as &$history){
        foreach($total_price as $price){
            if($history['purchase_id'] === $price['purchase_id']){
                $history['total_price'] = $price['total_price'];
            }
        }
    }
    unset($history);
    return $histories;
}

function add_detail_total_price($db, $details){
    $total_price = detail_total_price($db);
    foreach($details as &$detail){
        foreach($total_price as $price){
            if($detail['detail_id'] === $price['detail_id']){
                $detail['total_price'] = $price['total_price'];
            }
        }
    }
    unset($detail);
    return $details;
}