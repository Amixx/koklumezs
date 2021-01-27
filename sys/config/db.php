<?php

return [
    'class' => 'yii\db\Connection',
    ////datubāze no prod servera
    // 'dsn' => 'mysql:host=skola.koklumezs.lv;dbname=dev_skola_sys_db',
    // 'username' => 'skola',
    // 'password' => 'skola',

    ////lokālā datubāze
    'dsn' => 'mysql:host=localhost;dbname=skola_sys_db',
    'username' => 'skola',
    'password' => 'nJ%k]AQn36vA',

    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
