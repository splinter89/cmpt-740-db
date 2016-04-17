<?php

function pick(array $a)
{
    return array_rand(array_flip($a));
}

function pickFromFile($file)
{
    $a = [];
    foreach (explode("\n", file_get_contents($file)) as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        $a[] = $line;
    }
    return pick($a);
}
