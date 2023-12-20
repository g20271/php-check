<?php
// $studentIdPath = "/home/i/i02/g20271";
// $result = shell_exec("diff -q -r --unidirectional-new-file ./checkFolder/ $studentIdPath/public_info/info/");
// print $result;

shell_exec('find ./checkFolder -mindepth 1 | sed "s/\.\/checkFolder\///g" | sort > list1.txt');

$command = '/home/i/i02/g20271/public_html/info/';
$command = str_replace("/", "\/", $command);
$command = str_replace(".", "\.", $command);


shell_exec('find /home/i/i02/g20271/public_html/info -mindepth 1 | sed "s/'.$command.'//g" | sort > list2.txt');
// $result = shell_exec('diff list1.txt list2.txt -y --suppress-common-lines');
$result = shell_exec('diff list1.txt list2.txt --suppress-common-lines');
print $result;