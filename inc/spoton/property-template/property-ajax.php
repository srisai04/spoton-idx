<?php 
	$query = 'PKey eq ' . $pkey . ' and ' . " LN eq '" . $ln . "'";    
	$ImageQuery = $proxy->RetsListingImages()->filter($query)->Select('ImageURL'); 
	$ImageResponse = $ImageQuery->Execute(); 

    $i=1;
    $listingResponse = $proxy->GenericRetsDetailListings()->filter($query)->Select('PKey,Logo,OffNam,AgNam,Disclaimer,IntFea,Price,WatFront,PropView,PropType,Status,LN,Acre,Baths,YrBuilt,HouNo,HiSch,Beds,EleSch,JuHiSch,PropSub,SchDist,Stre,ExtFea,StrSuff,DirSuff,City,State,Zip,Remark,Style,AppThtStay,SquFeet')->IncludeTotalCount()->Execute(); 
    
    $Listing = $listingResponse->Result[0];
    $ListID=$Listing->LN;
            $PropType=$Listing->PropType;
            $Status=$Listing->Status;
            $SquareFeet=$Listing->SquFeet;
            $Price=$Listing->Price;
            $Acreage=$Listing->Acre;
            $Beds=$Listing->Beds;
            $Baths=$Listing->Baths;
            $Location=$Listing->Loc;
            $Remark=$Listing->Remark;
            $Style=$Listing->Style;
	        $YrBuilt=$Listing->YrBuilt;
            $InteriorFeatures=$Listing->IntFea;
            $ExteriorFeatures=$Listing->ExtFea;
            $PropertyView=$Listing->PropView;
            $PropSub=$Listing->PropSub;
            $AppliancesThatStay=$Listing->AppThtStay;
            $WaterFront=$Listing->WatFront;
            $SchoolDistrict=$Listing->SchDist;
            $HighSchool=$Listing->HiSch;
            $JuniorHighSchool=$Listing->JuHiSch;
            $ElementarySchool=$Listing->EleSch;            
            $OffName=$Listing->OffNam;
            $AgNam=$Listing->AgNam;
            $Disclaimer= $Listing->Disclaimer;
            $logo=$Listing->Logo;
            
            $Address = spoton_get_ppt_addr($Listing);
                         
                $Remark=$Listing->Remark;
      
    $imageexists=false;
    echo '<div style="margin-left:40px;">';
   ?> 
    
    <?php 
    echo '</div>';
    echo '<h2 class="soidx-listing-meta-title dp100 sepH_b">'.$Address.'</h2>';
    foreach($ImageResponse->Result as $listingimage) 
    { 
        $imageexists=true;
        ?><img  class="soidx-listing-thumbnail" style="border:1px solid #D0D0D0;padding:5px;" src="<?php echo str_replace('mapslideshowimages','mapthumbnailimages',str_replace('retsmapslideshowimages','retsmapthumbnailimages',$listingimage->ImageURL)); ?>" alt=""  width="260px" height="195px"/><?php 
        break;
    }  
    if($imageexists==false)
    {
        ?><img  class="soidx-listing-thumbnail" style="border:1px solid #D0D0D0;padding:5px;" src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg" alt=""  width="260px" height="195px"/><?php 
    }
    

       echo '<div style="margin-left:280px;">
       <div style="display:table;width:auto;">
                    <div style="display:table-row;">';
                    if($PropType !="")
                    echo '<div style="float:left;display:table-column;width:120px;">Property Type :</div><div style="float:left;display:table-column;width:200px;">'.$PropType.'</div></div>';
                    if($Price !="")
                    {
                    echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Price :</div><div style="float:left;display:table-column;width:200px;">$'.str_replace('.00','',number_format($Price, 2, '.', ',')).'</div></div>';
                    }
                    else
                    {
                     echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Price :</div><div style="float:left;display:table-column;width:200px;">--</div></div>';
                    }
                    if($Status !="")
                    echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Status :</div><div style="float:left;display:table-column;width:200px;">'.$Status.'</div></div>';
                    if($Beds !=0)
                    echo ' <div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Beds :</div><div style="float:left;display:table-column;width:200px;">'. $Beds.'</div></div>';
                    if($Baths !=0)
                    echo ' <div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Baths :</div><div style="float:left;display:table-column;width:200px;">'.$Baths.'</div></div>';
                    if($YrBuilt !="")
                    echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">Year Built:</div><div style="float:left;display:table-column;width:200px;">'.$YrBuilt.'</div></div>';
                    echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:120px;">MLS # :</div><div style="float:left;display:table-column;width:200px;">'.$ln.'</div></div>';
                    echo '<div><a href='. spoton_get_ppt_permalink($Listing) .' target="_self">View More Details...</a></div>';
          echo '</div></div>';
       echo '</div>';
       echo '<div class="clear"></div>';  
       $count=1;
        foreach($ImageResponse->Result as $listingimage) 
            {
              if( $count==1)
              {
               ?><a class="fancybox-thumb" rel="fancybox-thumb" href="<?php echo $listingimage->ImageURL; ?>" title="" style="float:left;">
	           <span style="margin-left:90px;">View all photos...</span></a><?php 
              }
              else
              {
              ?><a class="fancybox-thumb" rel="fancybox-thumb" href="<?php echo $listingimage->ImageURL; ?>" title="" style="float:left;">
	           </a><?php 
              }
              $count++;
            }   
?>
<script type="text/javascript">

jQuery(document).ready(function() {
  
 jQuery(".tabsSss").flowtabs("div.tabs_contenttab > div");

	jQuery(".fancybox-thumb").fancybox({
		prevEffect	: 'none',
		nextEffect	: 'none',
        scrolling	: 'no',
		helpers	: {
			title	: {
				type: 'outside'
			},
			overlay	: {
				opacity : 0.8,
				css : {
					'background-color' : '#000'
				}
			},
			thumbs	: {
				width	: 50,
				height	: 50
			}
		}
	});
});

</script>
<br />
<div class="soidx-text">Listing courtesy of <?php if($pkey=='8' || $pkey=='9') { if(!empty($AgNam)) {echo $AgNam." - ";}} ?><span id="spto_OffNam"><?php echo $OffName; ?></span>
</div>
<div class="box_c_heading cf">
			<span class="fl"></span>
			<ul class="tabsSss fr">

				<li><a id="Remark" href="#">Remarks</a></li>
              
				<li><a id="Features" href="#">Features</a></li>
                
                <li><a id="School" href="#">School Information</a></li>
               
			</ul>
		</div>

<div class="box_c_content cf tabs_contenttab">
            
			<div class="soidx-text">
			<?php 	echo $Remark; ?>
			</div>
			<div >
            <?php  echo'<div style="display:table;width:auto;">';
                   if($Style !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Style :</div><div style="float:left;display:table-column;width:500px;">'.$Style.'</div></div>';
                   if($AppliancesThatStay !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Appliances That stay :</div><div style="float:left;display:table-column;width:500px;">'. $AppliancesThatStay.'</div></div>';
                   if($ExteriorFeatures !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Exterior Features :</div><div style="float:left;display:table-column;width:500px;">'.$ExteriorFeatures.'</div></div>';
                   if($PropSub !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Property Subtype :</div><div style="float:left;display:table-column;width:500px;">'.$PropSub.'</div></div>';
                   if($InteriorFeatures !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Interior Features :</div><div style="float:left;display:table-column;width:500px;">'.$InteriorFeatures.'</div></div>';
                   if($PropertyView !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Property View :</div><div style="float:left;display:table-column;width:500px;">'.$PropertyView.'</div></div>';
                   if($AppliancesThatStay !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Appliances that Stay :</div><div style="float:left;display:table-column;width:500px;">'.$AppliancesThatStay.'</div></div>';
                   if($WaterFront !="")
                   echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Water Front :</div><div style="float:left;display:table-column;width:500px;">'.$WaterFront.'</div></div>'; 
                   echo'</div>';
            
            ?>                           
			</div>
            <div >
                <?php echo'<div style="display:table;width:auto;">';
                      if($SchoolDistrict != "")
                       
                        echo'<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">School District :</div><div style="float:left;display:table-column;">'.$SchoolDistrict.'</div></div>';
                        
                      if($ElementarySchool != "")
                       
                        echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Elementary School :</div><div style="float:left;display:table-column;">'. $ElementarySchool.'</div></div>';
                        
                      if($JuniorHighSchool != "")
                        
                        echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">Jr.High School :</div><div style="float:left;display:table-column;">'.$JuniorHighSchool.'</div></div>';
                       
                      if($HighSchool != "")
                        
                         echo '<div style="display:table-row;"><div style="float:left;display:table-column;width:200px;">High School :</div><div style="float:left;display:table-column;">'.$HighSchool.'</div></div>';
                        
                       echo'</div>';
                        
             ?>    
                                         
			</div>
			<ul class="cf rounBox_list sepH_c">
			</ul>
        </div> <!-- End List View -->
	</div>
	<?php if(!empty($Disclaimer)) { ?>
   	<div class="dp100 soidx-text"><?php if(!empty($logo)) { ?><img width="40px" height="40px" src="<?php echo $logo; ?>"></img><?php } ?><?php echo '"' . trim($Disclaimer, '"') . '"' ?></div>
	<?php } ?>