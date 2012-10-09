// Documentation : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('Spoton_IDX');
	
	tinymce.create('tinymce.plugins.Spoton_IDX', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('mceSpoton_IDX', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 420 + ed.getLang('Spoton_IDX.delta_width', 0),
					height : 80 + ed.getLang('Spoton_IDX.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register a button
			ed.addButton('Spoton_IDX', {
				title : 'Spoton_IDX.desc',
				cmd : 'mceSpoton_IDX',
				image : url + '/button.png'
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
					longname  : 'Saved Search Shortcode',
					author 	  : 'Khang Minh',
					authorurl : 'http://betterwp.net',
					infourl   : 'http://betterwp.net/contact/',
					version   : "1.0.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('Spoton_IDX', tinymce.plugins.Spoton_IDX);
})();