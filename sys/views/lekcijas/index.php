<?php

use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\ThumbnailHelper;

if (isset($type)) {
    if ($type == "new") {
        $this->title = \Yii::t('app',  'New lessons');
    } else if ($type == "favourite") {
        $this->title = \Yii::t('app',  'Favourite lessons');
    }
}

$toggledSortByDifficulty = 'desc';

if (isset($sortByDifficulty)) {
    if ($sortByDifficulty == 'desc') {
        $toggledSortByDifficulty = 'asc';
        $sortByDifficultyLabel = 'From hardest to easiest';
    } else if ($sortByDifficulty == 'asc') {     
        $toggledSortByDifficulty = 'desc';
        $sortByDifficultyLabel = 'From easiest to hardest';
    }
}

?>
<div class="lectures-index ">
    <h3><?= $this->title ?></h3>
    <?php if (count($models) > 1 || isset($title_filter)) { ?>
        <div class="row search-section">
            <div class="col-md-7 col-xs-12">  
                <?= Html::beginForm(['/lekcijas/?type='.$type.'&sortByDifficulty='.$sortByDifficulty], 'get') ?>
                <div class="display-flex">
                    <?= Html::input('text', 'title_filter', $title_filter, ['class' => 'content-input']) ?>
                    <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange']) ?>                
                    <?= Html::a(\Yii::t('app', 'Show all'), '?type='.$type.'&sortByDifficulty='.$toggledSortByDifficulty.'&title_filter=', ['class' => 'btn btn-orange']) ?>
                </div>
                 <?= Html::endForm() ?>
            </div>
            <div class="col-md-5 col-xs-12" > 
                <?= Html::input('hidden', 'sortByDifficulty', $toggledSortByDifficulty) ?>
                <?= Html::a(\Yii::t('app', $sortByDifficultyLabel), '?type='.$type.'&sortByDifficulty='.$sortByDifficulty.'&title_filter='.$title_filter,['class' => 'btn btn-gray sort-button']) ?>
            </div>
        </div>
    <?php } ?>
    <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px; min-height: 100vh;">
        <?php
        if (count($models) == 0) { ?>
            <div class="col-md-6">
                <h3><?= \Yii::t('app',  'No lessons') ?>!</h3>
            </div>

        <?php } ?>
        <?php foreach ($models as $model) {
            $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
            $thumbStyle = ThumbnailHelper::getThumbnailStyle($model->file, $videoThumb);
        ?>
            <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>" style="<?= $thumbStyle ?>"></a>
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