	<div class="soidx-saved-search-wrapper cf">	
	<?php

    $savedQuerry = explode("^", $saved_query);
    $recordPage = ceil($savedQuerry[1]/50);

	if (!empty($_GET["pg"]))
        $no = 50 * (intval($_GET["pg"])-1);
    else
        $no = 0;
      
    $top=50;
    if (!empty($_GET["pg"]))
    {
        if(intval($_GET["pg"])==$recordPage) 
        {
            $top=ceil($savedQuerry[1]%50);
            if($top==0)
                $top=50;
        } 
    }

	$recordCount = 0;
    $query = $savedQuerry[0];
    $proxy = spoton_get_entities();
	$listingResponse = $proxy->GenericRetsSearchListings()->skip(strval($no))->Top(strval($top))->filter($query)->Select('PKey,LN,HouNo,Stre,DirPre,StrSuff,DirSuff,City,State,Zip,PropType,Status,SquFeet,Price,Acre,Beds,Baths,ImgNam,Logo,OffNam,AgNam,AddrYN,Disclaimer,LUD')->IncludeTotalCount()->Execute(); 
    $recordCount=$listingResponse->TotalCount();
    global $spoton_idx;
	$comp_name=$spoton_idx->options['input_company_name'];
	?>
        <script type="text/javascript"> 
   
    function moreinfolisting(ln,PKey)
		{  
            var serviceUri = "http://realestateservice.cloudapp.net/odataservice.svc/";
            var host="";
            host="<?php echo home_url(); ?>";
            host+='/property/mls-'; 
            var requestUri = serviceUri + "GenericRetsDetailListings?$filter=PKey eq "+ PKey +" and (LN eq '" + ln +"')&$select=LN,Remark";
			OData.defaultHttpClient.enableJsonpCallback = true;      
			OData.read(requestUri, function (data, request) {
			var LN =''; var SquFeet =''; var Price =''; var Acre =''; var Beds =''; var Baths =''; var Loc =''; var Remark ='';//var HiSch ='';var JuHiSch ='';var EleSch ='';
		    var adrress=""; var AgName=""; var disclaimer="";
              
            
            for (var i = 0; i < data.results.length; i++) {
			   
				LN = data.results[i].LN;            
			
				
				
				Remark = data.results[i].Remark;   
				
              
               
            
				var property=""; 
              
                property+='<div>';
                
                
                
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
       
             //photoSwipe   
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
					handler.text('Photos');
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
	<div class="msg_box msg_ok cf">
	<?php if($recordCount>$savedQuerry[1]) {  ?>
	The first <?php echo str_replace('.00','',number_format($savedQuerry[1], 2, '.', ',')); ?> properties are displayed out of the <b><?php echo str_replace('.00','',number_format($recordCount, 2, '.', ',')); ?></b> properties found in this search.
	<?php } else { ?>
	<b><?php echo str_replace('.00','',number_format($recordCount, 2, '.', ',')); ?></b> properties found in this search.
	<?php } ?>
	</div>
	
	<?php require_once (dirname(__FILE__) . '/paging-box.php');  ?>
	
	<?php    
	$hostName='';
	$hostName= home_url() . '/property/';
    $agent_stt=spoton_list_hide("search_agent");
    $office_stt=spoton_list_hide("search_office");
	foreach($listingResponse->Result as $SearchListings)
    { 
	  $pkey=$SearchListings->PKey;
      $AddrYN=$SearchListings->AddrYN;
      $offname=$SearchListings->OffNam;
      $agentname=$SearchListings->AgNam;
       $Disclaimer=$SearchListings->Disclaimer;
      $lud=$SearchListings->LUD;
      if($agent_stt ==1)
      {
       $agentname="";
      }
      if($office_stt ==1)
      {
       $offname="";
      }
    ?>
	<!--Begin Results Display-->
     
	<div class="sepH_a_line cf">
	<div class="soidx-listing-thumbnail">
	<?php
	if (!empty($SearchListings->ImgNam)) { ?>
		<a id="imgLink<?php echo $SearchListings->LN; ?>" href=<?php echo spoton_get_ppt_permalink($SearchListings); ?>>
<img class="soidx-listing-thumbnail sepV_a" src=<?php echo str_replace('trebmlsinfoboximages', 'trebmlsthumbnailimages',str_replace('mfrmlsinfoboximages', 'mfrmlsthumbnailimages',str_replace('swmlsinfoboximages', 'swmlsthumbnailimages',str_replace('saborinfoboximages', 'saborthumbnailimages',str_replace('ctmlsinfoboximages', 'ctmlsthumbnailimages',str_replace('criterioninfoboximages', 'criterionthumbnailimages',str_replace('innoviainfoboximages', 'innoviathumbnailimages',str_replace('ecarinfoboximages', 'ecarthumbnailimages',str_replace('gamlsinfoboximages', 'gamlsthumbnailimages',str_replace('mfrmlsinfoboximages', 'rmlsthumbnailimages',str_replace('lcborinfoboximages', 'lcborthumbnailimages',str_replace('nwmlsinfoboximages', 'nwmlsthumbnailimages',str_replace('okmlsinfoboximages', 'okmlsthumbnailimages',str_replace('icaarinfoboximages', 'icaarthumbnailimages',str_replace('mapinfoboximages', 'mapthumbnailimages',str_replace('retsmapinfoboximages', 'retsmapthumbnailimages',$SearchListings->ImgNam)))))))))))))))); ?>></a>
	<?php } else { ?>
		<a id="imgDefaultLink<?php echo $SearchListings->LN; ?>" href="<?php echo spoton_get_ppt_permalink($SearchListings); ?>"><img src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg"/></a>
	<?php } ?>
	</div>
<div class="soidx-listing-meta-wrap cf">
<?php if($pkey == 3)
        {
          if($AddrYN == "Y")
                   { ?>
 
        <h2 class="soidx-listing-meta-title dp100 sepH_a">
        <a id="imgDefaultLink_<?php echo $SearchListings->LN; ?>" href="<?php echo spoton_get_ppt_permalink($SearchListings); ?>"><?php echo spoton_get_ppt_addr($SearchListings); ?></a>
        </h2>
              <?php } }
         else { ?>
              
            <h2 class="soidx-listing-meta-title dp100 sepH_a">
            <a id="imgDefaultLink_<?php echo $SearchListings->LN; ?>" href="<?php echo spoton_get_ppt_permalink($SearchListings); ?>"><?php echo spoton_get_ppt_addr($SearchListings); ?></a>
        </h2>  
             
           <?php   } ?>
	<div class="soidx-listing-meta-text dp100">
	
	<span class="bld"><?php echo '$'.str_replace('.00','',number_format($SearchListings->Price, 2, '.', ','));?></span>&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $SearchListings->Status; ?>&nbsp;|&nbsp;MLS#&nbsp;<?php echo $SearchListings->LN; ?></br>
	<?php echo str_replace('.00','',$SearchListings->Beds); echo " Bed, "; ?>&nbsp;<?php echo str_replace('.00','',$SearchListings->Baths); echo " Bath, "; ?>&nbsp;<?php echo str_replace('.00','',number_format($SearchListings->SquFeet, 2, '.', ',')); echo " SF "; ?><?php if($SearchListings->Acre!=0){ ?>on&nbsp;<?php echo number_format($SearchListings->Acre, 2, '.', ','); echo  " Acres"; ?><?php } ?></br>
	<div class="dp0"><?php echo $SearchListings->PropType; ?></div>
	<div class="dp0 soidx-links"> 
	                <a   href="#inline" onclick="moreinfolisting('<?php echo $SearchListings->LN; ?>',<?php echo $pkey; ?>);">Preview </a>
                  
         <?php if(!empty($SearchListings->ImgNam)) { ?>
			                    		| <a id="spotonPhotoHandler_<?php echo $SearchListings->LN; ?>" class="spotonPhotoHandler"  href="<?php echo home_url('?spoton_do_ajax=1&action=get_property&sub_action=image&ln=' . $SearchListings->LN);?>">Photos</a>
										  <div style="display: none;" id="spotonPhotoPlaceholder_<?php echo $SearchListings->LN; ?>"></div>
							<?php	}
                      ?>
                   
                   |<a href="<?php echo strtolower(spoton_get_ppt_permalink($SearchListings)); ?>" target="_self">Property Details</a>                
                    
	</div>
	<div class="fl"><?php if(!empty($SearchListings->Logo)) { ?><img width="40px" height="40px" src="<?php echo $SearchListings->Logo; ?>"></img><?php } ?></div>
    <?php if(($agentname !="") && ($offname !="")) { ?>
    <div class="soidx-text small">Listing courtesy of <?php echo $agentname;  ?> - <span><?php echo $offname; ?></span></div>
    <?php }else if(($agentname !="") && ($offname =="")) {?>
    <div class="soidx-text small">Listing courtesy of <?php echo $agentname;  ?> </div>
    <?php } else if(($agentname =="") && ($offname !=""))  {?>
    <div class="soidx-text small">Listing courtesy of <?php echo $offname;  ?> </div>
    <?php } ?>
    	</div>
	</div>
        <div id="inline_<?php echo $SearchListings->LN; ?>" class="dp100 soidx-inline-text" style="Display:none"></div>
	</div>
	<?php } ?>
	</div><!-- End Search Template -->
	<form method="get" id="searchformbtm" class="sepH_a">
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
    <input id="searchformbtm" class="btn btn_a" type="submit"  <?php if (!empty($_GET["pg"])) { if($_GET["pg"]==$count) {  ?> <?php  } } ?> name="pg" value='<?php echo $count; ?>'/>
    <?php } 
	}
?></div>
</form>
<?php  $year=substr($lud,0,4);  $month=substr($lud,5,2); $day=substr($lud,8,2);
      
      $date=$month."/".$day."/".$year;
?>
    <div id="discl_info" style="color:black;"><?php echo '"Disclaimer :'.str_replace("{company name}",$comp_name,$Disclaimer).$date; ?></div> 