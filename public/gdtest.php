<?php
if (isset($_POST['color'])) {
    $selected_color = $_POST['color'];

    $width = 200;
    $height = 200;
    $image = imagecreatetruecolor($width, $height);

    $hex = ltrim($selected_color, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $fill_color = imagecolorallocate($image, $r, $g, $b);
    imagefill($image, 0, 0, $fill_color);

    // 画像として出力する
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
    
    // ここで処理を止める（下のHTMLを表示させない）
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>GD演習</title>
</head>
<body>
    <p>色を選んで「決定」を押してね。</p>
    
    <form method="POST" action="">
        <input type="color" name="color" value="#000000">
        <input type="submit" value="決定">
    </form>
</body>
</html>
















































