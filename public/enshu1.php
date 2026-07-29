<?php
// データベース接続
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. フォームから送信された場合の処理 (INSERT)
if (isset($_POST['body'])) {
    $insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");
    $insert_sth->execute([
        ':body' => $_POST['body']
    ]);

    // 二重送信を防止するためのリダイレクト
    header("HTTP/1.1 302 Found");
    header("Location: ./enshu1.php");
    exit;
}

// 2. データベースから投稿一覧を取得する処理 (SELECT)
// 降順（新しい順）で取得するために ORDER BY created_at DESC を指定します
$select_sth = $dbh->prepare("SELECT * FROM hogehoge ORDER BY created_at DESC");
$select_sth->execute();
$rows = $select_sth->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>演習1</title>
    <style>
        /* 見本画像のように罫線で区切るための簡単なスタイル */
        hr { border: 0; border-top: 1px solid #ccc; margin: 20px 0; }
        .meta { font-size: 0.9em; font-weight: bold; margin-bottom: 5px; }
        .content { margin-bottom: 20px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <!-- 入力フォーム -->
    <form method="POST" action="./enshu1.php">
        <textarea name="body" rows="3" cols="30"></textarea>
        <button type="submit">送信</button>
    </form>
    
    <hr>

    <!-- 投稿一覧の表示 -->
    <?php foreach ($rows as $row): ?>
        <div class="meta">送信日時<br><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></div>
        
        <div class="meta">送信内容</div>
        <!-- htmlspecialcharsでXSS対策をしつつ、nl2brで改行を反映させます -->
        <div class="content"><?= nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')) ?></div>
        <hr>
    <?php endforeach; ?>
</body>
</html>
