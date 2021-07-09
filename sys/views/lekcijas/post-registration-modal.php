<?php

use app\models\PostRegistrationForm;

$model = new PostRegistrationForm();
$body = $this->render('post-registration-modal-body', [
    'model' => $model
]);

echo $this->render("@app/views/shared/modal", [
    'id' => 'post-registration-modal',
    'title' => 'Sveicieni! Kad vēlēsies sākt darboties?',
    'body' => $body
]);
