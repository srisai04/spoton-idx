<?php
/**
* A custom page template
*/
get_header(); ?>
<!-- Begin Form -->
<div id="so_search-form-wrapper" class="so_search-form">
<form method="post" action="<?php echo home_url('?la_action=list'); ?>">
<?php wp_nonce_field('idx_listing_alerts_add', '_lanonce'); ?>
<div class="cf">
<div id="container4">
	<div id="container3">
		<div id="container2">
			<div id="container1">
			  <div id="col1">
					<!-- Column one start -->
				<div class="so_search-form-title">Location</div>
				<label for="spto_state">State</label>
                <select name="spto_state" id="spto_state" style="width:140px">
			    </select>
                <label for="spto_county">County</label>
				<select style="width:140px" name="spto_county" id="spto_county">
				<option value='0'>All</option>
                </select>
        <label for="spto_city">City</label>      
        <select style="width:140px" name="spto_city" id="spto_city">
        <option value='0'>All</option>
        </select> 
        <label for="spto_zipcode">Zip Code</label>
        <input style="width:140px" type="text" id="spto_zipcode" name="spto_zipcode" />
			    <!-- Column one end -->
				</div>
			  <div id="col2">
					<!-- Column two start -->
				<div class="so_search-form-title">Price Range</div>
				  <label for="spto_pricefrom">Price From</label>
        <select style="width:140px" id="spto_pricefrom" name="spto_pricefrom">
					<option value='0'>No Minimum</option>
            <?php 
            $pricefrominitial = 10000;
            for($pricefrom=50000;$pricefrom<=2000000;$pricefrom=$pricefrom+$pricefrominitial)
            { 
            if($pricefrom>=100000)
            {
                $pricefrominitial=25000;
            }
                echo "<option value='$pricefrom'>".'$'.str_replace('.00','',number_format($pricefrom, 2, '.', ','))."</option>";
            } ?>
        </select>
        <br />
                
        <label for="spto_priceto">Price To</label>
        <select style="width:140px" id="spto_priceto" name="spto_priceto">
            <option value='99999999'>No Maximum</option>
            <?php 
            $pricetoinitial = 10000;
            for($priceto=50000;$priceto<=2000000;$priceto=$priceto+$pricetoinitial)
            { 
            if($priceto>=100000)
            {
                $pricetoinitial=25000;
            }
             echo "<option value='$priceto'>".'$'.str_replace('.00','',number_format($priceto, 2, '.', ','))."</option>"; 
            }
             ?>

        </select>
			    <!-- Column two end -->
				</div>
			  <div id="col3">
					<!-- Column three start -->
				<div class="so_search-form-title">Features</div>
        <label for="spto_bedrooms">Bedrooms</label>
				  <select style="width:140px" id="spto_bedrooms" name="spto_bedrooms">
            <option value='0'>Any Number</option>
            <?php 
            for($bedrooms=1;$bedrooms<=10;$bedrooms++)
            { 
            echo "<option value='$bedrooms'>at least $bedrooms</option>"; 
            }?>
        </select>
        <br />
        <label for="spto_bathrooms">Bathrooms</label>
        <select style="width:140px" id="spto_bathrooms" name="spto_bathrooms">
            <option value='0'>Any Number</option>
            <?php 
            for($bathrooms=1;$bathrooms<=10;$bathrooms++)
            { 
            echo "<option value='$bathrooms'>at least $bathrooms</option>";
            } ?>
        </select>
        <br />
        <label for="spto_squarefeet">Square Feet</label>
        <select style="width:140px" id="spto_squarefeet" name="spto_squarefeet">
            <option value='0'>Any Size</option>
            <?php 
            for($squarefeet=500;$squarefeet<=7500;$squarefeet=$squarefeet+500)
            { 
            echo "<option value='$squarefeet'>$squarefeet+</option>"; 
            }            
            ?>
        </select>
        <br />
        <label for="spto_yearbuilt">Year Built</label>
        <select style="width:140px"  id="spto_yearbuilt" name="spto_yearbuilt">
            <option  value='0'>Any Year</option>
            <option value='111'>Current Year</option>
            <?php 
            $yearbuiltinitial = 1;
            for($yearbuilt=1;$yearbuilt<=25;$yearbuilt=$yearbuilt+$yearbuiltinitial)
            { 
            if($yearbuilt>=5)
            {
                $yearbuiltinitial=5;
            }
            echo "<option value='$yearbuilt'>&lt $yearbuilt years</option>";
            }
            ?>
        </select>
        <br />
        <label for="spto_acreagefrom" >Acreage From</label>
        <select style="width:140px" id="spto_acreagefrom" name="spto_acreagefrom">
                <option value='0'>No Minimum</option>
                <?php 
                $acreagefromInitial = 0.25;
                for($acreagefrom=0.25;$acreagefrom<=20;$acreagefrom=$acreagefrom+$acreagefromInitial)
                { 
                if($acreagefrom>=1)
                {
                    $acreagefromInitial=1;
                }                
                echo "<option value='$acreagefrom'>$acreagefrom</option>"; 
                }
                ?>
                </select>
                <br />
                <label for="spto_acreageto">Acreage To</label>
                <select style="width:140px" id="spto_acreageto" name="spto_acreageto">
                <option value='999999'>No Maximum</option>
                <?php 
                $acreagetoInitial= 0.25;
                for($acreageto=0.25;$acreageto<=20;$acreageto=$acreageto+$acreagetoInitial)
                {
                if($acreageto>=1)
                {
                    $acreagetoInitial=1;
                }
                echo "<option value='$acreageto'>$acreageto</option>";
                }
                ?>
                </select>
        <!-- Column three end -->
				</div>
			  <div id="col4">
					<!-- Column four start checked="True"-->
				<div class="so_search-form-title">Type</div>
                <div id="Residential" style="display:none">
				<input style="so_search-form-label" type="checkbox" id="spto_Residential" value="Residential">&nbsp;&nbsp;Residential</input></br>
                </div>
                <div id="Condominium" style="display:none">
				<input type="checkbox" id="spto_Condominium" value="Condominium">&nbsp;&nbsp;Condominium</input></br>
                </div>
                <div id="BusinessOpportunity" style="display:none">
				<input type="checkbox" id="spto_BusinessOpportunity" value="Business Opportunity">&nbsp;&nbsp;Business Opportunity</input></br>
                </div>
                <div id="CommercialIndustrial" style="display:none">
       			<input type="checkbox" id="spto_CommercialIndustrial" value="Commercial Industrial">&nbsp;&nbsp;Commercial Industrial</input></br>
                </div>
                <div id="FarmRanch" style="display:none">
        		<input type="checkbox" id="spto_FarmRanch" value="Farm Ranch">&nbsp;&nbsp;Farm Ranch</input></br>
                </div>
                <div id="Manufactured" style="display:none">
        		<input type="checkbox" id="spto_Manufactured" value="Manufactured">&nbsp;&nbsp;Manufactured</input></br>
                </div>
                <div id="MultiFamily" style="display:none">
        		<input type="checkbox" id="spto_MultiFamily" value="Multi Family">&nbsp;&nbsp;Multi Family</input></br>
                </div>
                <div id="Rental" style="display:none">
        		<input type="checkbox" id="spto_Rental" value="Rental">&nbsp;&nbsp;Rental</input></br>
                </div>
                <div id="VacantLand" style="display:none">
        		<input type="checkbox" id="spto_VacantLand" value="Vacant Land">&nbsp;&nbsp;Vacant Land</input>
                </div>
			    <!-- Column four end -->
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!--End Main Form-->
<div class="so_search-form-footer cf">
<div id="container4footer">
	<div id="container3footer">
		<div id="container2footer">
			<div id="container1footer">
				<div id="col1footer">
					<!-- Column one start -->
					<!-- Column one end -->
				</div>
				<div id="col2footer">
					<!-- Column two start -->
                <label>Show Only</label>
                <input type="checkbox" id="spto_viewproperty" value="viewproperty" name="spto_viewproperty">View Properties</input><br />
                <input type="checkbox" id="spto_waterfront" value="waterfront" name="spto_waterfront">Water Front</input>
					<!-- Column two end -->
				</div>

				<div id="col3footer">
					<!-- Column three start -->
                    <label>Property Status</label>
					<input type="checkbox" id="spto_active" name="spto_active" checked="True" value="active"> 
					Active</input><br />
                    <input type="checkbox" id="spto_pending" name="spto_pending" checked="True" value="pending"> 
                    Pending</input>
				  <!-- Column three end -->
				</div>
				<div id="col4footer">
					<!-- Column four start -->
                    <label>MLS #</label>
                    <input style="width:140px" type="text" id="spto_mls" name="spto_mls" />
				  <!-- Column four end -->
				</div>
			</div>
		</div>
	</div>
</div>
</div> <!--End Footer-->
</div><!-- End Search Form -->
<!-- Begin Search Form Controls -->

<div style="margin: 0px auto; margin-bottom: 10px; width: 895px; background-color: #F2F4F7; border: 1px solid #C3D0E1; padding: 10px;">
<p>
	<label for="la_name"><?php _e('A friendly name for your Listing Alert:', 'list-alerts'); ?></label>
	<input style="width:300px" type="text" id="la_name" name="la_name" class="regular-text code" /><br />
	<label for="la_name"><?php _e('Alert me when there are updates for this listing', 'list-alerts'); ?></label>
	<input type="checkbox" id="la_enable" checked="checked" name="la_enable" />
	<input type="hidden" id="spto_market" value="1" />
</p>
<button type="submit" class="blue big" id="la_submit" name="la_submit"><span><?php _e('Create Alert', 'list-alerts'); ?></span></button>&nbsp;&nbsp;&nbsp;&nbsp;
<!--<button type="button" class="orange big" id="spto_resetsubmit"><span>Reset</span></button>&nbsp;&nbsp;&nbsp;&nbsp;-->

        Sort by:&nbsp;&nbsp;&nbsp;&nbsp;
        <select style="width:210px" id="spto_pricesort">
            <option value='0'>Price -  High to Low</option>
            <option value='1'>Price -  Low to High</option>
            <option value='2'>Time on market - Newest First</option>
            <option value='3'>Time on market - Oldest First</option>
            <option value='4'>Lot Size - Largest First</option>
            <option value='5'>Lot Size - Smallest First</option>
        </select>
</div>
<!-- End Search Form Controls -->
</form>

<div id="sidebar"><?php dynamic_sidebar('Property Search Page'); ?></div><!-- End Sidebar -->

<?php get_footer(); ?>