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
        <span><?= $this->title ?></span>
        <span>
            <span data-userid='<?= $user['id'] ?>' style='width: 41px;' class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>
                &nbsp;
            </span>
        </span>
        <span class='text-<?= $subscriptionTypeClassSuffix ?>'>(<?= $subscriptionTypeText ?>)</span>
    </h1>

    <?php
    if ($nextUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a(\Yii::t('app', 'Next'), [$nextButtonHref], ['class' => 'btn btn-orange pull-right']); ?>
        </span>
    <?php } ?>
</div>

<div class="grid-view" id="assign-page-main">
    <div class="TableContainer" style="max-height: 158px; overflow-y:scroll">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= \Yii::t('app', 'Last lesson') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Date') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Opened') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Times') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Difficulty') ?></th>
                    <th scope="col"><?= \Yii::t('app', 'Evaluation') ?></th>
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
                        <td class="text-center" style="white-space:nowrap"><?= date_format(new \DateTime($lecture->created), "Y-m-d") ?></td>
                        <td class="text-center"><?= (int) $lecture->opened ? 'Jā' : 'Nē' ?></td>
                        <td class="text-center"><?= $lecture->open_times ?></td>
                        <td class="text-center">
                            <strong><?= $lecture->lecture->complexity ? $lecture->lecture->complexity : $empty ?></strong>
                        </td>
                        <td class="text-center">
                            <?= isset($evaluations[$lecture->lecture_id]) ? $evaluations[$lecture->lecture_id] : $empty; ?>
                        </td>
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
        <p><?= \Yii::t('app', 'About user') ?>: <strong><?= $user->about ?></strong></p>
    <?php } ?>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['seven'], 7]); ?>.</p>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['thirty'], 30]); ?>.</p>
    <?php if ($firstEvaluationDate !== null) { ?>
        <p><?= \Yii::t('app', 'First lesson evaluated') ?>: <?= $firstEvaluationDate ?>.</p>
    <?php } else { ?>
        <p><?= \Yii::t('app', 'User has not evaluated any lessons yet') ?>!</p>
    <?php } ?>

    <?php if ($user->wants_more_lessons) { ?>
        <h4><strong><?= Yii::t('app', 'Student wants more lessons') ?>!</strong></h4>
    <?php } ?>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $lectureTexts = array_map(function ($lecture) {
        return $lecture['title'] . " (" . $lecture['complexity'] . ")";
    }, $manualLectures);
    ?>

    <?= $manualLectures ? $form->field($model, 'lecture_id')
        ->dropDownList(
            $lectureTexts,
            ['prompt' => '']
        ) : "<p>" . \Yii::t('app', 'No lessons to assign') . "</p>" ?>

    <label for="sendEmail"><?= Yii::t('app', 'Send message to student') ?>
        <input type="checkbox" name="sendEmail">
    </label>
    <label for="saveEmail" style="display: none;"><?= Yii::t('app', 'Save message as automatic') ?>
        <input type="checkbox" name="saveEmail">
    </label>
    <label for="updateEmail" style="display: none;"><?= Yii::t('app', 'Update automatic message texts') ?>
        <input type="checkbox" name="updateEmail">
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
<?php ActiveForm::end(); ?>
</div>

<script>
    window.manualLectures = <?= json_encode($manualLectures); ?>;
</script>