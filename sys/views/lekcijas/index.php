<?php

use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (isset($type)) {
    if ($type == "new") {
        $this->title = "Jaunās nodarbības/New lessons";
    } else if ($type == "learning") {
        $this->title = "Nodarbības, ko vēl mācos/Lessons I'm still learning";
    } else if ($type == "favourite") {
        $this->title = "Mīļākās nodarbības/Favourite lessons";
    }
}


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">
    <h3><?= $this->title ?></h3>
    <div class="row">
        <?php
        if (count($models) == 0) { ?>
            <div class="col-md-6">
                <h3>Nav nevienas nodarbības!</h3>
            </div>

        <?php } ?>
        <?php foreach ($models as $model) {
            $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
        ?>
            <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-image: url('<?= trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl])) ?>');"></a>
                <?= $model->title ?>
            </div>
        <?php } ?>
    </div>
    <?php
    if ($pages) {
        // display pagination
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
    }
    ?>
</div>