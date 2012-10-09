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
            <script type="text/javascript">
              jQuery(document).ready(function() {

                jQuery.cookie("ln",null,{ expires: -1, path: '/' });
         
			  });

			// PhotoSwipe
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

            <div id="soidx-wrapper" class="cf">
            <div id="soidx-content-sidebar-wrapper" class="cf">
            <div id="soidx-content" class="soidx-content-map">
                <div class="box_c">
                	<div class="box_c_heading cf">
                        <span class="fl">Properties </span>
                    </div>
                    <div class="box_c_content cf tabs_content">
                        <?php if($searchview[0]==0) { ?>
                        <?php } ?>
                        
                        <?php if($searchview[1]==0) { ?>
                        <div>
                            <div class="msg_box msg_ok">
                                <span id="listingcount" class="soidx-mapcount"></span>
                            </div>
                            <div id="paging" >        
                            </div>
                        
                            <div id="resultsArea"></div>
                            <script type="x-jquery-tmpl" id="searchResultsTemplate">
                            <div id="resultscount">
                            {{if $data.__count>500}}
                                <div style="display:none;">
                                ${listingCount($data.__count)}
                                </div>
                                <div style="display:none;">
                                ${hideProgress()}
                                ${showbottompaging()}
                                </div>
                            {{else}}
                                <div style="display:none;">
                                ${listingCount($data.__count)}
                                </div>
                                <div style="display:none;">
                                ${hideProgress()}
                                ${showbottompaging()}
                                </div>
                            {{/if}}
                            </div>
                            {{each results}}
                                <!--Begin Results Display-->
                                <div class="dp100 sepH_a_line cf">
                                <!--Begin Thumbnail Display-->
                                <div class="soidx-listing-thumbnail sepH_a">
                                {{if $value.ImgNam}}
                                    <a class="imgLink" href=${hostName()}${$value.LN.toLowerCase()}{{if $value.HouNo!=0}}-${$value.HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirPre}}-${$value.DirPre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Stre}}-${$value.Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.StrSuff}}-${$value.StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirSuff}}-${$value.DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.City}}-${$value.City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.State}}-${$value.State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Zip}}-${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase()}{{/if}}><img height="200px" class="soidx-listing-thumbnail" src=${$value.ImgNam.replace("retsmapinfoboximages","retsmapthumbnailimages").replace("mapinfoboximages","mapthumbnailimages").replace("/gamls/t/", "/gamls/m/")}></a>
                                {{else}}
                                    <a class="imgDefaultLink" href=${hostName()}${$value.LN.toLowerCase()}{{if $value.HouNo!=0}}-${$value.HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirPre}}-${$value.DirPre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Stre}}-${$value.Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.StrSuff}}-${$value.StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirSuff}}-${$value.DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.City}}-${$value.City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.State}}-${$value.State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Zip}}-${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase()}{{/if}}><img height="200px" src="http://xpioimages.blob.core.windows.net/mapthumbnailimages/default.jpg"/></a>
                                {{/if}}
                                
                                </div><!--End Thumbnail Display-->
                                <!-- Begin Listing Meta -->
                                <div class="dp50 cf">
                                <!-- Begin Listing Title -->
                                {{if $value.PKey == 3 }}
                                {{if $value.AddrYN != ""}}
                                {{if $value.AddrYN == "Y"}}
                                <h2 id=${$value.LN} class="soidx-listing-meta-title dp100 sepH_a">
                                <a class="postLink" href=${hostName()}${$value.LN.toLowerCase()}{{if $value.HouNo!=0}}-${$value.HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirPre}}-${$value.DirPre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Stre}}-${$value.Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.StrSuff}}-${$value.StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirSuff}}-${$value.DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.City}}-${$value.City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.State}}-${$value.State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Zip}}-${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase()}{{/if}}>{{if $value.HouNo!=0}}${$value.HouNo} {{/if}}{{if $value.DirPre}}${$value.DirPre} {{/if}}{{if $value.Stre}}${$value.Stre} {{/if}}{{if $value.StrSuff}}${$value.StrSuff} {{/if}}{{if $value.DirSuff}}${$value.DirSuff} {{/if}}{{if $value.City}}${$value.City}, {{/if}}{{if $value.State}}${$value.State} {{/if}}{{if $value.Zip}}${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "")}{{/if}}</a>
                                </h2>
                                {{/if}}{{/if}}
                                {{else}}
                                <h2 id=${$value.LN} class="soidx-listing-meta-title dp100 sepH_a">
                                <a class="postLink" href=${hostName()}${$value.LN.toLowerCase()}{{if $value.HouNo!=0}}-${$value.HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirPre}}-${$value.DirPre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Stre}}-${$value.Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.StrSuff}}-${$value.StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirSuff}}-${$value.DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.City}}-${$value.City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.State}}-${$value.State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Zip}}-${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase()}{{/if}}>{{if $value.HouNo!=0}}${$value.HouNo} {{/if}}{{if $value.DirPre}}${$value.DirPre} {{/if}}{{if $value.Stre}}${$value.Stre} {{/if}}{{if $value.StrSuff}}${$value.StrSuff} {{/if}}{{if $value.DirSuff}}${$value.DirSuff} {{/if}}{{if $value.City}}${$value.City}, {{/if}}{{if $value.State}}${$value.State} {{/if}}{{if $value.Zip}}${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "")}{{/if}}</a>
                                </h2>
                                {{/if}}
                                <!-- End Listing Title -->
                                <div class="soidx-listing-meta-text dp100">
                                <span class="bld">${FormatCurrency($value.Price)}</span>&nbsp;&nbsp;&nbsp;|&nbsp;MLS#&nbsp;${$value.LN}</br>
                                {{if $value.Beds}}${$value.Beds.replace(".00", "")} Bed, {{/if}}&nbsp;{{if $value.Baths}}${$value.Baths.replace(".00", "")} Bath, {{/if}}&nbsp;{{if $value.SquFeet}}${FormatCurrency($value.SquFeet).replace("$","")} Sq Ft {{/if}}{{if $value.Acre!=0}}on ${formatAcres($value.Acre)} Acres{{/if}}</br>
                                <span class="property">${$value.PropType}</span>                                           

                                <br />

                                <div>
			                    	<a id="href_${$value.LN}" href="#inline" onclick="moreinfolisting('${$value.LN}');">Preview </a> | 
									<a href=${hostName()}${$value.LN.toLowerCase()}{{if $value.HouNo!=0}}-${$value.HouNo.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Stre}}-${$value.Stre.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.StrSuff}}-${$value.StrSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.DirSuff}}-${$value.DirSuff.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.City}}-${$value.City.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.State}}-${$value.State.replace(/[^a-zA-Z0-9]/gi, "-").replace(/-+/gi, "-").toLowerCase()}{{/if}}{{if $value.Zip}}-${$value.Zip.replace(/[^a-zA-Z0-9]/gi, "").toLowerCase()}{{/if}} target="_self">Property Details</a>
									
									{{if $value.ImgNam}}
			                    		| <a id="spotonPhotoHandler_${$value.LN}" class="spotonPhotoHandler" href="<?php echo home_url('?spoton_do_ajax=1&action=get_property&sub_action=image&ln=${$value.LN.toLowerCase()}'); ?>">Photos</a>
										<div style="display: none;" id="spotonPhotoPlaceholder_${$value.LN}"></div>
									{{/if}}
			                    </div>
                    <div>
                    <div class="fl">
                    {{if $value.Logo}}
                    <img width="40px" height="40px" src='${$value.Logo}'></img>
                    {{/if}}</div>             
                                {{if $value.OffNam || $value.AgNam }}
                                <div  class="soidx-text small">Listing courtesy of ${$value.AgNam} - <span>${$value.OffNam}</span></div>
                               
                                 {{/if}}
								 </div>
                                </div>
                                </div>
                                <div id="inline_${$value.LN}" class="dp100 soidx-inline-text" style="Display:none"></div>
                                </div>

                                </div><!-- End Listing Meta -->
            
                               
            
                            {{/each}}
                            {{if __next}}
                                <div class='moreBox'>More results are available - try refining your query.</div>
                            {{/if}}
                            
                            </script>  
                            <div id="bottompaging">        
                            </div>
                            
                        </div>
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
                
                    jQuery('#property_list_det').live('click',function (e) { 
                        setSearchState();        
                    });
            
            
                    jQuery('.postLink').live('click',function (e) { 
                        setSearchState();        
                    });
                    jQuery('.imgDefaultLink').live('click',function (e) { 
                        setSearchState(); 
                    });
                    jQuery('.imgLink').live('click',function (e) { 
                        setSearchState(); 
                    });
                
                    
                     function setSearchState()

        {    
            jQuery.cookie('spot-on',null,{expires: -7,path:'/'});
            jQuery.cookie('spot-ln',null,{expires: -7,path:'/'});
            var searchState='';var searchLN='';
            searchState += jQuery("#spto_state").val()+"|";  
            searchState += jQuery("#spto_county").val()+"|"; 
            searchState += jQuery("#spto_city").val()+"|"; 
            searchState += jQuery("#spto_subdiv").val()+"|"; 
            searchState += jQuery("#spto_area").val()+"|";    
            searchState += jQuery("#spto_zipcode").val()+"|";  
            searchState += jQuery("#spto_pricefrom").val()+"|"; 
            searchState += jQuery("#spto_priceto").val()+"|"; 
            searchState += jQuery("#spto_bedrooms").val()+"|"; 
            searchState += jQuery("#spto_bathrooms").val()+"|";      
            searchState += jQuery("#spto_squarefeet").val()+"|";
            searchState += jQuery("#spto_acreagefrom").val()+"|"; 
            searchState += jQuery("#spto_acreageto").val()+"|";            
            searchState += jQuery("#spto_schooldistrict").val()+"|";  
            searchState += jQuery("#spto_elementaryschool").val()+"|"; 
            searchState += jQuery("#spto_middleschool").val()+"|"; 
            searchState += jQuery("#spto_highschool").val()+"|"; 
            searchState += jQuery("#spto_market").val()+"|";      
            searchState += jQuery("#spto_PropertyType").val()+"|";
            searchState += jQuery("#spto_pricesort").val()+"|";          
            searchState += PagingClick+"|";
            searchLN += jQuery("#spto_mls1").val()+"|";  
            searchLN += jQuery("#spto_mls2").val()+"|";  
            searchLN += jQuery("#spto_mls3").val()+"|";  
            searchLN += jQuery("#spto_mls4").val()+"|";  
            searchLN += jQuery("#spto_mls5").val()+"|"; 
            searchLN += jQuery("#spto_mls6").val()+"|"; 
            
            jQuery.cookie('spot-on',searchState,{expires: 7,path:'/'}); 
            jQuery.cookie('spot-ln',searchLN,{expires: 7,path:'/'});
        }
        
        function getSearchState()
        {
         
            var searchOption=''; var option='';
            searchOption = jQuery.cookie('spot-on');    
           
            
            var searchlnoption=''; var lnoption='';
            searchlnoption=jQuery.cookie('spot-ln');
           
        
            if(searchOption !=null)

            {            option = searchOption.split("|");  
                try{

                   jQuery("#spto_state").val(option[0]);  
                    jQuery("#spto_county").val(option[1]); 
                    jQuery("#spto_city").val(option[2]); 
                    jQuery("#spto_subdiv").val(option[3]); 
                    jQuery("#spto_area").val(option[4]);    
                    jQuery("#spto_zipcode").val(option[5]);  
                    jQuery("#spto_pricefrom").val(option[6]); 
                    jQuery("#spto_priceto").val(option[7]); 
                    jQuery("#spto_bedrooms").val(option[8]); 
                    jQuery("#spto_bathrooms").val(option[9]);      
                    jQuery("#spto_squarefeet").val(option[10]);
                    jQuery("#spto_acreagefrom").val(option[11]); 
                    jQuery("#spto_acreageto").val(option[12]);
                    jQuery("#spto_schooldistrict").val(option[13]);  

                    jQuery("#spto_elementaryschool").val(option[14]); 
                    jQuery("#spto_middleschool").val(option[15]); 
                    jQuery("#spto_highschool").val(option[16]); 
                    jQuery("#spto_market").val(option[17]);      
                   // jQuery("#spto_PropertyType").val(option[18]);
                    jQuery("#spto_pricesort").val(option[19]); 
                   
                    
                }catch(err){ 
                
                }
            }
            if(searchlnoption !=null)
            {             lnoption=searchlnoption.split("|");
                try{
                    
                   
                    jQuery("#spto_mls1").val(lnoption[0]);  
                    jQuery("#spto_mls2").val(lnoption[1]); 
                    jQuery("#spto_mls3").val(lnoption[2]); 
                    jQuery("#spto_mls4").val(lnoption[3]); 
                    jQuery("#spto_mls5").val(lnoption[4]);    
                    jQuery("#spto_mls6").val(lnoption[5]);    
                }catch(err){ 
                
                }
            }
            else
            {            
    
            }
        }
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
                            
                        property="";
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
                                
            
                                
                                lnvalue=jQuery.cookie("ln");
                                
                                jQuery("#inline_"+lnvalue).hide();
                                jQuery.cookie("ln",null,{ expires: -1, path: '/' });
                                jQuery.cookie("ln",ln,{ expires:1, path: '/' });
                                jQuery("#inline_"+ln).html(property).show('slow');
                            }
                        else if(jQuery.cookie("ln") == null )
            
                            {  
                                jQuery.cookie("ln",ln,{ expires:1, path: '/' });
                                jQuery("#inline_"+ln).html(property).show('slow');
                            }
                        else if(jQuery.cookie("ln") == ln)
                            {
            
                                
                                jQuery("#inline_"+ln).hide();
                                jQuery.cookie("ln",null,{ expires: -1, path: '/' });
                            }
            
                        
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
            
                    
            // jQuery("#inline_"+ln).html(property).show('slow');
            
                    }
            
            
                    function listingCount(count)
                    {
                        if(count>500)
                            jQuery('#listingcount').html("The first 500 properties are displayed out of the " + FormatCurrency(count).replace("$","") + " properties found in this search.");
                        else
                            jQuery('#listingcount').html(FormatCurrency(count).replace("$","") + " properties found in this search.");
                    }
                    
                    function hidebottompaging()
                    {
                        jQuery("#bottompaging").css({"display":"none"});
                    }
                    
                    function showbottompaging()
                    {
                        jQuery("#bottompaging").css({"display":"block"});
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
            
                    {      var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                            
                        } 
                        
                        jQuery("#spto_state >option").remove();
                        jQuery("#spto_state").append('<option value=0>All</option>');
                        
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsCounties?" + query +"&$orderby=State&$select=State";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                            var previous=i;var selected_opt="";
                            --previous; 
                            
                            if(previous==-1)
                            {
                                previous=0;
                                if(data.results[i].State.replace(/\s+/g, '_') == option[0])
                                {
                                    jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+' selected>'+data.results[i].State+'</option>')
            
                                }else
                                {
                                    jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+'>'+data.results[i].State+'</option>')
                                }
                            }
                            if(data.results[previous].State!=data.results[i].State)
            
                            {  
                                if(data.results[i].State.replace(/\s+/g, '_') == option[0])
                                {
                                    jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+' selected>'+data.results[i].State+'</option>')
                                }else
                                {
                                    jQuery("#spto_state").append('<option value='+data.results[i].State.replace(/\s+/g, '_')+'>'+data.results[i].State+'</option>')
                                }
                                
                            }
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
            
                    function loadCounty(state)
                    {
                        var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                            
                        } 
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
                                if(data.results[i].County.replace(/\s+/g, '_') == option[1])
                                {
                                jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+' selected>'+data.results[i].County+'</option>')
                                }else
                                {
                                jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+'>'+data.results[i].County+'</option>')
                                }
                                
                            }					
                            if(data.results[previous].County!=data.results[i].County)
            
                            { 
                                if(data.results[i].County.replace(/\s+/g, '_') == option[1])
                                {
                                jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+' selected>'+data.results[i].County+'</option>')
                                }else
                                {
                                jQuery("#spto_county").append('<option value='+data.results[i].County.replace(/\s+/g, '_')+'>'+data.results[i].County+'</option>')
                                }
                            }
                            
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });        
                    }
                
                    function loadCity(state,county)
                    {
                        var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                            
                        } 
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
                                if(data.results[i].City.replace(/\s+/g, '_') == option[2])
                                {
                                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+' selected>'+data.results[i].City+'</option>');
                                }else
                                {
                                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+'>'+data.results[i].City+'</option>');
                                }
                                
                            }
                                
                            if(data.results[previous].City!=data.results[i].City)
                            {
                                if(data.results[i].City.replace(/\s+/g, '_') == option[2])
                                {
                                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+' selected>'+data.results[i].City+'</option>');
                                }else
                                {
                                jQuery("#spto_city").append('<option value='+data.results[i].City.replace(/\s+/g, '_')+'>'+data.results[i].City+'</option>');
                                }
                            }
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                    
                    function loadarea()
            
                    {    
                        var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                        }
                        jQuery("#spto_area >option").remove();
                        jQuery("#spto_area").append('<option value=0>All</option>');
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsAreas?" + query +"&$orderby=Area&$select=Area";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                            if(data.results[i].Area.replace(/\s+/g, '_') == option[4])
                            {
                                jQuery("#spto_area").append('<option value='+data.results[i].Area.replace(/\s+/g, '_')+' selected>'+data.results[i].Area+'</option>');
                            } 
                        else
                            {
                                jQuery("#spto_area").append('<option value='+data.results[i].Area.replace(/\s+/g, '_')+'>'+data.results[i].Area+'</option>');  
                            }
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                    
                    function loadschooldistrict()
            
                    {  var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                        }
                            jQuery("#spto_schooldistrict >option").remove();
                            jQuery("#spto_schooldistrict").append('<option value=0>All</option>');
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsSchoolDistricts?" + query +"&$orderby=SchoolDistrict&$select=SchoolDistrict";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                        
                        if(option[13] == data.results[i].SchoolDistrict.replace(/\s+/g, '_'))
                                {
                                    jQuery("#spto_schooldistrict").append('<option value='+data.results[i].SchoolDistrict.replace(/\s+/g, '_')+' selected>'+data.results[i].SchoolDistrict+'</option>');
                                }
                                else
                                {
                                    jQuery("#spto_schooldistrict").append('<option value='+data.results[i].SchoolDistrict.replace(/\s+/g, '_')+'>'+data.results[i].SchoolDistrict+'</option>');
                                }
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                    
                    function loadelementaryschool()
            
                    {var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                        } 
                        
                        jQuery("#spto_elementaryschool >option").remove();
                        jQuery("#spto_elementaryschool").append('<option value=0>All</option>');
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsElementarySchools?" + query +"&$orderby=ElementarySchool&$select=ElementarySchool";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                            
                            
                            if(option[14] == data.results[i].ElementarySchool.replace(/\s+/g, '_'))
                                {
                                jQuery("#spto_elementaryschool").append('<option value='+data.results[i].ElementarySchool.replace(/\s+/g, '_')+' selected>'+data.results[i].ElementarySchool+'</option>');
                                }
                                else
                                {
                            jQuery("#spto_elementaryschool").append('<option value='+data.results[i].ElementarySchool.replace(/\s+/g, '_')+'>'+data.results[i].ElementarySchool+'</option>');
                            }
                        
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                    
                    function loadmiddleschool()
            
                    {var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                        }  
                        jQuery("#spto_middleschool >option").remove();
                        jQuery("#spto_middleschool").append('<option value=0>All</option>');
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsMiddleSchools?" + query +"&$orderby=MiddleSchool&$select=MiddleSchool";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                            
                            if(option[15] == data.results[i].MiddleSchool.replace(/\s+/g, '_'))
                                {
                            jQuery("#spto_middleschool").append('<option value='+data.results[i].MiddleSchool.replace(/\s+/g, '_')+' selected>'+data.results[i].MiddleSchool+'</option>');
                            }
                            else
                            {
                            jQuery("#spto_middleschool").append('<option value='+data.results[i].MiddleSchool.replace(/\s+/g, '_')+' >'+data.results[i].MiddleSchool+'</option>');    
                            }
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                    
                    function loadhighschool()
            
                    { var option='';
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; 
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                        }  
                        jQuery("#spto_highschool >option").remove();
                        jQuery("#spto_highschool").append('<option value=0>All</option>');
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsHighSchools?" + query +"&$orderby=HighSchool&$select=HighSchool";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        for (var i = 0; i < data.results.length; i++) {
                        
                        if(option[16] == data.results[i].HighSchool.replace(/\s+/g, '_'))
                                {
                            jQuery("#spto_highschool").append('<option value='+data.results[i].HighSchool.replace(/\s+/g, '_')+' selected>'+data.results[i].HighSchool+'</option>');
                            }
                            else 
                            {
                            jQuery("#spto_highschool").append('<option value='+data.results[i].HighSchool.replace(/\s+/g, '_')+'>'+data.results[i].HighSchool+'</option>');
                            }
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
            
            <?php if (!empty($post_query)) { ?>
            
                        serviceCall(createPostQuery() + '&', 0, 0);
            <?php }else { ?>
                    
                        serviceCall(createQuery()+"&", 0, 0);
            <?php }  ?>           
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
                    
           
                    function loadProperty()
            
                    {   var loadarray=new Array();
                        if(jQuery.cookie('spot-on') != null)
                        {
                            var searchOption=''; var option=new Array();
                            searchOption = jQuery.cookie('spot-on');    
                            option = searchOption.split("|");   
                            loadarray=option[18].split(","); 
                        
                        } 
                        
                        jQuery("#spto_PropertyType >option").remove();
                        
                        
                        var query ="$filter=PKey eq "+ PKey;   
                        var requestUri = serviceUri + "RetsProperties?" + query +"&$orderby=PropName&$select=PropType,PropName";
                        OData.defaultHttpClient.enableJsonpCallback = true;
                        OData.read(requestUri, function (data, request) {
                        var count=0;
                        for (var i = 0; i < data.results.length; i++) {
                        
                            if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[0])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[1])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[2])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[3])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[4])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else    if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[5])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                        else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[6])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                            else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[7])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                            else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[8])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                            else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[9])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                            else   if((data.results[i].PropName.replace(/\s+/g, "_")) == loadarray[10])
                            { 
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' selected>'+data.results[i].PropName+'</option>');
                            }
                            else 
                            {
                                jQuery("#spto_PropertyType").append('<option value='+data.results[i].PropName.replace(/\s+/g, '_')+' >'+data.results[i].PropName+'</option>');   
                            }
                            
                        
                        }
                        }, function(err){
                            //alert("Error occurred " + err.message);
                        });
                    }
                
                    function onclickResetsubmit()
            
                    {     jQuery.cookie('spot-on',null,{ expires: -7,path: '/' });
                        
                        jQuery.cookie("Query",null,{ expires: -7, path: '/' });
                        getSearchState();
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
                    //jQuery.cookie('spot-ln',null,{expires: -7,path:'/'});
                    jQuery('input[name="is_qsw"]').val(0);
            
                    jQuery.cookie('Query',null,{expires: -7,path: '/'}); 
                    submitClick=1; 
            
            
                    showProgress();  
            
           
                    setSearchState(); 
                    serviceCall(createQuery()+"&",0,0); 
                    jQuery.cookie('Query',createQuery(),{expires: 7,path: '/'}); 
                    
            
            
                    
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
            
            
            
                
                    function resultPagingClear()
                    {
                        submitClick=0;
                        PagingClick=0;
                        jQuery("#paging").empty();
                        jQuery("#bottompaging").empty();
                        jQuery("#resultsArea").empty();
                        jQuery("#resultscount").empty();   
            
                        jQuery('#listingcount').html("");
            
            
            
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
                        jQuery("#bottompaging").empty();
                        if(no>10)
                            no=10;
                            
                        for (var i = 1; i <= no; i++) {
                        var $ctrl = jQuery('<input/>').attr({ type: 'submit', id:i,value:i}).addClass("submitLink");
                        var $bctrl = jQuery('<input/>').attr({ type: 'submit', id:'bp'+i,value:i}).addClass("submitLink");
                        jQuery("#paging").append($ctrl);
                        jQuery("#bottompaging").append($bctrl);
                        };
                        
                        for (var i = 1; i <= no; i++) {
                        jQuery("#"+i).click(onclickPage);   
                        } 
                        
                        for (var i = 1; i <= no; i++) {
                        jQuery("#bp"+i).click(onclickPage);   
                        }			
                        //jQuery("#1").addClass("submitClick");
            
                    } 
                
                    function removePagingHighlight(no)
                    {
                    for (var i = 1; i <= 10; i++) {
                    jQuery("#"+i).removeClass("submitClick"); 
                    jQuery("#bp"+i).removeClass("submitClick"); 
                    }  
                    
                    if(no==0)
                    {
                        jQuery("#1").addClass("submitClick"); 
                        jQuery("#bp1").addClass("submitClick"); 
                    }
                    }    
                
                    function onclickPage()
                    {
                    showProgress();
                    hidebottompaging();
                    removePagingHighlight(1); 
                    var pageno=jQuery(this).val();
                    jQuery("#"+pageno).addClass("submitClick");	
                    jQuery("#bp"+pageno).addClass("submitClick");	
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
                    if(jQuery.cookie('Query') == null)
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
                            query = filterquery + providerKey + state + county + city + Area + SubDiv + SchDist + EleSch + JuHiSch + HiSch + zipcode + pricefrom + priceto + bathrooms +  bedrooms +  squarefeet + acreagefrom + acreageto + property + market + pricesort;       
            
                        
                        
                    }
                    else
                    {    var query="";
                        getSearchState();
                    
                        query=jQuery.cookie('Query');
                    // alert(jQuery.cookie('Query'));
                    
                    }
                    
                        return query;
                    }
                    
                    // SERVICE CALL
                    function serviceCall(query,no,call)
                    {  
                        var compname='';    
						var search_off='<?php echo spoton_list_hide("search_office") ?>';
                        var search_agn='<?php echo spoton_list_hide("search_agent") ?>';
                        compname='<?php echo $comp_name ?>';
                        var offname='OffNam,';
                        var AgName='AgNam,';
                        if(search_off == 1)
                        {
                              offname='';
                        }
                        if(search_agn == 1)
                        {
                              AgName='';
                        }
                        var requestUri="";
                        var multiLN="";
                        multiLN=getMultipleLN();
                        if(multiLN.length!=0 && submitClick==2)
                              requestUri = serviceUri + "GenericRetsSearchListings?" + query +"$select=PKey,LN,HouNo,DirPre,Stre,StrSuff,DirSuff,City,State,Zip,County,PropType,SquFeet,Price,Acre,Beds,Baths,CreDate,ImgNam,"+AgName+"Logo,"+offname+"AddrYN,LUD&$inlinecount=allpages";
                        else    
                              requestUri = serviceUri + "GenericRetsSearchListings?" + query +"$select=PKey,LN,HouNo,DirPre,Stre,StrSuff,DirSuff,City,State,Zip,County,PropType,SquFeet,Price,Acre,Beds,Baths,CreDate,Disclaimer,ImgNam,"+AgName+"Logo,"+offname+"AddrYN,LUD&" + "$skip="+String(no)+"&$top=50&$inlinecount=allpages";     


                        jQuery("#resultsArea").empty();
                        //jQuery("#resultsArea").text(requestUri);
                        OData.defaultHttpClient.enableJsonpCallback = true;			
                        if(call==0)
                        {            
                            OData.read(requestUri, function (data) { 
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
                                
                            }
                              
                                loadPageNo(Math.ceil(data.__count/50))
                                jQuery("#searchResultsTemplate").tmpl(data).appendTo("#resultsArea")  
                            }, function (err) {
                                jQuery("#resultsArea").text(JSON.stringify(err));
                            });   
                        }
                        else
                        {
                            OData.read(requestUri, function (data) {
                            var Disclaimer="";var LUD="";
                            for (var i = 0; i < data.results.length; i++) 
                            {	
                                LUD=data.results[0].LUD;
                                var dateValue = new Date(parseInt(LUD.replace("/Date(", "").replace(")/", "")));
                                lastUpdate_date=dateValue.getDate()+"/"+(dateValue.getMonth()+1)+"/"+dateValue.getFullYear();
                                Disclaimer=data.results[0].Disclaimer;
                                jQuery('#discl_info').html('"Disclaimer :'+Disclaimer+'--"Last Updated Date:'+lastUpdate_date+'"'); 
                            }
                                jQuery("#searchResultsTemplate").tmpl(data).appendTo("#resultsArea")  					
                            }, function (err) {
                                jQuery("#resultsArea").text(JSON.stringify(err));
                            });
                        }
                    
                    }
                    
                    function onclicklnSearchSubmit()
            
                    {   jQuery.cookie("Query",null,{ expires: -7, path: '/' });
                        jQuery.cookie('spot-on',null,{expires: -7,path:'/'});
                        jQuery("#bottompaging").hide();
                        submitClick=2; 
            
        
                        serviceCall(createQuery()+"&",0,0);
                        jQuery.cookie('Query',createQuery(),{expires: 7,path: '/'}); 
                    }
                    
                    function onclicklnResetsubmit()
            
                    {   jQuery.cookie("Query", null,{ expires: -7, path: '/' });
                    
                        jQuery.cookie('spot-ln', null,{expires: -7, path:'/'});
                        getSearchState();
                        jQuery("#paging").empty();
                        jQuery("#resultsArea").empty();
                        jQuery("#resultscount").empty();   
            
                        jQuery('#listingcount').html("");
                        clearMultipleLN();
            
                        }
                
			</script>

<?php get_footer(); ?>   
