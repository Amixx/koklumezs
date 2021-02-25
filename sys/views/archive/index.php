<?php

use app\models\Lecturesfiles;
use app\models\UserLectures;
use app\models\Lectures;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = \Yii::t('app', 'Archive');
?>
<div class="lectures-index">
    <?php
    if ($archive) { ?>
        <h3><?= \Yii::t('app', 'Archive') ?></h3>
        <div class="col-sm-6">
            <?= Html::beginForm([''], 'get') ?>
            <?= Html::input('text', 'archive_filter', $archive_filter) ?>
            <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(\Yii::t('app', 'Show all'), [''], ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>
        <div class="col-sm-6">
            <label for="only_favourites"> <input type="checkbox" name="only_favourites" id="only_favourites"><?= \Yii::t('app', 'Select only favourite lessons') ?></label>
            <label for="only_still_learning"><input type="checkbox" name="only_still_learning" id="only_still_learning"><?= \Yii::t('app', 'Select only lessons I\'m still learning') ?></label>
        </div>
        <div class="row">
            <?php foreach ($archive as $lecture) {
                $lecturefiles = Lecturesfiles::getLectureFiles($lecture->id);
                $userLecture = UserLectures::getUserLectureByLectureId($lecture->id);
                $likesCount = Lectures::getLikesCount($lecture->id);
            ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $lecture->id]) ?>" style="background-color: white; background-image: <?= trim($videoThumb ? 'url(' . $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $lecture->file, 'thumb' => $videoThumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) . ')' : "") ?>;"></a>
                    <?= $lecture->title ?>
                    <?php if ($userLecture->is_favourite) { ?>
                        <div class="icon-favourite"></div>
                    <?php } ?>
                    <?php if ($userLecture->still_learning) { ?>
                        <div class="icon-still-learning"></div>
                    <?php } ?>
                    <?php if($likesCount) { ?>
                        <span class="lecturelikes lecturelikes-archive">
                            <span class="glyphicon glyphicon-heart lecturelikes-icon"></span>
                            <span class="lecturelikes-count"><?= $likesCount ?></span>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>