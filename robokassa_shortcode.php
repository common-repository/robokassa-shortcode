<?php
/*
  Plugin Name: Robokassa Shortcode
  Plugin URI: 
  Description: Allows you to use Robokassa payment gateway with the shortcode button.
  Version: 1.4.1
  Author: Denis Alekseev
  Author URI: http://loom-studio.net/
 */
define(PLUGIN_PATH,dirname(__FILE__).'/');

include(PLUGIN_PATH.'inc/widget.php');
add_action('admin_menu', 'rksc_control_menu');
add_shortcode('rk_button', 'rksc_robokassa_sc' );
add_action('init','rksc_init');
add_action('media_buttons','rksk_add_icon',11);

function rksk_add_icon()
{
 $icon_url=network_site_url( '/wp-content/plugins/robokassa-shortcode/img/icon.png' );
//<a href="#TB_inline?width=480&inlineId=select_gravity_form" class="thickbox" id="add_gform" title="' . __("Add Gravity Form", 'gravityforms') . '">
 echo '<a href="#TB_inline?width=480&inlineId=insert_robokassa_shortcode" class="thickbox" id="insert_rksc" title="' . __("Insert robokassa shortcode", 'rksc') . '">';
 echo "<img src='{$icon_url}' /></a>";
}

function rksc_control_menu()
{
add_options_page('rk_plugins', 'Robokassa Shortcode', 'manage_options','rk_sc_config', 'rksc_config_cb' );
add_action('admin_init','rksc_register_settings');
}

function rksc_register_settings()
{
 register_setting( 'rksc-settings-group', 'rksc-settings-group');
 add_settings_section('rksc-settings-group', __('Main Settings','rksc'), 'rksc_section_text', 'rksc_config');
 add_settings_field('rksc_merchant', __('Merchant ID','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'merchant'));
 add_settings_field('rksc_key1', __('Key #1','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'key1'));
 add_settings_field('rksc_key2', __('Key #2','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'key2'));
 add_settings_field('rksc_test', __('Test mode','rksc'), 'rksc_setting_check', 'rksc_config', 'rksc-settings-group',array('id'=>'test'));
 add_settings_field('rksc_success_url', __('Success url','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'success_url'));
 add_settings_field('rksc_fail_url', __('Fail url','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'fail_url'));
 add_settings_field('rksc_sitepass', __('Site password','rksc'), 'rksc_setting_string', 'rksc_config', 'rksc-settings-group',array('id'=>'sitepass'));
 add_settings_field('rksc_send', __('Send email to admin','rksc'), 'rksc_setting_check', 'rksc_config', 'rksc-settings-group',array('id'=>'send'));
 add_settings_field('rksc_email', __('Email body','rksc'), 'rksc_setting_text', 'rksc_config', 'rksc-settings-group',array('id'=>'email'));
}

function rksc_config_cb()
{
?>
<form action="options.php" method="post">
<?php settings_fields('rksc-settings-group'); ?>
<?php do_settings_sections('rksc_config'); ?>
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
<?php
}
function rksc_section_text() {
echo '<p>'.__('Main description of this section here.').'</p>';
}
function rksc_setting_string($id) {
$options = get_option('rksc-settings-group');
$id=$id['id'];
echo "<input id='rksc_{$id}' name='rksc-settings-group[rksc_{$id}]' size='40' type='text' value='".$options['rksc_'.$id]."' />";
}

function rksc_setting_text($id)
{
$options = get_option('rksc-settings-group');
$id=$id['id'];
echo "<textarea id='rksc_{$id}' name='rksc-settings-group[rksc_{$id}]' cols='70' rows='10'>".$options['rksc_'.$id]."</textarea>"; 
}

function rksc_setting_check($id) {
$options = get_option('rksc-settings-group');
$id=$id['id'];
if($options['rksc_'.$id]==1){$val='checked';}
echo "<input id='rksc_{$id}' name='rksc-settings-group[rksc_{$id}]' type='checkbox' value='1' {$val}/>";
}

function rksc_robokassa_sc($attr)
{
 $fid=rand();
 $options = get_option('rksc-settings-group');
 wp_register_style( 'RobokassaStylesheet', plugins_url('styles.css', __FILE__) );
 wp_enqueue_style( 'RobokassaStylesheet' );
 $form='<div id="rkwarp'.$fid.'" style=" display: none;"><form id="rkform" action="/rksc/send.php" method="post" >';
 $form.=__('Name','rksc').':<input name="shpfirstname"><br />';
 $form.=__('email','rksc').':<input name="shpemail"><br />';
 if(!isset($attr['price']) || $attr['price']=='')
 {
 	$form.=__('Price','rksc').':<input name="price"><br />';
 }
 $hash='';
 foreach($attr as $key=>$value)
 {
  if($value!='')
  $form.="<input type='hidden' name='a_{$key}' value='{$value}'>";
 }

 $hash=md5(implode(':',$attr).":".$options['rksc_sitepass']);
 $form.="<input type='hidden' name='hash' value='{$hash}'>";
 $form.="<input class='rksksend' type='submit' value='Pay'>";
 $form.="<input type='button' class='rcskcancel' onclick='document.getElementById(\"rkwarp{$fid}\").style.display=\"none\";document.getElementById(\"rkbutton{$fid}\").style.display=\"inline-block\"' value='Cancel'>";
 $form.='</form></div>';
 $form.="<a id='rkbutton{$fid}' href='#' onclick='if(document.getElementById(\"rkwarp{$fid}\").style.display==\"block\"){document.getElementById(\"rkwarp{$fid}\").style.display=\"none\"}else{document.getElementById(\"rkwarp{$fid}\").style.display=\"block\";document.getElementById(\"rkbutton{$fid}\").style.display=\"none\"};return false;' class='classname'>Robokassa</a>";
 return $form;
}
function rksc_init()
{
 //echo get_locale();
 load_plugin_textdomain( 'rksc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 if(is_admin())
 {
 	if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	   add_action('admin_footer',  'add_rksc_popup');
	}
 }

 $options = get_option('rksc-settings-group');
if ( strpos($_SERVER["REQUEST_URI"], '/rksc/res.php')!==false ) {
 if(strtoupper(md5($_POST['OutSum'].":".$_POST['InvId'].":".$options['rksc_key2'].":shpemail=".$_POST['shpemail'].":shpfirstname=".$_POST['shpfirstname'].":shpsku=".$_POST['shpsku']))==$_POST['SignatureValue'])
 if($options['rksc_send']==1){
$mail=str_replace(array('[+email+]','[+sku+]','[+name+]','[+description+]','[+price+]'),array($_REQUEST['shpemail'],$_REQUEST['shpsku'],$_REQUEST['shpfirstname'],$_REQUEST['shpdescription'],$_POST['OutSum']),$options['rksc_email']);
wp_mail($_REQUEST['shpemail'], 'Subscription create', '<p>'.$mail.'</p>');
}
  die('OK'.$_POST['InvId']);
}
elseif(strpos($_SERVER["REQUEST_URI"], '/rksc/success.php')!==false )
{
wp_redirect($options['rksc_success_url']);
exit();
}
elseif(strpos($_SERVER["REQUEST_URI"], '/rksc/fail.php')!==false )
{
wp_redirect($options['rksc_fail_url']);
exit();
}
elseif(strpos($_SERVER["REQUEST_URI"], '/rksc/send.php')!==false )
{

 $hash='';
 foreach($_POST as $key=>$value)
 {
  if(strpos($key,'a_')!==false)
    $hash.=$value.":";
 }
 if(md5($hash.$options['rksc_sitepass'])!=$_REQUEST['hash'])
   return;
 $options = get_option('rksc-settings-group');
 $action_adr = 'https://merchant.roboxchange.com/Index.aspx';
 if($options['rksc_test']==1)
	$action_adr = 'http://test.robokassa.ru/Index.aspx';
 if(!isset($_POST['a_price']) && isset($_POST['price']))
	$_POST['a_price']=$_POST['price'];
 $summ=number_format($_POST['a_price'], 2, '.', '');
 $orderid=time();
 $rksk_path=plugin_dir_path(__FILE__);
 include($rksk_path.'inc/ReflectionTypeHint.php');
 include($rksk_path.'inc/UTF8.php');
//echo utf8_encode($_POST['a_desc']);die("test2");
 $signature=md5($options['rksc_merchant'].":".$summ.":".$orderid.":".$options['rksc_key1'].":shpdescription=".UTF8::convert_from($_POST['a_description']).":shpemail=".UTF8::convert_from($_POST['shpemail']).":shpfirstname=".UTF8::convert_from($_POST['shpfirstname']).":shpsku=".UTF8::convert_from($_POST['a_sku']));
 $args =array(
		// Merchant
		'MrchLogin' => $options['rksc_merchant'],
				
		// Session
		'Culture' => 'ru',
		'Desc'=>$_POST['a_description'],		
		// Order
		'OutSum' => $summ,
		'InvId' => $orderid,
		'Encoding'=>'utf-8',
		'SignatureValue' => $signature,
		"shpdescription"=>UTF8::convert_from($_POST['a_description']),
		"shpfirstname" => UTF8::convert_from($_POST["shpfirstname"]),
		"shpemail" => UTF8::convert_from($_POST["shpemail"]),
		"shpsku"=>UTF8::convert_from($_POST['a_sku']),
		);
 wp_register_style( 'RobokassaStylesheet', plugins_url('styles.css', __FILE__) );
 wp_enqueue_style( 'RobokassaStylesheet' );
 $url=$action_adr."?";
 $urlpar=array();
 $form='<div id="rkwarp" style="display:none;"><form id="rkform" action="'.$action_adr.'" method="post">';
 foreach($args as $key=>$value)
 {
  $form.='<input type="hidden" name="'.$key.'" value="'.$value.'">';
 }
 $form.="<input type='submit'>";
 $form.='</form></div>';
 $form.='<script>document.getElementById("rkform").submit();</script>';
 echo $form;

}

}
function add_rksc_popup()
{
?>
        <script>
            function InsertForm(){
		var sku = jQuery("#rksc_sku").val();
		var sku_val = (sku == '')?'': " sku=\"" + sku +"\"";
		var description = jQuery("#rksc_description").val();
		var description_val = (description == '')?'': " description=\"" + description +"\"";
		var price = jQuery("#rksc_price").val();
		var price_val = (price == '')?'': " price=\"" + price +"\"";
		
//[rk_button price="100" sku="test" description="Test payment"]
                window.send_to_editor("[rk_button " + sku_val + description_val + price_val + "]");
            }
        </script>

        <div id="insert_robokassa_shortcode" style="display:none;">
	<div class="wrap">
                    <input id="rksc_sku"  /> <label for="rksc_sku"><?php _e("SKU", "rksc"); ?></label><br />
                    <input id="rksc_description"  /> <label for="rksc_description"><?php _e("Description", "rksc"); ?></label><br />                
                    <input id="rksc_price"  /> <label for="rksc_price"><?php _e("Price", "rksc"); ?></label><br />
        <input type="button" class="button-primary" value="Add shortcode" onclick="InsertForm();"/>&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "gravityforms"); ?></a>
                
	</div>
        </div>
<?
}
?>