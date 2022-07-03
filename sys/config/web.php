<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'koklu-mezs',
    'name' => 'Kokļu mežs',
    'sourceLanguage' => 'en-US',
    'language' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'on beforeAction' => function ($event) {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Yii::$app->user->identity;
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        date_default_timezone_set('EET');
    },
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'q6hzujS3q73rs42dJYgBWOpNGOOtXf9E',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            'fileTransportPath' => 'sent-emails/'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/errors.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@runtime/logs/warnings.log',
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php'
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
    'defaultRoute' => 'site/index',
    'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['@'], //глобальный доступ к фаил менеджеру @ - для авторизорованных , ? - для гостей , чтоб открыть всем ['@', '?']
            'disabledCommands' => ['netmount'], //отключение ненужных команд https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#commands
            'roots' => [
                [
                    'baseUrl' => '@web',
                    'basePath' => '@webroot',
                    'path' => 'files/global',
                    'name' => Yii::t('app', 'Global files')
                ],
                [
                    'class' => 'mihaildev\elfinder\volume\UserPath',
                    'path'  => 'files/user_{id}',
                    'name'  =>  Yii::t('app', 'My files')
                ],
                // [
                //     'path' => 'files/some',
                //     'name' => ['category' => 'my','message' => 'Some Name'] //перевод Yii::t($category, $message)
                // ],
                // [
                //     'path'   => 'files/some',
                //     'name'   => ['category' => 'my','message' => 'Some Name'], // Yii::t($category, $message)
                //     'access' => ['read' => '*', 'write' => 'UserFilesAccess'] // * - для всех, иначе проверка доступа в даааном примере все могут видет а редактировать могут пользователи только с правами UserFilesAccess
                // ]
            ],
            'watermark' => [
                'source'         => __DIR__ . '/logo.png', // Path to Water mark image
                'marginRight'    => 5,          // Margin right pixel
                'marginBottom'   => 5,          // Margin bottom pixel
                'quality'        => 95,         // JPEG image save quality
                'transparency'   => 70,         // Water mark image transparency ( other than PNG )
                'targetType'     => IMG_GIF | IMG_JPG | IMG_PNG | IMG_WBMP, // Target image formats ( bit-field )
                'targetMinPixel' => 200         // Target image minimum pixel size
            ]
        ],
        'fitness-workouts' => 'app\fitness\controllers\WorkoutController',
        'fitness-exercises' => 'app\fitness\controllers\ExerciseController',
        'fitness-templates' => 'app\fitness\controllers\TemplateController',
        'fitness-student-exercises' => 'app\fitness\controllers\StudentExerciseController',
        'fitness-exercise-sets' => 'app\fitness\controllers\ExerciseSetController',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '213.21.219.28'],
    ];
}

return $config;
