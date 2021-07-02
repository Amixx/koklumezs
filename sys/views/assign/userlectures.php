<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$empty = '<code>Not set</code>';

$subscriptionTypeText;
$subscriptionTypeClassSuffix;
if ($user['subscription_type'] == 'free') {
    $subscriptionTypeText =  \Yii::t('app', 'Free');
    $subscriptionTypeClassSuffix = "primary";
} else if ($user['subscription_type'] == 'paid') {
    $subscriptionTypeText =  \Yii::t('app', 'Paid');
    $subscriptionTypeClassSuffix = "success";
} else {
    $subscriptionTypeText = \Yii::t('app', 'Lead');
    $subscriptionTypeClassSuffix = "warning";
}

$this->title = $user['first_name'] . ' ' . $user['last_name'];
?>
<div class="row">
    <?php if ($filterLang) { ?>
        <div class="col-sm-3"><?= \Yii::t('app', 'Language') ?>: <?= $filterLang ?></div>
    <?php } ?>
</div>
<div style="min-height: 50px;">
    <?php
    $prevButtonHref = null;
    $nextButtonHref = null;

    if ($prevUserId) {
        $prevButtonHref = "/assign/userlectures/$prevUserId";
        if ($filterLang) {
            $prevButtonHref .= "?lang=$filterLang";
            if ($filterSubTypes) {
                $prevButtonHref .= "&subTypes=$filterSubTypes";
            }
        } else if ($filterSubTypes) {
            $prevButtonHref .= "?subTypes=$filterSubTypes";
        }
    }

    if ($nextUserId) {
        $nextButtonHref = "/assign/userlectures/$nextUserId";
        if ($filterLang) {
            $nextButtonHref .= "?lang=$filterLang";
            if ($filterSubTypes) {
                $nextButtonHref .= "&subTypes=$filterSubTypes";
            }
        } else if ($filterSubTypes) {
            $nextButtonHref .= "?subTypes=$filterSubTypes";
        }
    }
    ?>
    <?php
    if ($prevUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a(\Yii::t('app', 'Previous'), [$prevButtonHref], ['class' => 'btn btn-orange']); ?>
        </span>
    <?php } ?>

    <h1 style="display:inline">
        <span>
            (<?= $currentUserIndex + 1 ?>/<?= $userCount ?>)
        </span>
        <?= $this->title . " (<span class='text-" . $subscriptionTypeClassSuffix . "'>" . $subscriptionTypeText . "</span>)" ?>
    </h1>

    <?php
    if ($nextUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a(\Yii::t('app', 'Next'), [$nextButtonHref], ['class' => 'btn btn-orange pull-right']); ?>
        </span>
    <?php } ?>
</div>

<div class="grid-view" id="assign-page-main">
    <div class="TableContainer" style="max-height:500px; overflow-y:scroll">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= \Yii::t('app', 'Last lesson') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Date of assignment') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Opened') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Times played') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Difficulty') ?></th>
                    <?php foreach ($evaluationsTitles as $et) { ?>
                        <th scope="col"><?= \Yii::t('app', $et) ?></th>
                    <?php } ?>
                    <th scope="col"><?= \Yii::t('app', 'Abilities') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $a = 1;
                foreach ($lastlectures as $lecture) { ?>
                    <tr>
                        <td><?= $a ?></td>
                        <td>
                            <?= $lecture->lecture->title ?>
                            <?php if ($lecture->is_favourite) { ?>
                                <span class="glyphicon glyphicon-heart"></span>
                            <?php } ?>
                            <?php if ($lecture->assigned == $lecture->user_id) { ?>
                                <span class="glyphicon glyphicon-asterisk"></span>
                            <?php } ?>
                        </td>
                        <td class="text-center"><?= $lecture->created ?></td>
                        <td class="text-center"><?= (int) $lecture->opened ? 'Jā' : 'Nē' ?></td>
                        <td class="text-center"><?= $lecture->open_times ?></td>
                        <td class="text-center"><?= $lecture->lecture->complexity ? $lecture->lecture->complexity : $empty ?></td>
                        <?= $this->render('evaluation-titles', [
                            'evaluationsTitles' => $evaluationsTitles,
                            'evaluationsValues' => $evaluationsValues,
                            'evaluations' => $evaluations,
                            'id' => $lecture->lecture_id,
                        ]) ?>
                        <td class="text-center"><?= isset($lecture->user_difficulty) ? $lecture->user_difficulty : $empty ?></td>
                        <td><?= Html::a(
                                '<span>Dzēst</span>',
                                ['/user-lectures/delete', 'id' => $lecture->id],
                                [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure?'),
                                        'pjax' => 0,
                                        'method' => 'post'
                                    ]
                                ]
                            ) ?> </td>
                    </tr>
                <?php $a++;
                } ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($user) && $user->wants_more_lessons) { ?>
        <h3 style="color: red;"><?= \Yii::t('app', 'User dosen\'t have enough lessons') ?>! </h3>
    <?php } ?>
    <p>
        <?= Html::a(
            \Yii::t('app', 'View students lesson view') . ' <span class="glyphicon glyphicon-user"></span>',
            ['/lekcijas/preview', 'studentId' => $id],
            [
                'title' => \Yii::t('app', 'View'),
                'target' => '_blank'
            ]
        ) ?>
    </p>
    <?php if ($trialEnded) { ?>
        <h3 style="color: red"><?= Yii::t('app', 'Student\'s trial has ended') ?>!</h3>
    <?php } ?>
    <?php if (isset($user) && $user->about) { ?>
        <p><?= \Yii::t('app', 'About user') ?>: <strong><?= $user->about ?></strong>.</p>
    <?php } ?>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['seven'], 7]); ?>.</p>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['thirty'], 30]); ?>.</p>
    <?php if ($firstOpenTime !== null) { ?>
        <p><?= \Yii::t('app', 'First lesson opened') ?>: <?= $firstOpenTime ?>.</p>
    <?php } else { ?>
        <p><?= \Yii::t('app', 'User has not opened any lessons yet') ?>!</p>
    <?php } ?>
    <p><?= \Yii::t('app', 'Abilities now') ?>:<?= isset($goals[$goalsnow]) ? '<strong>' . $goalsum . '</strong>' : $empty ?></p>
    <p><?= \Yii::t('app', 'Lesson plan end date') ?>: <?= $endDate == null ? \Yii::t('app', 'no plan assigned to pupil') : $endDate  ?></p>
    <?php if ($isNextLessons) { ?>
        <p> <?= Yii::t('app', 'After completing all lesosns, student can assign themself') . ' -' ?></p>
        <?php if (isset($nextLessons['easy'])) { ?>
            <p><?= Yii::t('app', 'Easier') . ': ' . $nextLessons['easy']->title; ?> (<?= $nextLessons['easy']->complexity; ?>)</p>
        <?php } ?>
        <?php if (isset($nextLessons['medium'])) { ?>
            <p> <?= Yii::t('app', 'Just as complicated') . ': ' . $nextLessons['medium']->title; ?> (<?= $nextLessons['medium']->complexity; ?>)</p>
        <?php } ?>
        <?php if (isset($nextLessons['hard'])) { ?>
            <p> <?= Yii::t('app', 'Challenge') . ': ' . $nextLessons['hard']->title; ?> (<?= $nextLessons['hard']->complexity; ?>)</p>
        <?php } ?>
    <?php } else { ?>
        <p> <?= Yii::t('app', 'The student is not active or there is no lesson student can assign themself') ?>.</p>
    <?php } ?>

    <?php if ($user->wants_more_lessons) { ?>
        <h4><strong><?= Yii::t('app', 'Student wants more lessons') ?>!</strong></h4>
    <?php } ?>

    <?php if (is_array($PossibleThreeLectures)) {
        $limit = 3;
    ?>
        <h3><?= \Yii::t('app', 'Suitable lessons') ?>:</h3>
        <?php foreach ($PossibleThreeLectures as $lid) {
            if ($limit == 0) {
                break;
            }
            if (!isset($manualLectures[$lid])) {
                continue;
            } ?>
            <p>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-plus"></span>' . $manualLectures[$lid],
                    ['/assign/userlectures', 'id' => $id, 'assign' => $lid],
                    [
                        'title' => \Yii::t('app', 'Assign'),
                        'data' => [
                            'confirm' => \Yii::t('app', 'Are you sure?'),
                        ]
                    ]
                ) ?>
            </p>

        <?php
            $limit--;
        }
    } else { ?>
        <h3><?= \Yii::t('app', 'New difficulty') ?>:
            <strong>
                <?= $PossibleThreeLectures > 0 ? $PossibleThreeLectures : $goalsum ?>
            </strong>
            <small>[<?= \Yii::t('app', 'No suitable lessons found') ?>]</small>
        </h3>
    <?php } ?>
    <h3><?= \Yii::t('app', 'Manual assignment of lessons') ?>:</h3>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $lectureTexts = array_map(function ($lecture) {
        return $lecture['title'] . " (" . $lecture['complexity'] . ")";
    }, $manualLectures);
    ?>

    <!-- noņemu pagaidām, jo nav sataisīts backends -->
    <!-- <div class="row">
        <?php
        $noOfTasks = 4;
        for ($x = 0; $x < $noOfTasks; $x++) { ?>
            <div class="col-md-3">
                <?= $manualLectures ? $form->field($model, "lecture_id[$x]")
                    ->dropDownList(
                        $lectureTexts,
                        ['prompt' => '']
                    ) : "<p>" . \Yii::t('app', 'No lessons to assign') . "</p>" ?>
                
            </div>
        <?php } ?>
    </div> -->

    <?= $manualLectures ? $form->field($model, 'lecture_id')
        ->dropDownList(
            $lectureTexts,
            ['prompt' => '']
        ) : "<p>" . \Yii::t('app', 'No lessons to assign') . "</p>" ?>



    <label for="sendEmail"><?= Yii::t('app', 'Send message to student') ?>
        <input type="checkbox" name="sendEmail">
    </label>
    <label for="subject"><?= Yii::t('app', 'Subject') ?>
        <input type="text" name="subject" class="form-control">
    </label>
    <label for="teacherMessage"><?= Yii::t('app', 'Message for student') ?></label>
    <textarea name="teacherMessage" style="width: 100%" rows="5"></textarea>
</div>
<?= $manualLectures ? $form->field($model, 'user_id')->hiddenInput(['value' => $id])->label(false) : ''; ?>
<div class="form-group">
    <?= Html::submitButton(\Yii::t('app', 'Assign lesson'), ['class' => 'btn btn-success']) ?>
</div>
<?= Yii::$app->session->getFlash('assignmentlog') ?>
<?php ActiveForm::end(); ?>
</div>