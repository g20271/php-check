<?php
$studentIdPath = "/home/i/i02/g20271";
$result = shell_exec("diff -q -r --unidirectional-new-file ./checkFolder/ $studentIdPath/public_info/info/");
print $result;
