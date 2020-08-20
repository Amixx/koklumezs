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
        <h3>Jaunās nodarbības/New lessons</h3>
        <?php if (count($newLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                Apsveicu! Visas jaunās nodarbības esi apskatījis!
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
        <h3>Nodarbības, ko vēl mācos/Lessons I'm still learning</h3>
        <?php if (count($stillLearningLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                Šajā sadaļā neesi pievienojis vel nevienu uzdevumu. To vari izdarīt, atzīmējot jebkurā nodarbībā, ka vēlies pievienot šai sadaļai.
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
        <h3>Mīļākās nodarbības/Favourite lessons</h3>
        <?php if (count($favouriteLectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                Šajā sadaļā neesi pievienojis vēl nevienu uzdevumu. To vari izdarīt, atzīmējot jebkurā nodarbībā, ka vēlies pievienot šai sadaļai.
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