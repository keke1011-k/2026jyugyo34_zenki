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

    // JavaScriptでBase64変換された画像データが送信された場合の処理
    if (!empty($_POST['image_base64'])) {
        $base64_data = $_POST['image_base64'];
        
        // "data:image/jpeg;base64," のようなプレフィックスを取り除く
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_data, $type)) {
            $data = substr($base64_data, strpos($base64_data, ',') + 1);
            $data = base64_decode($data); // 文字列から画像データに復元
            
            // デコード後のサイズが5MB以下かチェック
            if (strlen($data) <= 5242880) {
                $upload_dir = 'images/';
                $filename = uniqid() . '.jpg';
                $target_file = $upload_dir . $filename;
                
                // サーバーに画像ファイルとして保存
                if (file_put_contents($target_file, $data)) {
                    $image_path = $target_file;
                }
            } else {
                $error_msg = 'エラー：画像サイズが大きすぎます。';
            }
        }
    } 
    // JSが動かなかった場合の通常のファイルアップロード処理（フォールバック）
    elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>課題用 掲示板</title>
    <style>
        body { 
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", Meiryo, sans-serif; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 15px; 
            background-color: #f4f5f7;
            color: #333;
        }
        
        h1 { font-size: 1.5em; text-align: center; }

        .form-group { margin-bottom: 15px; }
        input[type="text"], 
        textarea, 
        input[type="file"] {
            width: 100%; 
            box-sizing: border-box; 
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px; 
        }
        
        button {
            width: 100%; 
            padding: 14px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .post { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 8px; 
            background-color: #ffffff; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            transition: background-color 0.5s;
        }
        
        .post:target {
            background-color: #fff9c4;
            border-color: #fbc02d;
            box-shadow: 0 0 10px rgba(251, 192, 45, 0.5);
        }

        .post img { 
            max-width: 100%; 
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
        
        .anchor-link {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        .anchor-link:hover {
            text-decoration: underline;
        }

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
            <label>本文 (「&gt;&gt;番号」でレスアンカー):<br> <textarea name="body" rows="4" required></textarea></label>
        </div>
        <div class="form-group">
            <label>画像 (自動縮小されます):<br> <input type="file" name="image" accept="image/*" id="imageInput"></label>
            
            <!-- 【追加】Base64データを格納するための隠しフィールド -->
            <input type="hidden" name="image_base64" id="imageBase64Input">
            <!-- 【追加】画像を描画・縮小するための隠しCanvas -->
            <canvas id="imageCanvas" style="display:none;"></canvas>
        </div>
        <button type="submit">投稿する</button>
    </form>

    <hr style="border: 0; border-top: 2px solid #ccc; margin: 20px 0;">

    <h2>投稿一覧</h2>
    <?php foreach ($posts as $post): ?>
        <div class="post" id="post-<?php echo htmlspecialchars($post['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="meta">
                No.<?php echo htmlspecialchars($post['id'], ENT_QUOTES, 'UTF-8'); ?> | 
                <strong><?php echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?></strong> | 
                <?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <p>
                <?php 
                    $safe_body = htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8');
                    $anchor_body = preg_replace(
                        '/&gt;&gt;([0-9]+)/', 
                        '<a href="#post-$1" class="anchor-link">&gt;&gt;$1</a>', 
                        $safe_body
                    );
                    echo nl2br($anchor_body); 
                ?>
            </p>
            <?php if ($post['image_path']): ?>
                <img src="<?php echo htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // 画像縮小処理 & base64のテキストに変換して name="image_base64" なinput要素につっこむ
            const imageBase64Input = document.getElementById("imageBase64Input"); // base64を送るようのinput
            const canvas = document.getElementById("imageCanvas"); // 描画するcanvas
            const reader = new FileReader();
            const image = new Image();
            
            reader.onload = () => { // ファイルの読み込み完了したら動く処理を指定
                image.onload = () => { // 画像として読み込み完了したら動く処理を指定
                    // 元の縦横比を保ったまま縮小するサイズを決めてcanvasの縦横に指定する
                    const originalWidth = image.naturalWidth; // 元画像の横幅
                    const originalHeight = image.naturalHeight; // 元画像の高さ
                    const maxLength = 2000; // 横幅も高さも2000px以下に縮小するものとする
                    
                    if (originalWidth <= maxLength && originalHeight <= maxLength) { // どちらもmaxLength以下の場合そのまま
                        canvas.width = originalWidth;
                        canvas.height = originalHeight;
                    } else if (originalWidth > originalHeight) { // 横長画像の場合
                        canvas.width = maxLength;
                        canvas.height = maxLength * originalHeight / originalWidth;
                    } else { // 縦長画像の場合
                        canvas.width = maxLength * originalWidth / originalHeight;
                        canvas.height = maxLength;
                    }

                    // canvasに実際に画像を描画 (canvasはdisplay:noneで隠れているためわかりにくいが...)
                    const context = canvas.getContext("2d");
                    context.drawImage(image, 0, 0, canvas.width, canvas.height);

                    // canvasの内容をjpeg形式のbase64に変換しinputのvalueに設定
                    imageBase64Input.value = canvas.toDataURL('image/jpeg', 0.9);
                };
                image.src = reader.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
