<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\SectionsVisible;

$this->title = 'Nodarbība (lesson): ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Nodarbības/Lessons', 'url' => ['index']];
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
                if ($uLecture->is_favourite) echo " (<span class='text-primary'>Mīļākā/Favourite</span>)";
                if ($uLecture->still_learning) echo " (<span class='text-primary'>Vēl mācos/Still learning</span>)";
            } ?>
        </h2>

        <?php if ($model->file) { ?>
            <?= $this->render('video', ['lecturefiles' => [0 => ['title' => $model->title, 'file' => $model->file, 'thumb' => $model->thumb ?? '']], 'videos' => $videos, 'baseUrl' => $baseUrl]); ?>
        <?php } ?>
        <?php if ($model->file && $userCanDownloadFiles && SectionsVisible::isVisible("Video lejupielādes poga")) { ?>
            <a href="<?= $model->file ?> " target="_blank" download>Lejupielādēt nodarbības video failu</a>
        <?php } ?>
        <?php if ($lecturefiles) { ?>
            <?= $this->render('video', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl]); ?>
            <?= $this->render('audio', ['lecturefiles' => $lecturefiles, 'audio' => $audio]); ?>
        <?php } ?>
        <?= $model->description ?>
        <?php if ($uLecture && $uLecture->evaluated) { ?>
            <div style="margin-top:10px;">
                <div class="col-sm-6" style="margin-bottom: 5px">
                    <?php
                    $firstButtonText = "Pievienot mīļākajām nodarbībām/Add to favourites";
                    if ($uLecture->is_favourite) {
                        $firstButtonText = "Noņemt no mīļākajām nodarbībām/Remove from favourites";
                    }
                    ?>
                    <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
                    <?= Html::submitButton($firstButtonText, ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::endForm() ?>
                </div>
                <div class="col-sm-6" style="margin-bottom: 5px">
                    <?php
                    $secondButtonText = "Pievienot nodarbībām, ko vēl mācos/Add to lessons I'm still learning";
                    if ($uLecture->still_learning) {
                        $secondButtonText = "Noņemt no nodarbībām, ko vēl mācos/Remove from lessons I'm still learning";
                    }
                    ?>
                    <?= Html::beginForm(["/lekcijas/toggle-still-learning?lectureId=$uLecture->lecture_id"], 'get') ?>
                    <?= Html::submitButton($secondButtonText, ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::endForm() ?>
                </div>

            </div>

        <?php } ?>
        <?php if ($difficulties and $lectureDifficulties and $difficultiesVisible) { ?>
            <?= $this->render('difficulties', ['difficulties' => $difficulties, 'difficultiesEng' => $difficultiesEng, 'lectureDifficulties' => $lectureDifficulties]) ?>
        <?php } ?>
        <?php /* if($handdifficulties AND $lectureHandDifficulties){  ?>
            <?= $this->render('handdifficulties',['handdifficulties' => $handdifficulties, 'lectureHandDifficulties' => $lectureHandDifficulties]) ?>      
        <?php } */ ?>
        <?php if ($comments) { ?>
            <?= $this->render('comments', ['comments' => $comments]); ?>
        <?php } ?>
        <?php if ($lecturefiles) { ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
        <?php } ?>
        <?php if ($evaluations and $lectureEvaluations) {  ?>
            <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations, 'force' => $force]) ?>
        <?php } ?>
        <?php if ($relatedLectures) { ?>
            <?= $this->render('related', ['relatedLectures' => $relatedLectures, 'lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl, 'userEvaluatedLectures' => $userEvaluatedLectures]) ?>
        <?php } ?>
    </div>
</div>
</div>