<?php
// 1. データベース接続
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. データベースからToDo一覧を取得
$select_sth = $dbh->query("SELECT * FROM todos ORDER BY created_at DESC");
$rows = $select_sth->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ToDoリスト</title>
    <style>
        .done { color: #888; text-decoration: line-through; background-color: #f9f9f9; }
    </style>
</head>
<body>

<?php foreach ($rows as $row): ?>
    <?php 
        $css_class = ($row['is_done'] == 1) ? 'done' : '';
    ?>
    <div class="<?= $css_class ?>">
        <?= htmlspecialchars($row['task'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <hr>
<?php endforeach; ?>

</body>
</html>











