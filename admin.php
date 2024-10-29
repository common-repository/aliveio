<?php

//function to get xml contents from url
function getXMLfromURL($url) { 
      $Proxy = getenv("HTTP_PROXY"); 

      if (strlen($Proxy) > 1) { 
        $r_default_context = stream_context_get_default ( array 
                    ('http' => array( 
                        'proxy' => $Proxy, 
                        'request_fulluri' => True, 
                    ), 
                ) 
            ); 
        libxml_set_streams_context($r_default_context); 
      } 
      $daten = @simplexml_load_file($url); 
      return ($daten); 
    } 
?>
<?php
// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');
add_action('init','alive_Admin_Directions');


function alive_Admin_Directions(){
$opt_name = 'mt_alive_username';
if(!get_option($opt_name)){
echo "<div class='updated fade'><p><strong>".__('Alive.IO is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your Alive.IO Username</a> for it to work.'), "options-general.php?page=alivesettings")."</p></div>";
}

}

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('Alive.io Settings','menu-alive.io'), __('Alive.io Settings','menu-alive.io'), 'manage_options', 'alivesettings', 'mt_settings_page');

}
// mt_settings_page() displays the page content for the alive.io settings submenu
function mt_settings_page() {


    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt_name = 'mt_alive_username';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_alive_username';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

$alive_url="http://".$opt_val.".alive.io/interface/xml";


$xml = getXMLfromURL($alive_url);
if($xml)
{
$result = $xml->value[0];
}
if($xml && $result != -1)
{
      
         
        // Save the posted value in the database
        update_option( $opt_name, $opt_val );	

  //      echo $alive_url." url exist"

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Your username has been linked! Your alive.io widget is now live!', 'menu-alive.io' ); ?></strong></p></div>
<?php
}else
{

//echo "You have not registered with alive.io yet.";
$opt_val = "";
update_option( $opt_name, $opt_val );
?>
<div class="updated"><p><strong><?php _e('We couldn\'t link the username you entered to alive.io =(. Maybe you haven\'t <a href="http://www.alive.io/user/new" target="_blank">registered</a>?', 'menu-alive.io' ); ?></strong></p></div>
<?php
}

}

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Alive.io Settings', 'menu-alive.io' ) . "</h2>";
    // settings form
    ?>

<h1> Welcome to Alive.IO </h1>
<h2> Ever wondered how will your viewers know if you pass away?</h2>
<h2> Let <a href="http://www.alive.io" target="_blank">Alive.IO</a> help you keep your online identity on par with your real one.</h2>
<h2> Three simple steps to put your alive.io status on your blog! </h2>
<h3> 1. Put your <a href="http://www.alive.io" target="_blank">Alive.IO</a> username below.<br> 
     2. Go to Apearance->Widgets and pull in the alive.io widget to your widget area. <br> 
     3. In Widget Options Set the messages you want to display while alive or deceased.</h3>
<h3>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Username:", 'menu-alive.io' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save') ?>" />  <br>
Don't have an <a href="http://www.alive.io" target="_blank">Alive.IO</a> username?<a href="http://www.alive.io/" target="_blank">Register now!!</a>
</p>
</form>
</h3>
</div>
<?php
}



?>
