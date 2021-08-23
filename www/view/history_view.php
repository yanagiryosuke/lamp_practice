<!DOCTYPE html>
<html lang="ja">
    <head>
        <?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>購入履歴</title>
    </head>
    <body>
        <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <h1>購入履歴</h1>
        <div class="container">

            <?php include VIEW_PATH . 'templates/messages.php'; ?>

            <?php if(count($histories) > 0){ ?>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <?php if(is_admin($user)){ ?>
                            <th>ユーザID</th>
                            <?php } ?>
                            <th>注文番号</th>
                            <th>購入日時</th>
                            <th>合計金額</th>
                            <th>購入明細</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($histories as $history){ ?>
                        <tr>
                            <?php if(is_admin($user)){ ?>
                            <td><?php print(h($history['user_id'])); ?></td>
                            <?php } ?>
                            <td><?php print(h($history['purchase_id'])); ?></td>
                            <td><?php print(h(date('Y-m-d', strtotime($history['created'])))); ?></td>
                            <td><?php print(h($history['total_price'])); ?></td>
                            <td>
                                <form method="post" action="purchase_detail.php">
                                    <input class="btn btn-block btn-primary" type="submit" value="購入明細">
                                    <input type="hidden" name="token" value="<?php print($token); ?>">
                                    <input type="hidden" name="purchase_id" value="<?php print(h($history['purchase_id'])); ?>">
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </body>
</html>