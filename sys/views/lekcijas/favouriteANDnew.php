<?php
use app\models\Lecturesfiles;
use app\models\Lectures;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="col-md-6 LectureOverview__Wrap">
    <div class="LectureOverview__Section">
        <h3><?= \Yii::t('app', $divTitle) ?></h3>
        <?php if (count($Lectures) == 0) { ?>
            <h4 class="LectureOverview__EmptyText">
                <?= \Yii::t('app', $emptyText) ?>
            </h4>
        <?php } ?>
        <div class="row">
            <?php foreach ($Lectures as $lecture) {
                $lecturefiles = Lecturesfiles::getLectureFiles($lecture->id);
                $likesCount = Lectures::getLikesCount($lecture->id);
                $backgroundImage = trim(
                    $videoThumb
                        ? 'url(' . $this->render(
                            'video_thumb',
                            ['lecturefiles' => [0 => ['file' => $lecture->file, 'thumb' => $videoThumb]],
                            'videos' => $videos,
                            'baseUrl' => $baseUrl]) . ')'
                        : "");?>                              
                <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                    <a
                        class="lecture-thumb"
                        href="<?= Url::to(['lekcijas/lekcija', 'id' => $lecture->id]) ?>"
                        style="background-color: white; background-image: <?= $backgroundImage ?>;"
                    ></a>
                    <span class="lecture-title"><?= $lecture->title ?> </span>
                    <?php if($likesCount) { ?>
                        <span class="lecturelikes">
                            <span class="glyphicon glyphicon-heart lecturelikes-icon"></span>
                            <span class="lecturelikes-count"><?= $likesCount ?></span>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="row text-center">
            <?= Html::a(\Yii::t('app', $clickableTitle), ['?type='.$type], ['class' => 'btn btn-gray']) ?>
        </div>
    </div>
</div>