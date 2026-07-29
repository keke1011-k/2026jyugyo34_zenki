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

    header("HTTP/1.1 302 Found");
    header("Location: ./enshu2.php");
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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>演習2</title>
    <style>
        /* 見本画像に合わせたスタイル設定 */
        body {
            font-family: sans-serif;
        }
        .pagination {
            text-align: center; /* ページ数を中央に */
            position: relative;
            margin: 30px 0;
        }
        .pagination a.next {
            position: absolute;
            right: 0; /* 「次のページ」を右端に配置 */
        }
        .pagination a.prev {
            position: absolute;
            left: 0;  /* 「前のページ」を左端に配置 */
        }
        .indent {
            margin-left: 2em; /* データのインデント（字下げ） */
        }
    </style>
</head>
<body>
    
    <!-- 入力フォーム（改行せずに横並びに） -->
    <form method="POST" action="./enshu2.php">
        <textarea name="body" rows="3" cols="25"></textarea>
        <button type="submit">送信</button>
    </form>
    
    <hr>

    <!-- ページネーション -->
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

    <!-- 投稿一覧の表示 -->
    <?php foreach ($rows as $row): ?>
        送信日時<br>
        <div class="indent"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></div>
        送信内容<br>
        <div class="indent"><?= nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')) ?></div>
        <hr>
    <?php endforeach; ?>

</body>
</html>
