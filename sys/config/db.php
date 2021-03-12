<?php

return [
    'class' => 'yii\db\Connection',
    ////datubāze no prod servera
    'dsn' => 'mysql:host=skola.koklumezs.lv;dbname=dev_skola_sys_db',
    'username' => 'skola',
    'password' => 'skola',

    ////lokālā datubāze
    // 'dsn' => 'mysql:host=host.docker.internal;dbname=skola_sys_db',
    // 'username' => 'skola',
    // 'password' => 'skola',

    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
