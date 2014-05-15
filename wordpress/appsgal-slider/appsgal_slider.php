<?php
/*
    Plugin Name: AppsGallery Slider
    Plugin URI: http://apps.usa.gov
    Description: Created for use by Wordpress sites who want to show federal apps gallery mobile applications -<a href="http://apps.usa.gov" title="Apps.USA.Gov">Apps.USA.Gov</a>.
    Author: CTAC
    Version: 1.0.1
    Author URI: http://ctacorp.com
    AppsGallery Slider is released under GPL:
    http://www.opensource.org/licenses/gpl-license.php
*/

function appsgal_addto_header() {
  ?>
  <script type="text/javascript">var $jq = jQuery.noConflict();</script>
  <?php
}

add_action( 'wp_head', 'appsgal_addto_header' );
add_action( 'admin_head', 'appsgal_addto_header' );

function appsgal_short_code($atts) {
	ob_start();
    extract(shortcode_atts(array(
		"name" => ''
	), $atts));
	echo show_appsgal($name);
	$output = ob_get_clean();
	return $output;
}
add_shortcode('appsgal', 'appsgal_short_code');

function show_appsgal($slider_name = ''){ 
    
    if($slider_name != "")
		$option = $slider_name;
	else
		$option = appsgal_slider_get_slider_from_url();

	appsgal_slider_head_scripts();
	appsgal_embed_headerfuncs($slider_name);
    return get_appsgal_html($option,$option);
}

if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', plugins_url());
}

register_activation_hook(__FILE__,'appsgal_slider_plugin_install');
register_deactivation_hook( __FILE__, 'appsgal_slider_plugin_uninstall');

function appsgal_slider_plugin_uninstall() {

	global $wpdb;
	$table_name = $wpdb->prefix . "appsgal_slider"; 
    $appslider_data = $wpdb->get_results("SELECT option_name FROM $table_name ORDER BY id");
    foreach ($appslider_data as $data) {
        delete_option($data->option_name);
        }
    $sql = "DROP TABLE " . $table_name;
		$wpdb->query( $sql );
}

function appsgal_slider_install(){
    global $wpdb;
	$table_name = $wpdb->prefix . "appsgal_slider"; 
    
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  option_name VARCHAR(255) NOT NULL DEFAULT  'appsgal_slider_defaults',
		  active tinyint(1) NOT NULL DEFAULT  '0',
		  PRIMARY KEY (`id`),
          UNIQUE (
                    `option_name`
            )
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

}

function appsgal_slider_plugin_install() {
    add_option('appsgal_slider_defaults', appsgal_slider_defaults());
    appsgal_slider_install();
    global $wpdb;
	$table_name = $wpdb->prefix . "appsgal_slider"; 
    $sql = "INSERT IGNORE INTO " . $table_name . " values ('','appsgal_slider_defaults','1');";
    $wpdb->query( $sql );
}

if ( is_admin() ){

	add_action('admin_menu', 'appsgal_slider_plugin_admin_menu');
	//select 1 from list of sliders.. (first).. append to params for edit subpage
	function appsgal_slider_plugin_admin_menu() {
	    add_menu_page('Add AppsGallery Slider ', 'AppsGallery Slider', 'publish_posts', 'appsgal_slider', 'appsgal_slider_main', WP_PLUGIN_URL.'/appsgal-slider/siteslogosmall.png');
	    add_submenu_page('appsgal_slider','Edit slider','Edit Slider', 'publish_posts', 'add-appslider', 'appsgal_slider_admin_page');
	    add_submenu_page('appsgal_slider','Uninstall AppsGal Slider','Uninstall Apps Gallery Slider', 'publish_posts', 'uninstall-AppsGal-Slider', 'appsgal_slider_uninstall');
	    

	    //add_options_page('AppsGallery Slider Settings', 'AppsGallery Slider', 'administrator', 'appsgal-slider', 'appsgal_slider_admin_page');
	}

	add_action('wp_print_scripts', 'appsgal_slider_head_scripts');

	function appsgal_slider_defaults() {

		$default = array(
			'appsapilist' => 'http://apps.usa.gov/apps-gallery/api/registrations.json',
			'randomize' => 'false',
			'numrows' => 2,
			'totalapps' => 14,
			'iconheight1' => 100,
			'iconheight2' => 100,
			'iconwidth1' => 80,
			'iconwidth2' => 80,
			'scroll_forward1' => "true",
			'scroll_forward2' => "false",
			'auto-play1' => "true",
			'auto-play2' => "true",
			'appsperrow1' => 7,
			'appsperrow2' => 7,
			'num_visible1' => 7,
			'num_visible2' => 7,
			'delay1' => 0,
			'delay2' => 0,
			
			'movement_speed1' => 4000,
			'movement_speed2' => 4000,
			
			'borderColor' => "000000",
			'borderColor1' => "",
			'borderColor2' => "",
			'hover_pause1' => "true",
			'hover_pause2' => "true",
			'show-nav' => "false",
			
			'appgal_img1' => plugins_url('images/gsa_logo.jpg',__FILE__),
			'appgal_link1' => "http://gsa.gov/",
			'appgal_title1' => "GSA",
			'appgal_img2' => plugins_url('images/cia_logo.png',__FILE__),
			'appgal_link2' => "http://cia.gov/",
			'appgal_title2' => "CIA",
			'appgal_img3' => plugins_url('images/fbi_logo.png',__FILE__),
			'appgal_link3' => "http://fbi.gov/",
			'appgal_title3' => "FBI",
			'appgal_img4' => plugins_url('images/hhs_logo.jpg',__FILE__),
			'appgal_link4' => "http://hhs.gov/",
			'appgal_title4' => "HHS",
			'appgal_img5' => plugins_url('images/cdc_logo.jpg',__FILE__),
			'appgal_link5' => "http://cdc.gov/",
			'appgal_title5' => "CDC",
			'appgal_img6' => plugins_url('images/nga_logo.gif',__FILE__),
			'appgal_link6' => "http://nga.gov/",
			'appgal_title6' => "NGA",
			'appgal_img7' => plugins_url('images/epa_logo.png',__FILE__),
			'appgal_link7' => "http://epa.gov/",
			'appgal_title7' => "EPA",
			'appgal_img8' => plugins_url('images/gsa_logo.jpg',__FILE__),
			'appgal_link8' => "http://gsa.gov",
			'appgal_title8' => "GSA2",
			'appgal_img9' => plugins_url('images/cia_logo.png',__FILE__),
			'appgal_link9' => "http://cia.gov",
			'appgal_title9' => "CIA2",
			'appgal_img10' => plugins_url('images/fbi_logo.png',__FILE__),
			'appgal_link10' => "http://fbi.gov",
			'appgal_title10' => "FBI2",
			'appgal_img11' => plugins_url('images/hhs_logo.jpg',__FILE__),
			'appgal_link11' => "http://hhs.gov",
			'appgal_title11' => "HHS2",
			'appgal_img12' => plugins_url('images/cdc_logo.jpg',__FILE__),
			'appgal_link12' => "http://cdc.gov",
			'appgal_title12' => "CDC2",
			'appgal_img13' => plugins_url('images/nga_logo.gif',__FILE__),
			'appgal_link13' => "http://nga.gov",
			'appgal_title13' => "NGA2",
			'appgal_img14' => plugins_url('images/epa_logo.png',__FILE__),
			'appgal_link14' => "http://epa.gov",
			'appgal_title14' => "EPA2",
			);
		return $default;
	}

	if(isset($_POST['uninstallappslider']) && $_POST['uninstallappslider']){
	    appsgal_slider_plugin_uninstall();
	}

	if (isset($_POST['appsgal_slider-reset']) && $_POST['appsgal_slider-reset'] == 1) { 
	    $option=$_GET['edit'];
	    update_option($option, appsgal_slider_defaults());
	    $message = '<div class="updated" id="message"><p><strong>Settings Reset to Default</strong></p></div>';
	}
	else
	{
	    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') 
	    {
	        $message = '<div class="updated" id="message"><p><strong>Settings Saved</strong></p></div>';
	        $variable = $_POST['option01'];
	        update_option('appsgal-slider-options', $variable);
	    }
	}

	add_action('admin_head', 'appsgal_embed_headerfuncs');

}
//------End Admin Section  --- Begin Public Sections

function appsgal_embed_headerfuncs($slider_in = '')
{
	echo "<style type=\"text/css\">".appsgal_embed('css',$slider_in)."</style>";
	echo "<script type=\"text/javascript\">".appsgal_embed('arrays',$slider_in)."</script>";
	echo "<script type=\"text/javascript\">".appsgal_embed('js',$slider_in)."</script>";
}

function appsgal_slider_head_scripts() {
    wp_enqueue_script('jquery');
    wp_register_script('appsgal_slider', plugins_url('aslider.js',__FILE__), array('jquery'));
	wp_enqueue_script('appsgal_slider');

	wp_register_script('appsgal_slider_custom', plugins_url('custom.js',__FILE__), array('jquery'));
	wp_enqueue_script('appsgal_slider_custom');

	wp_register_script ( 'colorpicker-js', plugins_url('colorpicker.js',__FILE__), array('jquery'));
    wp_enqueue_script  ('colorpicker-js' );

   	wp_register_style('colorpicker-css', plugins_url('colorpicker.css',__FILE__));
    wp_enqueue_style( 'colorpicker-css');

    wp_register_style('appsgal-css', plugins_url('style.css',__FILE__));
    wp_enqueue_style('appsgal-css');
}

function appsgal_slider_get_slider_from_url()  //will get 'edit' parameter from url and return name of slider
{
	$option = '';
	if(@$_GET["edit"] && @$_GET["edit"] != '')
		$option=$_GET['edit'];
	else
	    $option='appsgal_slider_defaults';
	return $option;
}

function appsgal_embed($type_in = '',$slider_name) //returns slider css, javascript for randomize or javascript for slider
{
	$script_embed = '';
	if($slider_name != "")
		$option = $slider_name;
	else
		$option = appsgal_slider_get_slider_from_url();
	
	$options = get_option($option);
	if($options == (''||NULL))
		return '';
	
	$script_embed_css = '';
	
		for($z = 1;$z<=$options['numrows'];$z++)
		{
			$script_embed_css .= '#appsgalrow'.$z.' .apps-gal-item img{width:'.@$options['iconwidth'.$z].'px !important;height:'.@$options['iconheight'.$z].'px !important;}'.' ';
			if(@$options['borderColor'.$z] != '')
				$script_embed_css .= '#app_container'.$z.'{border:1px solid #'.$options['borderColor'.$z].';}'.' ';
		}
		$script_embed_css .= ' #appsgal_'.$option.'_container {border:1px dashed #'.@$options['borderColor'].' !important;}';
	
	if($type_in == 'css')  //if css requested return here
		return $script_embed_css;
	else
	{
		$array_embed = '';
		$script_embed_array = 'jQuery(document).ready(function( $ ) {';
		$script_embed .= $script_embed_array;
				$total_shown = 0;
				for($y = 1; $y <= $options['numrows']; $y++)
				{
					$total_shown += $options['num_visible'.$y];
				}
		$array_embed .= '
		window.visibleCollection = [';
			for($h = 1; $h <= $total_shown; $h++)
			{
				$array_embed .= '"'.@$options['appgal_link'.$h].'"';
				$array_embed .= ($h<$total_shown) ? ',' : '';
			}
		$array_embed .= '];
		window.hiddenCollection = [';
			for($i = $total_shown+1; $i <= $options['totalapps']; $i++)
			{
				$array_embed .= '"'.@$options['appgal_link'.$i].'"';
				$array_embed .= ($i<$options['totalapps']) ? ',' : '';
			}
		$array_embed .= '];
		window.visibleCollectionImage = [';
			for($h = 1; $h <= $total_shown; $h++)
			{
				$array_embed .= '"'.@$options['appgal_img'.$h].'"';
				$array_embed .= ($h<$total_shown) ? ',' : '';
			}
		$array_embed .= '];
		window.hiddenCollectionImage = [';
			for($i = $total_shown+1; $i <= $options['totalapps']; $i++)
			{
				$array_embed .= '"'.@$options['appgal_img'.$i].'"';
				$array_embed .= ($i<$options['totalapps']) ? ',' : '';
			}
		$array_embed .= '];
		window.visibleCollectionTitle = [';
			for($h = 1; $h <= $total_shown; $h++)
			{
				$array_embed .= '"'.@$options['appgal_title'.$h].'"';
				$array_embed .= ($h<$total_shown) ? ',' : '';
			}
		$array_embed .= '];
		window.hiddenCollectionTitle = [';
			for($i = $total_shown+1; $i <= $options['totalapps']; $i++)
			{
				$array_embed .= '"'.@$options['appgal_title'.$i].'"';
				$array_embed .= ($i<$options['totalapps']) ? ',' : '';
			}
		$array_embed .= '];';

		if($type_in == 'arrays')  //if arrays requested return here.
			return $script_embed_array . $array_embed . '});';
		else
		{
			for($z = 1;$z<=$options['numrows'];$z++)  //appsgal javascript loop
			{
				$direction = ($z == 1) ? "forward" : "backwards";
				
				if($options['num_visible'.$z] != '' && $options['movement_speed'.$z] != '' && $options['auto-play'.$z] != '')
				{
					$script_embed .= '
				$("#appsgalrow'.$z.'").appsgal({
					visibleItems: '.$options['num_visible'.$z].',
					animationSpeed: '.$options['movement_speed'.$z].',
					autoPlay: '.(($options['auto-play'.$z] == "true") ? "true" : "false").',
					autoPlaySpeed: '.$options['delay'.$z].',
					showNav: '.(($options['show-nav'] == "true") ? "true" : "false").',
					randomize: '.$options['randomize'].',
					pauseOnHover: '.$options['hover_pause'.$z].',
					scrollForward: '.$options['scroll_forward'.$z].',
					divId: \'app_container'.$z.'\',
					enableResponsiveBreakpoints: false,
			    	responsiveBreakpoints: { 
			    		phone: { 
			    			changePoint:479,
			    			visibleItems: 1
			    		},
			    		portrait: { 
			    			changePoint:579,
			    			visibleItems: 2
			    		},  
			    		secondportrait: { 
			    			changePoint:767,
			    			visibleItems: 3
			    		}, 
			    		landscape: { 
			    			changePoint:867,
			    			visibleItems: 4
			    		},
			    		tablet: { 
			    			changePoint:959,
			    			visibleItems: 5
			    		}
			    	}
			    	});';
				}
				else
				{
					$script_embed .= '$("#appsgalrow'.$z.'").hide();';
				}
			}
			$script_embed .= '});';
		return $script_embed;
		}
	}
}

function appsgal_slider_admin_page()
{
	global $message;

	if($_GET["edit"]){
	$option=$_GET['edit'];
	}else{
	    $option='fedcms_slider_defaults';
	}
	
	?>
	<div class="wrap" style="width:820px;"><div id="icon-options-general" class="icon32"><br /></div>
	<?php echo $message; ?>
	<h2><?php _e("AppsGal Slider 1.0.1 Edit Slider Page [ ".$option." ]"); ?></h2>
	<form id="appsgal_form" method="post" action="options.php">
	<?php wp_nonce_field('update-options');
	$options = get_option($option);
    ?>	

	<div class="metabox-holder" style="max-width: 975px; margin:10px;">
        <div class="postbox">
        <h3><?php _e("General Settings", 'appsgal_slider'); ?></h3>
            <div id="general" class="inside" style="padding: 10px;">
            	<div class="inline-div">
	                 <p><?php _e("Number of Rows", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option; ?>[numrows]" value="<?php echo $options['numrows'] ?>" size="3" /></p>
	                 <?php /* <p><?php _e("Randomize?", 'appsgal_slider'); ?>:<select name="<?php echo $option. '[randomize]'; ?>"><option value="true" <?php selected('true', $options['randomize']); ?>>Yes</option><option value="false" <?php selected('false', $options['randomize']); ?>>No</option></select></p>*/ ?>
	                 <input type="hidden" name="<?php echo $option. '[randomize]'; ?>" value="false">
            	</div>
            	<div class="inline-div">
                <p><?php _e("Container Border Color", 'appsgal_slider'); ?>:<input id="borderColor" type="text" name="<?php echo $option; ?>[borderColor]" value="<?php echo $options['borderColor'] ?>" size="8" />&nbsp;HEX<br><small>(leave blank for none)</small>
                </p>
                <p><?php _e("Show Nav Arrows?", 'appsgal_slider'); ?>:<select name="<? echo $option; ?>[show-nav]"><option value="true" <?php selected('true', $options['show-nav']); ?>>Yes</option><option value="false" <?php selected('false', $options['show-nav']); ?>>No</option></select></p>;
                </div>
                <input type="hidden" name="<?php echo $option.'[original_rows]';?>" value="<?php echo $options['numrows'];?>">
          		<input type="hidden" name="<?php echo $option.'[totalapps]';?>" value="<?php echo $options['totalapps'];?>">
          		<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="<?php echo $option; ?>" />
                <span class="appsgal_submit"><p><input type="submit" class="button" value="<?php _e('Save Settings') ?>" /></p></span>
            </div>
        </div>
    </div>
    <div class="metabox-holder" style="width: 850px;margin-left:10px;">
        <div class="postbox">
        <h3><?php _e("Advanced Settings", 'appsgal_slider'); ?><div class="click" id="appsgal_adv_setts" style="float:right;cursor:pointer;"><?php _e("(+/-)", 'appsgal_slider'); ?></div></h3>
            <div class="inside" id="boxappsgal_adv_setts" style="padding: 10px;display:none;">

                <?php _e("AppsGallery Listing API URL", 'appsgal_slider'); ?>:<br />
                <input type="text" id="appsgal_api" name="<?php echo $option; ?>[appsapilist]" value="<?php echo $options['appsapilist'] ?>" size="62" /><br />
				<input type="hidden" name="appsgal_api-refresh" value="1" />
                <p><input type="submit" class="button" value="<?php _e('Save API Url') ?>" /><input type="submit" id="submit_refresh" class="button" value="<?php _e('Load Apps List') ?>" /></p>

            </div>
        </div>
    </div>
    <div class="metabox-holder" style="width: 850px;margin-left:10px;">
        <div class="postbox">
        <h3><?php _e("Available Apps", 'appsgal_slider'); ?><div class="click" id="appsgal_avl_apps" style="float:right;cursor:pointer;"><?php _e("(+/-)", 'appsgal_slider'); ?></div></h3>
            <div class="inside" id="boxappsgal_avl_apps" style="padding: 10px;display:none;">

                <?php _e("AppsGallery Listing API URL", 'appsgal_slider'); ?>:<br />
              	<?php
              	if(($options['totalapps']) <= 0)
					echo "No Apps Available";
				else
				{
          			?>
          			<table><thead><tr><td>Link URL</td><td>Image URL</td><td>Title</td><tr></thead><tbody>
          			<?php
	          				$base_row = '';
		              		for($x=1;$x<=$options['totalapps'];$x++)
		              		{
		              			?>
		              			<tr><td><input name="<?php echo $option.'['.$base_row.'appgal_link'.$x.']';?>" type="text" value="<?php echo $options[$base_row.'appgal_link'.$x];?>" size="40"></td><td><input name="<?php echo $option.'['.$base_row.'appgal_img'.$x.']';?>" type="text" value="<?php echo $options[$base_row.'appgal_img'.$x];?>" size="45"></td><td><input name="<? echo $option;?>[appgal_title<?php echo $x;?>]" type="text" value="<?php echo $options['appgal_title'.$x];?>" size="30"></td></tr>
		              			<?php
		              		}
              		?>
              		</tbody>
              		</table>
              		<?php
              	}
              	echo '<br/>';
              	?>

            </div>
        </div>
    </div>
    <?php
    for($i=1;$i<=$options['numrows'];$i++)
    {
    ?>
    <div class="metabox-holder" style="width: 850px;margin-left:10px;">
        <div class="postbox">
        <h3><?php _e("Row #".$i." Configuration", 'appsgal_slider'); ?><div class="click" id="appsgal_row<?php echo $i;?>" style="float:right;cursor:pointer;"><?php _e("(+/-)", 'appsgal_slider'); ?></div></h3>
            <div class="inside" id="boxappsgal_row<?php echo $i;?>" style="padding: 10px;display:none;">

            <div class="inline-div">
	                <p><?php _e("Number of Apps", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option.'[appsperrow'.$i.']'; ?>" value="<?php echo $options['appsperrow'.$i] != "" ? $options['appsperrow'.$i] : "7"; ?>" size="3" /></p>
	                 <p><?php _e("Number Visible", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[num_visible'.$i.']'; ?>" value="<?php echo $options['num_visible'.$i] != "" ? $options['num_visible'.$i] : "7"; ?>" size="3" /></p>
	                <p><?php _e("Movement speed", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[movement_speed'.$i.']'; ?>" value="<?php echo $options['movement_speed'.$i] != "" ? $options['movement_speed'.$i] : "4000"; ?>" size="3" />&nbsp;in ms</p>
	                <p><?php _e("Delay", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[delay'.$i.']'; ?>" value="<?php echo $options['delay'.$i] != "" ? $options['delay'.$i] : "0"; ?>" size="3" />&nbsp;in ms</p>
	                <p><?php _e("Scroll Direction", 'appsgal_slider'); ?>:<select name="<?php echo $option. '[scroll_forward'.$i.']'; ?>"><option value="true" <?php selected('true', $options['scroll_forward'.$i]); ?>>Forward</option><option value="false" <?php selected('false', $options['scroll_forward'.$i]); if($options['scroll_forward'.$i] == '' && $i%2==0)echo 'selected';?>>Backwards</option></select></p>
            	</div>
            	<div class="inline-div">
                <p><?php _e("Border Color", 'appsgal_slider'); ?>:<input id="borderColor" type="text" name="<?php echo $option. '[borderColor'.$i.']'; ?>" value="<?php echo $options['borderColor'.$i] != "" ? $options['borderColor'.$i] : ""; ?>" size="8" />&nbsp;HEX
                </p>
                <p><?php _e("Max Icon Height", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[iconheight'.$i.']'; ?>" value="<?php echo $options['iconheight'.$i] != "" ? $options['iconheight'.$i] : "100"; ?>" size="3" />in px
                </p>
                <p><?php _e("Max Icon Width", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[iconwidth'.$i.']'; ?>" value="<?php echo $options['iconwidth'.$i] != "" ? $options['iconwidth'.$i] : "80"; ?>" size="3" />in px
                </p>
                <p><?php _e("Stop on hover", 'appsgal_slider'); ?>:<select name="<?php echo $option. '[hover_pause'.$i.']'; ?>"><option value="true" <?php selected('true', $options['hover_pause'.$i]); ?>>Yes</option><option value="false" <?php selected('false', $options['hover_pause'.$i]); ?>>No</option></select>
	            </p>
                <?php /*<p><?php _e("Font family", 'appsgal_slider'); ?>:<select name="<?php echo $option. '[fontFamily'.$i.']'; ?>"><option value="'Trebuchet MS', Helvetica, sans-serif" <?php selected("'Trebuchet MS', Helvetica, sans-serif", $options['fontFamily'.$i]); ?>>'Trebuchet MS', Helvetica, sans-serif</option><option value="Arial, Helvetica, sans-serif" <?php selected('Arial, Helvetica, sans-serif', $options['fontFamily'.$i]); ?>>Arial, Helvetica, sans-serif</option><option value="Tahoma, Geneva, sans-serif" <?php selected('Tahoma, Geneva, sans-serif', $options['fontFamily'.$i]); ?>>Tahoma, Geneva, sans-serif</option><option value="Verdana, Geneva, sans-serif" <?php selected('Verdana, Geneva, sans-serif', $options['fontFamily'.$i]); ?>>Verdana, Geneva, sans-serif</option><option value="Georgia, serif" <?php selected('Georgia, serif', $options['fontFamily'.$i]); ?>>Georgia, serif</option><option value="'Arial Black', Gadget, sans-serif" <?php selected("'Arial Black', Gadget, sans-serif", $options['fontFamily'.$i]); ?>>'Arial Black', Gadget, sans-serif</option><option value="'Bookman Old Style', serif" <?php selected("'Bookman Old Style', serif", $options['fontFamily'.$i]); ?>>'Bookman Old Style', serif</option><option value="'Comic Sans MS', cursive" <?php selected("'Comic Sans MS', cursive", $options['fontFamily'.$i]); ?>>'Comic Sans MS', cursive</option><option value="'Courier New', Courier, monospace" <?php selected("'Courier New', Courier, monospace", $options['fontFamily'.$i]); ?>>'Courier New', Courier, monospace</option><option value="Garamond, serif" <?php selected("Garamond, serif", $options['fontFamily'.$i]); ?>>Garamond, serif</option><option value="'Times New Roman', Times, serif" <?php selected("'Times New Roman', Times, serif", $options['fontFamily'.$i]); ?>>'Times New Roman', Times, serif</option><option value="Impact, Charcoal, sans-serif" <?php selected("Impact, Charcoal, sans-serif", $options['fontFamily'.$i]); ?>>Impact, Charcoal, sans-serif</option><option value="'Lucida Console', Monaco, monospace" <?php selected("'Lucida Console', Monaco, monospace", $options['fontFamily'.$i]); ?>>'Lucida Console', Monaco, monospace</option><option value="'MS Sans Serif', Geneva, sans-serif" <?php selected("'MS Sans Serif', Geneva, sans-serif", $options['fontFamily'.$i]); ?>>'MS Sans Serif', Geneva, sans-serif</option></select></p> */?>
                <?php /*<input type="hidden" name="<?php echo $option. '[opacity'.$i.']'; ?>" value="<?php echo $options['opacity'.$i] != "" ? $options['opacity'.$i] : "1.0"; ?>" size="3" /></p>*/?>
                <?php /*<p><?php _e("Offset", 'appsgal_slider'); ?>:<input type="text" name="<?php echo $option. '[offset'.$i.']'; ?>" value="<?php echo $options['offset'.$i] != "" ? $options['offset'.$i] : "0"; ?>" size="3" />in px</p>*/?>
               	<p><?php _e("Auto Play", 'appsgal_slider'); ?>:<select name="<?php echo $option. '[auto-play'.$i.']'; ?>"><option value="true" <?php selected('true', $options['auto-play'.$i]); ?>>Yes</option><option value="false" <?php selected('false', $options['auto-play'.$i]); ?>>No</option></select></p>
                </div>
                <span class="appsgal_submit"><p><input type="submit" class="button" value="<?php _e('Save Settings') ?>" /></p></span>
            </div>
        </div>
    </div>
    <?php
	}?>
	</form>
		<?php
		echo get_appsgal_html($option,$option);
		?>
		<form method="post" style="clear:both;">
			<input type="hidden" name="appsgal_slider-reset" value="1" />
			<input type="hidden" name="action" value="reset" />
			<p><input type="submit" class="button-primary" onclick="return confirm('Are you sure you want to reset to default settings?')" value="<?php _e('Reset to Defaults') ?>" /></p>
		</form>
<?php/*
		<div id="target">
		  Click here
		</div>
		<div id="target2">
		</div>
		<div id="target3">
		</div>
*/?>			
	<?php
}

	function appsgal_slider_main()
	{
	    ?>
	    <div class="wrap" style="width:820px;"><div id="icon-options-general" class="icon32"><br /></div>
	    <h2>AppsGal Slider 1.0.1 Settings</h2>
	    <div class="metabox-holder" style="width: 820px; float:left;">
	    <small>Welcome to AppsGal Slider 1.0.1</small>
	     <div class="inside">
	     <br />
	     </div>
	     </div>
	<?php
	//AppsGal Slider Functions
	    
	if(@$_GET['add'])
	{
	    $option=$_POST['option_name'];
	    if(!get_option($_POST['option_name']))
	    {
	     if($option){
	            $option = preg_replace('/[^a-z0-9\s]/i', '', $option);  
	            $option = str_replace(" ", "_", $option);
	            global $wpdb;
	            $table_name = $wpdb->prefix . "appsgal_slider"; 
	             $options = get_option($option);
	            if($options)
	            {
	                $v_message= 'Unable to Add Slider,  different name';
	            }else{
	                $sql = "INSERT INTO " . $table_name . " values ('','".$option."','1');";
	                if ($wpdb->query( $sql )){
	                        add_option($option, appsgal_slider_defaults());
	                        $v_message= ' Slider successfully added';
	                        }
	                else{
	                        $v_message= 'Unable to Add Slider, can not insert Slider';
	                        }
	                };
	            }else{
	                    $v_message= ' Unable to Add Slider';
	                }
	    }else{
	        $v_message= ' Unable to Add Slider, try a different name';
	    }
	    ?>
	<div class="updated" id="message"><p><strong>
	    <?php echo $v_message; ?>
	</strong></p></div>
	<?php
	}

	if(@$_GET['delete'])
	{
	    $option=$_GET['delete'];
	    delete_option($option);
	    global $wpdb;
	    $table_name = $wpdb->prefix . "appsgal_slider"; 
	    $sql = "DELETE FROM " . $table_name . " WHERE option_name='".$option."';";
	        $wpdb->query( $sql );
	?>
	<div class="updated" id="message"><p><strong>
	    Slider Deleted
	</strong></p></div>
	<?php
	}

	if(@$_GET['deactivate'])  // if deactivate called, get id and set active to 0
	{
	    $id=$_GET['deactivate'];
	    global $wpdb;
	    $table_name = $wpdb->prefix . "appsgal_slider"; 
	    $sql = "UPDATE " . $table_name . " SET active='0' WHERE id='".$id."';";
	        $wpdb->query( $sql );
	        ?>
	<div class="updated" id="message"><p><strong>
	    Slider Deactivated
	</strong></p></div>
	<?php
	}
	if(@$_GET['activate'])  // if activate called, get id and set active to 1
	{
	    $id=$_GET['activate'];
	    global $wpdb;
	    $table_name = $wpdb->prefix . "appsgal_slider"; 
	    $sql = "UPDATE " . $table_name . " SET active='1' WHERE id='".$id."';";
	        $wpdb->query( $sql );
	        ?>
	<div class="updated" id="message"><p><strong>
	   AppsGal Activated
	</strong></p></div>
	<?php
	}
	?>
	    <table class="widefat" cellspacing="0">
	        <thead>
	            <tr>
	                <th scope="col" id="name" class="manage-column column-name" colspan="5">Table Of Apps Galleries</th>
	            </tr>
	            <tr style="background: #efefef;">
	            <td style="width: 100px;text-align:center;"> ID </td>
	            <td style="width: 100px;text-align:center;"> Slider Name </td>
	            <td style="width: 100px;text-align:center;"> Edit </td>
	            <td style="width: 100px;text-align:center;"> Active </td>
	            <td style="width: 100px;text-align:center;"> Delete </td>
	            </tr>
	            </thead>
	            <tbody>
	            <?php
	              get_appsgal_sliders();
	             ?>
	            </tbody>
	        </table>
	    </div>
	    <?php
	}

function get_appsgal_sliders()
{
    global $wpdb;$num=1;
    $table_name = $wpdb->prefix . "appsgal_slider"; 
    $fedslider_data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id");
    foreach ($fedslider_data as $data) { 
        
        if($data->active == 1)
        { $active='<a href="?page=appsgal_slider&deactivate='.$data->id.'" class="button">Deactivate</a>';
          $disabled = '';
        }
        else {
            $active='<a href="?page=appsgal_slider&activate='.$data->id.'" class="button">Activate</a>';
            $disabled='disabled="disabled"';
            }
        
       echo '<tr style="height:40px;"><td style="width: 100px;text-align:center;padding: 10px;" >'.$data->id.'</td><td style="width: 100px;text-align:center;padding: 10px;" valign="middle"> '.$data->option_name.' </td><td style="width: 100px;text-align:center;padding: 10px;" >
       <a href="?page=add-appslider&edit='.$data->option_name.'" class="button" '.$disabled.'>Edit</a>        
       </td><td style="width: 100px;text-align:center;padding: 10px;"> '.$active.' </td>
       <td style="width: 100px;text-align:center;padding: 10px;" > <a href="?page=appsgal_slider&delete='.$data->option_name.'" class="button">Delete</a> </td></tr>';
         $num++;}
         ?>
       <form method="post" action="?page=appsgal_slider&add=1">
       <tr style="height:60px;"> <td style="width: 100px;text-align:center;padding: 20px;"><?php echo ($data->id+1); ?> </td>
       <td style="padding: 20px;" colspan="2"><input type="text" id="option_name" name="option_name" size="70" />
       <font style="font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;* Do not use spaces, numbers or special characters in the name.</font>
       </td>
       <td style="width: 100px;text-align:center;padding: 20px;" colspan="2"><input type="submit" class="button-primary" style="padding: 10px 30px 10px 30px;" value="Add new Slider" />  </td>
       </tr>
       </form>
       <?php
}

	function appsgal_slider_uninstall()
	{
	  if($_POST['uninstallappslider']){

		echo '<div class="wrap"><div id="message" class="updated fade">';
	    echo '<p><h2> AppsGal Slider Successfully Uninstalled </h2></p></div>';
		echo '<h2>'.__('AppsGal Slider Uninstall', 'appsgal_slider').'</h2>';
		echo '<p><p><h3> AppsGal Slider Successfully Uninstalled </h3></p><strong>'.sprintf(__('Deactivate the AppsGal Slider from Plugins panel to Finish the Uninstallation.', 'appsgal_slider'), $deactivate_url).'</strong></p>';
		echo '</div>';    }else { ?>
		<form method="post" action="">
		<div class="wrap">
		<h2><?php _e('Uninstall AppsGal Slider', 'appsgal_slider'); ?></h2>
		<p>
			<?php _e('Deactivating AppsGal Slider plugin does not remove any data that may have been created, such as the slider data and the image links. To completely remove this plugin, you can uninstall it here.', 'appsgal_slider'); ?>
		</p>
		<p style="color: red">
			<strong><?php _e('WARNING:', 'appsgal_slider'); ?></strong><br />
			<?php _e('Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.', 'appsgal_slider'); ?>
		</p>
		<p style="color: red">
			<strong><?php _e('The following WordPress Options/Tables will be DELETED:', 'appsgal_slider'); ?></strong><br />
		</p>
		<table class="widefat" style="width: 200px;">
			<thead>
				<tr>
	            <?php
						global $wpdb;
		                $table_name = $wpdb->prefix . "appsgal_slider"; ?>
					<th><?php _e('Table: '.$table_name, 'appsgal_slider'); ?></th>
				</tr>
			</thead>
			<tr>
				<td valign="top" class="alternate">
					<ol>
					<?php
	                     $appslider_data = $wpdb->get_results("SELECT option_name FROM $table_name ORDER BY id");
	                      foreach ($appslider_data as $data) {
	                      echo '<li>'.$data->option_name.'</li>';
	                      }
					?>
					</ol>
				</td>
			</tr>
		</table>
		<p style="text-align: center;">
			<?php _e('Do you really want to uninstall AppsGal Slider?', 'appsgal_slider'); ?><br /><br />
			<input type="submit" name="uninstallappslider" value="<?php _e('UNINSTALL AppsGal Slider', 'appsgal_slider'); ?>" class="button-primary" onclick="return confirm('<?php _e('You Are About To Uninstall AppsGal Slider From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'appsgal_slider'); ?>')" />
		</p>
	</div>
	</form>
	  <?php    
	  }
	}

function get_appsgal_html($frame,$option)
{
	$options = get_option($option);
	$slider_code = '<div id="appsgal_'.$frame.'_container">';
	$apps_displayed = 0;

	for($j=1;$j<=$options['numrows'];$j++)
	{ 
		$slider_code .= '<ul id="appsgalrow'.$j.'" class="appsgal-row">';
		$display_apps = $apps_displayed;
		$num_left_to_show = $options['appsperrow'.$j];
		for($i=$display_apps+1;$i<=($display_apps+$num_left_to_show);$i++)
		{
			//drupal_set_message('appsdisplayed: '.$apps_displayed);
			if($apps_displayed == $options['totalapps'])
			{
				$num_left_to_show = (($display_apps + $num_left_to_show + 1) - $i);
				$i=0;
				$display_apps = 0;
				$apps_displayed = 0;
				//drupal_set_message('total reached');
			}
			else
			{
				$slider_code .= '
				<li>
					<a href="'.@$options['appgal_link'.$i].'" target="_blank" title="'.((@$options['appgal_title'.$i] != '') ? $options['appgal_title'.$i] : 'Click to View App Info').'"><img src="'.@$options['appgal_img'.$i].'" border="0" alt="'.((@$options['appgal_title'.$i] != '') ? $options['appgal_title'.$i] : 'Click to View App Info').'"/></a>
				</li>';
				$apps_displayed++;
			}
		}
		if($options['num_visible'.$j] != '')
			$slider_code .= '</ul><div class="clearout"></div>';
	}
	$slider_code .= '</div>';
	return $slider_code;
}

?>
