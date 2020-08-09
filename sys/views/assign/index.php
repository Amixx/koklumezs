<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Nodarbību piešķiršana';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<p style="display:inline-block">
    <?= Html::a(
        'Manuāli izsaukt automātisko nodarbību piešķiršanu visiem studentiem',
        ['/cron', 'send' => 1],
        [
            'class' => 'btn btn-success',
            'target' => '_blank',
            'data' => [
                'confirm' => 'Are you sure ?',
            ]
        ]
    ) ?>
</p>
<div style="display:inline-block">
    <label for="user-language-selector">
        Valoda:
        <select name="user-language-selector" id="UserLanguageSelector">
            <option value="all" selected>Visas</option>
            <option value="lv">Latviešu</option>
            <option value="eng">Angļu</option>
        </select>
    </label>
</div>
<div style="display:inline-block">
    <label for="user-subscription-type-selector">
        Abonēšanas veidi:
        <select name="user-subscription-type-selector" id="UserSubscriptionTypeSelector">
            <option value="all" selected>Visi</option>
            <option value="free">Par brīvu</option>
            <option value="paid">Par maksu</option>
            <option value="lead">Izmēģina</option>
        </select>
    </label>
</div>
<div class="grid-view">
    <table class="table table-striped table-bordered" id="AssignTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Lietotājs</th>
                <th>Pēdējā nodarbība</th>
                <th>Spēles reizes</th>
                <th>Sarežģītība</th>
                <?php foreach ($evaluationsTitles as $et) { ?>
                    <th><?= $et ?></th>
                <?php } ?>
                <th>Spējas</th>
                <th class="action-column">Darbības</th>
            </tr>
        </thead>
        <tbody>
            <?php $a = 1;
            foreach ($users as $id => $user) { ?>
                <tr>
                    <td><?= $a ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->title : '<code>Not set</code>' ?></td>
                    <td align="center"><?= $lastlectures[$id]['open_times'] ?></td>
                    <td align="center"><?= isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->complexity : '<code>Not set</code>' ?></td>
                    <?php foreach ($evaluationsTitles as $etid => $et) { ?>
                        <td align="center">
                            <?php if (isset($evaluations[$id][$etid])) {
                                echo isset($evaluationsValues[$etid]) ? (isset($evaluationsValues[$etid][(int) $evaluations[$id][$etid]]) ? $evaluationsValues[$etid][(int) $evaluations[$id][$etid]] : '<code>Not set</code>') : (isset($evaluations[$id][$etid]) ? $evaluations[$id][$etid] : '<code>Not set</code>');
                            } else {
                                echo '<code>Not set</code>';
                            }  ?>
                        </td>
                    <?php } ?>
                    <td align="center"><?= isset($goals[$id][$goalsnow]) ? array_sum($goals[$id][$goalsnow]) : '<code>Not set</code>' ?></td>
                    <td align="center">
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            ['/assign/userlectures', 'id' => $id],
                            [
                                'title' => 'Apskatīt',
                            ]
                        ) ?>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-wrench"> </span>',
                            ['/cron/userlectures', 'id' => $id],
                            [
                                'title' => 'Automātiska piešķiršana',
                                'data' => [
                                    'confirm' => 'Are you sure ?',
                                ]
                            ]
                        ) ?>
                    </td>
                    <td style="display:none" class="user-language"><?= $user['language'] ?></td>
                    <td style="display:none" class="user-subscription-type"><?= $user['subscription_type'] ?></td>
                </tr>
            <?php $a++;
            } ?>
        </tbody>

    </table>
</div>