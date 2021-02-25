<?php

use app\models\SectionsVisible;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $model->title;

?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="col-md-2">
        <div class="lesson-column">
            <?= $this->render("left-section", [
                'userLectures' => $userLectures,
                'sortByDifficulty' => $sortByDifficulty,
                'sortByDifficultyLabel' => $sortByDifficultyLabel,
            ]) ?>
        </div>
    </div>
    <div class="border-left col-md-7">
        <div class="lesson-column lesson-column-middle">
            <?php if($uLecture){ ?>
                 <?= $this->render("top-section.php", [
                    'title' => $model->title,
                    'nextLessonId' => $nextLessonId,
                    'uLecture' => $uLecture,
                    'lectureEvaluations' => $lectureEvaluations,
                    'force' => $force,
                    'hasEvaluatedLesson' => $hasEvaluatedLesson,
                    'difficultyEvaluation' => $difficultyEvaluation,
                ]) ?>
            <?php } ?>           

            <?= $this->render("main-content", [
                'description' => $model->description,
                'lecturefiles' => $lecturefiles,
            ]) ?>
            
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
        </div>        
    </div>
    <div class="col-md-3">
        <div class="lesson-column lesson-column-right">
            <?= $this->render("right-section.php", [
                'relatedLectures' => $relatedLectures,
                'lecturefiles' => $lecturefiles,
                'playAlongFile' => $model->play_along_file,
                'videos' => $videos,
                'baseUrl' => $baseUrl,
                'videoThumb' => $videoThumb,
                'model' => $model,
                'userEvaluatedLectures' => $userEvaluatedLectures,
            ]) ?>
        </div>            
    </div>    
</div>
</div>