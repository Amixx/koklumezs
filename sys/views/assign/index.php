<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = \Yii::t('app',  'Lesson assigning');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<p style="display:inline-block">
    <?= Html::a(
        \Yii::t('app', 'Manually invoke automatic assignment for all students'),
        ['/cron', 'send' => 1],
        [
            'class' => 'btn btn-success',
            'target' => '_blank',
            'data' => [
                'confirm' => \Yii::t('app', 'Are you sure?'),
            ]
        ]
    ) ?>
</p>
<div style="display:inline-block">
    <label for="user-language-selector">
        <?= \Yii::t('app', 'Language') ?>:
        <select name="user-language-selector" id="UserLanguageSelector">
            <option value="all" selected><?= \Yii::t('app', 'All') ?></option>
            <option value="lv"><?= \Yii::t('app', 'Latvian') ?></option>
            <option value="eng"><?= \Yii::t('app', 'English') ?></option>
        </select>
    </label>
</div>
<div style="display:inline-block">
        <?= \Yii::t('app', 'Abonement types') ?>:
        <label style="display:inline; margin-right:16px;"><input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-free"><?= \Yii::t('app', 'Free') ?></label>
        <label style="display:inline; margin-right:16px;"><input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-paid"><?= \Yii::t('app', 'Paid') ?></label>
        <label style="display:inline; margin-right:16px;"><input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-lead"><?= \Yii::t('app', 'Lead') ?></label>
        <!-- noņemts, kamēr nav sataisīta jaunā paužu sistēma -->
        <!-- <label style="display:inline; margin-right:16px; color:darkgrey;"><input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-pausing"><?= \Yii::t('app', 'Show paused') ?></label> -->
        <input type="hidden" name="subscription-type-selector" class="subscription-type-selector type-pausing">
</div>
<div class="grid-view">
    <table class="table table-striped table-bordered" id="AssignTable">
        <thead>
            <tr>
                <th>#</th>
                <th><?= \Yii::t('app', 'User') ?></th>
                <th><?= \Yii::t('app', 'Last lesson') ?></th>
                <th><?= \Yii::t('app', 'Times played') ?></th>
                <th><?= \Yii::t('app', 'Difficulty') ?></th>
                <?php foreach ($evaluationsTitles as $et) { ?>
                    <th><?= \Yii::t('app', $et) ?></th>
                <?php } ?>
                <th><?= \Yii::t('app', 'Abilities') ?></th>
                <th class="action-column"><?= \Yii::t('app', 'Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $a = 1;
            foreach ($users as $id => $user) { ?>
                <tr>
                    <td><?= $a ?></td>
                    <td><?= $user['first_name'] ?> <?= $user['last_name'] ?></td>
                    <td><?= isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->title : '<code>Not set</code>' ?></td>
                    <td align="center"><?= isset($lastlectures[$id]) ? $lastlectures[$id]['open_times'] : '<code>Not set</code>' ?></td>
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
                        <span data-userid='<?= $user['id'] ?>' style='width: 41px;' class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>&nbsp;</span>
                    </td>
                    <td align="center">
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            ['/assign/userlectures', 'id' => $id],
                            [
                                'title' => \Yii::t('app', 'View'),
                            ]
                        ) ?>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-wrench"> </span>',
                            ['/cron/userlectures', 'id' => $id],
                            [
                                'title' => \Yii::t('app', 'Automatic assignment'),
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Are you sure?'),
                                ]
                            ]
                        ) ?>
                    </td>
                    <td style="display:none" class="user-language"><?= $user['language'] ?></td>
                    <td style="display:none" class="user-subscription-type"><?= $user['subscription_type'] ?></td>
                    <td style="display:none" class="user-status"><?= $user['status'] ?></td>
                </tr>
            <?php $a++;
            } ?>
        </tbody>

    </table>
</div>