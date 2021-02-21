<?php

use app\models\Lecturesfiles;

if ($relatedLectures) {
?>
    <h4><?= \Yii::t('app',  'Previous assignments in this lesson') ?></h4>
    <div class="lectures-related">
            <?php foreach ($relatedLectures as $model) {
                if (in_array($model->id, $userEvaluatedLectures)) continue;
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
                    <a class="lecture-thumb" style="background-color: white; background-image: <?= $backgroundImage ?>;"></a>
                    <?= $model->title ?>
                </div>
            <?php } ?>
    </div>
<?php } ?>