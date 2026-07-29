<?php
$image_file = 'myphoto.jpeg';

echo "<img src='" . $image_file . "' width='400'><br>";
echo "この画像のexif情報は以下の通りです。<br>";

$exif_data = exif_read_data($image_file);

if ($exif_data === false) {
    echo "この画像にはEXIFデータが含まれていません。";
} else {
    // ★ここを追加！：謎の数字を日本語の日付に翻訳する
    $timestamp = $exif_data['FileDateTime'];
    $readable_date = date('Y年m月d日 H時i分s秒', $timestamp);
    
    // 翻訳した日付を目立つように表示
    echo "<h3>ファイルの日時： " . $readable_date . "</h3>";

    echo "<pre>";
    print_r($exif_data);
    echo "</pre>";
}
?>






















