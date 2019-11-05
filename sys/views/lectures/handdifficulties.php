<h2>Roku kategorijas</h2>      
<?php      
if($handdifficulties['left']){ ?>
<hr />
<h3>Kreisās rokas kategorijas</h3>      
<?php foreach($handdifficulties['left'] as $id => $name){ ?>
    <div class="form-group field-handdifficulties-title custom-control custom-checkbox mr-sm-2">
        <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="handdifficulties[<?=$id?>]" <?=isset($lectureHandDifficulties[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
        <label class="custom-control-label" for="handdifficulties-title-<?=$id?>"><?=$name?></label>
        <div class="help-block"></div> 
    </div>                  
<?php }
}
if($handdifficulties['right']){ ?>
    <h3>Labās rokas kategorijas</h3>    
    <hr />  
    <?php
    foreach($handdifficulties['right'] as $id => $name){ ?>
    <div class="form-group field-handdifficulties-title custom-control custom-checkbox mr-sm-2">
        <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="handdifficulties[<?=$id?>]" <?=isset($lectureHandDifficulties[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
        <label class="custom-control-label" for="handdifficulties-title-<?=$id?>"><?=$name?></label>
        <div class="help-block"></div> 
    </div>         
<?php }
} ?>      