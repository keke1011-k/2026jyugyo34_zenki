<?php
if (!extension_loaded('apcu')) {
    die("APCuモジュールが読み込まれていません！");
}

// カウンターの処理（ここは先ほどと同じです）
$count = apcu_inc('my_counter', 1, $success);
if (!$success) {
    $count = 1;
    apcu_store('my_counter', $count);
}

// ★見本通りに1行だけ表示する
echo "あなたは " . $count . " 人目の訪問者です!";
?>

