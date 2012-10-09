<div style="width: 790px; margin: 0 10px;">
        <h4>Map Search</h4>
        <table>
        <tr>
            <td style="width:150px">
                Hide Map View
            </td>
            <td style="width:150px">
                <input type="checkbox" id="spto_hidemap" name="spto_hidemap" value="Yes"/>
            </td>
        </tr>
        <tr>
            <td>
                Default Map Location
            </td>
            <td>   
                <input style="width:260px" type="text" id="spto_maplocation" name="spto_maplocation" class="regular-text code"/>
            </td>
        </tr>
        <tr>
            <td>
                Zoom Level
            </td>
            <td>
                <input style="width:40px" type="text"  id="spto_zoomlevel" name="spto_zoomlevel" class="regular-text code"/>
            </td>
        </tr>
        </table>
        
        <body onLoad="adminXpioDisplayMap();">
            <input type="button" id="addpushpin" name="addpushpin" value="Add Pushpin" onClick="attachPushpinDragEndEvent();" />
            <div id='adminXpioDivMap' style="position:relative; width:600px; height:350px;"></div>
        </body>
        
        <h4>List View</h4>
        <table>
            <tr>
                <td style="width:150px">
                    Hide List View
                </td>
                <td style="width:150px">
                    <input type="checkbox" id="spto_hidelist" name="spto_hidelist" value="Yes"/>
                </td>
            </tr>            
        </table>
        <h4>Check search controls to hide</h4>
        <table>
            <tr>
                <td style="width:150px">State</td><td style="width:150px"><input type="checkbox" id="spto_hidestate" name="spto_hidestate" value="Yes"/></td><td></td><td style="width:150px">County</td><td style="width:150px"><input type="checkbox" id="spto_hidecounty" name="spto_hidecounty" value="Yes"/></td>
            </tr>
            <tr>
                <td>City</td><td><input type="checkbox" id="spto_hidecity" name="spto_hidecity" value="Yes"/></td><td></td><td>Zip Code</td><td><input type="checkbox" id="spto_hidezipcode" name="spto_hidezipcode" value="Yes"/></td>
            </tr>
            <tr>
                <td>Area</td><td><input type="checkbox" id="spto_hideArea" name="spto_hideArea" value="Yes"/></td><td></td><td>Sub-Division</td><td><input type="checkbox" id="spto_hideSubDiv" name="spto_hideSubDiv" value="Yes"/></td>
            </tr>
            <tr>
                <td>Price From</td><td><input type="checkbox" id="spto_hidepricefrom" name="spto_hidepricefrom" value="Yes"/></td><td></td><td>Price To</td><td><input type="checkbox" id="spto_hidepriceto" name="spto_hidepriceto" value="Yes"/></td>
            </tr>
            <tr>
                <td>Beds</td><td><input type="checkbox" id="spto_hidebeds" name="spto_hidebeds" value="Yes"/></td><td></td><td>Baths</td><td><input type="checkbox" id="spto_hidebath" name="spto_hidebath" value="Yes"/></td>
            </tr>
            <tr>
                <td>Acreage From </td><td><input type="checkbox" id="spto_hideaceragefrom" name="spto_hideaceragefrom" value="Yes"/></td><td></td><td>Acreage To</td><td><input type="checkbox" id="spto_hideacerageto" name="spto_hideacerageto" value="Yes"/></td>
            </tr>
            <tr>
                <td>Square Feet</td><td><input type="checkbox" id="spto_hidesquarefeet" name="spto_hidesquarefeet" value="Yes"/></td><td></td><td>Time on Market </td><td><input type="checkbox" id="spto_hidetimeonmarket" name="spto_hidetimeonmarket" value="Yes"/></td>
            </tr>
            <tr>
                <td>School District</td><td><input type="checkbox" id="spto_hideSchDist" name="spto_hideSchDist" value="Yes"/></td><td></td><td>Elementary School</td><td><input type="checkbox" id="spto_hideEleSch" name="spto_hideEleSch" value="Yes"/></td>
            </tr>
            <tr>
                <td>Middle School</td><td><input type="checkbox" id="spto_hideJuHiSch" name="spto_hideJuHiSch" value="Yes"/></td><td></td><td>High School</td><td><input type="checkbox" id="spto_hideHiSch" name="spto_hideHiSch" value="Yes"/></td>
            </tr>
            <tr>
                <td>Property</td><td><input type="checkbox" id="spto_hideproperty" name="spto_hideproperty" value="Yes"/></td><td></td><td>Sort by</td><td><input type="checkbox" id="spto_hidesortby" name="spto_hidesortby" value="Yes"/></td>
            </tr>            
        </table>

<?php 
$searchview = spoton_get_adm_settings();
$defaultlocation = spoton_get_default_location($searchview);	
?>

    <script type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0"></script>
    <script type="text/javascript">
    
    var zoomlevel='0';
    var hidemap='<?php echo $searchview[0]; ?>';
    var hidelist='<?php echo $searchview[1]; ?>';
    var zoomlevel='<?php echo $searchview[3]; ?>';
    var hidestate='<?php echo $searchview[4]; ?>';
    var hidecounty='<?php echo $searchview[5]; ?>';
    var hidecity='<?php echo $searchview[6]; ?>';
    var hidezipcode='<?php echo $searchview[7]; ?>';
    var hidepricefrom='<?php echo $searchview[8]; ?>';
    var hidepriceto='<?php echo $searchview[9]; ?>';
    var hidebeds='<?php echo $searchview[10]; ?>';
    var hidebath='<?php echo $searchview[11]; ?>';
    var hideaceragefrom='<?php echo $searchview[12]; ?>';
    var hideacerageto='<?php echo $searchview[13]; ?>';
    var hidesquarefeet='<?php echo $searchview[14]; ?>';
    var hidetimeonmarket='<?php echo $searchview[15]; ?>';
    var hideproperty='<?php echo $searchview[16]; ?>';
    var hidesortby='<?php echo $searchview[17]; ?>';
    var hideArea='<?php echo $searchview[18]; ?>';
    var hideSubDiv='<?php echo $searchview[19]; ?>';
    var hideSchDist='<?php echo $searchview[20]; ?>';
    var hideEleSch='<?php echo $searchview[21]; ?>';
    var hideJuHiSch='<?php echo $searchview[22]; ?>';
    var hideHiSch='<?php echo $searchview[23]; ?>';
    var defaultlatitiude='0';
    var defaultlongitude='0';
    
      var adminxpiomap = null;
            
      function adminXpioDisplayMap()
      {
        adminxpiomap = new Microsoft.Maps.Map(document.getElementById('adminXpioDivMap'), {credentials: 'Arutm_QW76zIIxv6-vXrX9l9iK93NjDUdb3OGDbrcpb2_lPkTDx7NPHhbCmggKFy', enableSearchLogo: false, mapTypeId: Microsoft.Maps.MapTypeId.road, showCopyright:false, enableClickableLogo:false, showBreadcrumb:true});
        adminxpiomap.setView({center: new Microsoft.Maps.Location(defaultlatitiude, defaultlongitude), zoom: parseInt(zoomlevel)});	
      }
    
      function attachPushpinDragEndEvent()
      {
        adminxpiomap.entities.clear();
        var pushpinOptions = {draggable: true}; 
        var pushpin= new Microsoft.Maps.Pushpin(adminxpiomap.getCenter(), pushpinOptions); 
        var pushpinclick= Microsoft.Maps.Events.addHandler(pushpin, 'dragstart', mapDragDetails);
        var pushpindragend= Microsoft.Maps.Events.addHandler(pushpin, 'drag', mapDragDetails);  
        adminxpiomap.entities.push(pushpin); 
      }
      
      mapDragDetails = function (e) 
      {
        var location = String(e.entity.getLocation()).replace("[Location ","").replace("]","")
        jQuery("#spto_maplocation").val(location);
        jQuery("#spto_zoomlevel").val(adminxpiomap.getTargetZoom());
      }

    jQuery(document).ready(function() {
       
        jQuery("#spto_zoomlevel").val(zoomlevel);
        
        if(hidemap==1)
            jQuery('input[id=spto_hidemap]').attr('checked', true);
        else
            jQuery('input[id=spto_hidemap]').attr('checked', false);   
            
        if(hidelist==1)
            jQuery('input[id=spto_hidelist]').attr('checked', true);
        else
            jQuery('input[id=spto_hidelist]').attr('checked', false); 
		
		if(hidestate==1)
			jQuery('input[id=spto_hidestate]').attr('checked', true);
        else
            jQuery('input[id=spto_hidestate]').attr('checked', false); 
			
        if(hidecounty==1)
			jQuery('input[id=spto_hidecounty]').attr('checked', true);
        else
            jQuery('input[id=spto_hidecounty]').attr('checked', false); 
			
        if(hidecity==1)
			jQuery('input[id=spto_hidecity]').attr('checked', true);
        else
            jQuery('input[id=spto_hidecity]').attr('checked', false); 
			
        if(hidezipcode==1)
			jQuery('input[id=spto_hidezipcode]').attr('checked', true);
        else
            jQuery('input[id=spto_hidezipcode]').attr('checked', false); 
			
        if(hidepricefrom==1)
			jQuery('input[id=spto_hidepricefrom]').attr('checked', true);
        else
            jQuery('input[id=spto_hidepricefrom]').attr('checked', false); 
			
        if(hidepriceto==1)
			jQuery('input[id=spto_hidepriceto]').attr('checked', true);
        else
            jQuery('input[id=spto_hidepriceto]').attr('checked', false); 
			
        if(hidebeds==1)
			jQuery('input[id=spto_hidebeds]').attr('checked', true);
        else
            jQuery('input[id=spto_hidebeds]').attr('checked', false); 
			
        if(hidebath==1)
			jQuery('input[id=spto_hidebath]').attr('checked', true);
        else
            jQuery('input[id=spto_hidebath]').attr('checked', false); 
			
        if(hideaceragefrom==1)
			jQuery('input[id=spto_hideaceragefrom]').attr('checked', true);
        else
            jQuery('input[id=spto_hideaceragefrom]').attr('checked', false); 
			
        if(hideacerageto==1)
			jQuery('input[id=spto_hideacerageto]').attr('checked', true);
        else
            jQuery('input[id=spto_hideacerageto]').attr('checked', false); 
			
        if(hidesquarefeet==1)
			jQuery('input[id=spto_hidesquarefeet]').attr('checked', true);
        else
            jQuery('input[id=spto_hidesquarefeet]').attr('checked', false); 
			
        if(hidetimeonmarket==1)
			jQuery('input[id=spto_hidetimeonmarket]').attr('checked', true);
        else
            jQuery('input[id=spto_hidetimeonmarket]').attr('checked', false); 
			
        if(hideproperty==1)
			jQuery('input[id=spto_hideproperty]').attr('checked', true);
        else
            jQuery('input[id=spto_hideproperty]').attr('checked', false); 
			
        if(hideArea==1)
			jQuery('input[id=spto_hidesortby]').attr('checked', true);
        else
            jQuery('input[id=spto_hidesortby]').attr('checked', false); 	

        if(hideArea==1)
			jQuery('input[id=spto_hideArea]').attr('checked', true);
        else
            jQuery('input[id=spto_hideArea]').attr('checked', false); 
	    
         if(hideSubDiv==1)
			jQuery('input[id=spto_hideSubDiv]').attr('checked', true);
        else
            jQuery('input[id=spto_hideSubDiv]').attr('checked', false); 
            
         if(hideSchDist==1)
			jQuery('input[id=spto_hideSchDist]').attr('checked', true);
        else
            jQuery('input[id=spto_hideSchDist]').attr('checked', false); 
            
         if(hideEleSch==1)
			jQuery('input[id=spto_hideEleSch]').attr('checked', true);
        else
            jQuery('input[id=spto_hideEleSch]').attr('checked', false); 
            
         if(hideJuHiSch==1)
			jQuery('input[id=spto_hideJuHiSch]').attr('checked', true);
        else
            jQuery('input[id=spto_hideJuHiSch]').attr('checked', false); 
            
         if(hideHiSch==1)
			jQuery('input[id=spto_hideHiSch]').attr('checked', true);
        else
            jQuery('input[id=spto_hideHiSch]').attr('checked', false); 
            
        jQuery("#spto_maplocation").val('<?php echo $searchview[2]; ?>');
        defaultlatitiude= '<?php echo $defaultlocation[0]; ?>';
        defaultlongitude= '<?php echo $defaultlocation[1]; ?>';
    });
        
    </script>

</div>