<?php
// --- データベース接続 ---
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- 投稿が送信されたら保存する処理 (INSERT) ---
if (isset($_POST['body']) && $_POST['body'] !== '') {
    $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body) VALUES (:body)");
    $insert_sth->execute([':body' => $_POST['body']]);
    
    // 二重投稿を防ぐためにリロード
    header("Location: ./bbstest.php");
    exit;
}

// --- 検索機能の処理 (SELECT) ---
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if ($keyword !== '') {
    $select_sth = $dbh->prepare("SELECT * FROM bbs_entries WHERE body LIKE :keyword ORDER BY created_at DESC");
    $select_sth->execute([':keyword' => '%' . $keyword . '%']);
} else {
    $select_sth = $dbh->query("SELECT * FROM bbs_entries ORDER BY created_at DESC");
}
$rows = $select_sth->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>掲示板</title>
</head>
<body>

    <form method="POST" action="./bbstest.php">
        <textarea name="body" rows="3" cols="25"></textarea><br>
        <button type="submit">送信</button>
    </form>
    
    <hr>

    <form method="GET" action="./bbstest.php">
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">検索</button>
        <a href="./bbstest.php">検索解除</a>
    </form>
    
    <hr>

    <?php foreach ($rows as $entry): ?>
        <p>ID: <a href="./entry.php?id=<?= $entry['id'] ?>"><?= $entry['id'] ?></a></p>
        <p>日時: <?= htmlspecialchars($entry['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>内容: <?= nl2br(htmlspecialchars($entry['body'], ENT_QUOTES, 'UTF-8')) ?></p>
        <hr>
    <?php endforeach; ?>

</body>
</html>

































































































































































