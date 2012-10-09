<?php
/**
* A custom page template for the Property Search Form.
*/
get_header(); 

// Get Form Administration Settings
$searchview = spoton_get_adm_settings();
$defaultlocation = spoton_get_default_location($searchview);
$sff_filters = spoton_get_sff_filters();
$sff_def = spoton_get_sff_default();
$post_query = '';

// Get $_POST search criteria, if any
if (isset($_POST['spoton_qsw_submit']))
{
	$post_query = '$filter=' . spoton_get_qsw_odata_query($spoton_pkey);
	$post_query = explode('^', $post_query);
	$post_query = $post_query[0];
}

global $spoton_idx;
	        $comp_name=$spoton_idx->options['input_company_name'];
?>

<!-- Model Popup -->
<div id="sptopopupContact">
	<a id="sptopopupContactClose">Close</a>
    <h3>Property Details</h3>
	<p id="sptocontactArea">
		<div>
			<span  style="float:left;" id="spto_imageslider">
			</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:black;font-size:17px;" id="addressline">

			</span>


			<span style="float:right;color:black;" id="spto_listingdetails">
			</span>
            <br />
            <div  class="clear"></div>
            <a id="viewall_photolink"></a><span style="margin-left:180px;" id="view_more">
			</span>
			 <div  class="clear"></div>
            <div id="tabs_contect" class="box_c_heading cf">
			<span class="fl"></span>
			<ul class="tabsSss fr">

				<li><a id="Remark" href="#">Remarks</a></li>
              
				<li><a id="Features" href="#">Features</a></li>
                
                <li><a id="School" href="#">School Information</a></li>
               
			</ul>
		</div>
  <div id="content_tabs" class="box_c_content cf tabs_contenttab">
            
			<div id="spto_listingremarks" style="color:black;">
			</div>
			<div id="fetureslist" style="color:black;">
                                      
			</div>
            <div id="school_details" style="color:black;">
                                     
			</div>
			<ul class="cf rounBox_list sepH_c">
			</ul>
        </div>
            
			<div id="disclaimerdiv" class="dp100 soidx-text"></div>						
		</div>
	</p>
</div>

<div id="sptobackgroundPopup"></div>

<script type="text/javascript">
var popupStatus = 0;

function loadPopup(ln){
    centerPopup();    
	if(popupStatus==0){
		jQuery("#sptobackgroundPopup").css({
			"opacity": "0.8"
		});
		jQuery("#sptobackgroundPopup").fadeIn("slow");
		jQuery("#sptopopupContact").fadeIn("slow");
		popupStatus = 1;
	}
    
    if(ln!=0)
	{
        jQuery("#spto_imageslider").empty();
		image(ln);
		listing(ln);
	}
}

function disablePopup(){
	if(popupStatus==1){
		jQuery("#sptobackgroundPopup").fadeOut("slow");
		jQuery("#sptopopupContact").fadeOut("slow");
		popupStatus = 0;
	}
}

function centerPopup(){
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
	var popupHeight = jQuery("#sptopopupContact").height();
	var popupWidth = jQuery("#sptopopupContact").width();
    
	jQuery("#sptopopupContact").css({
		"position": "absolute",
		"top": jQuery(window).scrollTop()+((windowHeight-popupHeight)/2),
		"left": (windowWidth-popupWidth)/2
	});
    
    //2.5 , 4.5
    
	jQuery("#sptobackgroundPopup").css({
		"height": windowHeight
	});
}

jQuery(document).ready(function(){
	
	//listingcourtesy();
	
	jQuery("#sptopopupContactClose").click(function(){
		disablePopup();
	});
    
	jQuery("#sptobackgroundPopup").click(function(){
		disablePopup();
	});

	jQuery(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
 jQuery(".tabsSss").flowtabs("div.tabs_contenttab > div");
 
});

</script>
<div id="soidx-wrapper" class="cf">
<div id="soidx-content-sidebar-wrapper" class="cf">
<div id="soidx-content" class="soidx-content-map">
	<div class="box_c">
	  <div class="box_c_heading cf">
			<span class="fl">Property</span>
			<ul class="tabsS fr">
                <?php if($searchview[0]==0) { ?>
				<li><a id="spto_map" href="#"><img src=<?php echo SPOTON_IDX_IMAGES . '/icons/marker2.png'; ?> alt="" width="8" height="12" />Map View</a></li>
                <?php } ?>
                <?php if($searchview[1]==0) { ?>
				<!--<li><a id="spto_list" href="#"><img src=<?php echo SPOTON_IDX_IMAGES . '/icons/list_wImages.png'; ?> alt="" />List View</a></li>-->
                <?php } ?>
			</ul>
		</div>
		<div class="box_c_content cf tabs_content">
             <?php if($searchview[0]==0) { ?>
			<div>
				<div class="msg_box msg_ok">
					<span id="mapcount" class="soidx-mapcount"></span>
				</div>
				<body onLoad="getxpiomap();">
					<div id='spotonmap' style="position:relative; width:650px; height:600px;"></div>
				</body>
			</div>
            <?php } ?>
		    
            <?php if($searchview[1]==0) { ?>




            <?php } ?>
				
			<ul class="cf rounBox_list sepH_c">
			</ul></div><!-- End List View -->
		</div>	
    <div id="discl_info" style="color:black;"></div> 
	</div><!-- End Spot-on Content Section -->

  <div id="soidx-sidebar">
  	  	<div class="box_c_heading cf">
			<span class="fl">Refine Search</span>
		</div>
  <div class="mAccordion box_c_content">
	<div class="micro">



		<div class="sub_section cf">
        <form action="" class="formEl_a" id="form_a">
        	<div class="dp0 sepH_b">
        		<button type="button" class="btn btn_medium btn_dL fl sepV_a" id="spto_searchbutton">Search</button>
				<button type="button" class="btn btn_bL fl" id="spto_resetsubmit">Reset</button>
			</div>
			<input type="hidden" name="is_qsw" value="<?php echo (!empty($post_query)) ? 1 : 0; ?>" />
			
            <?php if($searchview[4]==0) { ?>
          	<div id="spto_dstate" class="sepH_b dp50">
				<?php echo spoton_get_states(); ?>




            </div>
			<?php } ?>
            
            <?php if($searchview[5]==0) { ?>
            <div id="spto_dcounty" class="sepH_b dp50">
				<?php echo spoton_get_counties(); ?>




            </div>
            <?php } ?>
			
            <?php if($searchview[6]==0) { ?>
            <div id="spto_dcity" class="sepH_b dp50">
				<?php echo spoton_get_cities(); ?>

            </div>
            <?php } ?>






            <?php if($searchview[19]==0) { ?>
            <div id="spto_dsubdiv" class="sepH_b dp50">
				<?php echo spoton_get_subdivisions(); ?>


            </div>
            <?php } ?>




            <?php if($searchview[18]==0) { ?>
            <div id="spto_darea" class="sepH_b dp50">
				<?php echo spoton_get_areas(); ?>

            </div>

           <?php } ?>






            <?php if($searchview[7]==0) { ?>
            <div id="spto_dzipcode" class="sepH_b dp50">
            <label for="spto_zipcode" class="lbl_a">Zip Code</label>
			<input type="text" id="spto_zipcode" name="spto_zipcode" class="inpt_a medium" />  
            </div>
            <?php } ?>




            <?php if($searchview[8]==0) { ?>
            <div class="dp0">
            <div id="spto_dpricefrom" class="sepH_b dp50">
				<?php spoton_get_prices(); ?>
            </div>
            <?php } ?>



            <?php if($searchview[9]==0) { ?>
            <div id="spto_dpriceto" class="sepH_b dp50">
				<?php spoton_get_prices(true); ?>
            </div></div>
            <?php } ?>






            <?php if($searchview[10]==0) { ?>
            <div class="dp0">
            <div id="spto_dbedrooms" class="sepH_b dp50">
				<?php spoton_get_sff_rooms(); ?>



            </div>
            <?php } ?>




            <?php if($searchview[11]==0) { ?>
            <div id="spto_dbathrooms" class="sepH_b dp50">
				<?php spoton_get_sff_rooms(true); ?>
            </div></div>
            <?php } ?>


            <?php if($searchview[14]==0) { ?>
            <div id="spto_dsquarefeet" class="sepH_b dp50">
				<?php spoton_get_sff_sqfeet(); ?>
            </div>
            <?php } ?>



            <?php if($searchview[12]==0) { ?>
            <div class="dp0">
            <div id="spto_dacreagefrom" class="sepH_b dp50">
				<?php spoton_get_acres(); ?>















            </div>
            <?php } ?>



















            <?php if($searchview[13]==0) { ?>
            <div id="spto_dacreageto" class="sepH_b dp50">
				<?php spoton_get_acres(true); ?>
            </div></div>
            <?php } ?>








            <?php if($searchview[20]==0) { ?>
            <div id="spto_dschooldistrict" class="sepH_b dp50">
				<?php echo spoton_get_schools('SchDist'); ?>
            </div>
            <?php } ?>








            <?php if($searchview[21]==0) { ?>
            <div id="spto_delementaryschool" class="sepH_b dp50">
				<?php echo spoton_get_schools('Ele'); ?>
            </div> 
            <?php } ?>








            <?php if($searchview[22]==0) { ?>
            <div id="spto_dmiddleschool" class="sepH_b dp50">
				<?php echo spoton_get_schools('JuHi'); ?>
            </div>
            <?php } ?>








            <?php if($searchview[23]==0) { ?>
            <div id="spto_dhighschool" class="sepH_b dp50">
				<?php echo spoton_get_schools('Hi'); ?>
            </div> 
			<?php } ?>












            <?php if($searchview[15]==0) { ?>
            <div id="spto_dmarket" class="sepH_b dp100">
				<?php spoton_get_sff_tom(); ?>
            </div>
            <?php } ?>
			
            <?php if($searchview[16]==0) { ?>
            <div id="spto_dPropertyType" class="sepH_a dp100">
				<?php echo spoton_get_proptype(); ?>




            </div>
            <?php } ?>
            
            <?php if($searchview[17]==0) { ?>
            <div id="spto_dpricesort" class="sepH_b dp100">
            <label for="spto_pricesort" class="lbl_a">Sort by</label>
            <select id="spto_pricesort" class="large">
            <option value='0'>Price -  High to Low</option>
            <option value='1'>Price -  Low to High</option>
            <option value='2'>Time on market - Newest First</option>
            <option value='3'>Time on market - Oldest First</option>
            <option value='4'>Lot Size - Largest First</option>
            <option value='5'>Lot Size - Smallest First</option>
            </select>
            </div>
            <?php } ?>








            </form>
			<img id="spto_loader" src=<?php echo SPOTON_IDX_IMAGES . '/ajax_blue.gif'; ?> alt="" />
		</div><!-- End Subsection -->
	</div><!-- End Micro Section -->
    







    

	<div class="micro">
		<h4><span class="head-inner">Search by MLS#</span></h4>
		<div class="sub_section cf">   
			<label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls1" /><p/>    
            <label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls2" /><p/>  
            <label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls3" /><p/>  
            <label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls4" /><p/>  
            <label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls5" /><p/>  
            <label>MLS #&nbsp;</label>
			<input style="width:140px" type="text" id="spto_mls6" /><p/>
            <button type="button" class="btn btn_medium btn_dL fl sepV_a" id="spto_lnsearchbutton">Search MLS#</button>
			<button type="button" class="btn btn_bL fl" id="spto_lnresetsubmit">Reset</button>
		</div>
	</div>


  </div>
</div>
</div><!-- End Spot-on Content Sidebar Wrapper -->
</div><!-- End Spot-on Main Section -->
<input type="hidden" id="spto_location" name="spto_location"/> 


<script type="text/javascript">
    jQuery(document).ready(function(){
		jQuery(".mAccordion div.sub_section").each (function() {
			jQuery(this).css("height", jQuery(this).height());
		});	
		jQuery('.mAccordion').microAccordion({
			openSingle: false,
			closeFunction: function (obj) {
				obj.slideUp('fast');
			},
			toggleFunction: function (obj) {
				obj.slideToggle('fast');
			}
		});
		jQuery(".tabsS").flowtabs("div.tabs_content > div");
		//lga_contentSlider.init();
		//listingcourtesy();
	});
</script>

<script type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0"></script>
<script type="text/javascript">
var submitClick=0;
var PagingClick=0;
var host="<?php echo home_url(); ?>";
var PKey= "<?php echo $spoton_pkey; ?>";
var logopath="<?php echo SPOTON_IDX_IMAGES; ?>";
var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
//var serviceUri = "http://127.0.0.1:81/odataservice.svc/";
//num.toString().replace(/\$|\,/g, '');
var imageRecord=0;
var lnsubid = 0;
var mapimageRecord=0;
var maplnsubid = 0;
var xpiomap = null;
var xpioLayer = null;
var mapclick=0;
var listclick=0;
var mapview='<?php echo $searchview[0]; ?>';
var listview='<?php echo $searchview[1]; ?>';
var defaultlatitiude= '<?php echo $defaultlocation[0]; ?>';
var defaultlongitude= '<?php echo $defaultlocation[1]; ?>';
var zoomlevel='<?php echo $searchview[3]; ?>';

// State
var hidestate = <?php echo $searchview[4]; ?>;
var filterstate = <?php echo $sff_filters['State']; ?>;
// County
var hidecounty = '<?php echo $searchview[5]; ?>';
var filtercounty = <?php echo $sff_filters['County']; ?>;
// City
var hidecity = '<?php echo $searchview[6]; ?>';
var filtercity = <?php echo $sff_filters['City']; ?>;
var hidezipcode='<?php echo $searchview[7]; ?>';
// Price From
var hidepricefrom = '<?php echo $searchview[8]; ?>';
// Price To
var hidepriceto = '<?php echo $searchview[9]; ?>';
// Beds
var hidebeds = '<?php echo $searchview[10]; ?>';
// Baths
var hidebath = '<?php echo $searchview[11]; ?>';
// Acreage From
var hideaceragefrom = '<?php echo $searchview[12]; ?>';
// Acreage To
var hideacerageto = '<?php echo $searchview[13]; ?>';
// Square Feet
var hidesquarefeet = '<?php echo $searchview[14]; ?>';
// Time on Market
var hidetimeonmarket = '<?php echo $searchview[15]; ?>';
// Property Type
var hideproperty='<?php echo $searchview[16]; ?>';
var filterproperty = <?php echo $sff_filters['Property Type']; ?>;
// Sort by
var hidesortby='<?php echo $searchview[17]; ?>';
// Area
var hideArea = '<?php echo $searchview[18]; ?>';
var filterArea = <?php echo $sff_filters['Area']; ?>;
// Sub-Division
var hideSubDiv = '<?php echo $searchview[19]; ?>';
var filterSubDiv = <?php echo $sff_filters['SubDivision']; ?>;
// School District
var hideSchDist = '<?php echo $searchview[20]; ?>';
var filterSchDist = <?php echo $sff_filters['School District']; ?>;
// Elementary School
var hideEleSch = '<?php echo $searchview[21]; ?>';
var filterEleSch = <?php echo $sff_filters['Elementary School']; ?>;
// Middle School
var hideJuHiSch = '<?php echo $searchview[22]; ?>';
var filterJuHiSch = <?php echo $sff_filters['Middle School']; ?>;
// High School
var hideHiSch = '<?php echo $searchview[23]; ?>';
var filterHiSch = <?php echo $sff_filters['High School']; ?>;
var houseIcon = 'https://xpioimages.blob.core.windows.net/spotonconnect/house-pin.png';



function moreinfolisting(ln)
		{  
            var lnvalue="";
           
           
            var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
            var host="";
            host="<?php echo home_url(); ?>";
            host+='/property/mls-'; 
            var requestUri = serviceUri + "GenericRetsDetailListings?$filter=PKey eq "+ PKey +" and (LN eq '" + ln +"')&$select=Logo,OffNam,Disclaimer,AgNam,IntFea,Price,WatFront,PropView,PropType,LN,Acre,YrBuilt,HouNo,HiSch,EleSch,JuHiSch,PropSub,SchDist,Stre,ExtFea,StrSuff,DirSuff,City,State,Zip,Remark,Style,AppThtStay,SquFeet";
			OData.defaultHttpClient.enableJsonpCallback = true;      
			OData.read(requestUri, function (data, request) {
			var LN =''; var SquFeet =''; var Price =''; var Acre =''; var Beds =''; var Baths =''; var Loc =''; var Remark ='';//var HiSch ='';var JuHiSch ='';var EleSch ='';
		    var adrress=""; var AgName=""; var disclaimer="";
                
            
            for (var i = 0; i < data.results.length; i++) {
			 
				LN = data.results[i].LN;            
				SquFeet = data.results[i].SquFeet; 
				Price = FormatCurrency(data.results[i].Price);  
				Acre = data.results[i].Acre;  
				Beds = data.results[i].Beds;  
				Baths = data.results[i].Baths;   
				Remark = data.results[i].Remark;   
				PropType=data.results[i].PropType; 
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
                HouNo=data.results[i].HouNo;
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
               
            
				var property=""; var Property_view="";var Water_front="";var Style_span="";var YrBuilt_span="";
              
                property+='<div>';
                
                if(PropView !="")
               
				Property_view='Property view : '+PropView;
              
                
                if(WaterFront !="")
				Water_front='Water Front : '+WaterFront;
                
                if(Style !="")
                Style_span='Style : '+Style;
                
               // property+='<div><span>'+Style_span+'</span><span style="padding-left:50px;">'+Property_view+'</span></div>';
                
                if(YrBuilt !="")
                YrBuilt_span='Year Built : '+YrBuilt;
                
                //property+='<div ><span>'+YrBuilt_span+'</span><span style="padding-left:50px;">'+Water_front+'</span></div>';
                
                if(Remark !="")
                {
                    property+='<div>Description : '+Remark+'</div>';
                }
                else
                {
                     property+='<div>Description : Not Available</div>';
                }
                   
                property+='</div>';   
               if((jQuery.cookie("ln") != ln)&&(jQuery.cookie("ln") != null))
                {
                    
                    //alert('cook');
                    lnvalue=jQuery.cookie("ln");
                    
                    jQuery("#inline_"+lnvalue).hide();
                    jQuery.cookie("ln",null,{ expires: -1, path: '/' });
                    jQuery.cookie("ln",ln,{ expires:1, path: '/' });
                    jQuery("#inline_"+ln).html(property).show('slow');
                }
               else if(jQuery.cookie("ln") == null )
                {   //alert('hi');
                    jQuery.cookie("ln",ln,{ expires:1, path: '/' });
                    jQuery("#inline_"+ln).html(property).show('slow');
                }
              else if(jQuery.cookie("ln") == ln)
                {
                     //alert('curr');
                    jQuery("#inline_"+ln).hide();
                    jQuery.cookie("ln",null,{ expires: -1, path: '/' });
                }
			   
                
			}
			}, function(err){
				
			});
		
          
		}
function listing(ln)
		{
            var host="";
            host="<?php echo home_url(); ?>";
            host+='/property/mls-'; 
			var requestUri = serviceUri + "GenericRetsDetailListings?$filter=PKey eq "+ PKey +" and (LN eq '" + ln +"')&$select=Logo,OffNam,Disclaimer,AgNam,IntFea,Price,WatFront,PropView,PropType,Status,LN,Acre,Baths,YrBuilt,HouNo,HiSch,Beds,EleSch,JuHiSch,PropSub,SchDist,Stre,ExtFea,StrSuff,DirSuff,City,State,Zip,Remark,Style,AppThtStay,SquFeet,AddrYN";
			OData.defaultHttpClient.enableJsonpCallback = true;      
			OData.read(requestUri, function (data, request) {
			var LN =''; var SquFeet =''; var Price ='';var logo='';var disclaimer='';var Disclaimer=''; var Acre ='';var shoolinfo=''; var Status=''; var PropView=''; var YrBuilt=''; var Beds =''; var Baths =''; var Loc =''; var Remark ='';var HiSch ='';var JuHiSch ='';var EleSch ='';var SchDist=''; var Addryn="";
			for (var i = 0; i < data.results.length; i++) {
			  
				LN = data.results[i].LN;            
				SquFeet = data.results[i].SquFeet; 
				Price = FormatCurrency(data.results[i].Price);  
				Acre = data.results[i].Acre;  
				Beds = data.results[i].Beds;  
				Baths = data.results[i].Baths;   
				Remark = data.results[i].Remark;   
				PropType=data.results[i].PropType;
                Status=data.results[i].Status;
                YrBuilt=data.results[i].YrBuilt;
                Stre=data.results[i].Stre;
                HouNo=data.results[i].HouNo;
                StrSuff=data.results[i].StrSuff;
                DirSuff=data.results[i].DirSuff;
                City=data.results[i].City;
                State=data.results[i].State;
                Zip=data.results[i].Zip;
				SchDist=data.results[i].SchDist;
                HiSch=data.results[i].HiSch;
                JuHiSch=data.results[i].JuHiSch;
                EleSch=data.results[i].EleSch;
                AppThtStay=data.results[i].AppThtStay;
                ExtFea=data.results[i].ExtFea;
                Style=data.results[i].Style;
                PropSub=data.results[i].PropSub;
                IntFea=data.results[i].IntFea;
                PropView=data.results[i].PropView;
                WaterFront=data.results[i].WatFront;
                logo=data.results[i].Logo;
                Disclaimer=data.results[i].Disclaimer;
                Addryn=data.results[i].AddrYN;
                
                if(logo == '') 
                { 
                 disclaimer+='<img width="30px" height="30px" src="'+logo+'"></img>';
                } 
                disclaimer+='"'+Disclaimer+'"';
                document.getElementById("disclaimerdiv").innerHTML=disclaimer;
                
                host+=LN.toLowerCase()+'-'+HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+'-'+Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+'-'+StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+'-'+DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+'-'+State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+'-'+Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase();
                jQuery('#view_more').html("<a href='"+host+"' target='_blank'>View More..</a>");
                    
                adrress=HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+' '+Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+' '+StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+' '+DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, " ").toLowerCase()+' '+State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()+' '+Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase();
                 
                if(PKey == 3)
                {
                    if(Addryn == 'Y')
                    {
                        jQuery("#addressline").text(adrress);
                    }
                }
                else
                {
                      jQuery("#addressline").text(adrress);
                }
                
                shoolinfo+='<div style="display:table;width:auto;">';
                if(SchDist !="")
                shoolinfo+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">School District :</div><div style="float:left;display:table-column;width:auto;">'+SchDist+'</div></div>';
                if(EleSch !="")
                shoolinfo+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Elementary School :</div><div style="float:left;display:table-column;width:auto;">'+EleSch+'</div></div>';
                if(JuHiSch !="")
                shoolinfo+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Jr.High School :</div><div style="float:left;display:table-column;width:auto;">'+JuHiSch+'</div></div>';
                if(HiSch !="")
                shoolinfo+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">High School :</div><div style="float:left;display:table-column;width:auto;">'+HiSch+'</div></div>';
                
                shoolinfo+='</div>';
                document.getElementById("school_details").innerHTML=shoolinfo;
                
                var Fetures="";
                Fetures+='<div style="display:table;width:auto;">';
                
                if(Style !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Style :</div><div style="float:left;display:table-column;width:auto;">'+Style+'</div></div>';
                if(AppThtStay !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Appliances That stay :</div><div style="float:left;display:table-column;width:auto;">'+AppThtStay+'</div></div>';
                if(ExtFea !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Exterior Features :</div><div style="float:left;display:table-column;width:auto;">'+ExtFea+'</div></div>';
                if(PropSub !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Property Subtype :</div><div style="float:left;display:table-column;width:auto;">'+PropSub+'</div></div>';
                if(IntFea !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Interior Features :</div><div style="float:left;display:table-column;width:auto;">'+IntFea+'</div></div>';
                if(PropView !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Property View :</div><div style="float:left;display:table-column;width:auto;">'+PropView+'</div></div>';
                
                if(WaterFront !="")
                Fetures+='<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Water Front :</div><div style="float:left;display:table-column;width:auto;">'+WaterFront+'</div></div>'; 
                Fetures+='</div>';
                
                jQuery("#fetureslist").html(Fetures);
              
                var property="";
				property+="<div style='display:table;width:auto;'><div style='display:table-row;'>";
                if(PropType !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Property Type :</div><div style='float:left;display:table-column;width:200px;'>"+PropType+"</div></div>";  
                }
                if(Status !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Status :</div><div style='float:left;display:table-column;width:200px;'>"+Status+"</div></div>";
                }
				 if(Price !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Price :</div><div style='float:left;display:table-column;width:200px;'>"+Price+"</div></div>";  
                }
                
                 if(Beds !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Beds :</div><div style='float:left;display:table-column;width:200px;'>"+Beds+"</div></div>"; 
                }
                 if(Baths !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Baths :</div><div style='float:left;display:table-column;width:200px;'>"+Baths+"</div></div>"; 
                }
                 if(YrBuilt !="")
                {
                    property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>Year Built: :</div><div style='float:left;display:table-column;width:200px;'>"+YrBuilt+"</div></div>";  
                }
                 property+="<div style='display:table-row;'><div style='float:left;display:table-column;width:120px;'>MLS # :</div><div style='float:left;display:table-column;width:200px;'>"+LN+"</div></div>";
				 property+="</div></div>";
                
                
				document.getElementById("spto_listingdetails").innerHTML=property;
				document.getElementById("spto_listingremarks").innerHTML=Remark;
			}
			}, function(err){
				//alert("Error occurred " + err.message);
			});
		}

        function image(ln)

		{    
            jQuery("#viewall_photolink").empty();	
			imageRecord = 0;
			lnsubid = 0;

			var Imageurl='';var imagresult='';
			jQuery.ajax({
                dataType: 'jsonp',
                url: "http://realestateservice.cloudapp.net/OdataService.svc/RetsListingImages?$filter=( PKey eq "+ PKey +" and LN eq '" + ln + "')&$inlinecount=allpages&$format=json",
                jsonp: '$callback',
                success: function (data) {


                
                
				var result='';	
				imageRecord=data.d.__count;		
				if(imageRecord==0)
				{
					result+="<img id='im0' class='imghidden' style='width:280px;height:195px;border:1px solid #D0D0D0;padding:6px;' src='http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg'>";	 
				}
				else



				{   imagresult+='<span id="gallery">';
                    for (var i = 0; i < data.d.results.length; i++) 
                    {
                             result+="<img id='im"+i+"'  style='width:280px;height:195px;border:1px solid #D0D0D0;padding:5px;' class=imghidden src="+data.d.results[i].ImageURL.replace("mapslideshowimages", "mapthumbnailimages").replace("/gamls/l/", "/gamls/m/")+">";     


					

                    Imageurl=data.d.results[i].ImageURL;
                            if(i==0)
                                {
                                    imagresult+='<a  href="'+Imageurl+'" title="" >View all photos.</a>';
                                }
                                else
                                {
                                    imagresult+='<a  href="'+Imageurl+'" title="" ></a>'; 
                                }
                
                  }

                  imagresult+='</span>';
            }

                    
                    
				
				jQuery("#viewall_photolink").html(imagresult); 
				jQuery("#spto_imageslider").empty();	
				jQuery("#spto_imageslider").append(result); 
				aspectRatio(0);
                jQuery("#gallery a").photoSwipe({
                enableMouseWheel: false,
                captionAndToolbarAutoHideDelay : 0,
                captionAndToolbarShowEmptyCaptions : false,
                imageScaleMethod : 'fitNoUpscale',
                enableKeyboard: false
               
                });
				}				
			});
		}
        
        jQuery('#ne').click(function() {        
            if(imageRecord>0)
            {
                lnsubid=lnsubid+1;		
                if(lnsubid>=imageRecord)
                    lnsubid=imageRecord-1;
                
                var hide=lnsubid;
                --hide;
                slideshow(lnsubid,hide);
            }
		});
		        
        function pre(){
			if(imageRecord>0)
            {
                lnsubid=lnsubid-1;		
                if(lnsubid<=0)
                    lnsubid=0;
                
                var hide=lnsubid;
                ++hide;
                slideshow(lnsubid,hide);	
            }
        }
        
        function slideshow(lnsubid,hide)
        {                        
            aspectRatio(lnsubid);
            jQuery('#im'+hide).removeClass("imgvisible");
            jQuery('#im'+hide).addClass("imghidden");
        }
        
        function aspectRatio(lnsubid)
        {
            jQuery('#im'+lnsubid).removeAttr("width");
            jQuery('#im'+lnsubid).removeAttr("height");            
            jQuery('#im'+lnsubid).removeClass("imghidden");            
            var browserWidth=jQuery(window).width();
            browserWidth=browserWidth-40;
            var imgWidth=jQuery('#im'+lnsubid).width();
            var imgheight=jQuery('#im'+lnsubid).height();
            var aspectHeight='300';          
            
            if(imgWidth>browserWidth)
            {
                jQuery('#im'+lnsubid).attr("width", browserWidth+"px");
                aspectHeight=480*browserWidth/640;
                jQuery('#im'+lnsubid).attr("height", aspectHeight+"px");
            }
                
            if(imgheight>300)
                jQuery('#im'+lnsubid).attr("height", aspectHeight+"px");
            
            jQuery('#im'+lnsubid).addClass("imgvisible"); 
        }
     
		
		function hideProgress()
		{
			jQuery("#spto_loader").removeClass("spto_loadervisible");
			jQuery("#spto_loader").addClass("spto_loaderhidden");
		}
		
		function showProgress()
		{
			jQuery("#spto_loader").removeClass("spto_loaderhidden");
			jQuery("#spto_loader").addClass("spto_loadervisible");
		}
		
		// PLACE ODATA LOAD
		function loadState()
		{
			jQuery("#spto_state >option").remove();
			jQuery("#spto_state").append('<option value=0>All</option>');
			var query ="$filter=PKey eq "+ PKey;   
			var requestUri = serviceUri + "RetsCounties?" + query +"&$orderby=State&$select=State";
			OData.defaultHttpClient.enableJsonpCallback = true;
			OData.read(requestUri, function (data, request) {
			for (var i = 0; i < data.results.length; i++) {
				var previous=i;
				--previous; 
				if(previous==-1)
				{
					previous=0;
					jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+'>'+data.results[i].State+'</option>')
				}
				if(data.results[previous].State!=data.results[i].State)
				{
					jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+'>'+data.results[i].State+'</option>');
				}
			}
			}, function(err){
				//alert("Error occurred " + err.message);
			});
		}

		function loadCounty(state)
		{
			jQuery("#spto_county >option").remove();
			jQuery("#spto_county").append('<option value=0>All</option>');
						
			var query ="";
			if(state==0)
				query="$filter=PKey eq "+ PKey;
			else
				query="$filter=PKey eq "+ PKey +" and State eq '"+state+"'";   
			var requestUri = serviceUri + "RetsCounties?" + query +"&$orderby=County&$select=County";
			OData.defaultHttpClient.enableJsonpCallback = true;
			OData.read(requestUri, function (data, request) {
			for (var i = 0; i < data.results.length; i++) {
                var previous=i;
				--previous; 
				if(previous==-1)
				{
					previous=0;
					jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+'>'+data.results[i].County+'</option>')
				}					
				if(data.results[previous].County!=data.results[i].County)
				{
					jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+'>'+data.results[i].County+'</option>');
				}
			}
			}, function(err){
				//alert("Error occurred " + err.message);
			});        
		}
    
		function loadCity(state,county)
		{
			jQuery("#spto_city >option").remove();
			jQuery("#spto_city").append('<option value=0>All</option>');
			
			var query =""        
			if(state==0 && county==0)
				query="$filter=PKey eq "+ PKey;            
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
					jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+'>'+data.results[i].City+'</option>')
				}
					
				if(data.results[previous].City!=data.results[i].City)
				{
					jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+'>'+data.results[i].City+'</option>');
				}
			}
			}, function(err){
				//alert("Error occurred " + err.message);
			});
		}
        
        function loadarea()
        {
            jQuery("#spto_area >option").remove();
            jQuery("#spto_area").append('<option value=0>All</option>');
            
            var query ="$filter=PKey eq "+ PKey;   
            var requestUri = serviceUri + "RetsAreas?" + query +"&$orderby=Area&$select=Area";
            OData.defaultHttpClient.enableJsonpCallback = true;
            OData.read(requestUri, function (data, request) {
            for (var i = 0; i < data.results.length; i++) {               
					jQuery("#spto_area").append('<option value='+data.results[i].Area.replace(/\s+/g, '_')+'>'+data.results[i].Area+'</option>');
            }
            }, function(err){
                //alert("Error occurred " + err.message);
            });
        }
        
        function loadschooldistrict()
        {
            jQuery("#spto_schooldistrict >option").remove();
            jQuery("#spto_schooldistrict").append('<option value=0>All</option>');
            
            var query ="$filter=PKey eq "+ PKey;   
            var requestUri = serviceUri + "RetsSchoolDistricts?" + query +"&$orderby=SchoolDistrict&$select=SchoolDistrict";
            OData.defaultHttpClient.enableJsonpCallback = true;
            OData.read(requestUri, function (data, request) {
            for (var i = 0; i < data.results.length; i++) {
                jQuery("#spto_schooldistrict").append('<option value='+data.results[i].SchoolDistrict.replace(/\s+/g, '_')+'>'+data.results[i].SchoolDistrict+'</option>');
            }
            }, function(err){
                //alert("Error occurred " + err.message);
            });
        }
        
        function loadelementaryschool()
        {
            jQuery("#spto_elementaryschool >option").remove();
            jQuery("#spto_elementaryschool").append('<option value=0>All</option>');
            
            var query ="$filter=PKey eq "+ PKey;   
            var requestUri = serviceUri + "RetsElementarySchools?" + query +"&$orderby=ElementarySchool&$select=ElementarySchool";
            OData.defaultHttpClient.enableJsonpCallback = true;
            OData.read(requestUri, function (data, request) {
            for (var i = 0; i < data.results.length; i++) {
                jQuery("#spto_elementaryschool").append('<option value='+data.results[i].ElementarySchool.replace(/\s+/g, '_')+'>'+data.results[i].ElementarySchool+'</option>');
            }
            }, function(err){
                //alert("Error occurred " + err.message);
            });
        }
        
        function loadmiddleschool()
        {
            jQuery("#spto_middleschool >option").remove();
            jQuery("#spto_middleschool").append('<option value=0>All</option>');
            
            var query ="$filter=PKey eq "+ PKey;   
            var requestUri = serviceUri + "RetsMiddleSchools?" + query +"&$orderby=MiddleSchool&$select=MiddleSchool";
            OData.defaultHttpClient.enableJsonpCallback = true;
            OData.read(requestUri, function (data, request) {
            for (var i = 0; i < data.results.length; i++) {
                jQuery("#spto_middleschool").append('<option value='+data.results[i].MiddleSchool.replace(/\s+/g, '_')+'>'+data.results[i].MiddleSchool+'</option>');
            }
            }, function(err){
                //alert("Error occurred " + err.message);
            });
        }
        
        function loadhighschool()
        {
            jQuery("#spto_highschool >option").remove();
            jQuery("#spto_highschool").append('<option value=0>All</option>');
            
            var query ="$filter=PKey eq "+ PKey;   
            var requestUri = serviceUri + "RetsHighSchools?" + query +"&$orderby=HighSchool&$select=HighSchool";
            OData.defaultHttpClient.enableJsonpCallback = true;
            OData.read(requestUri, function (data, request) {
            for (var i = 0; i < data.results.length; i++) {
                jQuery("#spto_highschool").append('<option value='+data.results[i].HighSchool.replace(/\s+/g, '_')+'>'+data.results[i].HighSchool+'</option>');
            }
            }, function(err){
                //alert("Error occurred " + err.message);
            });
        } 
        
		jQuery(document).ready(function(){            

			hideProgress();
            
            if (hideproperty != 1 && filterproperty != 1)

			    loadProperty();
                
            if (hidestate != 1 && filterstate != 1)

			    loadState();

            if (hidecounty != 1 && filtercounty != 1)


			    loadCounty(0);
            
            if (hidecity != 1 && filtercity != 1)

			    loadCity(0,0);
            
            //loadsubdiv();
			if (filterArea != 1)
				loadarea();
			if (filterSchDist != 1)
				loadschooldistrict();
			if (filterEleSch != 1)
	            loadelementaryschool();
			if (filterJuHiSch != 1)
	            loadmiddleschool();
			if (filterHiSch != 1)
	            loadhighschool();
            
            showProgress();
	        //serviceCall(createQuery()+"&",0,0);
            
            jQuery("#spto_state").change(onChangestate);
			jQuery("#spto_county").change(onSelectChangeCounty);     
			jQuery("#spto_city").change(onChangecity);
			jQuery("#spto_pricefrom").change(onChangepricefrom);
			jQuery("#spto_priceto").change(onChangepriceto);
			jQuery("#spto_bedrooms").change(onChangebedrooms);
			jQuery("#spto_bathrooms").change(onChangebathrooms);
			jQuery("#spto_squarefeet").change(onChangesquarefeet);
            jQuery("#spto_acreagefrom").change(onChangeacreagefrom);
            jQuery("#spto_acreageto").change(onChangeacreageto);
			jQuery("#spto_market").change(onChangemarket);
			jQuery("#spto_PropertyType").change(onChangePropertyType);
            jQuery("#spto_pricesort").change(onChangePriceSort); 
			//jQuery("#spto_subdiv").change(onChangeSubDiv);  
			jQuery("#spto_area").change(onChangeArea);  
			jQuery("#spto_schooldistrict").change(onChangeSchoolDistrict);  
			jQuery("#spto_elementaryschool").change(onChangeElementarySchool);  
			jQuery("#spto_middleschool").change(onChangeMiddleSchool);  
			jQuery("#spto_highschool").change(onChangeHighSchool); 
			jQuery("#spto_searchbutton").click(onclickSearchSubmit); 
			jQuery("#spto_resetsubmit").click(onclickResetsubmit);	
            jQuery("#spto_lnsearchbutton").click(onclicklnSearchSubmit);	
            jQuery("#spto_lnresetsubmit").click(onclicklnResetsubmit);	
           // jQuery("#spto_list").click(onlisttabclick);	
            //jQuery("#spto_map").click(onmaptabclick);
		});
		
        function onclickfacncybox()
        {
            jQuery("#fancybox-thumb").clear();
        }
        
        function onmaptabclick()
        {
            mapclick=1;
            listclick=0;
        }
        
        function onlisttabclick()
        {
            mapclick=0;
            listclick=1;
        }
        
		function loadProperty()
		{
			jQuery("#spto_PropertyType >option").remove();
			var query ="$filter=PKey eq "+ PKey;   
			var requestUri = serviceUri + "RetsProperties?" + query +"&$orderby=PropName&$select=PropType,PropName";
			OData.defaultHttpClient.enableJsonpCallback = true;
			OData.read(requestUri, function (data, request) {
			for (var i = 0; i < data.results.length; i++) {
				jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+'>'+data.results[i].PropName+'</option>');
			   
			}
			}, function(err){
				//alert("Error occurred " + err.message);
			});
		}
    
		function onclickResetsubmit()
		{
			// The user wants to use normal search form again
			jQuery('input[name="is_qsw"]').val(0);

			if (hideproperty != 1 && filterproperty != 1)
			    loadProperty();
			else if (filterproperty == 1)
				jQuery("#spto_PropertyType").val('<?php echo $sff_def['Property Type']; ?>');
                
            if (hidestate != 1 && filterstate != 1)

			    loadState();
			else if (filterstate == 1)
				jQuery("#spto_state").val('<?php echo $sff_def['State']; ?>');
    
            if (hidecounty != 1 && filtercounty != 1)

			    loadCounty(0);
			else if (filtercounty == 1)
				jQuery("#spto_county").val('<?php echo $sff_def['County']; ?>');
            
            if (hidecity != 1 && filtercity != 1)

			    loadCity(0,0);
			else if (filtercity == 1)
				jQuery("#spto_city").val('<?php echo $sff_def['City']; ?>');
			
            //loadsubdiv();
			if (filterArea != 1)
				loadarea();
			else
				jQuery("#spto_area").val('<?php echo $sff_def['Area']; ?>');
			if (filterSchDist != 1)
				loadschooldistrict();
			else
				jQuery("#spto_schooldistrict").val('<?php echo $sff_def['School District']; ?>');
			if (filterEleSch != 1)
	            loadelementaryschool();
			else
				jQuery("#spto_elementaryschool").val('<?php echo $sff_def['Elementary School']; ?>');
			if (filterJuHiSch != 1)
	            loadmiddleschool();
			else
				jQuery("#spto_middleschool").val('<?php echo $sff_def['Middle School']; ?>');
			if (filterHiSch != 1)
	            loadhighschool();
			else
				jQuery("#spto_highschool").val('<?php echo $sff_def['High School']; ?>');
            
			jQuery("#spto_zipcode").val('');   
            jQuery("#spto_subdiv").val('<?php echo $sff_def['SubDivision']; ?>');

			jQuery("#spto_pricefrom").val('<?php echo $sff_def['Price From']; ?>')  
			jQuery("#spto_priceto").val('<?php echo $sff_def['Price To']; ?>')
			jQuery("#spto_bedrooms").val('<?php echo $sff_def['Beds']; ?>'); 
			jQuery("#spto_bathrooms").val('<?php echo $sff_def['Baths']; ?>'); 
			jQuery("#spto_squarefeet").val('<?php echo $sff_def['Square Feet']; ?>');
			jQuery("#spto_market").val('<?php echo $sff_def['Time on Market']; ?>'); 
			resultPagingClear(); 
		}  
        
		function onclickSearchSubmit()
		{
			// The user wants to search normally again
			jQuery('input[name="is_qsw"]').val(0);

           submitClick=1;  
        
           //if(listview==0 || mapview==0)
		    showProgress();

         ///  if(mapview==0)
         //  {
              clearpushPin();
              removeInfobox();
              addPushpins(createQuery()+"&");
        //   }            
           
           /*if(listview==0)
		    serviceCall(createQuery()+"&",0,0); */
		}    
    
		//  Get HostName
		function hostName()
		{
		   return host+'/property/mls-'; 
		}
        
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
    
		function formatAcres(acr)
		{
			 var val = parseFloat(acr);
			 return val.toFixed(2);
		}    
		
		function NWMLSlogo()
		{        
			return logopath + "images/nwmls_logo.png";
		}
    
		function resultPagingClear()
		{
			submitClick=0;
			PagingClick=0;
			//jQuery("#paging").empty();
			//jQuery("#resultsArea").empty();
			//jQuery("#resultscount").empty();   
            jQuery('#mapcount').html("");
            jQuery('#listingcount').html("");
            
            if(mapview==0)
            {
              removeInfobox();
              clearpushPin();              
            }
		}
		
		function mapcount(count)
		{			
			if(count>500)
				jQuery('#mapcount').html("The first 500 properties are displayed out of the " + FormatCurrency(count).replace("$","") + " properties found in this search.");
			else
				jQuery('#mapcount').html(FormatCurrency(count).replace("$","") + " properties found in this search.");
		}
    
		function onChangecity(){resultPagingClear();}
		function onChangepricefrom(){resultPagingClear();}
		function onChangepriceto(){resultPagingClear();}
		function onChangebedrooms(){resultPagingClear();}
		function onChangebathrooms(){resultPagingClear();}
		function onChangesquarefeet(){resultPagingClear();} 
        function onChangeacreagefrom(){resultPagingClear();}
        function onChangeacreageto(){resultPagingClear();}
		function onChangemarket(){resultPagingClear();}
		function onChangePropertyType(){resultPagingClear();}
        function onChangePriceSort(){resultPagingClear();}
		function onChangeSubDiv(){resultPagingClear();}
		function onChangeArea(){resultPagingClear();}
		function onChangeSchoolDistrict(){resultPagingClear();}
		function onChangeElementarySchool(){resultPagingClear();}
		function onChangeMiddleSchool(){resultPagingClear();}
		function onChangeHighSchool(){resultPagingClear();}

		
        // PLACE SELECTION
		function onChangestate()
		{
			resultPagingClear();
			var selected = jQuery("#spto_state option:selected");   
			var state = selected.val();
            if(hidecounty!=1)
			    loadCounty(jQuery("#spto_state").val());  
                
            if(hidecity!=1)
            {
			if(state==0)
				loadCity(0,0);
            }
		}
		
		function onSelectChangeCounty(){
			resultPagingClear();
			var selected = jQuery("#spto_county option:selected");   
			var county = selected.val();
            if(hidestate!=1)
			    loadCity(jQuery("#spto_state").val(),county.replace(/_+/gi, " "));
		}
        
        // PAGING FUNCTION
		function loadPageNo(no)
		{
			jQuery("#paging").empty();
			if(no>10)
				no=10;
				
			for (var i = 1; i <= no; i++) {
			var $ctrl = jQuery('<input/>').attr({ type: 'submit', id:i,value:i}).addClass("submitLink");
			jQuery("#paging").append($ctrl);
			};
			
			for (var i = 1; i <= no; i++) {
			jQuery("#"+i).click(onclickPage);   
			} 
			
			//jQuery("#1").addClass("submitClick");
		}   
    
		function removePagingHighlight(no)
		{
		   for (var i = 1; i <= 10; i++) {
		   jQuery("#"+i).removeClass("submitClick");   
		   }  
		   
		   if(no==0)
			jQuery("#1").addClass("submitClick"); 
		}    
    
		function onclickPage()
		{
		   showProgress();
		   removePagingHighlight(1);  
		   jQuery(this).addClass("submitClick");	   
		   PagingClick=jQuery(this).val()-1;
			// QSW or normal search
		   var is_qsw = jQuery('input[name="is_qsw"]').val();
			if (1 == is_qsw)
				serviceCall(createPostQuery()+"&",(jQuery(this).val()-1)*50,1);
			else
				serviceCall(createQuery()+"&",(jQuery(this).val()-1)*50,1);
		}     
        
        function getMultipleLN()
        {
            var lnValues="";
            for(var ln=1;ln<=6;ln++)
            {
                if(jQuery.trim(jQuery("#spto_mls"+ln).val())!='')
                {
                    lnValues +=" LN eq '" + jQuery.trim(jQuery("#spto_mls"+ln).val()) +"' or "; 
                }
            }
            
            if(lnValues.length > 2)
				lnValues =lnValues.substring(0,lnValues.length-3);
                
            return lnValues;
        }
        
        function clearMultipleLN()
        {
             for(var ln=1;ln<=6;ln++)
            {
                jQuery("#spto_mls"+ln).val("");
            }
        }
	    
        // QUERY BUILDER

		function createPostQuery()
		{
			var postQuery = "<?php echo $post_query; ?>";
			return postQuery;
		}

		function createQuery()
		{
		   var filterquery="$filter=";
			
		   var state ='';	
           if(hidestate!=1)
           {
		   if(jQuery("#spto_state").val()!='0')			
				state = " and State eq '"+ jQuery("#spto_state").val() +"'";
           }
                   
           var Area='';
           if(hideArea!=1)
           {
            var ar=jQuery("#spto_area").val();
		        if(ar!='0')
                {                
				Area = " and Area eq '"+ ar.replace(/_+/gi, " ") +"'";
                }
           }
            
           var SubDiv='';
            if(hideSubDiv!=1)
           {
            var sud=jQuery.trim(jQuery("#spto_subdiv").val());
		        if(sud!='')	
                {                
				SubDiv = " and substringof('"+ sud.replace(/_+/gi, " ") +"',SubDiv) ";
                }
           }
            
           var SchDist='';
            if(hideSchDist!=1)
           {
           var sd=jQuery("#spto_schooldistrict").val();
		        if(sd!='0')	
                {                
				SchDist = " and SchDist eq '"+ sd.replace(/_+/gi, " ") +"'";
                }
           }
            
           var EleSch='';
            if(hideEleSch!=1)
           {
           var es=jQuery("#spto_elementaryschool").val();
		        if(es!='0')		
                {               
				EleSch = " and EleSch eq '"+ es.replace(/_+/gi, " ") +"'";
                }
           }
            
           var JuHiSch='';
            if(hideJuHiSch!=1)
           {
           var jhs=jQuery("#spto_middleschool").val();
		        if(jhs!='0')	
                {                
				JuHiSch = " and JuHiSch eq '"+ jhs.replace(/_+/gi, " ") +"'";
                }
           }
        
           var HiSch='';
            if(hideHiSch!=1)
           {
           var hs=jQuery("#spto_highschool").val();
		        if(hs!='0')
                {                
				HiSch = " and HiSch eq '"+ hs.replace(/_+/gi, " ") +"'";
                }
           }
        
		   var pricefrom='';
           if(hidepricefrom!=1)
           {
		   if(jQuery("#spto_pricefrom").val()!='0')
				pricefrom=" and Price ge "+jQuery("#spto_pricefrom").val();
		   }
        
		   var priceto ='';
           if(hidepriceto!=1)
           {
		   if(jQuery("#spto_priceto").val()!='99999999')
				priceto = " and Price le "+jQuery("#spto_priceto").val();
		   }
        
		   var bedrooms ='';
           if(hidebeds!=1)
           {
		   if(jQuery("#spto_bedrooms").val()!='0')
				bedrooms=" and Beds ge "+jQuery("#spto_bedrooms").val();
           }
			
		   var bathrooms ='';
           if(hidebath!=1)
           {
		   if(jQuery("#spto_bathrooms").val()!='0')
				bathrooms =" and Baths ge "+jQuery("#spto_bathrooms").val();
           }
		   
		   var squarefeet ='';
           if(hidesquarefeet!=1)
           {
		   if(jQuery("#spto_squarefeet").val()!='0')
			   squarefeet =" and SquFeet ge "+jQuery("#spto_squarefeet").val();
           }
               
            var acreagefrom ='';
            if(hideaceragefrom!=1)
            {
            if(jQuery("#spto_acreagefrom").val()!='0')
                acreagefrom =" and Acre ge "+jQuery("#spto_acreagefrom").val();
            }
            
            var acreageto ='';
            if(hideacerageto!=1)
            {            
            if(jQuery("#spto_acreageto").val()!='999999') 
                acreageto =" and Acre le "+jQuery("#spto_acreageto").val();
		   	}
            
		   var zipcode="";           
           if(hidezipcode!=1)
           {
           var zip=jQuery.trim(jQuery("#spto_zipcode").val());
		   if(zip!="")
			 zipcode=" and substringof('"+ zip +"',Zip) ";
		   }
        
           var county=""; 
           if(hidecounty!=1)
           {
		   var cou = jQuery("#spto_county").val(); 		     
		   if(cou!="0")
				county=" and County eq '"+ cou.replace(/_+/gi, " ") +"' ";   
           }
		   
           var city="";
           if(hidecity!=1)
           {
		   var cit = jQuery("#spto_city").val(); 		      
		   if(cit!="0")
				city=" and City eq '"+ cit.replace(/_+/gi, " ") +"' ";
		   }
        
			var market='';
            if(hidetimeonmarket!=1)
            {
                <?php date_default_timezone_set('America/Los_Angeles'); ?>
                if(jQuery("#spto_market").val()=="1")        
                    market= " and (CreDate ge DateTime" + "<?php echo "'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-1 day',strtotime(date('Y-m-d,H:i:s')))))."')"; ?>";
                else if(jQuery("#spto_market").val()=="3")
                    market= " and (CreDate ge DateTime" + "<?php echo "'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-3 day',strtotime(date('Y-m-d,H:i:s')))))."')"; ?>";
                else if(jQuery("#spto_market").val()=="7")
                    market= " and (CreDate ge DateTime" + "<?php echo "'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-7 day',strtotime(date('Y-m-d,H:i:s')))))."')"; ?>";
                else if(jQuery("#spto_market").val()=="14")
                    market= " and (CreDate ge DateTime" + "<?php echo "'".str_replace(',','T',date('Y-m-d,H:i:s',strtotime('-14 day',strtotime(date('Y-m-d,H:i:s')))))."')"; ?>";
                else
                    market= "";
			}
            
			var prop="";    
			var property="";
            if(hideproperty!=1)
            {
			var selectedProperties=[];
			jQuery('#spto_PropertyType :selected').each(function(i, selected) {          
				selectedProperties[i] = jQuery(selected).text(); 
				prop+=" or PropType eq '"+selectedProperties[i]+"'";
			}); 
            
			if(prop.length > 2)
				property =" and ("+ prop.replace("or", "") +")";
			}
            
            var location="";
            //if(mapview==0 && submitClick!=2 && listclick==0)
            //{
                var bounds=xpiomap.getBounds(); 
                var north = bounds.getNorth(); 
                var south = bounds.getSouth();
                var east = bounds.getEast();
                var west = bounds.getWest();
                
                location=' and (Lat gt '+ south +' and Lat lt '+ north +' and Lon lt '+ east +' and Lon gt '+ west +')';
                jQuery("#spto_location").val(location);
            //}
            
            /*if(listview==0 && listclick==1)
            {   
                location = jQuery("#spto_location").val();
            }*/
            
            var pricesort= "";
            if(hidesortby!=1)
            {
            if (jQuery("#spto_pricesort").val()=='0')
                pricesort ="&$orderby=Price desc";
            else if(jQuery("#spto_pricesort").val()=='1')
                pricesort ="&$orderby=Price";
            else if(jQuery("#spto_pricesort").val()=='2')
                pricesort ="&$orderby=CreDate desc";
            else if(jQuery("#spto_pricesort").val()=='3')
                pricesort ="&$orderby=CreDate";
            else if(jQuery("#spto_pricesort").val()=='4')
                pricesort ="&$orderby=Acre desc";
            else
                pricesort ="&$orderby=Acre";
			}
            
			var query="";
			var providerKey="PKey eq "+ PKey; 
			
            var multiLN="";
            multiLN=getMultipleLN();
			if(multiLN.length!=0 && submitClick==2)
				query ="$filter="+ providerKey +" and " + multiLN;
			else
				query = filterquery + providerKey + state + county + city + Area + SubDiv + SchDist + EleSch + JuHiSch + HiSch + zipcode + pricefrom + priceto + bathrooms +  bedrooms +  squarefeet + acreagefrom + acreageto + property + market + location + pricesort;       

			return query;
		}
	    
        
        
        function onclicklnSearchSubmit()
        {
            submitClick=2;
            
            //if(listview==0 || mapview==0)
		        showProgress();

            // if(mapview==0)
            //{
              clearpushPin();
              removeInfobox();
              addPushpins(createQuery()+"&");
            //}            
           
           /*if(listview==0)
		       serviceCall(createQuery()+"&",0,0); */
        }
        
        function onclicklnResetsubmit()
        {
			// The user wants to search normally again
			jQuery('input[name="is_qsw"]').val(0);
            //jQuery("#paging").empty();
            //jQuery("#resultsArea").empty();
			//jQuery("#resultscount").empty();   
            jQuery('#mapcount').html("");
            //jQuery('#listingcount').html("");
            clearMultipleLN();
			clearpushPin();
        }
    
        //Map Search        
    
        function getxpiomap() 
        {		
			xpiomap = new Microsoft.Maps.Map(document.getElementById('spotonmap'), {credentials: 'Arutm_QW76zIIxv6-vXrX9l9iK93NjDUdb3OGDbrcpb2_lPkTDx7NPHhbCmggKFy', enableSearchLogo: false, mapTypeId: Microsoft.Maps.MapTypeId.road, showCopyright:false, enableClickableLogo:false, showBreadcrumb:true});
			xpiomap.setView({center: new Microsoft.Maps.Location(defaultlatitiude, defaultlongitude), zoom: parseInt(zoomlevel)});			
			Microsoft.Maps.Events.addHandler(xpiomap, 'viewchangeend', xpiomapdrag);
        }
		
		function removePushpins()
		{	
			var pushpinlist = new Array();
			pushpinlist.length=0;
			var bounds=xpiomap.getBounds(); 
			var north = bounds.getNorth(); 
			var south = bounds.getSouth();
			var east = bounds.getEast();
			var west = bounds.getWest();
		  
			for(var i=xpiomap.entities.getLength()-1;i>=0;i--) {
			var pushpin= xpiomap.entities.get(i); 
			if (pushpin instanceof Microsoft.Maps.Pushpin) { 
			
				var location = pushpin.getLocation();
				var splitloc=String(location).replace("[Location ","").replace("]","").split(",");
				var latitiude=parseFloat(splitloc[0]);
				var longitude=parseFloat(splitloc[1]);
				if(latitiude < south || latitiude > north || longitude > east || longitude < west)
					xpiomap.entities.removeAt(i); 
				else
					pushpinlist.push(splitloc[0]+splitloc[1]);
			} ;
			}
			
			return pushpinlist;	
		}	

		function addPushpins(query)	  
		{	     
			var xpiomappushpin=removePushpins();
			jQuery('#mapcount').html("0 properties found in this search."); 
			var compname='<?php echo $comp_name ?>';
			var requestUri = serviceUri + "GenericRetsSearchListings?" + query +"&$select=Lat,Disclaimer,LUD,Lon&$top=500&$inlinecount=allpages";
			OData.defaultHttpClient.enableJsonpCallback = true;
			OData.read(requestUri, function (data, request) {		
            var disclaimer="";var LUD="";var suffix = "AM";
				for (var i = 0; i < data.results.length; i++) 
				{	
                    LUD=data.results[0].LUD;
                    var dateValue = new Date(parseInt(LUD.replace("/Date(", "").replace(")/", "")));
                    lastUpdate_date=(dateValue.getMonth()+1)+"/"+dateValue.getDate()+"/"+dateValue.getFullYear();
                     hours=dateValue.getHours();
                    if (hours >= 12) 
                    {
                    suffix = "PM";
                    
                    }
                   
                    updatetime=hours+":"+dateValue.getMinutes()+":"+dateValue.getSeconds()+" "+suffix;
                    disclaimer=data.results[0].Disclaimer.replace("{company name}",compname);
                    
                    jQuery('#discl_info').html('"Disclaimer :'+disclaimer+':'+lastUpdate_date+' '+updatetime);
                    
					var addlocation=String(data.results[i].Lat)+String(data.results[i].Lon);
					var index=jQuery.inArray(addlocation, xpiomappushpin)
					if(index==-1)
					{
						var pushpin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(data.results[i].Lat,data.results[i].Lon),{icon: houseIcon,width: 24, height: 24});
						Microsoft.Maps.Events.addHandler(pushpin, 'click', addInfobox);
						Microsoft.Maps.Events.addHandler(pushpin, 'mouseover', displaypointercursor);
						xpiomap.entities.push(pushpin);
					}
					if(i==data.results.length-1)
					{
						mapcount(data.__count);
						hideProgress();
					}
                   
                    
				}			
			}, function(err){
				//alert("Error occurred 2" + err.message);
			}); 
		}
        
        function clearpushPin()
        {
            xpiomap.entities.clear();
        }
        
        function xpiomapdrag(e)	{	
            removeInfobox();			
			if(submitClick!=2)
			{
			 showProgress();
			 var searchQuery='';
			 searchQuery=createQuery();
			 //serviceCall(searchQuery+"&",0,0); 
			 addPushpins(searchQuery+"&");
			}
        }      
    
        function removeInfobox()
        {
            for (var i = xpiomap.entities.getLength() - 1; i >= 0; i--)
            {
                var pushpin = xpiomap.entities.get(i);
                if (pushpin.toString() == '[Infobox]')
                {
                    xpiomap.entities.removeAt(i);
                };
            }
        }         
        
        function displaypointercursor(e)
        {
            var mapElem = xpiomap.getRootElement();
            if (e.targetType == "pushpin") {
                mapElem.style.cursor = "pointer";
            } else {
                mapElem.style.cursor = "hand";
            }
        }
    
        function addInfobox(e) 
        {
            removeInfobox();
            if (e.targetType == "pushpin") 
            {
                var location = e.target.getLocation();
                var splitloc=String(location).replace("[Location ","").replace("]","").split(",");		
				var bounds=xpiomap.getBounds(); 
		        var north = bounds.getNorth(); 
		        var south = bounds.getSouth();
		        var east = bounds.getEast();
		        var west = bounds.getWest();
                var noso=(north-south)/2;
                var eawe=(east-west)/2;
                var middlenorth=north-noso;
                var middlesouth=south+noso;
                var middleeast=east-eawe;
                var middlewest=west+eawe;
                var x=0;
                var y=0;
                
                //alert(north+" n "+south+" s "+east+" e "+west+" w "+location);
                //alert(noso+","+eawe);   
                
                if(splitloc[0]>middlenorth)
                    y=-165;

                if(splitloc[0]<middlesouth)
                    y=10;

                if(splitloc[1]<middleeast)
                    x=0;

                if(splitloc[1]>middlewest)
                    x=-320;         
                var query ="$filter=PKey eq "+ PKey +" and Lat eq "+ splitloc[0] +" and Lon eq " + splitloc[1];   
                var requestUri = serviceUri + "GenericRetsSearchListings?" + query +"&$select=LN,HouNo,DirPre,Stre,StrSuff,DirSuff,City,State,Zip,PropType,SquFeet,Price,Acre,Beds,Baths,ImgNam,AddrYN&$top=1";
                OData.defaultHttpClient.enableJsonpCallback = true;
                OData.read(requestUri, function (data, request) { var address="";var Addryn="";
                for (var i = 0; i < data.results.length; i++) {	
                
                       Addryn=data.results[i].AddrYN;
                       if(PKey == 3)
                       {
                         if(Addryn == "Y")
                         {
                         address=data.results[i].HouNo+" "+data.results[i].DirPre+" "+data.results[i].Stre+" "+data.results[i].
StrSuff+" "+data.results[i].DirSuff+" "+data.results[i].City+", "+data.results[i].State+" "+data.results[i].Zip;
                         }
                       }
                     else
                       {
                         address=data.results[i].HouNo+" "+data.results[i].DirPre+" "+data.results[i].Stre+" "+data.results[i].
StrSuff+" "+data.results[i].DirSuff+" "+data.results[i].City+", "+data.results[i].State+" "+data.results[i].Zip; 
                       }

                        var image="";
                        var ln="";
                        ln=data.results[i].LN
                        if(data.results[i].ImgNam!='')
                            image=data.results[i].ImgNam;
                        else
                            image="http://xpioimages.blob.core.windows.net/mapinfoboximages/default.jpg";
                        
                        var desc="";
                        desc='<strong>'+FormatCurrency(data.results[i].Price)+'</strong> | MLS# '+ln+'<br/>'+data.results[i].Beds+' Beds | '+data.results[i].Baths+' Baths<br/>'+data.results[i].SquFeet+' Sq Ft | '+data.results[i].Acre.toFixed(2)+' Acre<br/>'+data.results[i].PropType;
     
                        var infoboxOptions =  {width :320, height :180, showCloseButton: true, zIndex: 1000,showPointer:false, offset:new Microsoft.Maps.Point(x,y),title:address, description:'<img width="100px" height="75px" src="'+image+'"/><span style="float:right;">'+desc+'</span>',actions:[{label: 'Property Details', eventHandler:
                         function propertydetails()
                        {
                            loadPopup(ln);
                        } 



 }]}; 
                        var defaultInfobox = new Microsoft.Maps.Infobox(e.target.getLocation(), infoboxOptions);  
                        xpiomap.entities.push(defaultInfobox);
                            
                          
                    }					
                }, function(err){
                    //alert("Error occurred 3" + err.message);
                }); 
            }
        }
        
 </script>
<?php get_footer(); ?>