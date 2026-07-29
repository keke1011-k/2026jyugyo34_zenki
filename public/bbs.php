<?php
// エラーを表示する設定
ini_set('display_errors', 1);
error_reporting(E_ALL);

// データベース接続設定
$dsn = 'mysql:host=mysql;dbname=example_db;charset=utf8mb4';
$user = 'root';
$password = '';

// エラーメッセージ用変数
$error_msg = '';

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続失敗: " . $e->getMessage();
    exit;
}

// フォームが送信されたときの処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $body = $_POST['body'] ?? '';
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['image']['size'] <= 5242880) {
            $upload_dir = 'images/';
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        } else {
            $error_msg = 'エラー：アップロードできる画像は5MB以下です。';
        }
    }

    if (empty($error_msg) && !empty($name) && !empty($body)) {
        $sql = "INSERT INTO posts (name, body, image_path) VALUES (:name, :body, :image_path)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':body', $body, PDO::PARAM_STR);
        $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: bbs.php");
        exit;
    }
}

// データベースから投稿一覧を取得（新しい順）
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <!-- 【追加】スマホ対応の必須設定（Viewport） -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>課題用 掲示板</title>
    <style>
        /* 【追加】全体をスマホで見やすく、少しモダンなデザインに */
        body { 
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", Meiryo, sans-serif; 
            max-width: 600px; /* パソコンで見ても横に広がりすぎないように制限 */
            margin: 0 auto; 
            padding: 15px; 
            background-color: #f4f5f7;
            color: #333;
        }
        
        h1 { font-size: 1.5em; text-align: center; }

        /* 入力フォームのスマホ対応 */
        .form-group { margin-bottom: 15px; }
        input[type="text"], 
        textarea, 
        input[type="file"] {
            width: 100%; /* スマホ画面いっぱいに広げる */
            box-sizing: border-box; /* はみ出し防止 */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px; /* 16pxにすることでiPhoneでの勝手なズームを防止 */
        }
        
        button {
            width: 100%; /* スマホでタップしやすいようにボタンを横幅100%に */
            padding: 14px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        /* 投稿一覧のデザイン */
        .post { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 8px; 
            background-color: #ffffff; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* 影をつけて浮き出た感じに */
        }
        .post img { 
            max-width: 100%; /* スマホ画面からはみ出さない設定 */
            height: auto; 
            display: block; 
            margin-top: 10px; 
            border-radius: 4px; 
        }
        .meta { 
            color: #777; 
            font-size: 0.85em; 
            margin-bottom: 10px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 8px; 
        }
        .error { 
            color: #d9534f; 
            font-weight: bold; 
            margin-bottom: 15px; 
            background: #fdf7f7; 
            padding: 10px; 
            border-radius: 4px; 
            border: 1px solid #d9534f; 
        }

        /* 画面幅が小さい時（スマホ）向けの微調整 */
        @media (max-width: 500px) {
            body { padding: 10px; }
            .post { padding: 12px; }
        }
    </style>
</head>
<body>
    <h1>課題用 掲示板</h1>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?php echo htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="bbs.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>名前:<br> <input type="text" name="name" required></label>
        </div>
        <div class="form-group">
            <label>本文:<br> <textarea name="body" rows="4" required></textarea></label>
        </div>
        <div class="form-group">
            <label>画像 (任意 / 5MB以下):<br> <input type="file" name="image" accept="image/*"></label>
        </div>
        <button type="submit">投稿する</button>
    </form>

    <hr style="border: 0; border-top: 2px solid #ccc; margin: 20px 0;">

    <h2>投稿一覧</h2>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <div class="meta">
                No.<?php echo htmlspecialchars($post['id'], ENT_QUOTES, 'UTF-8'); ?> | 
                <strong><?php echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?></strong> | 
                <?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <p><?php echo nl2br(htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8')); ?></p>
            <?php if ($post['image_path']): ?>
                <img src="<?php echo htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>


























OK) {





e)) {








ody, :image_path)";



























", Meiryo, sans-serif; 
   */



















 */



/

















  に */




































S, 'UTF-8'); ?></div>




>


rea></label>


" accept="image/*"></label>










-8'); ?> | 
S, 'UTF-8'); ?></strong> | 
 'UTF-8'); ?>

'UTF-8')); ?></p>

NT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">

































_OK) {




;



le)) {










body, :image_path)";
























adding: 20px; }

 border-radius: 5px; background-color: #f9f9f9; }
op: 10px; border: 1px solid #ddd; }
bottom: 1px dotted #ccc; padding-bottom: 5px; }








ES, 'UTF-8'); ?></div>





l>


"width: 100%;"></textarea></label>


e" accept="image/*"></label>











F-8'); ?> | 
ES, 'UTF-8'); ?></strong> | 
, 'UTF-8'); ?>



 'UTF-8')); ?></p>



ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">






























OK) {





{






ody, :image_path)";























dding: 20px; }

border-radius: 5px; }
p: 10px; }









>


width: 100%;"></textarea></label>


image/*"></label>










UTF-8'); ?></strong> 
'], ENT_QUOTES, 'UTF-8'); ?></span>
'UTF-8')); ?></p>



NT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">





