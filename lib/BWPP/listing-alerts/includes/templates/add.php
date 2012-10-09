<?php get_header(); ?>

<div id="soidx-content-sidebar-wrapper" class="soidx-la-create">
	<?php do_action('list_alerts_add'); ?>
<!-- Begin Form -->
<form method="post" action="<?php echo add_query_arg(array('la_action' => 'list')); ?>">
<?php wp_nonce_field('idx_listing_alerts_add', '_lanonce'); ?>
<table style="width: 790px; overflow-x: scroll; margin: 0 10px;">
    <tr>
        <td style="width:250px"><h4><strong>Location</strong></h4></td>
        <td style="width:250px"><h4><strong>Price Range</strong></h4></td>
        <td style="width:250px"><h4><strong>Features</strong></h4></td>
        <td style="width:250px"><h4><strong>Property Type</strong></h4></td>
    </tr>
    <tr>
        <td>
<?php echo spoton_get_states(); ?>
        </td>
        <td>
<?php spoton_get_prices(); ?>
        </td>
        <td>
<?php spoton_get_sff_rooms(); ?>
        </td>
        <td rowspan="6">
            <table>
<?php echo spoton_get_proptype(); ?>
			</table>
        </td>
    </tr>
    <tr>
        <td>
<?php echo spoton_get_counties(); ?>
        </td>
        <td>
<?php spoton_get_prices(); ?>
        </td>
        <td>
<?php spoton_get_sff_rooms(true); ?>
        </td>
    </tr>
    <tr>
        <td>
<?php echo spoton_get_cities(); ?>
        </td>
        <td>          
        </td>
        <td>
<?php spoton_get_sff_sqfeet(); ?>
        </td>
    </tr>
    <tr>
        <td>
<?php echo spoton_get_areas(); ?>
        </td>
        <td>            
        </td>
        <td>
<?php spoton_get_acres(); ?>
        </td>
    </tr>
    <tr>
        <td>
<?php echo spoton_get_subdivisions(); ?>
		</td>
        <td></td>
        <td>
<?php spoton_get_acres(true); ?>
        </td>
    </tr>
    <tr>
        <td>
            <label for="spto_zipcode">Zip Code</label>&nbsp;&nbsp;
            <input style="width:140px" type="text" id="spto_zipcode" name="spto_zipcode" class="regular-text code"/>
		</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><h4><label for="spto_market"><strong>New on Market</strong></label></h4></td>
        <td><h4><strong>Show Only</strong></h4></td>
        <td><h4><strong>Property Status</strong></h4></td>
    </tr>
        <tr>
        <td>            
<?php spoton_get_sff_tom('medium'); ?>
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

<?php
	$sff_filters = spoton_get_sff_filters();
?>

<script type="text/javascript" src="https://xpioimages.blob.core.windows.net/media/datajs-1.0.1.min.js"></script>   
<script type="text/javascript">
jQuery(function(){
var PKey = <?php echo $spoton_pkey; ?>;
var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
<?php if (1 != $sff_filters['State']) { ?>
loadState();
<?php } ?>
<?php if (1 != $sff_filters['City']) { ?>
jQuery("#spto_city").ready(onLoadCity);
<?php } ?>
jQuery("#spto_county").change(onSelectChangeCounty);

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

<?php get_footer(); ?>