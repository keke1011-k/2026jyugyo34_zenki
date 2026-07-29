









































































('mysql:host=mysql;dbname=example_db', 'root', '');

if (isset($_POST['body'])) {
// POSTで送られてくるフォームパラメータ body がある場合

// insertする
$insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");
$insert_sth->execute([
		':body' => $_POST['body'],
]);

// 処理が終わったらリダイレクトする
// リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
header("HTTP/1.1 302 Found");
header("Location: ./formtodbtest.php;
('mysql:host=mysql;dbname=example_db', 'root', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 「送信」ボタンが押されて、データが送られてきた時の処理
if (isset($_POST['body'])) {
    
    // データベースの hogehoge テーブルに保存(INSERT)する
    $insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");
    $insert_sth->execute([
        ':body' => $_POST['body']
    ]);

    // 保存が終わったら、画面を再読み込みさせる（これをしないと再リロード時に同じデータが二重送信されてしまいます）
    header("HTTP/1.1 302 Found");
    header("Location: ./formtodbtest.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>DB保存フォーム</title>
</head>
<body>
    <p>データベースに保存する内容を入力してください。</p>
    <!-- フォームの送信先はこのファイル自身にする -->
    <form method="POST" action="./formtodbtest.php">
        <textarea name="body"></textarea>
        <br>
        <button type="submit">送信</button>
    </form>
</body>
</html>
"
}

?>

<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./formtodbtest.php">
<textarea name="body"></textarea>
<button type="submit">送信</button>
</form>

																																																																											
if (isset($_POST['body'])) {                                                                                                                            
// POSTで送られてくるフォームパラメータ body がある場合                                                                                               
																																																																											
// insertする                                                                                                                                         
$insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");                                                                            
$insert_sth->execute([                                                                                                                                
		':body' => $_POST['body'],                                                                                                                        
]);                                                                                                                                                   
																																																																											
// 処理が終わったらリダイレクトする                                                                                                                   
// リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる                                                                                 
header("HTTP/1.1 302 Found");                                                                                                                         
header("Location: ./formtodbtest.php");                                                                                                               
return;                                                                                                                                               
}                                                                                                                                                       
																																																																											
?>                                                                                                                                                      
																																																																											
<!-- フォームのPOST先はこのファイル自身にする -->                                                                                                       
<form method="POST" action="./formtodbtest.php">                                                                                                        
<textarea name="body"></textarea>                                                                                                                     
<button type="submit">送信</button>                                                                                                                   
</form>                                                                                                                                                 
																																																																											
// データベース接続                                                                                                                                     
$pdo = new PDO('mysql:dbname=example_db;host=mysql;charset=utf8mb4', 'root', '');                                                                       
																																																																											
// 1ページに表示する件数                                                                                                                                
$limit = 10;                                                                                                                                            
																																																																											
// URLから「今何ページ目か」を受け取る（指定がなければ1ページ目）                                                                                       
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;                                                                                                  
if ($page < 1) { $page = 1; }                                                                                                                           
																																																																											
// 飛ばす件数（OFFSET）を計算する                                                                                                                       
$offset = ($page - 1) * $limit;                                                                                                                         
																																																																											
// 全体のデータ数を数える（最後のページ数を計算するため）                                                                                               
$total_stmt = $pdo->query("SELECT COUNT(*) FROM access_log2");                                                                                          
$total_count = $total_stmt->fetchColumn();                                                                                                              
$total_pages = ceil($total_count / $limit); // 端数切り上げ                                                                                             
																																																																											
// データを取得する（LIMITとOFFSETを使用）                                                                                                              
// 演習2で作成した access_log2 テーブルから取得します                                                                                                   
$stmt = $pdo->prepare("SELECT * FROM access_log2 ORDER BY created_at DESC LIMIT :limit OFFSET :offset");                                                
// LIMITとOFFSETは必ず「数値（INT）」として扱うように指定する                                                                                           
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);                                                                                                     
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);                                                                                                   
$stmt->execute();                                                                                                                                       
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);                                                                                                              
																																																																											
?>                                                                                                                                                      
																																																																											
<!DOCTYPE html>                                                                                                                                         
<html lang="ja">                                                                                                                                        
<head>                                                                                                                                                  
	<meta charset="utf-8">                                                                                                                              
	<title>アクセスログ閲覧</title>                                                                                                                     
	<style>                                                                                                                                             
			body { font-family: sans-serif; text-align: center; }                                                                                           
			table { margin: 20px auto; border-collapse: collapse; width: 80%; }                                                                             
			th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }                                                                           
			th { background-color: #f4f4f4; }                                                                                                               
			.pagination { margin: 20px 0; }                                                                                                                 
			.pagination a { margin: 0 10px; text-decoration: none; color: blue; }                                                                           
	</style>                                                                                                                                            
</head>                                                                                                                                                 
<body>                                                                                                                                                  
																																																																											
	<h2>アクセスログ閲覧</h2>                                                                                                                           
	<p><a href="enshu2.php">アクセスログの保存は演習2のページで</a></p>                                                                                 
                                                                                                                                                        
    <div class="pagination">                                                                                                                            
