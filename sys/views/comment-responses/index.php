<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DifficultiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Atbildes uz komentāriem/Comment responses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-responses-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <div class="row">
            <div class="col-sm-6"><strong>Komentāra atbilde</strong></div>
            <div class="col-sm-6"><strong>Pilna sarakste šeit:</strong></div>
        </div>
        <?php foreach ($commentResponses as $response) { ?>
            <div class="row">
                <div class="col-sm-6"><?= $response['text'] ?></div>
                <div class="col-sm-6"><a href="/sys/lekcijas/lekcija/<?= $response['userlectureevaluation']['lecture']['id'] ?>"> <?= $response['userlectureevaluation']['lecture']['title'] ?></a></div>
            </div>
        <?php } ?>
    </div>
</div>