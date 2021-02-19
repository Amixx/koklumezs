<?php

use yii\helpers\Url;
use app\models\SectionsVisible;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $model->title;
?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="col-md-3 lesson-column">
        <h3><?=\Yii::t('app',  'New lessons')?></h3>
        <?php foreach ($userLectures as $lecture) {  ?>
            <?php if ($lecture->sent) { ?>
                <p><a href="<?= Url::to(['lekcijas/lekcija', 'id' => $lecture->lecture_id]); ?>"><?= $lecture->lecture->title ?></a></p>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="border-left col-md-9">
        <?= $this->render("top-section.php", [
            'title' => $model->title,
            'isFavourite' => $uLecture->is_favourite,
            'nextLessonId' => $nextLessonId,
            'uLecture' => $uLecture
        ]) ?>
        <div class="row">
            <div class="col-md-11">
                <?= $model->description ?>
            </div>
            <div class="col-md-1">
                <?php if ($lecturefiles) { ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                            <?= \Yii::t('app', 'Notes');?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg-left">
                            <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]);?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        </hr>

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
        <?php if ($difficulties and $lectureDifficulties and $difficultiesVisible) { ?>
            <?= $this->render('difficulties', ['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>
        <?php } ?>
        <?php if ($evaluations and $lectureEvaluations) {  ?>
            <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations, 'force' => $force]) ?>
        <?php } ?>
        <?php if ($relatedLectures) { ?>
            <?= $this->render('related', ['relatedLectures' => $relatedLectures, 'lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl, 'userEvaluatedLectures' => $userEvaluatedLectures, 'videoThumb' => $videoThumb]) ?>
        <?php } ?>
    </div>
</div>
</div>