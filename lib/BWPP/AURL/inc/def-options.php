<?php
	$this->add_option(
		new BWP_Options(BWP_AURL_GENERAL, array(
				'enable_instant_login'		=> 'yes',
				'enable_pp_link'			=> 'yes',
				'enable_tos_link'			=> 'yes',
				'sel_pp_page'				=> 0,
				'sel_tos_page'				=> 0,
				'inp_com_name'				=> '',
				'inp_com_state'				=> ''
			), 'db', array('sel_pp_page' => 'int', 'sel_tos_page' => 'int'))
	);
?>