<div id="so_search-form-wrapper" class="so_search-form">
<!-- Begin Form -->
<form method="post" action="<?php echo home_url('?la_action=list'); ?>">
<?php wp_nonce_field('idx_listing_alerts_add', '_lanonce'); ?>
<table style="width: 790px; overflow-x: scroll; margin: 0 10px;">
    <tr>
        <td style="width:250px"><h4>Location</h4></td>
        <td style="width:250px"><h4>Price Range</h4></td>
        <td style="width:250px"><h4>Features</h4></td>
        <td style="width:250px"><h4>Property Type</h4></td>
    </tr>
    <tr>
        <td>
                <label for="spto_state">State</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="spto_state" id="spto_state" style="width:140px">
			    </select>
        </td>
        <td>
            <label for="spto_pricefrom">Price From</label>&nbsp;&nbsp;
            <select style="width:140px" id="spto_pricefrom" name="spto_pricefrom">
		    <option value='0' <?php if (!empty($_POST["spto_pricefrom"])) { if($_POST["spto_pricefrom"]==0) { print 'selected'; } } ?>>No Minimum</option>
                <?php 
                $pricefrominitial = 10000;
                for($pricefrom=50000;$pricefrom<=2000000;$pricefrom=$pricefrom+$pricefrominitial)
                { 
                if($pricefrom>=100000)
                {
                    $pricefrominitial=25000;
                }
                if (!empty($_POST["spto_pricefrom"])) {
                if($_POST["spto_pricefrom"]==$pricefrom)
                    echo "<option value='$pricefrom' selected>".'$'.str_replace('.00','',number_format($pricefrom, 2, '.', ','))."</option>";
                else
                    echo "<option value='$pricefrom'>".'$'.str_replace('.00','',number_format($pricefrom, 2, '.', ','))."</option>";
                }else {
                    echo "<option value='$pricefrom'>".'$'.str_replace('.00','',number_format($pricefrom, 2, '.', ','))."</option>";
                }} ?>
            </select>
        </td>
        <td>
            <label for="spto_bedrooms">Bedrooms</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <select style="width:140px" id="spto_bedrooms" name="spto_bedrooms">
            <option value='0' <?php if (!empty($_POST["spto_bedrooms"])) { if($_POST["spto_bedrooms"]==0) { print 'selected'; } } ?>>Any Number</option>
            <?php 
            for($bedrooms=1;$bedrooms<=10;$bedrooms++)
            { 
            if (!empty($_POST["spto_bedrooms"])) {
              if($_POST["spto_bedrooms"]==$bedrooms)
                echo "<option value='$bedrooms' selected>at least $bedrooms</option>";
              else
                echo "<option value='$bedrooms'>at least $bedrooms</option>";
            } else {
            echo "<option value='$bedrooms'>at least $bedrooms</option>"; }}?>
            </select>
        </td>
        <td rowspan="6">
            <table>
<?php echo spoton_get_provider_types(); ?>
			</table>
        </td>
    </tr>
    <tr>
        <td>
            <label for="spto_county">County</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <select style="width:140px" name="spto_county" id="spto_county">
				<option value='0'>All</option>           
            </select>
        </td>
        <td>
            <label for="spto_priceto">Price To</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select style="width:140px" id="spto_priceto" name="spto_priceto">
            <option value='99999999' <?php if (!empty($_POST["spto_priceto"])) { if($_POST["spto_priceto"]==99999999) { print 'selected'; } }?>>No Maximum</option>
            <?php 
            $pricetoinitial = 10000;
            for($priceto=50000;$priceto<=2000000;$priceto=$priceto+$pricetoinitial)
            { 
            if($priceto>=100000)
            {
                $pricetoinitial=25000;
            }
            if (!empty($_POST["spto_priceto"])) {
            if($_POST["spto_priceto"]==$priceto)
                echo "<option value='$priceto' selected>".'$'.str_replace('.00','',number_format($priceto, 2, '.', ','))."</option>";
            else
                echo "<option value='$priceto'>".'$'.str_replace('.00','',number_format($priceto, 2, '.', ','))."</option>";
            } else {
             echo "<option value='$priceto'>".'$'.str_replace('.00','',number_format($priceto, 2, '.', ','))."</option>"; }}
             ?>
            </select>
        </td>
        <td>
            <label for="spto_bathrooms">Bathrooms</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select style="width:140px" name="spto_bathrooms" id="spto_bathrooms">
            <option value='0' <?php if (!empty($_POST["spto_bathrooms"])) { if($_POST["spto_bathrooms"]==0) { print 'selected'; } } ?>>Any Number</option>
            <?php 
            for($bathrooms=1;$bathrooms<=10;$bathrooms++)
            { 
            if (!empty($_POST["spto_bathrooms"])) {
              if($_POST["spto_bathrooms"]==$bathrooms)
                echo "<option value='$bathrooms' selected>at least $bathrooms</option>";
              else
                echo "<option value='$bathrooms'>at least $bathrooms</option>";
            } else {
            echo "<option value='$bathrooms'>at least $bathrooms</option>"; }}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="spto_city">City</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select style="width:140px" name="spto_city" id="spto_city">
            <option value='0'>All</option>
            </select> 
        </td>
        <td>          
        </td>
        <td>
            <label for="spto_squarefeet">Square Feet</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select style="width:140px" id="spto_squarefeet" name="spto_squarefeet">
            <option value='0' <?php if (!empty($_POST["spto_squarefeet"])) { if($_POST["spto_squarefeet"]==0) { print 'selected'; } } ?>>Any Size</option>
            <?php 
            for($squarefeet=500;$squarefeet<=7500;$squarefeet=$squarefeet+500)
            { 
            if (!empty($_POST["spto_squarefeet"])) {
              if($_POST["spto_squarefeet"]==$squarefeet)
                echo "<option value='$squarefeet' selected>$squarefeet+</option>";
              else
                echo "<option value='$squarefeet'>$squarefeet+</option>";
            } else {
            echo "<option value='$squarefeet'>$squarefeet+</option>"; }}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="spto_zipcode">Zip Code</label>&nbsp;&nbsp;
            <input style="width:140px" type="text" id="spto_zipcode" name="spto_zipcode" class="regular-text code"/>
        </td>
        <td>            
        </td>
        <td>
            <label for="spto_acreagefrom" >Acreage From</label>&nbsp;&nbsp;
            <select style="width:140px" id="spto_acreagefrom" name="spto_acreagefrom">
            <option value='0' <?php if (!empty($_POST["spto_acreagefrom"])) { if($_POST["spto_acreagefrom"]==0) { print 'selected'; } } ?>>No Minimum</option>
            <?php 
            $acreagefromInitial = 0.25;
            for($acreagefrom=0.25;$acreagefrom<=20;$acreagefrom=$acreagefrom+$acreagefromInitial)
            { 
            if($acreagefrom>=1)
            {
                $acreagefromInitial=1;
            }
            if (!empty($_POST["spto_acreagefrom"])) {
            if($_POST["spto_acreagefrom"]==$acreagefrom)
                echo "<option value='$acreagefrom' selected>$acreagefrom</option>";
            else
                echo "<option value='$acreagefrom'>$acreagefrom</option>";
            } else {
            echo "<option value='$acreagefrom'>$acreagefrom</option>"; }}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>
            <label for="spto_acreageto">Acreage To</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select style="width:140px" id="spto_acreageto" name="spto_acreageto">
            <option value='999999' <?php if (!empty($_POST["spto_acreageto"])) { if($_POST["spto_acreageto"]==0) { print 'selected'; } } ?>>No Maximum</option>

            <?php 
            $acreagetoInitial= 0.25;
            for($acreageto=0.25;$acreageto<=20;$acreageto=$acreageto+$acreagetoInitial)
            {
            if($acreageto>=1)
            {
                $acreagetoInitial=1;
            }
            if (!empty($_POST["spto_acreageto"])) {
            if($_POST["spto_acreageto"]==$acreageto)
                echo "<option value='$acreageto' selected>$acreageto</option>";
            else
                echo "<option value='$acreageto'>$acreageto</option>";
            } else {
            echo "<option value='$acreageto'>$acreageto</option>"; }}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><br/></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><h4><label for="spto_market">New on Market</label></h4></td>
        <td><h4>Show Only</h4></td>
        <td><h4>Property Status</h4></td>
    </tr>
        <tr>
        <td>            
            <select style="width:140px" id="spto_market" name="spto_market">
            <option value='0' <?php if (!empty($_POST["spto_market"])) { if($_POST["spto_market"]==0) { print 'selected'; } } ?>>All Properties</option>
            <option value='14' <?php if (!empty($_POST["spto_market"])) { if($_POST["spto_market"]==14) { print 'selected'; } } ?>>2 Week</option>
            <option value='7' <?php if (!empty($_POST["spto_market"])) { if($_POST["spto_market"]==7) { print 'selected'; } } ?>>1 Week</option>
            <option value='3' <?php if (!empty($_POST["spto_market"])) { if($_POST["spto_market"]==3) { print 'selected'; } } ?>>3 Day</option>  
            <option value='1' <?php if (!empty($_POST["spto_market"])) { if($_POST["spto_market"]==1) { print 'selected'; } } ?>>1 Day</option>
            </select>
        </td>
        <td>
            <input type="checkbox" id="spto_viewproperty" value="viewproperty" name="spto_viewproperty" <?php if(!empty($_POST["spto_viewproperty"])) {print 'checked';} ?>>&nbsp;&nbsp;View Properties</input><br />
            <input type="checkbox" id="spto_waterfront" value="waterfront" name="spto_waterfront" <?php if(!empty($_POST["spto_waterfront"])) {print 'checked';} ?>>&nbsp;&nbsp;Water Front</input>
		</td>
        <td>
<?php echo spoton_get_property_status(); ?>
        </td>
        <td>
        </td>
    </tr>
</table>

<script type="text/javascript" src="https://xpioimages.blob.core.windows.net/media/datajs-1.0.1.min.js"></script>   
<script type="text/javascript">
jQuery(function(){
var PKey=<?php echo $spoton_pkey; ?>;
var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
jQuery("#spto_city").ready(onLoadCity);
jQuery("#spto_county").change(onSelectChangeCounty);
jQuery("#resetsubmit").click(onclickResetsubmit); 
loadState();
function onLoadCity(){        
    var zipcode =  "<?php echo !empty($_POST['spto_zipcode']) ? $_POST['spto_zipcode'] : ''; ?>";
    jQuery("#spto_zipcode").val(zipcode);
    var city = "<?php echo !empty($_POST['spto_city']) ? $_POST['spto_city'] : ''; ?>";
    jQuery("#spto_city").val(city); 
    var agentid =  "<?php echo !empty($_POST['spto_agentid']) ? $_POST['spto_agentid'] : ''; ?>";
    jQuery("#spto_agentid").val(agentid);        
    var officeid = "<?php echo !empty($_POST['spto_officeid']) ? $_POST['spto_officeid'] : ''; ?>";
    jQuery("#spto_officeid").val(officeid); 
    var schooldistrict =  "<?php echo !empty($_POST['spto_schooldistrict']) ? $_POST['spto_schooldistrict'] : ''; ?>";
    jQuery("#spto_schooldistrict").val(schooldistrict);        
}
function onSelectChangeCounty(){
	var selected = jQuery("#spto_county option:selected");    
	var county = selected.val();    
    loadCity(jQuery("#spto_state").val(),county.replace("_"," ").replace("_"," ").replace("_"," "));
}   
function onclickResetsubmit()
    {     
        loadState();        
        jQuery("#spto_zipcode").val(''); 
        jQuery("#spto_pricefrom").val('0');  
        jQuery("#spto_priceto").val('99999999');
        jQuery("#spto_bedrooms").val('0'); 
        jQuery("#spto_bathrooms").val('0'); 
        jQuery("#spto_squarefeet").val('0'); 
        jQuery("#spto_yearbuilt").val('0'); 
        jQuery("#spto_acreagefrom").val('0'); 
        jQuery("#spto_acreageto").val('999999'); 
        jQuery("#spto_market").val('0');         
        jQuery("#spto_agentid").val('');
        jQuery("#spto_officeid").val('');
        jQuery("#spto_schooldistrict").val(''); 
        jQuery("#spto_pricesort").val('0');
        jQuery('input[id=spto_viewproperty]').attr('checked', false);
        jQuery('input[id=spto_waterfront]').attr('checked', false);
        jQuery('input[id=spto_active]').attr('checked', false);
        jQuery('input[id=spto_pending]').attr('checked', false);
        jQuery('input[id=spto_Residential]').attr('checked', false);
	    jQuery('input[id=spto_Condominium]').attr('checked', false);
        jQuery('input[id=spto_BusinessOpportunity]').attr('checked', false);
        jQuery('input[id=spto_CommercialIndustrial]').attr('checked', false);
        jQuery('input[id=spto_FarmRanch]').attr('checked', false);
        jQuery('input[id=spto_Manufactured]').attr('checked', false);
        jQuery('input[id=spto_MultiFamily]').attr('checked', false);
        jQuery('input[id=spto_Rental]').attr('checked', false);
        jQuery('input[id=spto_VacantLand]').attr('checked', false);
    }
    
    
    function loadState()
    {
        jQuery("#spto_state >option").remove();
        
		var query ="$filter=PKey eq "+ PKey;   
		var requestUri = serviceUri + "RetsCounties?" + query +"&$select=State&$top=1";
		OData.defaultHttpClient.enableJsonpCallback = true;
		OData.read(requestUri, function (data, request) {
		for (var i = 0; i < data.results.length; i++) {
			jQuery("#spto_state").append('<option value='+data.results[i].State.replace(" ","_").replace(" ","_").replace(" ","_")+'>'+data.results[i].State+'</option>');
            loadCounty(data.results[i].State);
            loadCity(data.results[i].State,0);
		}
		}, function(err){
            alert("Error occurred " + err.message);
        });
    }
    function loadCounty(state)
    {
        jQuery("#spto_county >option").remove();
        jQuery("#spto_county").append('<option value=0>All</option>');
                    
		var query ="$filter=PKey eq "+ PKey +"and State eq '"+state+"'";   
		var requestUri = serviceUri + "RetsCounties?" + query +"&$orderby=County&$select=County";
		OData.defaultHttpClient.enableJsonpCallback = true;
		OData.read(requestUri, function (data, request) {
		for (var i = 0; i < data.results.length; i++) {
		    jQuery("#spto_county").append('<option value='+data.results[i].County.replace(" ","_").replace(" ","_").replace(" ","_")+'>'+data.results[i].County+'</option>');
		}
		}, function(err){
            alert("Error occurred " + err.message);
        });        
    }
    function loadCity(state,county)
    {
        jQuery("#spto_city >option").remove();
        jQuery("#spto_city").append('<option value=0>All</option>');
		
		var query =""        
		if(county==0)
			query="$filter=PKey eq "+ PKey +"and State eq '"+state+"'";            
		else
			query="$filter=PKey eq "+ PKey +"and State eq '"+jQuery("#spto_state").val()+"' and County eq '"+county+"'";
            
		var requestUri = serviceUri + "RetsCities?" + query +"&$orderby=City&$select=City";
		OData.defaultHttpClient.enableJsonpCallback = true;
		OData.read(requestUri, function (data, request) {
		for (var i = 0; i < data.results.length; i++) { 
            var previous=i;
            --previous; 
            if(previous==-1)
            {
                previous=0;
                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(" ","_").replace(" ","_").replace(" ","_")+'>'+data.results[i].City+'</option>')
            }
                
            if(data.results[previous].City!=data.results[i].City)
            {
                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(" ","_").replace(" ","_").replace(" ","_")+'>'+data.results[i].City+'</option>');
            }
		}
		}, function(err){
            alert("Error occurred " + err.message);
        });
    }
});
</script>
<!-- Begin Search Form Controls -->
<div style="margin: 10px auto; width: 895px; background-color: #F2F4F7; border: 1px solid #C3D0E1; padding: 10px;">
<p>
	<label for="la_name"><?php _e('A friendly name for your Listing Alert:', 'list-alerts'); ?></label>
	<input style="width:300px" type="text" id="la_name" name="la_name" class="regular-text code" /><br />
	<label for="la_name"><?php _e('Alert me when there are updates for this listing', 'list-alerts'); ?></label>
	<input type="checkbox" id="la_enable" checked="checked" name="la_enable" /><br />
      Sort by:&nbsp;&nbsp;&nbsp;&nbsp;
        <select style="width:210px" id="spto_pricesort">
            <option value='0'>Price -  High to Low</option>
            <option value='1'>Price -  Low to High</option>
            <option value='2'>Time on market - Newest First</option>
            <option value='3'>Time on market - Oldest First</option>
            <option value='4'>Lot Size - Largest First</option>
            <option value='5'>Lot Size - Smallest First</option>
        </select>
	<input type="hidden" id="spto_market" value="1" />
</p>
<button type="submit" class="blue big" id="la_submit" name="la_submit"><span><?php _e('Create Alert', 'list-alerts'); ?></span></button>&nbsp;&nbsp;&nbsp;&nbsp;
<!--<button type="button" class="orange big" id="spto_resetsubmit"><span>Reset</span></button>&nbsp;&nbsp;&nbsp;&nbsp;-->
<!-- End Search Form Controls -->
</div>
</form>
</div>