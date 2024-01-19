<?php
$serverName = "localhost";
$databaseName = "g20271_db";
$username = "g20271";
$password = "2023php-DB";

try {
    // SQL Serverに接続
    $pdo = new PDO("mysql:host=$serverName; dbname=$databaseName; charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // テーブルが存在しない場合は作成する
    $tableName = "PrepareData";
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS $tableName (
            FileName string,
        )
    ";
    // $createTableSQL = "
    //     CREATE TABLE IF NOT EXISTS $tableName (
    //         FileName NVARCHAR(MAX),
    //         SubmitDue NVARCHAR(MAX)
    //     )
    // ";

    $pdo->exec($createTableSQL);

    $directoryPath = "/home/i/i02/g20326/public_html/info";

    // ディレクトリ内のファイル一覧を取得
    $files = scandir($directoryPath);

    // ファイル名をSQL Serverのテーブルに挿入
    foreach ($files as $file) {
        // "." および ".." は無視
        if ($file != "." && $file != "..") {
            $sql = "INSERT INTO $tableName (FileName, SubmitDue) VALUES (:filename, :submitdue)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':filename', $file);
            // $stmt->bindValue(':submitdue', 'some_value'); // SubmitDueに適切な値を設定

            // クエリの実行
            $stmt->execute();
        }
    }

    // 接続を閉じる
    $pdo = null;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
