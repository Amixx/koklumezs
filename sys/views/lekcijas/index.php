<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\ThumbnailHelper;

$titleText = '';
if (isset($type)) {
    if ($type == "new") {
        $titleText = 'New lessons';
    } else if ($type == "favourite") {
        $titleText = 'Favourite lessons';
    }
}

$this->title = \Yii::t('app', $titleText);

$sortType = $sortType ?? 'DESC';

?>
<div class="lectures-index ">
    <h3><?= $this->title ?></h3>
    <?php if (count($models) > 1 || isset($title_filter)) { ?>
        <div class="row search-section">
            <div class="col-md-7 col-xs-12">
                <?= Html::beginForm(['/lekcijas/?type=' . $type . '&sortType=' . $sortType], 'get') ?>
                <div class="display-flex">
                    <?= Html::input('text', 'title_filter', $title_filter, ['class' => 'content-input']) ?>
                    <?= Html::submitButton(\Yii::t('app', 'Search'), ['class' => 'btn btn-orange']) ?>
                    <?= Html::a(
                        \Yii::t('app', 'Show all'),
                        '?type=' . $type . '&sortType=' . $sortType . '&title_filter=',
                        ['class' => 'btn btn-orange']
                    ) ?>
                </div>
                <?= Html::endForm() ?>
            </div>
            <div class="col-md-5 col-xs-12">
                <?= Html::label(Yii::t('app', 'Sorting of lessons') . ': ') ?>
                <?= Html::dropDownList("sortType", $sortType, [
                    0 => Yii::t('app', 'From hardest to easiest'),
                    1 => Yii::t('app', 'From easiest to hardest'),
                    2 => Yii::t('app', 'By assignment date'),
                ], [
                    'id' => 'lessons-sorting-select'
                ]) ?>
            </div>
        </div>
    <?php } ?>
    <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px; min-height: 100vh;">
        <?php
        if (count($models) == 0) { ?>
            <div class="col-md-6">
                <h3><?= \Yii::t('app', 'No lessons') ?>!</h3>
            </div>
        <?php } else {
            foreach ($models as $model) {
                $thumbStyle = ThumbnailHelper::getThumbnailStyle($model->lecture->file, $videoThumb); ?>
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a class="lecture-thumb"
                       href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->lecture->id]) ?>"
                       style="<?= $thumbStyle ?>"
                    ></a>
                    <?= $model->lecture->title ?>
                </div>
            <?php }
        } ?>
    </div>
    <?php
    if ($pages) {
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
    }
    ?>
</div>