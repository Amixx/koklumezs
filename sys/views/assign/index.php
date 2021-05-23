<?php

use yii\helpers\Html;

$empty = '<code>Not set</code>';

$getEvaluations = function ($etid, $id) use ($empty, $evaluations, $evaluationsValues) {
    if (isset($evaluations[$id][$etid])) {
        if (isset($evaluationsValues[$etid]) && isset($evaluationsValues[$etid][(int) $evaluations[$id][$etid]])) {
            return $evaluationsValues[$etid][(int) $evaluations[$id][$etid]];
        } else if (isset($evaluations[$id][$etid])) {
            return $evaluations[$id][$etid];
        }
    }

    return $empty;
};

$this->title = \Yii::t('app',  'Lesson assigning');
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
    <label style="display:inline; margin-right:16px;">
        <input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-free"><?= \Yii::t('app', 'Free') ?>
    </label>
    <label style="display:inline; margin-right:16px;">
        <input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-paid"><?= \Yii::t('app', 'Paid') ?>
    </label>
    <label style="display:inline; margin-right:16px;">
        <input type="checkbox" name="subscription-type-selector" class="subscription-type-selector type-lead"><?= \Yii::t('app', 'Lead') ?>
    </label>
    <input type="hidden" name="subscription-type-selector" class="subscription-type-selector type-pausing">
</div>
<div class="grid-view">
    <table class="table table-striped table-bordered" id="AssignTable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?= \Yii::t('app', 'User') ?></th>
                <th scope="col"><?= \Yii::t('app', 'Last lesson') ?></th>
                <th scope="col"><?= \Yii::t('app', 'Times played') ?></th>
                <th scope="col"><?= \Yii::t('app', 'Difficulty') ?></th>
                <?php foreach ($evaluationsTitles as $et) { ?>
                    <th scope="col"><?= \Yii::t('app', $et) ?></th>
                <?php } ?>
                <th scope="col"><?= \Yii::t('app', 'Abilities') ?></th>
                <th scope="col" class="action-column"><?= \Yii::t('app', 'Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $a = 1;
            foreach ($users as $id => $user) { ?>
                <tr>
                    <td><?= $a ?></td>
                    <td><?= $user['first_name'] ?> <?= $user['last_name'] ?></td>
                    <td><?= isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->title : $empty ?></td>
                    <td class="text-center"><?= isset($lastlectures[$id]) ? $lastlectures[$id]['open_times'] : $empty ?></td>
                    <td class="text-center"><?= isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->complexity : $empty ?></td>
                    <?= $this->render('evaluation-titles', [
                        'evaluationsValues' => $evaluationsValues,
                        'evaluations' => $evaluations,
                        'id' => $id,
                    ]) ?>
                    <td class="text-center"><?= isset($goals[$id][$goalsnow]) ? array_sum($goals[$id][$goalsnow]) : $empty ?></td>
                    <td class="text-center">
                        <span data-userid='<?= $user['id'] ?>' style='width: 41px;' class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>
                            &nbsp;
                        </span>
                    </td>
                    <td class="text-center">
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-user"> </span>',
                            ['/lekcijas/preview', 'studentId' => $id],
                            [
                                'title' => \Yii::t('app', 'View'),
                                'target' => '_blank'
                            ]
                        ) ?>
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