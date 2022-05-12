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
                    'newLessons' => $newLessons,
                    'favouriteLessons' => $favouriteLessons,
                    'sortType' => $sortType,
                    'currentLessonEvaluated' => $uLecture && $uLecture->evaluated,
                    'isFitnessSchool' => $isFitnessSchool,
                ]) ?>
            </div>
        </div>
        <div class="border-left col-md-7">
            <div class="lesson-column lesson-column-middle wrap-overlay">
                <?php if ($uLecture) { ?>
                    <?= $this->render("top-section.php", [
                        'title' => $model->title,
                        'nextLessonId' => $nextLessonId,
                        'uLecture' => $uLecture,
                        'lectureEvaluations' => $lectureEvaluations,
                        'force' => $force,
                        'hasEvaluatedLesson' => $hasEvaluatedLesson,
                        'difficultyEvaluation' => $difficultyEvaluation,
                        'lecturefiles' => $lecturefiles['docs'],
                        'showChangeTaskButton' => $showChangeTaskButton,
                    ]) ?>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?= $model->description ?>
                    </div>
                    <?php if ($uLecture && $uLecture->weight) { ?>
                        <div class="col-md-12" style="font-size: 16px; font-weight: bold; margin-left: 8px; margin-top: 16px;">
                            <?= Yii::t('app', 'Weight') ?>: <?= $uLecture->weight ?> (kg)
                        </div>
                    <?php } ?>

                </div>

                <?php if (!empty($equipmentVideos)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class='text-center'><?= Yii::t('app', 'How to use the equipment for next exercises') ?></h3>
                                <ul class='equipment-video'>
                                    <?php foreach ($equipmentVideos as $key => $video) { ?>
                                        <?= $this->render(
                                            'video',
                                            [
                                                'lectureVideoFiles' => [0 => ['title' => Yii::t('app', 'How to use equipment'), 'file' => $video]],
                                                'thumbnail' => $videoThumb ?? '',
                                                'idPrefix' => 'equipment-vid-' . $key,
                                            ]
                                        ); ?>
                                    <?php } ?>
                                </ul>
                        </div>
                    </div>
                <?php } ?>


                <?php if ($model->file) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'lectureVideoFiles' => [0 => ['title' => $model->title, 'file' => $model->file]],
                            'thumbnail' => $videoThumb ?? '',
                            'idPrefix' => 'main',
                        ]
                    ); ?>
                <?php } ?>
                <?php if ($model->file && $userCanDownloadFiles && SectionsVisible::isVisible("Video lejupielādes poga")) { ?>
                    <a href="<?= $model->file ?> " target="_blank" download><?= \Yii::t('app',  'Download lesson video file') ?></a>
                <?php } ?>
                <?php if ($lecturefiles) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'lectureVideoFiles' => $lecturefiles['video'],
                            'thumbnail' => $videoThumb ?? '',
                            'idPrefix' => 'file',
                        ]
                    ); ?>
                    <?= $this->render(
                        'audio',
                        ['lectureAudioFiles' => $lecturefiles['audio']]
                    ); ?>
                <?php } ?>
                <?php if ($difficulties && $lectureDifficulties && $difficultiesVisible) { ?>
                    <?= $this->render('difficulties', ['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>
                <?php } ?>

                <?php if ($model->play_along_file || ($relatedLectures && !empty($relatedLectures))) {
                    echo $this->render('mob-related-section', [
                        'model' => $model,
                        'relatedLectures' => $relatedLectures,
                        'lecturefiles' => $lecturefiles,
                        'userEvaluatedLectures' => $userEvaluatedLectures,
                        'videoThumb' => $videoThumb,
                        'isFitnessSchool' => $isFitnessSchool,
                    ]);
                } ?>
            </div>
        </div>
    </div>
    <?php if ($model->play_along_file || ($relatedLectures && !empty($relatedLectures))) { ?>
        <div class="col-md-3 hidden-xs">
            <div class="lesson-column lesson-column-right wrap-overlay">
                <?= $this->render("right-section.php", [
                    'relatedLectures' => $relatedLectures,
                    'lecturefiles' => $lecturefiles['video'],
                    'videoThumb' => $videoThumb,
                    'model' => $model,
                    'userEvaluatedLectures' => $userEvaluatedLectures,
                    'isFitnessSchool' => $isFitnessSchool,
                ]) ?>
            </div>
        </div>
    <?php } ?>
</div>
</div>

<script>
    var isRegisteredAndNewLesson = <?= $isRegisteredAndNewLesson ? "true" : "false" ?>;
</script>