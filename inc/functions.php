<?php

function trueWithProbability($probability)
{
    $probability = max(min($probability, 1), 0);
    return (mt_rand() / mt_getrandmax() <= $probability);
}

function pickFrom(array $a)
{
    if (empty($a)) return null;

    $k = array_rand($a);
    return $a[$k];
}

function pickFromFile($file)
{
    $a = [];
    foreach (explode("\n", file_get_contents($file)) as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        $a[] = $line;
    }
    return pickFrom($a);
}

function handleRandomUserName($user_name, Database\Connection $Connection)
{
    $res = 0;
    $use_existing = trueWithProbability(0.6);
    if ($use_existing) {
        $hosts = $Connection->select('SELECT * FROM user WHERE name = :n', ['n' => $user_name]);
        if (!empty($hosts)) {
            $res = pickFrom($hosts)['id'];
        }
    }
    if (empty($res)) {
        $res = $Connection->insert(INSERT_USER_QUERY, ['name' => $user_name, 'date_created' => date('Y-m-d H:i:s')]);
    }
    if (empty($res)) {
        throw new Exception('Failed to create user');
    }
    return $res;
}