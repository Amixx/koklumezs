<?php

use app\models\Lecturesfiles;

if ($relatedLectures) {
?>
    <h4><?= \Yii::t('app',  'Previous assignments in this lesson') ?></h4>
    <div class="lectures-related">
            <?php foreach ($relatedLectures as $model) {
                // if (in_array($model->id, $userEvaluatedLectures)) continue;
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
                $backgroundImage = trim(
                    $videoThumb
                        ? 'url(' . $this->render('video_thumb', [
                            'lecturefiles' => [
                                0 => [
                                    'file' => $model->file,
                                    'thumb' => $videoThumb
                                    ]
                                ],
                                'videos' => $videos,
                                'baseUrl' => $baseUrl]) . ')'
                        : "");
            ?>
                <div class="text-center lecture-wrap lecture-wrap-related">
                     <div class="text-center lecture-wrap lecture-wrap-related">
                        <a class="lecture-thumb" data-toggle="modal" data-target="#lecture-modal-<?= $model->id ?>" style="background-color: white; background-image: <?= $backgroundImage ?>;"></a>
                    </div>
                    <?= $model->title ?>
                    
                </div>
                <?= $this->render('view-lesson-modal', [
                    'baseUrl' => $baseUrl,
                    'videoThumb' => $videoThumb,
                    'videos' => $videos,
                    'lecturefiles' => [0 => ['title' => $model->title, 'file' => $model->file]],
                    'id' => $model->id,
                ]) ?>
            <?php } ?>
    </div>
<?php } ?>