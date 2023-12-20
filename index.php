<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

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


    if (array_key_exists("studentId", $_POST)) {
        print "<hr>";
        
        $studentId = $_POST["studentId"];
        print "あなたの学籍番号: <code>" . $studentId . "</code><br>";

        exec("getent passwd | grep $studentId | awk -F \":\" '{ print \$6 }'", $studentIdPath, $status);
        $studentIdPath = implode("\n", $studentIdPath);
        if ($status != 0 || $studentIdPath === "") {
            print "<h3>予期しないエラーが発生しました。学籍番号が正しいか確認してください。<br>エラーコード: " . $status . "</h3><br>";

            return -1;
        }
        

        // $studentIdPath = mb_substr($studentIdPath, 0, -1, 'UTF-8');

        print "あなたのホームディレクトリ: <code>" . $studentIdPath . "</code><br>";

        print "<hr>";

        

        

        
        // echo join("<br/>", $result);

        // exec("cd checkFolder ; find . > list1.txt", $result, $status);
        // print `cd check2 ; find . > list2.txt`;
        // exec("diff <(find ./checkFolder) <(find ./check) -y --suppress-common-lines", $result, $status);
        exec("LANG=ja_JP.UTF-8 diff -q -r ./checkFolder/ $studentIdPath/public_html/info/ | grep のみに存在 | grep checkFolder", $result, $status);
        if (!($status == 0 || $status == 1)) {
            print "<h3>予期しないエラーが発生しました。学籍番号が正しいか確認してください。<br>エラーコード: " . $status . "</h3><br>";
            
        } else {
            $result = implode("\n", $result);
            $result = str_replace("./checkFolder", "./必要な提出ファイル", $result);
            
            if ($result == "") {
                print '<h2>比較結果: <font color="green">提出完了</font>🟢</h2>' ;
                print "<b>ファイル名での差異は検出されませんでした。提出おめでとうございます。</b>";

                print "<pre>" . $result . "</pre>";
            } else {
                print '<h2>比較結果: <font color="red">未完了</font>🔴</h2>' ;
                print "<pre>" . $result . "</pre>";
            }

            

            // $result = shell_exec("LANG=ja_JP.UTF-8 diff -q -r /home/i/i02/g20324/public_html/info/ $studentIdPath/public_html/info/ | grep -v 異なります");
            
            // $result = str_replace("./checkFolder", "./完璧フォルダ", $result);
            // $result = str_replace("$studentIdPath/public_html/info/", $studentId . "のフォルダ", $result);

            // print "<pre>" . $result . "</pre>";
        }

        

        
    }

    ?>
</body>

</html>