<?php
namespace Greenheart\Hub;

class AdminMenu {
    private static $pages = [];
    public static $instance = null;

    private function __construct(){
        \add_action( 'wp_loaded', [__CLASS__, 'register_pages']);
    }

    public static function get_instance() : AdminMenu
    {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function Create( ...$args ) : void
    {
        foreach ($args as $page) {
            if ($page instanceof AdminMenuPage ) {
                self::$pages[] = $page;
            } 
        }
    }

    public static function register_pages() {
        // Register each page here using self::$pages
        error_log("REGISTER PAGES CALLED");
        error_log(print_r(self::$pages, true));
        foreach (self::$pages as $page) {
            error_log("Registering menu page for: " . $page->title);
            error_log(print_r($page,true));
            $page->register();
        }
    }
}
AdminMenu::get_instance();