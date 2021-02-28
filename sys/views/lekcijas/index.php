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
        $this->title = \Yii::t('app',  'New lessons');
    } else if ($type == "favourite") {
        $this->title = \Yii::t('app',  'Favourite lessons');
    }
}

?>
<div class="lectures-index">
    <h3><?= $this->title ?></h3>
    <div class="row">
        <div class="col-sm-7">
            <?= Html::a(\Yii::t('app', $sortByDifficultyLabel), '?type='.$type.'&sortByDifficulty='.$sortByDifficulty,['class' => 'btn sort-button']) ?>
        </div>
        <div class="col-sm-5">
            <?= Html::beginForm([''], 'get') ?>
            <?= Html::input('text', 'title_filter', $title_filter) ?>
            <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(\Yii::t('app', 'Show all'), ['/lekcijas/?type=new&sortByDifficulty='.$sortByDifficulty], ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
    <div class="row">
        <?php
        if (count($models) == 0) { ?>
            <div class="col-md-6">
                <h3><?= \Yii::t('app',  'No lessons') ?>!</h3>
            </div>

        <?php } ?>
        <?php foreach ($models as $model) {
            $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
        ?>
            <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="background-color: white; background-image: <?= trim($videoThumb ? 'url(' . $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $videoThumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) . ')' : "") ?>;"></a>
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