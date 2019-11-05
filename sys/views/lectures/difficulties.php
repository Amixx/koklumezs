<h2>Parametri</h2>      
<?php      
foreach($difficulties as $id => $name){  ?>
<div class="form-group field-studentgoals">
    <label class="control-label"for="difficulties-title<?=$id?>"><?=$name?></label>
    <select id="difficulties-title<?=$id?>" class="form-control" name="difficulties[<?=$id?>]" aria-required="true" aria-invalid="false">
        <option value=""></option>
        <?php for($a=1;$a<=10;$a++){ ?>
        <option value="<?=$a?>" <?=(isset($lectureDifficulties[$id]) AND ($lectureDifficulties[$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
        <?php } ?>
    </select>
    <div class="help-block"></div>
</div>                       
<?php } ?>    