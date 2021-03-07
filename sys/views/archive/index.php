<?php

use app\models\Lecturesfiles;
use app\models\UserLectures;
use app\models\Lectures;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = \Yii::t('app', 'Archive');
?>
<div class="lectures-index">
    <div class="row search-section">
        <div class="col-md-5 col-xs-12" style="padding: 0;">      
            <?= Html::beginForm([''], 'get') ?>
            <?= Html::input('text', 'archive_filter', $archive_filter, ['class' => 'content-input']) ?>
            <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange search-button']) ?>
            <?= Html::endForm() ?>
        </div> 
        <div class="row col-md-5 col-xs-12" style="padding: 0;">
            <a href="/lekcijas/?type=favourite&sortByDifficulty=asc" class="btn btn-gray sort-button"><?= \Yii::t('app', 'Open all favourite lessons') ?></a>
        </div>
    </div>
    <?php
    if ($archive) { ?>
        <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px; min-height: 100vh;">
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