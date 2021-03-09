<?php

use app\models\SectionsVisible;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $model->title;

?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="top-and-left-section">
        <div class="col-md-2 lesson-column-outer">
            <div class="lesson-column wrap-overlay">
                <?= $this->render("left-section", [
                    'userLectures' => $userLectures,
                    'newLessons' => $newLessons,
                    'favouriteLessons' => $favouriteLessons,
                    'sortByDifficulty' => $sortByDifficulty,
                    'currentLessonEvaluated' => $uLecture->evaluated,
                ]) ?>
            </div>
        </div>
        <div class="border-left col-md-7">
            <div class="lesson-column lesson-column-middle wrap-overlay">
                <?php if($uLecture){ ?>
                    <?= $this->render("top-section.php", [
                        'title' => $model->title,
                        'nextLessonId' => $nextLessonId,
                        'uLecture' => $uLecture,
                        'lectureEvaluations' => $lectureEvaluations,
                        'force' => $force,
                        'hasEvaluatedLesson' => $hasEvaluatedLesson,
                        'difficultyEvaluation' => $difficultyEvaluation,
                        'lecturefiles' => $lecturefiles,
                        'docs' => $docs,
                    ]) ?>
                <?php } ?>           

                <div class="row">
                    <div class="col-md-12">
                        <?= $model->description ?>
                    </div>
                </div>
                
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
                    [
                        'lecturefiles' => $lecturefiles,
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

                <?php
                $backgroundImage = trim(
                    $videoThumb
                        ? 'url(' . $this->render('video_thumb', [
                            'lecturefiles' => [
                                0 => [
                                    'file' => $model->play_along_file,
                                    'thumb' => $videoThumb
                                    ]
                                ],
                                'videos' => $videos,
                                'baseUrl' => $baseUrl]) . ')'
                        : ""
                );
                ?>

                <div class="visible-xs">
                    <div style="display: inline-block; width:49%; vertical-align: top; margin-top:8px">
                        <?php if ($lecturefiles) { ?>
                            <button type="button" class="btn btn-orange dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                                <?= \Yii::t('app', 'Lyrics and notes');?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg-left">
                                <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]);?>
                            </div> 
                        <?php } ?>
                        <p style="color:black; margin: 60px 0 0 6px"><?= Yii::t('app', 'Previous assignments in this lesson') ?></p>
                    </div>
                    
                    <div style="display: inline-block; width:49%;">
                        <?php if($model->play_along_file){ ?>
                            <h4 class="lecture-play-along-title-mobile">Spēlēsim kopā</h4>
                            <div>
                                <div class="lecture-wrap">
                                    <a class="lecture-thumb" data-toggle="modal" data-target="#lecture-modal-<?= $model->id ?>" style="background-color: white; background-image: <?= $backgroundImage ?>;"></a>
                                </div>
                                <?= $this->render('view-lesson-modal', [
                                    'baseUrl' => $baseUrl,
                                    'videoThumb' => $videoThumb,
                                    'videos' => $videos,
                                    'lecturefiles' => [0 => ['title' => $model->title . " izspēle", 'file' => $model->play_along_file]],
                                    'id' => $model->id,
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($relatedLectures) { ?>
                        <?= $this->render('related', [
                            'relatedLectures' => $relatedLectures,
                            'lecturefiles' => $lecturefiles,
                            'videos' => $videos,
                            'baseUrl' => $baseUrl,
                            'userEvaluatedLectures' => $userEvaluatedLectures,
                            'videoThumb' => $videoThumb
                        ])?>
                    <?php } ?>
                </div>
            </div>        
        </div>
    </div>
    <?php if($model->play_along_file || ($relatedLectures && !empty($relatedLectures))) { ?>
        <div class="col-md-3 hidden-xs">
            <div class="lesson-column lesson-column-right wrap-overlay">
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
    <?php } ?>    
</div>
</div>