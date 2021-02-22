<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\SectionsVisible;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $model->title;
 ['label' => \Yii::t('app',  'Lessons'), 'url' => ['index']];
 $model->title;

?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="col-md-12">
        <?php if ($model->file) { ?>
            <?= $this->render('video', ['lecturefiles' => [0 => ['title' => $model->title, 'file' => $model->file]], 'videos' => ['mp4', 'mov', 'ogv', 'webm', 'flv', 'avi', 'f4v'], 'baseUrl' => Yii::$app->request->baseUrl, 'thumbnail' => $videoThumb ?? '']); ?>
        <?php } ?>
     
        <?= $model->description ?>
        <?php if ($evaluations and $lectureEvaluations) {  ?>
            <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations, 'force' => false]) ?>
        <?php } ?>
    </div>
</div>
</div>