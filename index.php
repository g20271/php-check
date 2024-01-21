<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

<style>
body{
    margin: 0;
}
table{
    width: 100vw;
}
</style>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP提出チェックツール (前期期末提出に対応)</title>
</head>

<body>
    <h1>PHP提出チェックツール (前期期末提出に対応)</h1>
    
    <p>
        下のテキストボックスに学籍番号(例:g29999)を入力すると正しく提出しているか全自動でチェックできます。<br>
        あくまでファイル名のみの比較を行なっています。このツールは正確性を保証するものでありません。
    </p>
    
    <p>
        <a href="subjectlist.php">実際に比較を行うファイル一覧はこちらです</a>
    </p>
    

    <form action="." method="post">
        学籍番号: <input type="text" name="studentId">
        <input type="submit" value="チェックする">
    </form>

    <br>
    <br>

<?php


if (!(array_key_exists("studentId", $_POST))) {
    return 0;
}
print "<hr>";

$studentId = $_POST["studentId"];
print "あなたの学籍番号: <code>" . $studentId . "</code><br>";

exec("getent passwd | grep $studentId | awk -F \":\" '{ print \$6 }'", $studentIdPath, $status);
$studentIdPath = implode("\n", $studentIdPath);
// if ($status != 0 || $studentIdPath === "") {
//     print "<h3>予期しないエラーが発生しました。学籍番号が正しいか確認してください。<br>エラーコード: " . $status . "</h3><br>";

//     return -1;
// }


// $studentIdPath = mb_substr($studentIdPath, 0, -1, 'UTF-8');

print "あなたのホームディレクトリ: <code>" . $studentIdPath . "</code><br>";

print "<hr>";

?>
        




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

    $stmt = $pdo->prepare($common);
    $stmt->execute();
    $common_result = $stmt->fetchAll(PDO::FETCH_BOTH);

    $stmt = $pdo->prepare($onlySource);
    $stmt->execute();
    $onlySource_result = $stmt->fetchAll(PDO::FETCH_BOTH);

    $stmt = $pdo->prepare($onlyTarget);
    $stmt->execute();
    $onlyTarget_result = $stmt->fetchAll(PDO::FETCH_BOTH);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<?php


if ($onlySource_result) {
    print '<h2>比較結果: <font color="red">未完了</font>🔴</h2>' ;
    echo "<b>未提出のファイルが提出されていません。提出してください</b>";
} else {
    // 一件もない場合のメッセージを表示
    print '<h2>比較結果: <font color="green">提出完了</font>🟢</h2>' ;
    print "<b>ファイル名での差異は検出されませんでした。提出おめでとうございます。</b>";

}


// 配列の長さを比較し、最長の長さを取得
$max_length = max(count($onlySource_result), count($onlyTarget_result), count($common_result));

// 配列を最長の長さに合わせる
$onlySource_result = array_pad($onlySource_result, $max_length, '');
$onlyTarget_result = array_pad($onlyTarget_result, $max_length, '');
$common_result = array_pad($common_result, $max_length, '');

// 表形式で表示
echo "<table border='1'>";
echo "<tr><th>未提出</th><th>不正なファイル</th><th>提出済み</th></tr>";

for ($i = 0; $i < $max_length; $i++) {
    echo "<tr>";
    echo "<td>" . (isset($onlySource_result[$i]["filename"]) ? $onlySource_result[$i]["filename"] : '') . "</td>";
    echo "<td>" . (isset($onlyTarget_result[$i]["filename"]) ? $onlyTarget_result[$i]["filename"] : '') . "</td>";
    echo "<td>" . (isset($common_result[$i]["filename"]) ? $common_result[$i]["filename"] : '')  . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br>";
echo "ヒント: 不正なファイルにあるファイルは、提出に必要なファイルではありません。名前を間違えている可能性があります。確認してください。";


// 結果の表示
// if ($onlySource_result) {
//     print '<h2>比較結果: <font color="red">未完了</font>🔴</h2>' ;
//     echo "<b>以下のファイルが提出されていません。提出してください</b>";
//     echo "<pre>";
//     foreach ($onlySource_result as $row) {
//         echo $row["filename"] . "\n";
//     }
//     echo "</pre>";
// } else {
//     // 一件もない場合のメッセージを表示
//     print '<h2>比較結果: <font color="green">提出完了</font>🟢</h2>' ;
//     print "<b>ファイル名での差異は検出されませんでした。提出おめでとうございます。</b>";

//     // print "<pre>" . $onlySource_result . "</pre>";
// }

// echo "<hr>";


// if ($onlySource_result && $onlyTarget_result) {
//     echo "<h3>あなたの提出フォルダのみに存在</h3>";
//     echo "<b>以下のファイルは名前を間違えていませんか? 確認してください</b>";
//     echo "<pre>";
//     foreach ($onlyTarget_result as $row) {
//         echo $row["filename"] . "\n";
//     }
//     echo "</pre>";
// }

// echo "<hr>";


// if ($common_result) {
//     print '<h3>提出済みファイル</h3>' ;
//     // echo "<b>以下のファイルが提出されていません。提出してください。</b>";
//     echo "<table border=\"1\">";
//     echo "<tr><th>提出済みファイル</th></tr>";
    
//     foreach ($common_result as $row) {
//         echo "<tr><td>" . $row["filename"] . "</td></tr>";
//     }
//     echo "</table>";
// }


?>



<?php

// 接続を閉じる
$pdo = null;
?>

</body>

</html>