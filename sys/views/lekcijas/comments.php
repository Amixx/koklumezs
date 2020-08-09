<?php

use yii\helpers\Html;
?>

<hr />
<div class="row">
    <div class="col-md-12">
        <h3>Komentāri/Comments</h3>
    </div>
    <?php foreach ($comments as $comment) { ?>
        <div class="col-md-12">
            <div>
                <div class="LessonComments__Container">
                    <span><strong><?= $comment['student']['first_name'] ?></strong>: </span>
                    <span><?= $comment['evaluation'] ?></span>
                </div>
                <?= Html::submitButton('Atbildēt', ['class' => 'btn btn-primary btn-sm ReplyButton', 'id' => 'comment-' . $comment['id']]) ?>
            </div>

        </div>
        <?php if ($comment['responses']) { ?>
            <?php foreach ($comment['responses'] as $response) { ?>
                <div class="col-md-12">
                    <div class="LessonComments__Container LessonComments__Container--response">
                        <span><strong><?= $response['author']['first_name'] ?></strong>: </span>
                        <span><?= $response['text'] ?></span>
                    </div>
                    <?= Html::submitButton('Atbildēt', ['class' => 'btn btn-primary btn-sm ReplyButton', 'id' => 'comment-' . $comment['id']]) ?>
                </div>
            <?php } ?>

        <?php } ?>

        <div class="col-md-12 hidden response-<?= $comment['id'] ?>">
            <?= Html::beginForm(['/comment-responses/create'], 'post') ?>
            <textarea name="response_text" placeholder="Atbilde" class="LessonComments__Response"></textarea>
            <input type="hidden" name="evaluation_id" value="<?= $comment['id'] ?>" />
            <?= Html::submitButton('Nosūtīt', ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php } ?>
</div>