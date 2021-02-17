<?php
use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="col-md-6 LectureOverview__Section">
    <h3><?= \Yii::t('app', $divTitle) ?></h3>
    <h4 class="LectureOverview__LinkToAll">
        <a><?= Html::a(\Yii::t('app', $clickableTitle), ['?type=new']) ?></a>
    </h4>
    <?php if (count($Lectures) == 0) { ?>
        <h4 class="LectureOverview__EmptyText">
            <?= \Yii::t('app', $emptyText) ?>
        </h4>
    <?php } ?>
    <div class="row">
        <?php foreach ($Lectures as $model) {
            $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
            $backgroundImage = trim(
                $videoThumb
                    ? 'url(' . $this->render(
                        'video_thumb',
                        ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $videoThumb]],
                        'videos' => $videos,
                        'baseUrl' => $baseUrl]) . ')'
                    : "");?>                              
            <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                <a
                    class="lecture-thumb"
                    href="<?= Url::to(['lekcijas/lekcija', 'id' => $model->id]) ?>"
                    style="background-color: white; background-image: <?= $backgroundImage ?>;"
                ></a>
                <span class="lecture-title"><?= $model->title ?> </span>
            </div>
        <?php } ?>
    </div>
</div>