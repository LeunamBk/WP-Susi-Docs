<?php
/*
Plugin Name: Susi-Docs
Plugin URI: http://mydomain.com
Description: My first widget
Author: Me
Version: 1.0
Author URI: http://mydomain.com
*/

// Block direct requests
if ( !defined('ABSPATH') )
    die('-1');
/*
add_action( 'widgets_init', function(){
    register_widget( 'Docs_Widget' );
});
*/



/**
 * Adds My_Widget widget.
 */
class Docs_Widget extends WP_Widget {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */

    private $assetsFolder;

    public function __construct() {

        $this->plugin_name = 'susi-protocols';
        $this->version = '1.0.0';
        $this->pluginFolder = plugins_url() .'/'. $this->plugin_name;

        parent::__construct(
            'Docs_Widget', // Base ID
            __('My Widget', 'text_domain'), // Name
            array( 'description' => __( 'My first widget!', 'text_domain' ), ) // Args
        );

        $this->enqueue_styles();

    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        include(ABSPATH.'/wp-content/plugins/susi-protocols/public/partials/views/protocols/tmpl/protocols.php');

    }

    /**
     * Register the stylesheets for the widget side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, $this->assetsFolder . "css/cssreset.css", array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name, $this->assetsFolder . "css/bootstrap.min.css", array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name, $this->assetsFolder . "css/taskman.css", array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the widget side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script( $this->plugin_name, $this->assetsFolder . "js/angular.min.js", array(), $this->version, false);
        wp_enqueue_script( $this->plugin_name, $this->assetsFolder . "js/app.js", array(), $this->version, false);
        wp_enqueue_script( $this->plugin_name, $this->assetsFolder . "js/services.js", array(), $this->version, false);
        wp_enqueue_script( $this->plugin_name, $this->assetsFolder . "js/controllers.js", array(), $this->version, false);

    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

}