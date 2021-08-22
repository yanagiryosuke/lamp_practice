<!DOCTYPE html>
<html lang="ja">
    <head>
        <?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>購入明細</title>
    </head>
    <body>
        <?php var_dump($details); ?><br>
        <?php print($purchase_id); ?>

        <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <h1>購入明細</h1>
        <div class="container">

            <?php include VIEW_PATH . 'templates/messages.php'; ?>

            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <?php if(is_admin($user)){ ?>
                        <th>ユーザID</th>
                        <?php } ?>
                        <th>注文番号</th>
                        <th>購入日時</th>
                        <th>合計金額</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php if(is_admin($user)){ ?>
                        <td><?php print(h($histories['user_id'])); ?></td>
                        <?php } ?>
                        <td><?php print(h($histories['purchase_id'])); ?></td>
                        <td><?php print(h(date('Y-m-d', strtotime($histories['created'])))); ?></td>
                        <td><?php print($total_price); ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered">
            <?php foreach($details as $detail){ ?>
                <thead class="thead-light">
                    <tr>
                        <th>商品名</th>
                        <th>購入時の商品価格</th>
                        <th>購入数</th>
                        <th>小計</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php print(h($detail['item_name'])); ?></td>
                        <td><?php print(h($detail['item_price'])); ?></td>
                        <td><?php print(h($detail['item_amount'])); ?></td>
                        <td><?php print(h($detail['item_price'] * $detail['item_amount'])); ?></td>
                    </tr>
                </tbody>
            <?php } ?>
            </table>
        </div>
    </body>
</html>