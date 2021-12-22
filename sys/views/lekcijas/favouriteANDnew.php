<?php

use app\models\Lecturesfiles;
use app\models\Lectures;
use app\helpers\ThumbnailHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$sectionClass = "LectureOverview__Section";
if (empty($lectures) && $teacherPortrait) {
    $sectionClass .= " LectureOverview__Section--empty-with-portrait";
}

?>

<div class="col-md-6 LectureOverview__Wrap">
    <div class="<?= $sectionClass ?>">
        <h4 class="text-center"><?= \Yii::t('app', $divTitle) ?></h4>
        <?php if (empty($Lectures)) {
            $userId = Yii::$app->user->identity->id; ?>
            <h4 class="LectureOverview__EmptyText">
                <?= \Yii::t('app', $emptyText) ?>
            </h4>
            <?php if ($type == 'new' && $isNextLesson && $isActive) { ?>
                <div class="row text-center">
                    <div>
                        <?php if ($teacherPortrait) { ?>
                            <div class="more-lessons-teacher-portrait" style="background-image: url(<?= $teacherPortrait ?>)"></div>
                        <?php } ?>
                        <button type="button" class="btn btn-orange more-lessons-button" data-toggle="modal" data-target="#moreLessons">
                            <?= \Yii::t('app', 'Hey! click here if you want another task!'); ?>
                        </button>
                    </div>

                    <?= $this->render('moreLessonsModal', [
                        'nextLessons' => $nextLessons,
                    ]) ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="row LectureOverview__Content">
                <?php foreach ($Lectures as $lecture) {
                    $lecturefiles = Lecturesfiles::getLectureFiles($lecture->id);
                    $likesCount = Lectures::getLikesCount($lecture->id);
                    $thumbStyle = ThumbnailHelper::getThumbnailStyle($lecture->file, $videoThumb);
                ?>
                    <div class="col-xs-6 col-lg-3 text-center lecture-wrap">
                        <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $lecture->id]) ?>" style="<?= $thumbStyle ?>"></a>
                        <span class="lecture-title"><?= $lecture->title ?> </span>
                        <?php if ($divTitle === 'New lessons' && $isStudent) { ?>
                            <?php $lectureStatus = Lectures::getLectureStatus($lecture->id); ?>
                            <span class="lecture-status <?= $lectureStatus['class'] ?>">
                                <?= \Yii::t('app', $lectureStatus['text']); ?>
                            </span>
                        <?php } ?>
                        <?php if ($likesCount) { ?>
                            <span class="lecturelikes">
                                <span class="glyphicon glyphicon-heart lecturelikes-icon"></span>
                                <span class="lecturelikes-count"><?= $likesCount ?></span>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div class="row text-center LectureOverview__ButtonRow">
                <?= Html::a(\Yii::t('app', $clickableTitle), ['?type=' . $type], ['class' => 'btn btn-gray btn-long']) ?>
            </div>
        <?php } ?>
    </div>
</div>