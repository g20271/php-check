<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h3>比較を行うファイル一覧 (前期期末提出に対応)</h3>
    <div>
        現在、前期期末提出までの以下のファイルとの比較に対応しています。このツールは正確性を保証するものではありません。
    </div>

    <?php
    $result = shell_exec("find ./checkFolder/ -mindepth 1");
    $result = str_replace("./checkFolder", ".", $result);

    print "<pre>" . $result . "</pre>";
    ?>
</body>

</html>