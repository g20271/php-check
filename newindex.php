<?php


$serverName = "localhost";
$databaseName = "test";
$username = "root";
$password = "";
$source_directory_path = "C:\\xampp\\htdocs\\test\\aaa";
$target_directory_path = "C:\\xampp\\htdocs\\test\\bbb";

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
    recreate_table($pdo, "target_php_check");

    
    set_insert_db_from_dir($pdo, "source_php_check", $source_directory_path);
    set_insert_db_from_dir($pdo, "target_php_check", $target_directory_path);


    $common = <<<DDL
SELECT 
    t1.filename,
    t2.filename
FROM 
    source_php_check t1
JOIN 
    target_php_check t2 ON t1.filename = t2.filename;
DDL;


    $onlySource = <<<DDL
    SELECT t1.filename
    FROM source_php_check t1
    LEFT JOIN target_php_check t2 ON t1.filename = t2.filename
    WHERE t2.filename IS NULL;
DDL;

    $onlyTarget = <<<DDL
    SELECT t1.filename
    FROM target_php_check t1
    LEFT JOIN source_php_check t2 ON t1.filename = t2.filename
    WHERE t2.filename IS NULL;
DDL;

    $stmt = $pdo->prepare($onlyTarget);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['filename'] . "<br>\n";
    }

    
    // 接続を閉じる
    $pdo = null;


    
    

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
