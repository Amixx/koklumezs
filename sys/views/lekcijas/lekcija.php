<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\SectionsVisible;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Lessons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="col-md-3 ">
        <?php foreach ($userLectures as $lecture) {  ?>
            <?php if ($lecture->sent) { ?>
                <p><a href="<?= Url::to(['lekcijas/lekcija', 'id' => $lecture->lecture_id]); ?>"><?= $lecture->lecture->title ?></a></p>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="border-left col-md-9">
        <h2 class="text-center">
            <?= $model->title ?>
            <?php if ($uLecture) {
                if ($uLecture->is_favourite) echo " (<span class='text-primary'>" . \Yii::t('app',  'Favourite') . "</span>)";
                if ($uLecture->still_learning) echo " (<span class='text-primary'>" . \Yii::t('app',  'Still learning') . "</span>)";
            } ?>
        </h2>

        <?php if ($model->file) { ?>
            <?= $this->render(
                'video',
                ['lecturefiles' => [0 => ['title' => $model->title, 'file' => $model->file]],
                'videos' => $videos,
                'baseUrl' => $baseUrl,
                'thumbnail' => $videoThumb ?? '',
                'idPrefix' => 'main',
            ]); ?>
        <?php } ?>
        <?php if ($model->file && $userCanDownloadFiles && SectionsVisible::isVisible("Video lejupielādes poga")) { ?>
            <a href="<?= $model->file ?> " target="_blank" download><?= \Yii::t('app',  'Download lesson video file') ?></a>
        <?php } ?>
        <?php if ($lecturefiles) { ?>
            <?= $this->render(
                'video',
                ['lecturefiles' => $lecturefiles,
                'videos' => $videos,
                'baseUrl' => $baseUrl,
                'thumbnail' => $videoThumb ?? '',
                'idPrefix' => 'file',
            ]); ?>
            <?= $this->render(
                'audio',
                ['lecturefiles' => $lecturefiles, 'audio' => $audio]
            ); ?>
        <?php } ?>
        <?= $model->description ?>
        <?php if ($uLecture && $uLecture->evaluated) { ?>
            <div style="margin-top:10px;">
                <div class="col-sm-6" style="margin-bottom: 5px">
                    <?php
                    $firstButtonText = \Yii::t('app',  'Add to favourites');
                    if ($uLecture->is_favourite) {
                        $firstButtonText = \Yii::t('app',  'Remove from favourites');
                    }
                    ?>
                    <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
                    <?= Html::submitButton($firstButtonText, ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::endForm() ?>
                </div>
                <div class="col-sm-6" style="margin-bottom: 5px">
                    <?php
                    $secondButtonText = \Yii::t('app',  'Add to lessons I\'m still learning');
                    if ($uLecture->still_learning) {
                        $secondButtonText = \Yii::t('app',  'Remove from lessons I\'m still learning');
                    }
                    ?>
                    <?= Html::beginForm(["/lekcijas/toggle-still-learning?lectureId=$uLecture->lecture_id"], 'get') ?>
                    <?= Html::submitButton($secondButtonText, ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::endForm() ?>
                </div>

            </div>

        <?php } ?>
        <?php if ($difficulties and $lectureDifficulties and $difficultiesVisible) { ?>
            <?= $this->render('difficulties', ['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>
        <?php } ?>
        <?php if ($comments) { ?>
            <?= $this->render('comments', ['comments' => $comments]); ?>
        <?php } ?>
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
        <?php } ?>
        <?php if ($evaluations and $lectureEvaluations) {  ?>
            <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations, 'isFavourite' => $uLecture['is_favourite'], 'isStillLearning' => $uLecture['still_learning'], 'force' => $force]) ?>
        <?php } ?>
        <?php if ($relatedLectures) { ?>
            <?= $this->render('related', ['relatedLectures' => $relatedLectures, 'lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl, 'userEvaluatedLectures' => $userEvaluatedLectures, 'videoThumb' => $videoThumb]) ?>
        <?php } ?>
    </div>
</div>
</div>