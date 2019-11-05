<hr />
<?php      
if($handdifficulties['left']){ ?>
<div class="row">  
    <div class="col-md-12"> 
        <h3>Kreisās rokas kategorijas</h3>                       
    <ul>     
    <?php foreach($handdifficulties['left'] as $id => $name){ 
        $continue = !isset($lectureHandDifficulties[$id]);
        if($continue){
            continue;
        }
        ?>
        <li>
            <?=$name?>
        </li>            
    <?php } ?>
    </ul>
    </div>
</div>
<?php }
if($handdifficulties['right']){ ?>                    
<div class="row">
    <div class="col-md-12">
        <h3>Labās rokas kategorijas</h3>                      
    <ul>  
    <?php
    foreach($handdifficulties['right'] as $id => $name){ 
        $continue = !isset($lectureHandDifficulties[$id]);
        if($continue){
            continue;
        }
        ?>
        <li>
            <?=$name?>
        </li>      
    <?php } ?>
    </ul>
    </div>
</div>  
<?php } ?> 