<?php
// --- 実務の基本：WarningやNoticeを画面に出さない設定 ---
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// クッキーの保存処理
if (isset($_POST['name'])) {
    setcookie('saved_name', $_POST['name'], time() + 60 * 60 * 24 * 30, '/');
}

// データベース接続
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. フォームから送信された場合の処理 (INSERT)
if (isset($_POST['body'])) {
    $insert_sth = $dbh->prepare("INSERT INTO hogehoge (name, text) VALUES (:name, :body)");
    $insert_sth->execute([
        ':name' => $_POST['name'],
        ':body' => $_POST['body']
    ]);

    header("HTTP/1.1 302 Found");
    header("Location: ./enshu3.php");
    exit;
}

// ページネーションの計算処理
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $limit;

$count_stmt = $dbh->query("SELECT COUNT(*) FROM hogehoge");
$total_count = $count_stmt->fetchColumn();
$total_pages = ceil($total_count / $limit);

// 2. データベースから投稿一覧を取得する処理 (SELECT)
$select_sth = $dbh->prepare("SELECT * FROM hogehoge ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$select_sth->bindValue(':limit', $limit, PDO::PARAM_INT);
$select_sth->bindValue(':offset', $offset, PDO::PARAM_INT);
$select_sth->execute();
$rows = $select_sth->fetchAll(PDO::FETCH_ASSOC);

// フォームの初期値設定（クッキー読み込み）
$default_name = isset($_COOKIE['saved_name']) ? $_COOKIE['saved_name'] : '';
if (isset($_POST['name'])) {
    $default_name = $_POST['name'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>演習3</title>
    <style>
        body { font-family: sans-serif; }
        .pagination { text-align: center; position: relative; margin: 30px 0; }
        .pagination a.next { position: absolute; right: 0; }
        .pagination a.prev { position: absolute; left: 0; }
        .indent { margin-left: 2em; }
    </style>
</head>
<body>
    
    <form method="POST" action="./enshu3.php">
        名前: <input type="text" name="name" value="<?= htmlspecialchars($default_name, ENT_QUOTES, 'UTF-8') ?>"><br>
        <textarea name="body" rows="3" cols="25"></textarea><br>
        <button type="submit">送信</button>
    </form>
    
    <hr>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="prev">前のページ</a>
        <?php endif; ?>
        
        <?= $page ?>ページ目 (全 <?= $total_pages ?>ページ中)
        
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="next">次のページ</a>
        <?php endif; ?>
    </div>

    <hr>

    <?php foreach ($rows as $row): ?>
        投稿者名<br>
        <?php 
            // 昔のデータで名前が存在しない場合にもWarningを出さないように安全に取得
            $display_name = isset($row['name']) ? $row['name'] : '';
            if ($display_name === null || $display_name === '') {
                $display_name = '名無し';
            }
        ?>
        <div class="indent"><?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?></div>
        
        送信日時<br>
        <div class="indent"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></div>
        
        送信内容<br>
        <div class="indent"><?= nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')) ?></div>
        <hr>
    <?php endforeach; ?>

</body>
</html>
