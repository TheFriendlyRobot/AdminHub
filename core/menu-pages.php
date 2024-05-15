<?php
namespace Greenheart\Hub;

/**
 * Idea from https://gist.github.com/IsaacVanName/753287/c7c3c20255d1b2b7d9cfcbd7cd799c2c88337553
 * PostType::add('headers')->singular('header')->register();
 * AdminMenuPage::add('Analytics')->setCallback
 * 
 * This Class is called sort of like a singleton. It has a constructor but you can't access it directly. 
 * This is so you can chain methods off the constructor, which you couldn't otherwise do in PHP (as of 8)
 * 
 * @package Greenheart Core
 * @subpackage Hub
 */
class AdminMenuPage extends AdminMenu {
    protected $title;
    protected $menu_title;
    protected $cap;
    protected $slug;
    protected $callback;
    protected $icon;
    protected $menuposition;
    protected $type = 'submenu';
    protected $parent = null;
    public $ready = false;

    private function __construct($title, $menu_title, $cap, $icon, $menuposition){
        $this->title = $title;
        $this->menu_title = $menu_title ?? $title;
        $this->cap = $cap;
        $this->slug = \sanitize_title( $title );
        $this->icon = $icon;
        $this->menuposition = $menuposition;
    }
    public static function add(string $title, string $menu_title = null, string $cap = 'manage_options', string $icon = 'dashicons-admin-generic', int $menuposition = 25 ) : static
    {  

        return new AdminMenuPage($title, $menu_title, $cap, $icon, $menuposition);
    }

    public function setSlug( string $slug ){
        $this->slug = $slug;
        return $this;
    }
    public function setCallback( $func )
    {
        if (is_callable($func)) {
            $this->callback = $func;
            $this->ready = true;
        } elseif (is_string($func) && method_exists('Greenheart\Hub\Methods', $func)) {

            $this->callback = array('Greenheart\Hub\Methods', $func);
            $this->ready = true;
        } else {
            $this->ready = false;
            if(defined('WP_DEBUG') && true == WP_DEBUG && defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ){
                error_log("Tried to set menu page for " . $this->title . ": Invalid Callback.");
            }
        }
        return $this;
    }

    public function setMainMenu(){
        $this->type = 'menu';
        return $this;
    }

    public function setSubmenu(string $parent_slug = \GreenheartHub::text_domain ){
        $this->parent = $parent_slug;
        $this->type = 'submenu';
        return $this;
    }
    public function setOptions(){
        $this->type = 'options';
        return $this;
    }
    public function register() : AdminMenuPage
    {
        $title = $this->title;
        $menu_title = $this->menu_title;    
        $cap = $this->cap;   
        $slug = $this->slug;
        $callback = $this->callback;
        $icon = $this->icon;
        $menuposition = $this->menuposition;
        $parent = $this->parent;
        switch($this->type){
            case("menu") : 

                \add_action('admin_menu', function() use ($title, $menu_title, $cap, $slug, $callback, $icon, $menuposition) {
                    \add_menu_page(
                        $title, 
                        $menu_title,     
                        $cap,     
                        $slug,    
                        $callback, 
                        $icon,
                        $menuposition   
                    );
                }, 10);
                return $this;
                break;
            case("submenu") : 

                \add_action('admin_menu', function() use ($title, $menu_title, $parent, $cap, $slug){
                    \add_submenu_page(
                        $parent,
                        $title,
                        $menu_title,
                        $cap,
                        $slug,
                        $this->callback
                    );
                    #\add_submenu_page('headers-footers', 'Headers', 'Headers', 'manage_options', 'edit.php?post_type=headers');
                }, 11);
                break;
            case("options") : 
                \add_action('admin_menu', function() use ($title, $menu_title, $cap, $slug, $callback, $icon, $menuposition) {
                    \add_options_page(
                        $title, 
                        $menu_title,     
                        $cap,     
                        $slug,    
                        $callback, 
                        $icon,
                        $menuposition   
                    );
                }, 10);
                return $this;
                break;

            default : return $this;
        }
        return $this;
    }
}