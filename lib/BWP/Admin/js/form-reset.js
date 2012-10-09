jQuery(document).ready(function(){
	jQuery('.bwp-option-box-inside input[name^="reset_"]').click(function() {
		var inputs = jQuery(this).parents('.bwp-option-box-inside').find(':input');
		inputs.each(function() {
			var input_name = this.name;
			if (typeof bwp_form_reset_l10n[input_name] !== 'undefined')
			{
				if (this.type == 'checkbox' || this.type == 'radio')
					if (bwp_form_reset_l10n[input_name] == '')
						jQuery(this).attr('checked', false);
					else
						jQuery(this).attr('checked', true);
				else
					jQuery(this).val(bwp_form_reset_l10n[input_name]);
			}
		});
		return false;
	});
});