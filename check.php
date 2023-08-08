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
    <title>Document</title>
</head>

<body>
    <form action="check.php" method="post">
        学籍番号: <input type="text" name="studentId">
        <input type="submit" name="送信">
    </form>

    <br>
    <br>

    <?php

    if (array_key_exists("studentId", $_POST)) {
        $studentId = $_POST["studentId"];
        print "あなたの学籍番号: " . $studentId . "<br>";

        print "比較結果: " . "<br>";

        // exec("cd checkFolder ; find . > list1.txt", $result, $status);
        // print `cd check2 ; find . > list2.txt`;
        // exec("diff <(find ./checkFolder) <(find ./check) -y --suppress-common-lines", $result, $status);
        // exec("diff -q -r --unidirectional-new-file --brief ./checkFolder/ ./check", $result, $status);
        // exec("ls ./", $result, $status);
        //     if ($status) {
        //         print $status . "<br><br>";
        //     }

        //     echo join("<br/>", $result);

        echo `diff <(find ./checkFolder) <(find ./check) -y --suppress-common-lines`;
    }

    ?>
</body>

</html>