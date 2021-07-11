<?php

use app\models\StartLaterCommitments;

$model = new StartLaterCommitments();
$model->start_time_of_day = 'morning';

$body = $this->render('post-registration-modal-body', [
    'model' => $model
]);

echo $this->render("@app/views/shared/modal", [
    'id' => 'post-registration-modal',
    'title' => 'Sveicieni! Kad vēlēsies sākt darboties?',
    'body' => $body
]);
