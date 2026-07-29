<?php
// データベース接続
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// URLから ID を取得 (例: entry.php?id=4 なら $id は 4)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// IDを指定してデータを1件だけ抽出するSQL
$sth = $dbh->prepare("SELECT * FROM bbs_entries WHERE id = :id");
$sth->execute([':id' => $id]);
$entry = $sth->fetch(PDO::FETCH_ASSOC);

// もしデータが見つからなかったら
if (!$entry) {
    die("投稿が見つかりません。");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>投稿詳細</title>
</head>
<body>
    <h1>投稿詳細</h1>
    <p>ID: <?= htmlspecialchars($entry['id'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>日時: <?= htmlspecialchars($entry['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>内容:</p>
    <div style="border: 1px solid #ccc; padding: 10px;">
        <?= nl2br(htmlspecialchars($entry['body'], ENT_QUOTES, 'UTF-8')) ?>
    </div>
    <br>
    <a href="./bbstest.php">一覧に戻る</a>
</body>
</html>
