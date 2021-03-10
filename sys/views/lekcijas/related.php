<?php

use app\models\Lecturesfiles;
use app\helpers\ThumbnailHelper;

if ($relatedLectures) {
if(!isset($modalIdPrefix)) $modalIdPrefix = "";
?>
    <h4 class="hidden-xs"><?= \Yii::t('app',  'Previous assignments in this lesson') ?></h4>
    <div class="lectures-related">
            <?php foreach ($relatedLectures as $model) {
                // if (in_array($model->id, $userEvaluatedLectures)) continue;
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
                $thumbStyle = ThumbnailHelper::getThumbnailStyle($model->file, $videoThumb);
            ?>
                <div class="text-center lecture-wrap lecture-wrap-related">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lecture-modal-<?= $modalIdPrefix ?><?= $model->id ?>" style="<?= $thumbStyle ?>"></a>
                    <span class="lecture-title"><?= $model->title ?></span>                    
                </div>
                <?= $this->render('view-lesson-modal', [
                    'videoThumb' => $videoThumb,
                    'lecturefiles' => [0 => ['title' => $model->title, 'file' => $model->file]],
                    'id' => $modalIdPrefix . $model->id,
                ]) ?>
            <?php } ?>
    </div>
<?php } ?>