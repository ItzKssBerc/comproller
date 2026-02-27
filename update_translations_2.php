<?php

$files = [
    'lang/en.json' => [
        'Add Locker Key' => 'Add Locker Key',
        'Key Number' => 'Key Number',
    ],
    'lang/hu.json' => [
        'Add Locker Key' => 'Szekrénykulcs hozzáadása',
        'Key Number' => 'Kulcs száma',
    ],
];

foreach ($files as $path => $newKeys) {
    if (file_exists($path)) {
        $content = json_decode(file_get_contents($path), true);
        foreach ($newKeys as $key => $value) {
            $content[$key] = $value;
        }
        file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated $path\n";
    }
}
