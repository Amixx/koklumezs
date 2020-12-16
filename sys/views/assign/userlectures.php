<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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

$this->title = $user['email'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <?php if ($filterLang) { ?>
        <div class="col-sm-3"><?= \Yii::t('app', 'Language') ?>: <?= $filterLang ?></div>
    <?php } ?>
    <!-- <?php if ($filterSubTypes) { ?>
        <div class="col-sm-3">Abonementa veids/i: <?= $subscriptionTypeText ?></div>
    <?php } ?> -->
    <!-- <div class="col-sm-3">
        <?= $currentUserIndex + 1 ?>/<?= $userCount ?>
    </div> -->
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
            <?= Html::a(\Yii::t('app', 'Previous'), [$prevButtonHref], ['class' => 'btn btn-primary']); ?>
        </span>
    <?php } ?>

    <h1 style="display:inline"><span>(<?= $currentUserIndex + 1 ?>/<?= $userCount ?>)</span> <?= $this->title . " (<span class='text-" . $subscriptionTypeClassSuffix . "'>" . $subscriptionTypeText . "</span>)" ?></h1>

    <?php
    if ($nextUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a(\Yii::t('app', 'Next'), [$nextButtonHref], ['class' => 'btn btn-primary pull-right']); ?>
        </span>
    <?php } ?>
</div>

<div class="grid-view" id="assign-page-main">
    <div class="TableContainer" style="max-height:500px; overflow-y:scroll">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?= \Yii::t('app', 'Last lesson') ?></th>
                    <th><?= \Yii::t('app', 'Opened') ?></th>
                    <th><?= \Yii::t('app', 'Times played') ?></th>
                    <th><?= \Yii::t('app', 'Difficulty') ?></th>
                    <?php foreach ($evaluationsTitles as $et) { ?>
                        <th><?= \Yii::t('app', $et) ?></th>
                    <?php } ?>
                    <th><?= \Yii::t('app', 'Abilities') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $a = 1;
                foreach ($lastlectures as $lecture) { ?>
                    <tr>
                        <td><?= $a ?></td>
                        <td><?= $lecture->lecture->title ?></td>
                        <td align="center"><?= (int) $lecture->opened ? 'Jā' : 'Nē' ?></td>
                        <td align="center"><?= $lecture->open_times ?></td>
                        <td align="center"><?= $lecture->lecture->complexity ? $lecture->lecture->complexity : '<code>Not set</code>' ?></td>
                        <?php foreach ($evaluationsTitles as $etid => $et) { ?>
                            <td align="center">
                                <?php if (isset($evaluations[$lecture->lecture_id][$etid])) {
                                    echo isset($evaluationsValues[$etid]) ? (isset($evaluationsValues[$etid][$evaluations[$lecture->lecture_id][$etid]]) ? $evaluationsValues[$etid][$evaluations[$lecture->lecture_id][$etid]] : '<code>Not set</code>') : (isset($evaluations[$lecture->lecture_id][$etid]) ? $evaluations[$lecture->lecture_id][$etid] : '<code>Not set</code>');
                                } else {
                                    echo '<code>Not set</code>';
                                }  ?>
                            </td>
                        <?php } ?>
                        <td align="center"><?= isset($lecture->user_difficulty) ? $lecture->user_difficulty : '<code>Not set</code>' ?></td>
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
    <?php if (isset($user) && $user->about) { ?>
        <p>Par lietotāju: <b><?= $user->about ?></b>.</p>
    <?php } ?>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['seven'], 7]); ?>.</p>
    <p><?= \Yii::t('app', 'User has viewed lessons {0} times in the last {1} days', [$openTimes['thirty'], 30]); ?>.</p>
    <?php if ($firstOpenTime !== null) { ?>
        <p><?= \Yii::t('app', 'First lesson opened') ?>: <?= $firstOpenTime ?>.</p>
    <?php } else { ?>
        <p><?= \Yii::t('app', 'User has not opened any lessons yet') ?>!</p>
    <?php } ?>
    <p><?= \Yii::t('app', 'Abilities now') ?>:<?= isset($goals[$goalsnow]) ? '<strong>' . $goalsum . '</strong>' : '<code>Not set</code>' ?></p>
    <?php if (is_array($PossibleThreeLectures)) {
        $limit = 3;
    ?>

        <h3><?= \Yii::t('app', 'Suitable lessons') ?>:</h3>
        <?php
        foreach ($PossibleThreeLectures as $lid) {
            if ($limit == 0) break;
            if (!isset($manualLectures[$lid])) continue;
        ?>
            <p><?= Html::a(
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
        <h3><?= \Yii::t('app', 'New difficulty') ?>: <strong><?= $PossibleThreeLectures > 0 ? $PossibleThreeLectures : $goalsum ?></strong> <small>[<?= \Yii::t('app', 'No suitable lessons found') ?>]</small></h3>
    <?php } ?>
    <h3><?= \Yii::t('app', 'Manual assignment of lessons') ?>:</h3>
    <label for="preferred-lecture-difficulty">
        <?= \Yii::t('app', 'Difficulty') ?>
        <input type="number" name="preferred-lecture-difficulty" id="PreferredLectureDifficulty">
    </label>

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