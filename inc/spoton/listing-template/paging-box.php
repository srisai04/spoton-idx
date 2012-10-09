<form method="get" id="searchform" class="sepH_a cf">
<div class="soidx-button">
<?php  
    if($recordCount>0)   
    { 
    $page='';
    $page=ceil($recordCount/50);        
    if($page>$recordPage)
        $page=$recordPage;
        
    for($count=1;$count<=$page;$count++)
    {?>
    <input id="searchform" class="btn btn_a" type="submit"  <?php if (!empty($_GET["pg"])) { if($_GET["pg"]==$count) {  ?> <?php  } } ?> name="pg" value='<?php echo $count; ?>'/>
    <?php } 
	}
?></div>
</form>