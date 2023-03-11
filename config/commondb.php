<?php

if (YII_ENV_DEV) {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=sdamex_main',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    
        // Schema cache options (for production environment)
        //'enableSchemaCache' => true,
        //'schemaCacheDuration' => 60,
        //'schemaCache' => 'cache',
    ];
} else {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=gurumaiba_sdamex',
        'username' => 'gurumaiba_sdamex',
        'password' => 'DataBaseMainSdamex2020@',
        'charset' => 'utf8',
    ];
}