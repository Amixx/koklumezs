<?php

use yii\helpers\Html;
use yii\helpers\Url;

$empty = '<code>Not set</code>';

$this->title = \Yii::t('app', $isFitnessSchool ? 'Workout assigning' : 'Lesson assigning');
?>
<h1><?= $this->title ?></h1>
<?php if (!$isFitnessSchool) { ?>
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
            <input type="checkbox" name="subscription-type-selector"
                   class="subscription-type-selector type-free"><?= \Yii::t('app', 'Free') ?>
        </label>
        <label style="display:inline; margin-right:16px;">
            <input type="checkbox" name="subscription-type-selector"
                   class="subscription-type-selector type-paid"><?= \Yii::t('app', 'Paid') ?>
        </label>
        <label style="display:inline; margin-right:16px;">
            <input type="checkbox" name="subscription-type-selector"
                   class="subscription-type-selector type-lead"><?= \Yii::t('app', 'Lead') ?>
        </label>
        <input type="hidden" name="subscription-type-selector" class="subscription-type-selector type-pausing">
    </div>
<?php } ?>
<div class="grid-view">
    <table class="table table-striped table-bordered" id="AssignTable">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col" class="action-column"><?= \Yii::t('app', 'Actions') ?></th>
            <th scope="col"><?= \Yii::t('app', 'User') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Last lesson') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Times played') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Difficulty') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Evaluation') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Abilities') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php $a = 1;
        foreach ($users as $id => $user) { ?>
            <tr>
                <td><?= $a ?></td>
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
                        $isFitnessSchool ? ['/fitness-workouts/index', 'studentId' => $id] : ['/assign/userlectures', 'id' => $id],
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
                    <?php if ($isFitnessSchool) {
                        echo Html::a(
                            '<span class="glyphicon glyphicon-stats"> </span>',
                            Url::to(['client-data/update', 'userId' => $id]),
                            [
                                'title' => \Yii::t('app', 'Edit client data'),
                            ]
                        );
                    } ?>
                </td>
                <td><?= $user['first_name'] ?> <?= $user['last_name'] ?></td>
                <?php if (isset($lastlectures[$id])) { ?>
                    <td><?= $lastlectures[$id]->lecture->title ?></td>
                    <td class="text-center"><?= $lastlectures[$id]['open_times'] ?></td>
                    <td class="text-center"><?= $lastlectures[$id]->lecture->complexity ?></td>
                    <td class="text-center">
                        <?= isset($evaluations[$id][$lastlectures[$id]->lecture_id])
                            ? $evaluations[$id][$lastlectures[$id]->lecture_id]
                            : $empty; ?>
                    </td>
                <?php } else { ?>
                    <td><?= $empty ?></td>
                    <td><?= $empty ?></td>
                    <td><?= $empty ?></td>
                    <td><?= $empty ?></td>
                <?php } ?>
                <td class="text-center"><?= isset($goals[$id][$goalsnow]) ? array_sum($goals[$id][$goalsnow]) : $empty ?></td>
                <td class="text-center">
                        <span data-userid='<?= $user['id'] ?>' style='width: 41px;'
                              class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>
                            &nbsp;
                        </span>
                </td>
                <td style="display:none" class="user-language"><?= $user['language'] ?></td>
                <td style="display:none" class="user-subscription-type"><?= $user['subscription_type'] ?></td>
            </tr>
            <?php $a++;
        } ?>
        </tbody>
    </table>
</div>