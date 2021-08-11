<?php

use yii\helpers\Html;

switch ($evaluation) {
    case 2:
        $title = \Yii::t('app', 'Super easy, boring');
        break;
    case 4:
        $title = \Yii::t('app', 'Easy');
        break;
    case 6:
        $title = \Yii::t('app', 'Goal');
        break;
    case 8:
        $title = \Yii::t('app', 'Hard');
        break;
    case 10:
        $title = \Yii::t('app', 'Challenging');
        break;
    default:
        $title = '';
}
?>

<div>
    <hr>
    <h2><?= $title ?></h2>
    <?php if (!count($messages) > 0) { ?>
        <h3><?= \Yii::t('app', 'No messages') ?></h3>
    <?php } else { ?>
        <table class="table">
            <tr>
                <th><?= \Yii::t('app', 'Message') ?></th>
                <th class="text-center" style="width: 160px;"><?= \Yii::t('app', 'Actions') ?></th>
            </tr>

            <?php foreach ($messages as $message) { ?>
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