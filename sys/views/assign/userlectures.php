<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $user['email'] . ' nodarbības';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<div class="grid-view">
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
            <? $a++;
            }

            ?>
        </tbody>
    </table>
    <p>Spēles reizes pēdējās 7 dienās: <strong><?= $sevenDayResult ?></strong></p>
    <p>Spēles reizes pēdējās 30 dienās: <strong><?= $thirtyDayResult ?></strong> </p>
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
    <?php $form = ActiveForm::begin(); ?>
    <?= $manualLectures ? $form->field($model, 'lecture_id')
        ->dropDownList(
            $manualLectures,           // Flat array ('id'=>'label')
            ['prompt' => '']    // options
        ) : ' <p>Nav nodarbību ko piešķirt</p>' ?>
</div>
<?= $manualLectures ? $form->field($model, 'user_id')->hiddenInput(['value' => $id])->label(false) : ''; ?>
<div class="form-group">
    <?= Html::submitButton('Piešķirt nodarbību', ['class' => 'btn btn-success']) ?>
</div>
<?= Yii::$app->session->getFlash('assignmentlog') ?>
<?php ActiveForm::end(); ?>
</div>