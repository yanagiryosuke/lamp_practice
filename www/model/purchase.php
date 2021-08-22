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
    return fetch_query($db, $sql, $params);
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

function detail_total_price($details){
    $total_price = array();
    foreach($details as $detail){
        $total_price[] = $detail['item_price'] * $detail['item_amount'];
    }
    return $total_price;
}

function add_history_total_price($details, $histories){
    foreach($histories as &$history){
        $price = 0;
        foreach($details as $detail){
            foreach($detail as $det){
                if($history['purchase_id'] === $det['purchase_id']){
                    $price += $det['item_price'] * $det['item_amount'];
                }
            }
        }
        $history['total_price'] = $price;
    }
    unset($history);
    return $histories;
}

function add_history_total_price_admin($details, $histories){
    foreach($histories as &$history){
        $price = 0;
        foreach($details as $detail){
            if($history['purchase_id'] === $detail['purchase_id']){
                $price += $detail['item_price'] * $detail['item_amount'];
            }
        }
        $history['total_price'] = $price;
    }
    unset($history);
    return $histories;
}

function total_price($details){
    $total_price = 0;
    foreach($details as $detail){
        $total_price += $detail['item_price'] * $detail['item_amount'];
    }
    return $total_price;
}