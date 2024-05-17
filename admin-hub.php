<?php

/*
* Plugin Name:     Greenheart Admin Hub
* Plugin URI:      http://qstudio.us/
* Description:     A central Admin Menu for Greenheart Functionality to reside, allowing better wp-admin organization and user experience.
* Version:         1.0.1
* Author:          Greenheart International
* Author URI:      https:greenheart.org
* License:         GPL2
* Class:           GreenheartHub
* Text Domain:     GreenheartHub
* Domain Path:     languages/
*/



defined( 'ABSPATH' ) OR exit;

if ( ! class_exists( 'GreenheartHub' ) ) {

    // instatiate plugin via WP plugins_loaded - init was too late for CPT ##
    add_action( 'plugins_loaded', array ( 'GreenheartHub', 'get_instance' ), 4 );

    class GreenheartHub {

        // Refers to a single instance of this class. ##
        private static $instance = null;

        // Plugin Settings
        const version = '1.0.1';
        static $debug = true;
        const text_domain = 'greenhearthub'; // for backwards compatibility##


        /**
         * Creates or returns an instance of this class.
         *
         * @return  GreenheartHub     A single instance of this class.
         */
        public static function get_instance() : GreenheartHub
        {

            if ( 
                null == self::$instance 
            ) {

                self::$instance = new self;

            }

            return self::$instance;

        }
        
        
        /**
         * Instatiate Class
         * 
         * @since       0.2
         * @return      void
         */
        private function __construct() 
        {
            
            // activation ##
            register_activation_hook( __FILE__, array ( $this, 'register_activation_hook' ) );

            // deactvation ##
            register_deactivation_hook( __FILE__, array ( $this, 'register_deactivation_hook' ) );

            // set text domain ##
            add_action( 'init', array( $this, 'load_plugin_textdomain' ), 1 );
            
            // load libraries ##
            self::load_libraries();

        }

        // the form for sites have to be 1-column-layout
        public function register_activation_hook() {
                
            \add_option( __CLASS__, '1' );  
        }

        public function register_deactivation_hook() {

            \delete_option( __CLASS__ );
        }
 
        /**
         * Load Text Domain for translations
         * 
         * @since       1.7.0
         * 
         */
        public function load_plugin_textdomain() 
        {
            
            // set text-domain ##
            $domain = self::text_domain;
            
            // The "plugin_locale" filter is also used in load_plugin_textdomain()
            $locale = apply_filters('plugin_locale', get_locale(), $domain);

            // try from global WP location first ##
            load_textdomain( $domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo' );
            
            // try from plugin last ##
            load_plugin_textdomain( $domain, FALSE, plugin_dir_path( __FILE__ ).'library/language/' );
            
        }
        
        /**
         * Get Plugin URL
         * 
         * @since       0.1
         * @param       string      $path   Path to plugin directory
         * @return      string      Absoulte URL to plugin directory
         */
        public static function get_plugin_url( $path = '' ) 
        {

            return \plugins_url( $path, __FILE__ );
        }
        
        
        /**
         * Get Plugin Path
         * 
         * @since       0.1
         * @param       string      $path   Path to plugin directory
         * @return      string      Absoulte URL to plugin directory
         */
        public static function get_plugin_path( $path = '' ) 
        {

            return \plugin_dir_path( __FILE__ ).$path;
        }



        /**
        * Load Libraries
        *
        * @since        2.0
        */
		private static function load_libraries()
        {

           # if(\is_admin() ) : 
                require_once self::get_plugin_path( 'core/menu-parent.php' ); 
                require_once self::get_plugin_path( 'core/menu-pages.php' ); 
                require_once self::get_plugin_path( 'core/menu-methods.php');
                require_once self::get_plugin_path( 'core/menu-options.php');
                require_once self::get_plugin_path( 'menu/menu.php' ); 
                Greenheart\Hub\AdminMenu::get_instance();
                Greenheart\Hub\HubMenu::get_instance();
          #  endif;
        }
    }
}