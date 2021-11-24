<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'School registration emails');

?>
<div class="settings-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create email'), ['/school-registration-emails/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php if ($emails) { ?>
        <table class="table table-striped table-bordered">
            <?php foreach ($emails as $type => $email) { ?>
                <tr>
                    <th scope="row"><?= $email['label'] ?></th>
                    <td><?= $email['value'] ? $email['value']  : '<em><strong>Nav ievadīts<strong></em>' ?></td>
                    <td>
                        <?= Html::a(Yii::t('app', $email['value']  ? 'Edit' : 'Create'), ['update', 'type' => $type], ['class' => 'btn btn-primary']) ?>
                        <?= $email['value'] ? Html::a(Yii::t('app', 'Delete'), ['delete', 'type' => $type], ['class' => 'btn btn-danger', 'style' => 'margin-top: 16px']) : '' ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <h3><?= Yii::t('app', 'No e-mails entered') ?></h3>
    <?php } ?>
</div>