<?php
/*
  Plugin name: OptimizeCustomize
  Plugin URI: https://github.com/s-n-i-p-e-r/
  Description: Custom membership forms for OptimizeMember
  Version: 1.0.0
  Author: sniper@openmail.cc
  Author URI: https://github.com/s-n-i-p-e-r/
  Text Domain: optimizecustomize
  Domain Path: /lang/
  License: GPL
*/

defined( 'ABSPATH' ) or die;

if(!class_exists('OptimizeCustomize')) {

    class OptimizeCustomize
    {
        const PLUGIN_DIR = '/optimizeCustomize';

        const VERSION = '1.0.0';

        function __construct()
        {

          //enqueue admin scripts
          add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);

          //menu in admin
          add_action('admin_menu', [$this, 'adminMenu']);

          //Shortcodes
          add_shortcode('optimize_customize', [$this, 'shortcodes']);

          //admin init settings
          add_action('admin_init', [$this, 'adminInit']);

          /*$this->maps_settings = get_option( 'maps_settings' );
          //print_r($this->maps_settings);
          register_activation_hook(__FILE__, array($this, 'maps_load_default_settings') ); //loads default settings for the plugin while activating the plugin
          //add_action( 'init', array($this, 'maps_session_init') ); //starts the session

          //add_action( 'admin_post_maps_markers_action', array($this, 'maps_markers_save'));
          //add_action('template_redirect', array($this, 'maps_markers_save'));
          //  add_action( 'admin_post_maps_restore_default', array($this, 'apif_restore_default') ); //restores default settings;


          add_action('rest_api_init', function () {
            register_rest_route( '/maps/postcode', '/position', [
              'methods' => 'GET',
              'callback' => array($this, 'getPostcodePosition')
            ]);
          });



          add_action( 'rest_api_init', function () {
            register_rest_route( '/maps', '/markers', [
              'methods' => 'POST',
              'callback' => array($this, 'addMapMarker')
            ]);
          });

          add_action( 'rest_api_init', function () {
            register_rest_route( '/maps', '/markers', [
              'methods' => 'DELETE',
              'callback' => array($this, 'removeMapMarker')
            ]);
          });

          add_action( 'rest_api_init', function () {
            register_rest_route( '/maps', '/markers/location', [
              'methods' => 'GET',
              'callback' => array($this, 'getLocation')
            ]);
          });
          */
        }


        // Admin Settings options section
        function adminSettingsSection()
        {
          print '<h1 class="title">
            <img src="' . plugins_url() . OptimizeCustomize::PLUGIN_DIR . '/assets/img/icon.png" title="logo" class="logo">
            Optimize Customize
          </h1>
          <h2>OptimizePress custom membership forms</h2>
          <hr/>';
        }

        //Admin settings key field
        function adminSettingsKey()
        {
            print '<input type="text" name="optimize_customize_setting_key" id="optimize_customize_api_key" value="' . get_option('optimize_customize_setting_key') . '"><br/>
              <a href="' . admin_url() . '/admin.php?page=ws-plugin--optimizemember-scripting" target="_blank">Get Optimize Remote API Key.</a><br/>
              <i>Scroll down to the "Pro API For Remote Operations" section and retrieve the value from the "Remote Operations API: Your secret API Key" textbox, paste that value in the textbox above.</i>';
        }

        //Admin settings code field
        function adminSettingsCode()
        {
            print '<textarea name="optimize_customize_setting_code" id="optimize-customize-code-editor">' . get_option('optimize_customize_setting_code') . '</textarea>';
        }

        function getSettingsCode()
        {
          return  get_option('optimize_customize_setting_code');
        }

        function adminSettingsPayments()
        {
          print '
          <select name="optimize_customize_settting_payments">
              <option value="1">Stripe</option>
          </select>';
        }

        //Admin Initialize settings fields
        function adminInit()
        {
            // Add section
           	add_settings_section(
          		'optimize_customize_section',
          		'',
          		[$this, 'adminSettingsSection'],
          		'optimize-customize'
          	);

           	//API Key - Add setting field
           	add_settings_field(
          		'optimize_customize_setting_key',
          		'OpimizePress API Key',
          		[$this, 'adminSettingsKey'],
          		'optimize-customize',
          		'optimize_customize_section'
          	);
           	//API Key - Register
            register_setting('optimize-customize', 'optimize_customize_setting_key');

            //Payments - Add setting field
            add_settings_field(
              'optimize_customize_setting_payments',
              'Payment Provider',
              [$this, 'adminSettingsPayments'],
              'optimize-customize',
              'optimize_customize_section'
            );
            //Payments - Register
            register_setting('optimize-customize', 'optimize_customize_setting_payments');

            //Form Code - Add setting field
            add_settings_field(
              'optimize_customize_setting_code',
              'Signup Form',
              [$this, 'adminSettingsCode'],
              'optimize-customize',
              'optimize_customize_section'
            );
            //Form Code - Register
            register_setting('optimize-customize', 'optimize_customize_setting_code');


        }

        //Add Admin Menu Item
        function adminMenu()
        {
            add_menu_page(__('OPMCustomize', 'optimize-customize' ), __( 'OPMCustomize', 'optimize-customize'), 'manage_options', 'optimize-customize', array($this, 'adminPage'), plugins_url() . self::PLUGIN_DIR . '/assets/img/icon.png' );

        }

        //show plugins admin page
        function adminPage()
        {
            include( 'inc/backend/page.php' );

        }

        //enqueue scripts for admin page
        function adminEnqueueScripts($hook)
        {
          $url = plugins_url() . self::PLUGIN_DIR ;

          wp_enqueue_style('optimize-customize-cm-style', $url . '/assets/vendor/codemirror/css/codemirror.css', [], self::VERSION);

          wp_enqueue_script('optimize-customize-cm', $url . '/assets/vendor/codemirror/js/codemirror.js', ['jquery'], self::VERSION, true);
          wp_enqueue_script('optimize-customize-cm-xml', $url . '/assets/vendor/codemirror/js/xml.js', ['jquery'], self::VERSION, true);
          wp_enqueue_script('optimize-customize-cm-javascript', $url . '/assets/vendor/codemirror/js/javascript.js', ['jquery'], self::VERSION, true);
          wp_enqueue_script('optimize-customize-cm-css', $url . '/assets/vendor/codemirror/js/css.js', ['jquery'], self::VERSION, true);
          wp_enqueue_script('optimize-customize-cm-htmlmixed', $url . '/assets/vendor/codemirror/js/htmlmixed.js', ['jquery'], self::VERSION, true);

        }

        function formSubmit()
        {
          \Stripe\Stripe::setApiKey('sk_test_0cnexLEtK2hISIrYazN3xn7g00YZWtMRZU');

          try
          {
            $customer = \Stripe\Customer::create([
              'email' => $_POST['stripeEmail'],
              'source'  => $_POST['stripeToken'],
            ]);

            $subscription = \Stripe\Subscription::create([
              'customer' => $customer->id,
              'items' => [['plan' => 'weekly_box']],
            ]);

            if ($subscription->status != 'incomplete')
            {
              //header('Location: thankyou.html');
            }
            else
            {
              //header('Location: payment_failed.html');
              //error_log("failed to collect initial payment for subscription");
            }
            exit;
          }
          catch(Exception $e)
          {
            //header('Location:oops.html');
            //error_log("unable to sign up customer:" . $_POST['stripeEmail'].
            //  ", error:" . $e->getMessage());
          }

        }

        //Shortcodes
        function shortcodes($atts)
        {
          $atts = shortcode_atts(['level' => '1', 'currency' => '', 'plan' => '0'], $atts, 'optimize_customize');

          ob_start();

          include( __DIR__ . '/inc/frontend/form.php');

          $html = ob_get_contents();
          ob_get_clean();

          return $html;
        }
        /*
         * Plugin Translation

        function maps_plugin_text_domain() {
          // /  load_plugin_textdomain( 'goog-maps-tools', false, basename( dirname( __FILE__ ) ) . '/languages/' );
        }
        */
        /**
         * Load Default Settings
         *
         */
        /*function maps_load_default_settings() {
          if( !get_option( 'maps_settings' ) ) {
            $maps_settings = $this->get_maps_default_settings();
            update_option( 'maps_settings', $maps_settings );
          }
        }
        */



        /**
         * Starts the session
         */
        /*function maps_session_init() {
            if( !session_id() && !headers_sent() ) {
                session_start();
            }
        }*/

        /**
         * Returns Default Settings
         */
         /*
        function get_maps_default_settings()
        {
            $maps_settings = [
              'default_lat' => '',
              'default_lng' => '',
              'access_token' => ''
            ];
            return $maps_settings;
        }
        */

        /**
          *   Register backend js and css
        **/
        /*function maps_register_admin_assets()
        {
          if( isset( $_GET['page'] ) && $_GET['page'] == 'goog-maps-tools' ) {
            wp_enqueue_style('maps-admin-style', MAPS_CSS_DIR . '/admin.css', [], null);
            wp_enqueue_script('maps-admin-script', MAPS_JS_DIR . '/admin.js', array('jquery'), null);
          }
        }
*/
  /*      function maps_register_frontend_assets()
        {
          wp_enqueue_style('ᘻaps.css', MAPS_CSS_DIR . '/maps.css', ['bootstrap'], null );

          //wp_enqueue_script('goog-maps', "https://maps.google.com/maps/api/js?key=" . self::MAPS_KEY . "&libraries=geometry", null, false);
          wp_enqueue_script('ᘻaps.js',  MAPS_JS_DIR . '/maps.js', ['jquery', 'goog-maps'], null, true);
        }
*/
        //slider shortcode
  /*      function maps_do_shortcode($atts)
        {
          $atts = shortcode_atts(['type' => ''], $atts, 'ᘻaps' );

          ob_start();

          include( __DIR__ . '/inc/frontend/maps.php' );
          $html = ob_get_contents();
          ob_get_clean();
          return $html;
        }
*/
/*
        public static function getMapMarkers()
        {
          global $wpdb;

          $sql = "SELECT * FROM markers";

          return $wpdb->get_results($sql, ARRAY_A);
        }
*/

        /**
          *
          *   Get markers
          *
        **/
  /*      function addMapMarker(WP_REST_Request $request)
        {
        //  var_dump($_POST);die;
          $qs = $request->get_query_params();

          $data = [
            'address' => $_POST['address'],
            'lat' => $_POST['lat'],
            'lon' => $_POST['lng'],
          ];

          global $wpdb;

          $table = 'markers';

          // next line will insert the data
          $wpdb->insert($table, $data, '%s');

          return (isset($wpdb->insert_id)) ? $wpdb->insert_id : -1;
        }
/*
        /**
          *   Remove Map Marker
          *
          *
        **/
  /*      function removeMapMarker(WP_REST_Request $request)
        {
        //  var_dump($_POST);die;
          $qs = $request->get_query_params();

          if (isset($qs['id'])) {

            global $wpdb;

            $table = 'markers';

            // next line will insert the data
            $wpdb->delete( $table, ['id' => $qs['id']]);

            return true;
          } else {
            return false;
          }
        }
*/


    }

    $opmCustomize = new OptimizeCustomize(); //initialization of plugin

}
?>
