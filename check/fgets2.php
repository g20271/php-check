<?php
$fh = fopen("temp/test.txt", "rt");
if ($fh) {
    while (($t = fgets($fh)) !== FALSE) {
        print $t . "<br>";
    }
    fclose($fh);
} else {
    print "ファイルのオープンに失敗しました";
}
