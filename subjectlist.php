<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h3>比較を行うファイル一覧 (後期期末提出に対応)</h3>
    <div>
        現在、前期期末提出までの以下のファイルとの比較に対応しています。このツールは正確性を保証するものではありません。
    </div>

    <br>
    <br>

    <?php


$serverName = "localhost";
$databaseName = "test";
$username = "root";
$password = "";
$source_directory_path = "C:\\xampp\\htdocs\\test\\aaa";
// $target_directory_path = "C:\\xampp\\htdocs\\test\\bbb";

function recreate_table($pdo, $table_name) {
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS $table_name;");
    $stmt->execute();
    $stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS $table_name(filename VARCHAR(255));");
    $stmt->execute();
}

function set_insert_db_from_dir($pdo, $table_name, $directoryPath) {
    // ディレクトリ内のファイル一覧を取得
    $files = scandir($directoryPath);

    // ファイル名をデータベースに挿入
    foreach ($files as $file) {
        // "." と ".." を無視
        if ($file != "." && $file != "..") {
            // ファイル名をデータベースに挿入
            $stmt = $pdo->prepare("INSERT INTO $table_name (filename) VALUES (:filename)");
            $stmt->bindParam(':filename', $file);

            if ($stmt->execute()) {
                #echo "ファイル名 '$file' をデータベースに挿入しました<br>\n";
            } else {
                echo "エラー: ファイル名 '$file' をデータベースに挿入できませんでした<br>\n";
            }
        }
    }
}

try {
    // SQL Serverに接続
    $pdo = new PDO("mysql:host=$serverName; dbname=$databaseName; charset=utf8mb4", $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    recreate_table($pdo, "source_php_check");

    
    set_insert_db_from_dir($pdo, "source_php_check", $source_directory_path);

    $stmt = $pdo->prepare("SELECT * FROM source_php_check");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>\n";
    echo "<tr><th>個数</th><th>ファイル名</th></tr>\n";
    $count = 0;
    
    foreach ($result as $row) {
        $count++;
        echo "<tr>\n";
        echo "<td>" . $count . "</td>\n";
        echo "<td>" . $row['filename'] . "</td>\n";
        echo "</tr>\n";
    }
    echo "<td>合計</td>\n";
    echo "<td>" . $count . "</td>\n";
    echo "</table>\n";

    echo "<br>\n";
   
}
catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
?>
</body>

</html>