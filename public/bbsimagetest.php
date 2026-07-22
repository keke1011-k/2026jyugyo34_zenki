<?php
// --- データベース接続 ---
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- 投稿・画像保存処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_file'])) {
    $body = $_POST['body'] ?? '';
    $filename = null;

    // 画像がアップロードされている場合のみチェックを実行
    if (!empty($_FILES['image_file']['tmp_name'])) {
        $tmp_file = $_FILES['image_file']['tmp_name'];
        
        // セキュリティ対策（演習1）：中身を解析してMIMEタイプを取得
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        // 許可する画像形式のリスト
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($mime_type, $allowed_types)) {
            // かぶらないファイル名を生成
            $filename = uniqid() . '_' . $_FILES['image_file']['name'];
            
            // 【重要・修正箇所】コンテナ内の正しい絶対パスへ保存
            move_uploaded_file($tmp_file, '/var/www/upload/image/' . $filename);
        } else {
            die("エラー：許可されていないファイル形式です。");
        }
    }

    // データベースに保存
    $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :filename)");
    $insert_sth->execute([':body' => $body, ':filename' => $filename]);
    header("Location: ./bbsimagetest.php");
    exit;
}

// --- 一覧取得 ---
$select_sth = $dbh->query("SELECT * FROM bbs_entries ORDER BY created_at DESC");
?>
<head>
  <title>画像が投稿できる掲示板</title>
</head>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>画像投稿掲示板</title>
</head>
<body>
    <!-- 投稿フォーム -->
    <form method="POST" action="./bbsimagetest.php" enctype="multipart/form-data">
        <textarea name="body" placeholder="本文を入力"></textarea><br>
        <!-- 演習3：onchange属性を追加し、JSの関数を呼び出す -->
        <input type="file" name="image_file" onchange="checkFileSize(this)"><br>
        <button type="submit">送信</button>
    </form>

    <hr>

    <!-- 投稿一覧 -->
    <?php foreach($select_sth as $entry): ?>
        <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
            <dt>ID</dt>
            <dd><?= htmlspecialchars($entry['id']) ?></dd>
            <dt>日時</dt>
            <dd><?= htmlspecialchars($entry['created_at']) ?></dd>
            <dt>内容</dt>
            <dd>
                <?= nl2br(htmlspecialchars($entry['body'])) ?>
                <?php if(!empty($entry['image_filename'])): ?>
                    <div>
                        <!-- HTML上では /image/ と指定すればNginxが転送してくれる -->
                        <img src="/image/<?= htmlspecialchars($entry['image_filename']) ?>" style="max-height: 10em;">
                    </div>
                <?php endif; ?>
            </dd>
        </dl>
    <?php endforeach; ?>

    <!-- 演習3：JavaScriptでのファイルサイズチェック -->
    <script>
    function checkFileSize(input) {
        // 5MBをバイト数で計算 (5MB = 5 * 1024 * 1024 = 5242880バイト)
        const maxSize = 5 * 1024 * 1024;
        
        if (input.files && input.files[0]) {
            if (input.files[0].size > maxSize) {
                alert("エラー：5MB以上の画像はアップロードできません。");
                input.value = ''; // 選択を強制解除
            }
        }
    }
    </script>
</body>
</html>






























e) VALUES (:body, :filename)");






");










ata">


br>







x solid #ccc;">









ilename']) ?>" style="max-height: 10em;">

















（空にする）



































e) VALUES (:body, :filename)");






");









ata">








x solid #ccc;">









ilename']) ?>" style="max-height: 10em;">







