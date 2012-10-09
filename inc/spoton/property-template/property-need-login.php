<?php get_header(); ?>

<div class="soidx-msg_box msg_info soidx-property-details-wrapper cf sepH_b">
	<?php printf(__('Please register to view more properties and so we can tailor your property search experience. <a href="%s" title="%s" class="bwp_aurl_link soidx-links">Register/Login</a>.', $spoton_idx->domain), site_url('wp-login.php?action=register', 'login'), __('Click to register or login', $spoton_idx->domain)); ?>
</div>
<div class="soidx-text cf">
<div class="dp100 sepH_b">Registration Benefits:</div>
<div class="dp33">
<div class="box_c">
<div class="box_c_heading cf">Daily Property Alerts</div>
<div class="box_c_content cf"><img class="fl sepV_b" src=<?php echo SPOTON_IDX_IMAGES . '/icons/mail64.png'; ?> alt="email alerts" width="64" height="64" />Receive a daily email list of new homes as they hit the market.</div>
</div></div>
<div class="dp33">
<div class="box_c">
<div class="box_c_heading cf">Save to Favorites</div>
<div class="box_c_content cf"><img class="fl sepV_b" src=<?php echo SPOTON_IDX_IMAGES . '/icons/heart-add64.png'; ?> alt="save to favorites" width="64" height="64" />Save your favorite properties to view later (Coming Soon).</div>
</div></div>
<div class="dp33">
<div class="box_c">
<div class="box_c_heading cf">Stay Connected</div>
<div class="box_c_content cf"><img class="fl sepV_b" src=<?php echo SPOTON_IDX_IMAGES . '/icons/chat-conversation64.png'; ?> alt="connect with us" width="64" height="64" />Stay up to date with great local content and market conditions.</div>
</div></div>
</div>
<div class="dp100 soidx-text">Please read our Privacy Policy and Terms of Service or feel free to contact us directly if you have any questions.</div>
<?php get_footer(); ?>