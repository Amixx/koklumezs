<?php

use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nodarbības/Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">
    <div class="LectureOverview__Section LectureOverview__Section--new">
        <h3><?= \Yii::t('app', 'New lessons') ?></h3>
        <h4 class="LectureOverview__LinkToAll">
            <a><?= Html::a(\Yii::t('app', 'All new lessons'), ['?type=new']) ?></a>
        </h4>
        <?php if (count($newLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                <?= \Yii::t('app', 'Congratulations! You\'ve seen all new lessons') ?>
            </h4>
        <?php } ?>
        <div class="row">
            <?php foreach ($newLectures as $model) {
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
            ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-image: url('<?= trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl])) ?>');"></a>
                    <?= $model->title ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="LectureOverview__Section LectureOverview__Section--favourite">
        <h3> <?= \Yii::t('app',  'Lessons I\'m still learning') ?></h3>
        <h4 class="LectureOverview__LinkToAll">
            <a><?= Html::a(\Yii::t('app', 'All lessons I\'m still learning'), ['?type=learning']) ?></a>
        </h4>
        <?php if (count($stillLearningLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                <?= \Yii::t('app',  'You have not added any lessons to this section yet. You can do this by marking in any lesson that you want to add it to this section.') ?>
            </h4>
        <?php } ?>
        <div class="row">
            <?php foreach ($stillLearningLectures as $model) {
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
            ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-image: url('<?= trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl])) ?>');"></a>
                    <?= $model->title ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="LectureOverview__Section LectureOverview__Section--learning">
        <h3><?= \Yii::t('app',  'Favourite lessons') ?></h3>
        <h4 class="LectureOverview__LinkToAll">
            <a><?= Html::a(\Yii::t('app', 'All favourite lessons'), ['?type=favourite']) ?></a>
        </h4>
        <?php if (count($favouriteLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                <?= \Yii::t('app',  'You have not added any lessons to this section yet. You can do this by marking in any lesson that you want to add it to this section.') ?>
            </h4>
        <?php } ?>
        <div class="row">
            <?php foreach ($favouriteLectures as $model) {
                $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
            ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-image: url('<?= trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl])) ?>');"></a>
                    <?= $model->title ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>