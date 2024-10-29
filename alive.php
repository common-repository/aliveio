<?php
/*
Plugin Name: alive.io
Plugin URI: http://www.alive.io
Description: This plugin displays a bloggers alive.io status to their blog and shows optional messages depending on if the blogger is alive or not.
Author: alive_io
Version: 1.0.3
Author URI: http://www.alive.io

*/

	require_once dirname( __FILE__ ) . '/admin.php';


//function that checks the xml of user and returns true if alive and false if deceased.
function showResult()
{
$opt_name = 'mt_alive_username'; //name of option
$username = get_option($opt_name);//get value from wprdpress database
/*user's alive.io url*/    
$url = 'http://'.$username.'.alive.io/interface/xml';

$xml = getXMLfromURL($url);
if($xml)
{

$result = $xml->value[0];

return $result;
}else{
return -2;
}

}

//Action to initialize widget
add_action('widgets_init','alive_init');
//Register alive widget  
function alive_init(){
register_widget('Widget_alive');
}



//Widget class to define functions of widget
class Widget_alive extends WP_Widget{
//define how widget looks on widgets page of user
function Widget_alive(){
$widget_ops = array( 'classname' => 'alive', 'description' => 'A widget that displays if the blogger is alive or deseaced.' );
$control_ops = array( 'width' => 200, 'height' => 300, 'id_base' => 'alive-widget' );
$this->WP_Widget( 'alive-widget', 'alive.io', $widget_ops, $control_ops );

}
function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$alive_msg = $instance['alive_msg'];
                $dead_msg = $instance['dead_msg'];
		$show_image = isset( $instance['show_image'] ) ? $instance['show_image'] : false;
                /*find out if user is alive or not*/
                $status=showResult();
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
$opt_name = 'mt_alive_username'; //name of option
$username = get_option($opt_name);//get value from wprdpress database
$user_url = "http://".$username.".alive.io";
		/* Display alive_msg when alive and dead_msg when deceased from widget settings. */
        
if($status == 1){
		?>
       		<a href="<?echo $user_url ?>" target="_blank">  <p ALIGN="CENTER"> 
		<?php
		if($show_image)
			echo '<img align="center" src="http://'.$username.'.alive.io/interface/image" alt= "alive.io"/><br />'; 
		
		 echo $alive_msg?></a></p><?php
		} 
elseif ($status==0){
         	?>       
		<a href= "<?echo $user_url ?>" target="_blank"> <p ALIGN="CENTER">
		<?php 
		
		if($show_image)
			echo '<img align="center" src="http://'.$username.'.alive.io/interface/image" alt= "alive.io"/><br />'; 

		echo $dead_msg  ?></a></p>
		<?php
		}
else{
		echo "please check alive.io settings";
}           

		/* After widget (defined by themes). */
echo $after_widget;
}


//Udate the settings selected by user
function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] =  $new_instance['title'] ;
		$instance['alive_msg'] =  $new_instance['alive_msg'] ;
                $instance['dead_msg'] =  $new_instance['dead_msg'] ;
		$instance['show_image'] = $new_instance['show_image'];	

		return $instance;
	}
//options form of the widget
function form( $instance ) {

 if(showResult() == -1 || !get_option('mt_alive_username') )
{
echo '<p>Your widget is not ready! <br>  Goto Settings->Alive.io settings and enter valid username to start using this widget. </p>';
}else{ 

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'alive.io', 'alive_msg' => 'I am Alive','dead_msg'=>'I am deceased', 'show_image' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p ALIGN="CENTER"> HTML OK FOR ALL FIELDS </p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'alive_msg' ); ?>">Message while Alive:</label>
			<input id="<?php echo $this->get_field_id( 'alive_msg' ); ?>" name="<?php echo $this->get_field_name( 'alive_msg' ); ?>" value="<?php echo $instance['alive_msg']; ?>" style="width:100%;" />
		</p>
<p>
			<label for="<?php echo $this->get_field_id( 'dead_msg' ); ?>">Message when you have Deceased:</label>
			<input id="<?php echo $this->get_field_id( 'dead_msg' ); ?>" name="<?php echo $this->get_field_name( 'dead_msg' ); ?>" value="<?php echo $instance['dead_msg']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<input value="1" class="checkbox" type="checkbox" <?php checked( $instance['show_image'], true ); ?> id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_image' ); ?>">Display alive.io image?</label>
		</p>

<?php
       }
       
	}
}

?>
