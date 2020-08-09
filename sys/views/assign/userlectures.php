<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$subscriptionTypeText;
$subscriptionTypeClassSuffix;
if ($user['subscription_type'] == 'free') {
    $subscriptionTypeText =  "par brīvu";
    $subscriptionTypeClassSuffix = "primary";
} else if ($user['subscription_type'] == 'paid') {
    $subscriptionTypeText =  "par maksu";
    $subscriptionTypeClassSuffix = "success";
} else {
    $subscriptionTypeText = "izmēģina";
    $subscriptionTypeClassSuffix = "warning";
}

$this->title = $user['email'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <?php if ($filterLang) { ?>
        <div class="col-sm-3">Valoda: <?= $filterLang ?></div>
    <?php } ?>
    <?php if ($filterSubType) { ?>
        <div class="col-sm-3">Abonementa veids: <?= $subscriptionTypeText ?></div>
    <?php } ?>
    <div class="col-sm-3">
        <?= $currentUserIndex + 1 ?>/<?= $userCount ?>
    </div>
</div>
<div style="min-height: 50px;">
    <?php
    $prevButtonHref = null;
    $nextButtonHref = null;

    if ($prevUserId) {
        $prevButtonHref = "/assign/userlectures/$prevUserId";
        if ($filterLang) {
            $prevButtonHref .= "?lang=$filterLang";
            if ($filterSubType) {
                $prevButtonHref .= "&subType=$filterSubType";
            }
        } else if ($filterSubType) {
            $prevButtonHref .= "?subType=$filterSubType";
        }
    }

    if ($nextUserId) {
        $nextButtonHref = "/assign/userlectures/$nextUserId";
        if ($filterLang) {
            $nextButtonHref .= "?lang=$filterLang";
            if ($filterSubType) {
                $nextButtonHref .= "&subType=$filterSubType";
            }
        } else if ($filterSubType) {
            $nextButtonHref .= "?subType=$filterSubType";
        }
    }
    ?>
    <?php
    if ($prevUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a('Iepriekšējais', [$prevButtonHref], ['class' => 'btn btn-primary']); ?>
        </span>
    <?php } ?>

    <h1 style="display:inline"><?= $this->title . " (<span class='text-" . $subscriptionTypeClassSuffix . "'>" . $subscriptionTypeText . "</span>)" ?></h1>

    <?php
    if ($nextUserId) { ?>
        <span style="vertical-align:top;">
            <?= Html::a('Nākamais', [$nextButtonHref], ['class' => 'btn btn-primary pull-right']); ?>
        </span>
    <?php } ?>
</div>

<div class="grid-view" id="assign-page-main">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Pēdējā nodarbība</th>
                <th>Atvērta</th>
                <th>Spēles reizes</th>
                <th>Sarežģītība</th>
                <?php foreach ($evaluationsTitles as $et) { ?>
                    <th><?= $et ?></th>
                <?php } ?>
                <th>Spējas</th>
                <?php /*
                <th class="action-column">Darbības</th>
                */ ?>
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
                    <?php /*
                <td align="center">
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"> </span>', 
                    ['/assign/userlectures','id' => $id], 
                    [
                        'title' => 'Apskatīt',                        
                    ]) ?> 
                    <?= Html::a('<span class="glyphicon glyphicon-wrench"> </span>', 
                    ['/cron/userlectures','id' => $id], 
                    [
                        'title' => 'Automātiska piešķiršana',
                        'data' => [
                            'confirm' => 'Are you sure ?',
                        ]
                    ]) ?> 
                </td>
                */ ?>
                </tr>
            <?php $a++;
            }

            ?>
        </tbody>
    </table>
    <!-- <p>Spēles reizes pēdējās 7 dienās: <strong><?= $sevenDayResult ?></strong></p>
    <p>Spēles reizes pēdējās 30 dienās: <strong><?= $thirtyDayResult ?></strong> </p> -->
    <p>Spējas šobrīd:<?= isset($goals[$goalsnow]) ? '<strong>' . $goalsum . '</strong>' : '<code>Not set</code>' ?></p>
    <?php if (is_array($PossibleThreeLectures)) {
        $limit = 3;
    ?>

        <h3>Piemeklētās nodarbības:</h3>
        <?php
        foreach ($PossibleThreeLectures as $lid) {
            if ($limit == 0) break;
            if (!isset($manualLectures[$lid])) continue;
        ?>
            <p><?= Html::a(
                    '<span class="glyphicon glyphicon-plus"></span>' . $manualLectures[$lid],
                    ['/assign/userlectures', 'id' => $id, 'assign' => $lid],
                    [
                        'title' => 'Piešķirt',
                        'data' => [
                            'confirm' => 'Are you sure ?',
                        ]
                    ]
                ) ?>
            </p>

        <?php
            $limit--;
        }
    } else { ?>
        <h3>Jaunā sarežģītības vērtība ir: <strong><?= $PossibleThreeLectures > 0 ? $PossibleThreeLectures : $goalsum ?></strong> <small>[netika atrasts neviens atbilstošs uzdevums]</small></h3>
    <?php } ?>
    <h3>Manuāla nodarbības piešķiršana:</h3>
    <label for="preferred-lecture-difficulty">
        Sarežģītība
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
        ) : ' <p>Nav nodarbību ko piešķirt</p>' ?>
</div>
<?= $manualLectures ? $form->field($model, 'user_id')->hiddenInput(['value' => $id])->label(false) : ''; ?>
<div class="form-group">
    <?= Html::submitButton('Piešķirt nodarbību', ['class' => 'btn btn-success']) ?>
</div>
<?= Yii::$app->session->getFlash('assignmentlog') ?>
<?php ActiveForm::end(); ?>
</div>