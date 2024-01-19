<?php


$serverName = "localhost";
$databaseName = "g20271_db";
$username = "g20271";
$password = "2023php-DB";
$source_directory_path = "/home/i/i02/g20326/public_html/info";
$target_directory_path = "/home/i/i02/g20271/public_html/info";

try {
    // SQL Serverに接続
    $pdo = new PDO("mysql:host=$serverName; dbname=$databaseName; charset=utf8mb4", $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    recreate_table($pdo, "source_php_check");
    recreate_table($pdo, "target_php_check");

    
    set_insert_db_from_dir($pdo, "source_php_check", $source_directory_path);
    set_insert_db_from_dir($pdo, "target_php_check", $target_directory_path);


    $commonSB = <<<DDL
SELECT 
    t1.common_column,
    t1.table1_specific_column,
    t2.table2_specific_column
FROM 
    table1 t1
JOIN 
    table2 t2 ON t1.common_column = t2.common_column;
DDL;

    // 接続を閉じる
    $pdo = null;


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

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
