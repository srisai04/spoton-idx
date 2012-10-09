<?php
	$notFound = 0;
	$ln = !empty($spoton_page) ? $spoton_page : 1;

	$Provider_Key = (!empty($spoton_pkey)) ? $spoton_pkey : 1;

	$PKey = ' PKey eq ' . $Provider_Key . ' and ';
	$query = $PKey . " LN eq '" . $ln . "'";

	$ListID='';$HouseNo='';$Street='';$City='';$State='';$StreetSuff='';$DirectionSuff='';$ZipCode='';$PropType='';$Status='';$SquareFeet='';$Price='';$Acreage='';$Beds='';$Baths='';$Location='';$Remark='';$Style='';$YrBuilt='';$AddrYN='';$ExteriorFeatures='';$PropertyView='';$AppliancesThatStay='';$WaterFront='';$SchoolDistrict='';$HighSchool='';$JuniorHighSchool='';$ElementarySchool='';$OffName='';$Disclaimer='';$logo='';$AgNam='';$PropSub='';$DirPre='';
        
	if($notFound==0)
    {
        try{  
        $proxy = spoton_get_entities();
	    $listingResponse = $proxy->GenericRetsDetailListings()->filter($query)->Select('PKey,LN,HouNo,Stre,DirPre,StrSuff,DirSuff,City,State,Zip,PropType,Status,SquFeet,Price,Acre,Beds,WatFront,Baths,Loc,Remark,YrBuilt,Logo,AddrYN')->Top('1')->Execute();
		if (0 == sizeof($listingResponse->Result))
		{
			// No property found, redirect to a 404 page
			wp_redirect(home_url('404'), 301);
			exit;
		}
		else
		{
			$Listing = $listingResponse->Result[0];
			BWP_PropertySEO::set_property($Listing);
            $ListID=$Listing->LN;
            $HouseNo=$Listing->HouNo;
            $Street=$Listing->Stre;
            $DirPre=$Listing->DirPre;
            $City=$Listing->City;
            $State=$Listing->State;
            $StreetSuff=$Listing->StrSuff;
            $DirectionSuff=$Listing->DirSuff;
            $ZipCode=$Listing->Zip;
            $PropType=$Listing->PropType;
            $Status=$Listing->Status;
            $SquareFeet=$Listing->SquFeet;
            $Price=$Listing->Price;
            $Acreage=$Listing->Acre;
            $Beds=$Listing->Beds;
            $Baths=$Listing->Baths;
            $Location=$Listing->Loc;
            $Remark=$Listing->Remark;
           
	        $YrBuilt=$Listing->YrBuilt;
            $AddrYN=$Listing->AddrYN;
           
            $logo=$Listing->Logo;
	        $Address = spoton_get_ppt_addr($Listing);
			//Get property screenshot
			$ImageQuery = $proxy->RetsListingImages()->filter($query)->Select('PKey,LN,ImageURL');
            $ImageResponse = $ImageQuery->Top('1')->Execute();
		
        }
        }catch(Exception $e)
        {
            $notFound=1; 
        }
    }
    global $spoton_idx;
	$comp_name=$spoton_idx->options['input_company_name'];
    
?>
<?php get_header(); ?>
<div class="dp100 soidx-property-details-wrapper cf">
<?php the_post(); ?>
<?php the_content(); ?>
<script >

jQuery(document).ready(function() { 

var ln='<?php echo $ln;  ?>';
    var PKey='<?php echo $Provider_Key;  ?>';
     listing(ln,PKey);
   });
function FormatCurrency(num) 
		{
			num = num.toString().replace(/\$|\,/g, '');
			if (isNaN(num))
				num = "0";
			sign = (num == (num = Math.abs(num)));
			num = Math.floor(num * 100 + 0.50000000001);
			cents = num % 100;
			num = Math.floor(num / 100).toString();
			if (cents < 10)
				cents = "0" + cents;
			for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
				num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
				
			return (((sign) ? '' : '-') + '$' + num + '.' + cents).replace(".00","");
		}
function listing(ln,PKey)
		{  

			var search_off='<?php echo spoton_list_hide("details_office"); ?>';
            var search_agn='<?php echo spoton_list_hide("details_agent") ?>';
            var compname=''; 
            compname='<?php echo $comp_name ?>';
            var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
            var host="";
            host="<?php echo home_url(); ?>";
            host+='/property/mls-'; 
            var requestUri = serviceUri + "GenericRetsDetailListings?$filter=PKey eq "+ PKey +" and (LN eq '" + ln +"')&$select=Logo,OffNam,AgNam,Price,IntFea,ExtFea,PropView,AppThtStay,SchDist,HiSch,JuHiSch,EleSch,Status,WatFront,LN,Acre,Baths,YrBuilt,HouNo,HiSch,Beds,PropSub,Stre,StrSuff,DirSuff,City,State,Zip,Remark,Style,AddrYN,LUD,Disclaimer";
			OData.defaultHttpClient.enableJsonpCallback = true;      
			OData.read(requestUri, function (data, request) {
			var LN =''; var SquFeet =''; var Price =''; var Acre =''; var Beds =''; var Baths =''; var Loc =''; var Remark ='';var HouNo =''; var Hounum ='';var EleSch ='';
		    var adrress=""; var AgName=""; var Disclaimer="";var OffNam=""; var suffix=""; 
            var addryn='';
            for (var i = 0; i < data.results.length; i++) 
            {
			 
				LN = data.results[i].LN;            
			
				Acre = data.results[i].Acre;  
                YrBuilt=data.results[i].YrBuilt; 
                Status=data.results[i].Status;
                AppThtStay=data.results[i].AppThtStay;
                ExtFea=data.results[i].ExtFea;
                Style=data.results[i].Style;
                PropSub=data.results[i].PropSub;
                IntFea=data.results[i].IntFea;
                PropView=data.results[i].PropView;
                WaterFront=data.results[i].WatFront;
                SchDist=data.results[i].SchDist;
                HiSch=data.results[i].HiSch;
                JuHiSch=data.results[i].JuHiSch;
                EleSch=data.results[i].EleSch;
               
                Stre=data.results[i].Stre;
                StrSuff=data.results[i].StrSuff;
                DirSuff=data.results[i].DirSuff;
                City=data.results[i].City;
                State=data.results[i].State;
                Zip=data.results[i].Zip;
                AgNam=data.results[i].AgNam;
                OffNam=data.results[i].OffNam;
                logo=data.results[i].Logo;
                Disclaimer=data.results[i].Disclaimer;
                addryn=data.results[i].AddrYN;
                LUD=data.results[i].LUD;
                var dateValue = new Date(parseInt(LUD.replace("/Date(", "").replace(")/", "")));
                lastUpdate_date=(dateValue.getMonth()+1)+"/"+dateValue.getDate()+"/"+dateValue.getFullYear();
                hours=dateValue.getHours();
                    if (hours >= 12) 
                    {
                    suffix = "PM";
                    
                    }
                   
                    updatetime=hours+":"+dateValue.getMinutes()+":"+dateValue.getSeconds()+" "+suffix;
                    disclaimer=data.results[0].Disclaimer.replace("{company name}",compname);
                jQuery('#spto_disclaimer').html('"Disclaimer :'+disclaimer+':'+lastUpdate_date+'"');
                
                if(search_agn == 1)
                    {

                        AgNam='';
                    }
                    if(search_off == 1)
                    {
                       OffNam='';
                    }
                if((AgNam != "")&&(OffNam !=""))
                {
                     jQuery('#soidxtext').html("Listing courtesy of "+AgNam+" - <span id='spto_OffNam'>"+OffNam+"</span>");
                }
                else if((AgNam != "")&&(OffNam ==""))
                {
                     jQuery('#soidxtext').html("Listing courtesy of "+AgNam);

                }
                else if((AgNam == "")&&(OffNam !=""))
                {
                     jQuery('#soidxtext').html("Listing courtesy of "+OffNam);
                }
           
                var school_info="";
                school_info+="<div class='soidx-listing-meta-text dp100 sepH_c'><h2 class='soidx-property-title sepH_a_line'>School Information</h2>";
                school_info+="<div class='sepH_a cfs'>";
                if(SchDist !="")
                {
                    school_info+="<div class='dp25'>School District :</div><div class='dp25 bld'>"+SchDist+"</div>";  
                }
                if(EleSch !="")
                {
                    school_info+="<div class='dp25'>Elementary School :</div><div class='dp25 bld'>"+EleSch+"</div>";  
                }
                school_info+="</div>";
                school_info+="<div class='sepH_b cfs'>";
                if(JuHiSch !="")
                {
                    school_info+="<div class='dp25'>Middle School :</div><div class='dp25 bld'>"+JuHiSch+"</div>";  
                }
                if(HiSch !="")
                {
                    school_info+="<div class='dp25'>High School :</div><div class='dp25 bld'>"+HiSch+"</div>";  
                }
                school_info+="</div></div>";
                jQuery("#school_info").html(school_info);
				var property_fet="";
		        property_fet+="<div class='soidx-listing-meta-text sepH_c dp100'><h2 class='soidx-property-title sepH_a_line'>Property Features</h2>";
            
				 if(PropSub !="")
                {
                    property_fet+="<div class='sepH_b cf'><div class='dp25'>Property Subtype :</div><div class='dp75' >"+PropSub+"</div></div>";  
                }
              
                 if(Style !="")
                {
                    property_fet+="<div class='sepH_b cf'><div class='dp25'>Style :</div><div class='dp75' >"+Style+"</div></div>";  
                }
                 if(IntFea !="")
                {
                   property_fet+="<div class='sepH_b cf'><div class='dp25'>Interior Features :</div><div class='dp75' >"+IntFea+"</div></div>";   
                }
                  if(ExtFea !="")
                {
                   property_fet+="<div class='sepH_b cf'><div class='dp25'>Exterior Features :</div><div class='dp75' >"+ExtFea+"</div></div>";  
                }
                
                 if(PropView !="")
                {
                    property_fet+="<div class='sepH_b cf'><div class='dp25'>Property View :</div><div class='dp75' >"+PropView+"</div></div>";  
                }
                if(AppThtStay !="")
                {
                    property_fet+="<div class='sepH_b cf'><div class='dp25'>Appliances that Stay :</div><div class='dp75' >"+AppThtStay+"</div></div>";  

                }
                if(WaterFront !="")
                {
                    property_fet+="<div class='sepH_b cf'><div class='dp25'>Water Front :</div><div class='dp75' >"+WaterFront+"</div></div>";  

                }
                property_fet+="</div>";
				
			     jQuery("#property_fetures").html(property_fet);
			}
			}, function(err){
				//alert("Error occurred " + err.message);


			});
		}


</script>
    <script>
    (function(window, $, PhotoSwipe){

				function loadSpotonPhotos(handler, photoSwipeInstance, galleryLN, galleryURL, galleryID)
				{
					// Try hiding active instance first to avoid errors
					var activeInstace = window.Code.PhotoSwipe.activeInstances[0];
					if (typeof activeInstace != "undefined" || activeInstace != null)
						activeInstace.instance.hide(0);
					// Show PhotoSwipe
					var options = {
						enableMouseWheel: true,
                        captionAndToolbarAutoHideDelay : 0,
                        captionAndToolbarShowEmptyCaptions : false,
                        imageScaleMethod : 'fitNoUpscale',
						enableKeyboard: false
					};
					if (typeof photoSwipeInstance == "undefined" || photoSwipeInstance == null)
						photoSwipeInstance = jQuery("#spotonGallery_" + galleryLN + " a").photoSwipe(options, galleryID);
					photoSwipeInstance.show(0);
					handler.html('<input type="submit" value="View all Photos" />');
					handler.attr('href', galleryURL);
				}

				jQuery(document).ready(function(){
					jQuery('a.spotonPhotoHandler').live('click', function() {
						var handler = jQuery(this);
						var galleryURL = jQuery(this).attr('href');
						var galleryID = jQuery(this).attr('id');
						var galleryLN = galleryID.replace('spotonPhotoHandler_', '');
						var galleryPlaceholderID = 'spotonPhotoPlaceholder_' + galleryLN;
						jQuery(this).text('Loading ...');
						jQuery(this).attr('href', '#');
						photoSwipeInstance = PhotoSwipe.getInstance(galleryID);
						if (1 == jQuery('#' + galleryPlaceholderID + ' .spotonGalleryLoaded').val())
							    loadSpotonPhotos(handler, photoSwipeInstance, galleryLN, galleryURL, galleryID);
						else
						{
							jQuery('#' + galleryPlaceholderID).load(galleryURL, function() {
								loadSpotonPhotos(handler, photoSwipeInstance, galleryLN, galleryURL, galleryID);
							});
						}
						return false;
					});
				});
			}(window, window.jQuery, window.Code.PhotoSwipe));
</script>
<div class="soidx-links dp100">
<a href="javascript:history.go(-1)">&laquo; Back to List</a>
</div>
<div class="dp100 sepH_b cf">
 <?php 
        if($Provider_Key == 3)
        {
          if($AddrYN == "Y")
                   { ?>
                       <h1 id="spto_title" class="soidx-property-title"><?php echo $Address;?></h1>
             <?php } 
             
         }
         else 
         {?>
                      <h1 id="spto_title" class="soidx-property-title"><?php echo $Address;?></h1>
        <?php } ?>
</div>
<!--begin content area-->
<div class="dp70 cf">
<div id="sliderphotoSwipe">
   <?php
	foreach($ImageResponse->Result as $listingimage)
	{
    ?>       
    <img class='soidx-listing-thumbnail' style='border:1px solid #D0D0D0;padding:35px;width:450px;'  src='<?php echo $listingimage->ImageURL; ?>' height='335px' >
<?php 
    } 
    ?>
</div><br /><br/><br /><br/><br/>
    <div class="clear"></div>
<a style="padding-left:190px;font-size:19px;" id="spotonPhotoHandler_<?php echo $ln; ?>" class="spotonPhotoHandler" href="<?php echo home_url('?spoton_do_ajax=1&action=get_property&sub_action=image&ln='.$ln); ?>"><input type="submit" value="View all Photos" /></a>
										  <div style="display:none;" id="spotonPhotoPlaceholder_<?php echo $ln; ?>"></div>           
<br />

<div id="soidxtext" class="sepH_c dp100 soidx-listing-meta-text"></div>

<div class="hfeed" id="soidx-content">   
    <div class="dp100 sepH_c">
        <h2 class="soidx-property-title sepH_a_line">
            <?php _e('Property Information', $spoton_idx->domain); ?>
        </h2>
        <span class="soidx-listing-meta-text" id="spto_Remark"><?php echo $Remark; ?></span>
    </div>
    <div id="property_fetures">
       
    </div>
    <div id="school_info">  
    </div>
    <div class="dp100 sepH_c">
        <h2 class="soidx-property-title sepH_a_line">
            <?php _e('Location', $spoton_idx->domain); ?>
        </h2>
        <iframe width="610" height="366" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="http://dev.virtualearth.net/embeddedMap/v1/ajax/road?zoomLevel=15&center=<?php echo str_replace(',','_',$Location); ?>&pushpins=<?php echo str_replace(',','_',$Location); ?>"></iframe>
    </div>    
    <div class="dp100 soidx-listing-meta-text large sepH_b">
    <div class="fl sepV_a"><?php if(!empty($logo)) { ?><img width="40px" height="40px" src="<?php echo $logo; ?>"></img><?php } ?></div>
        <span id="spto_disclaimer"></span>
    </div>

<script type="text/javascript">        
    jQuery('#soidx-property-meta .bld').each(function() {
        var content = jQuery(this).text();
        if (content == '' || content == ' ' || content == '0' || content == '0.0' || content == '0.00')
            jQuery(this).text('--');
    });
    
    // Hide empty fields
    jQuery('#soidx-content .soidx-listing-meta-text div.cf').each(function() {
        var content = jQuery(this).find('div:nth-child(2)').text();
        if (content == '' || content == ' ')
            jQuery(this).hide();
    });
    
    jQuery('#soidx-content .soidx-listing-meta-text div.cf .bld').each(function() {
        var content = jQuery(this).text();
        if (content == '' || content == ' ')
        {
            jQuery(this).prev('div').hide();
            jQuery(this).hide();
        }
    });
</script>
    
</div>
</div><!--end content area-->
<!--begin sidebar-->
<div class="dp30 cf"><div >
<div id="soidx-property-meta" class="soidx-listing-meta-text sepH_b cf">
        <div class="sepH_a_line cf">
            <div class="dp100 large bld meta_property_type"><?php if(empty($PropType)){echo '--';} else {echo $PropType;} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Price:</div>
            <div class="dp40 large green_color bld meta_price"><?php if(str_replace('.00','',number_format($Price, 2, '.', ','))=='0'){echo '--';} else {echo '$'.str_replace('.00','',number_format($Price, 2, '.', ','));} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Beds:</div>
            <div class="dp40 large bld meta_bed"><?php if(str_replace('.00','',$Beds)=='0'){echo '--';} else {echo str_replace('.00','',$Beds);} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Baths:</div>
            <div class="dp40 large bld meta_bath"><?php if(str_replace('.00','',$Baths)=='0'){echo '--';} else {echo str_replace('.00','',$Baths);} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Square Feet:</div>
            <div class="dp40 large bld meta_squarefeet"><?php if(str_replace('.00','',number_format($SquareFeet, 2, '.', ','))=='0'){echo '--';} else {echo str_replace('.00','',number_format($SquareFeet, 2, '.', ','));} ?></div>
        </div>
		<div class="sepH_a_line cf">
            <div class="dp60 large">Year Built:</div>
            <div class="dp40 large bld meta_built"><?php if($YrBuilt=='0'){echo '--';} else {echo $YrBuilt;} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Lot Size (Acres):</div>
            <div class="dp40 large bld meta_lotsize"><?php if($Acreage=='0'){echo '--';} else {echo number_format($Acreage, 2, '.', ',');} ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">MLS #:</div>
            <div class="dp40 large bld meta_mlsno"><?php echo $ListID; ?></div>
        </div>
        <div class="sepH_a_line cf">
            <div class="dp60 large">Status:</div>
            <div class="dp40 large bld meta_status"><?php if(empty($Status)){echo '--';} else {echo $Status;} ?></div>
        </div>

    </div>

<?php
    unset($ListID);unset($HouseNo);unset($Street);unset($City);unset($State);unset($StreetSuff);unset($DirectionSuff);unset($ZipCode);unset($PropType);unset($Status);unset($SquareFeet);unset($Price);unset($Acreage);unset($Beds);unset($Baths);unset($Location);unset($Remark);unset($Style);unset($YrBuilt);unset($InteriorFeatures);unset($ExteriorFeatures);unset($PropertyView);unset($AppliancesThatStay);unset($WaterFront);unset($SchoolDistrict);unset($HighSchool);unset($JuniorHighSchool);unset($ElementarySchool);unset($OffName);unset($Disclaimer);unset($logo);unset($AgNam);unset($PropSub);unset($DirPre);
?>

 
<div id="soidx-sidebar">
		<?php dynamic_sidebar('Property Details Page'); ?>
</div>    

</div><!--end sidebar-->
</div>
</div>
<?php get_footer(); ?>