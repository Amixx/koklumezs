<?php

use yii\helpers\Html;
?>

<div>
    <hr>
    <h2><?= $title ?></h2>
    <?php if (!count($messages[$evaluation]) > 0) { ?>
        <h3><?= \Yii::t('app', 'No messages') ?></h3>
    <?php } else { ?>
        <table class="table">
            <tr>
                <th><?= \Yii::t('app', 'Message') ?></th>
                <th class="text-center" style="width: 160px;"><?= \Yii::t('app', 'Actions') ?></th>
            </tr>

            <?php foreach ($messages[$evaluation] as $message) { ?>
                <tr>
                    <td>
                        <p><?= $message->message; ?>
                    </td>
                    <td class="text-right">
                        <?= Html::a(\Yii::t('app',  'Edit'), ['update', 'id' => $message->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(\Yii::t('app', 'Delete'), ['delete', 'id' => $message->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => \Yii::t('app', 'Do you really want to delete this entry?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
                </tr>
            <?php } ?>
        </table>


    <?php  } ?>
    <?= Html::a(\Yii::t('app', 'Add new message'), ['/school-after-evaluation-messages/create', 'evaluation' => $evaluation], ['class' => 'btn btn-success']) ?>

</div>