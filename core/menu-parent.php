<?php
namespace Greenheart\Hub;

class AdminMenu {
    public static $pages = [];
    public static $instance = null;
    public static $options = [];

    private function __construct(){
        \add_action( 'wp_loaded', [__CLASS__, 'register_pages']);
        $GLOBALS['AMInstance'] = 0;
        error_log("AdminMenu Instantiated:" . $GLOBALS['AMInstance']);
    }
    public function __clone() {
        throw new \Exception('Cloning of AdminMenu is not allowed.');
    }

    public function __wakeup() {
        throw new \Exception('Unserializing of AdminMenu is not allowed.');
    }

    public static function get_instance() : AdminMenu
    {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function AddOption( string $option_name, string $option_title = '', string $option_callback = 'text', string $option_group = '', array $option_args = [] )
    { 
        
        #real defaults
        if('' === $option_group) $option_group = $option_name . '_options';
        if('' === $option_title) $option_title = ucwords(str_replace('_', ' ', $option_name));

        $Option = [
            'name' => $option_name,
            'title' => $option_title,
            'group' => $option_group,
            'args' => $option_args
        ];

        if(is_callable(['Greenheart\Hub\Options', $option_callback])){

            if('select' === $option_callback){
                if(!empty($option_args)){
                    $Option['options'] = $option_args['options'] ?? $option_args;
                    $Option['callback'] = $option_callback; #must be text, textarea, select
                }
            } else {
                $Option['callback'] = $option_callback; #must be text, textarea, select
            }
            
            
        } 
        
        if(isset($Option['callback'])){
            self::$options[] = $Option;
        }


        
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
        foreach (self::$pages as $page) {
            $page->register();
        }
    }
}
