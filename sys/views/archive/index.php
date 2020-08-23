<?php

use app\models\Lecturesfiles;
use app\models\UserLectures;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Archive');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">
    <?php
    if ($archive) { ?>
        <hr />
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
            <?php foreach ($archive as $model) {
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
                $userLecture = UserLectures::getUserLectureByLectureId($model->id);
            ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-image: url('<?= trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl])) ?>');"></a>
                    <?= $model->title ?>
                    <?php if ($userLecture->is_favourite) { ?>
                        <div class="icon-favourite"></div>
                    <?php } ?>
                    <?php if ($userLecture->still_learning) { ?>
                        <div class="icon-still-learning"></div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>