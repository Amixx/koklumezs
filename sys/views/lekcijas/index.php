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

$toggledSortByDifficulty = 'desc';

if (isset($sortByDifficulty)) {
    if ($sortByDifficulty == 'desc') {$toggledSortByDifficulty = 'asc';}
    else if ($sortByDifficulty == 'asc') {$toggledSortByDifficulty = 'desc';}
}

if (isset($sortByDifficulty)) {
    if ($sortByDifficulty == 'desc') {
        $sortByDifficultyLabel = 'From hardest to easiest';
    } else {
        $sortByDifficultyLabel = 'From easiest to hardest';    
    }
} 

?>
<div class="lectures-index ">
    <h3><?= $this->title ?></h3>
    <?php if (count($models) > 1 || isset($title_filter)) { ?>
        <div class="row" style="margin-top: 10px; margin-bottom: 25px;">
            <?= Html::beginForm(['/lekcijas/?type='.$type.'&sortByDifficulty='.$sortByDifficulty], 'get') ?>
            <?= Html::input('text', 'title_filter', $title_filter, ['class' => 'content-input']) ?>
            <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange']) ?>                <?= Html::input('hidden', 'sortByDifficulty', $toggledSortByDifficulty) ?>
            <?= Html::a(\Yii::t('app', $sortByDifficultyLabel), '?type='.$type.'&sortByDifficulty='.$sortByDifficulty.'&title_filter='.$title_filter,['class' => 'btn btn-gray sort-button']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php } ?>
    <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px;">
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