<?php

$secrets = require __DIR__ . '/secrets.php';

$params = [
    'adminEmail' => 'jansonsansis@gmail.com',
    //TODO: nomainīt uz kkādu @tutory.lv e-pastu
    'senderEmail' => 'skola@koklumezs.lv',
    'noreplyEmail' => 'noreply@koklumezs.lv',
    'senderName' => 'Kokļu mežs',
    'supportEmail' => 'jansonsansis@gmail.com',
    'user.passwordResetTokenExpire' => 3600,
];

return array_merge($params, $secrets);
