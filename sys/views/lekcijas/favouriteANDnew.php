<?php

use app\models\Lecturesfiles;
use app\models\Lectures;
use app\helpers\ThumbnailHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$sectionClass = "LectureOverview__Section";
if (empty($userLessons) && $teacherPortrait) {
    $sectionClass .= " LectureOverview__Section--empty-with-portrait";
}

?>

<div class="col-md-6 LectureOverview__Wrap">
    <div class="<?= $sectionClass ?>">
        <h4 class="text-center"><?= \Yii::t('app', $divTitle) ?></h4>
        <?php if (empty($userLessons)) {
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
                <?php foreach ($userLessons as $userLesson) {
                    $lecturefiles = Lecturesfiles::getLectureFiles($userLesson->lecture->id);
                    $likesCount = Lectures::getLikesCount($userLesson->lecture->id);
                    $thumbStyle = ThumbnailHelper::getThumbnailStyle($userLesson->lecture->file, $videoThumb);
                ?>
                    <div class="col-xs-6 col-lg-3 text-center lecture-wrap">
                        <a class="lecture-thumb" href="<?= Url::to(['lekcijas/lekcija', 'id' => $userLesson->lecture->id]) ?>" style="<?= $thumbStyle ?>"></a>
                        <span class="lecture-title"><?= $userLesson->lecture->title ?> </span>
                        <?php if ($divTitle === 'New lessons' || $divTitle === 'New workouts' && $isStudent) { ?>
                            <?php $userLessonstatus = Lectures::getLectureStatus($userLesson); ?>
                            <span class="lecture-status <?= $userLessonstatus['class'] ?>">
                                <?= \Yii::t('app', $userLessonstatus['text']); ?>
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