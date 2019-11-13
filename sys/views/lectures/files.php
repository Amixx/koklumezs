<?php
use Yii;
?>
<h2>Pievienotie faili</h2>
<hr />
<p>
    <a target="_blank" class="btn btn-success" href="<?=$link?>">Pievienot failu</a>
</p>       
<?php if($lecturefiles){  ?>        
    <table class="table table-striped table-bordered">
    <?php foreach($lecturefiles as $id => $file){ 
        $view = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/create', 'id' => $file['id']]);
        $up = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/update', 'id' => $file['id']]);
        $del = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/delete', 'id' => $file['id']]);
        ?>
        <tr> 
            <td><?=$file['title']?></td>       
            <td>
                <a target="_blank" href="<?=$view?>" title="Skatīt" aria-label="Skatīt" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a> 
                <a target="_blank" href="<?=$up?>" title="Rediģēt" aria-label="Rediģēt" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a> 
                <a href="<?= $del?>" title="Dzēst" aria-label="Dzēst" data-pjax="0" data-confirm="Vai Jūs tiešām vēlaties dzēst šo failu?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>            
    <?php } ?>
    </table>
<?php } ?>