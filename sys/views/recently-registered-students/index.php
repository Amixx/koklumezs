<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Students who have registered but have not started playing yet');
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create parameter'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php foreach ($sections as $section) { ?>
        <h3><?= $section['title'] ?></h3>
        <table class="table">
            <tbody>
                <tr>
                    <th scope="col">Vārds</th>
                    <th scope="col">Uzvārds</th>
                    <th scope="col">E-pasts</th>
                    <?php if (isset($section['dateColText'])) { ?>
                        <th scope="col"><?= $section['dateColText'] ?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($section['users'] as $user) { ?>
                    <tr>
                        <td><?= $user['first_name'] ?></td>
                        <td><?= $user['last_name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <?php if (isset($user['date'])) { ?>
                            <td><?= $user['date'] ?></td>
                        <?php } ?>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>


</div>