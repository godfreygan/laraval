<?php

$hash  = []; // file => file position
$first = true;

while ($file = fgets(STDIN)) {
    $file = trim($file);

    $handle = @fopen($file, 'r');
    if (!$handle) {
        continue;
    }

    if (!array_key_exists($file, $hash)) {
        $hash[$file] = 0;
    } else {
        fseek($handle, $hash[$file]);
    }

    while (($buffer = fgets($handle)) !== false) {
        if (!$first) {
            echo $buffer;
        }
        $hash[$file] = ftell($handle);
    }

    $first = false;
    fclose($handle);
}
