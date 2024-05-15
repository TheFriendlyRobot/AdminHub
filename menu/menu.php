<?php
namespace Greenheart\Hub;
/**
 * The main HubMenu class creates the Greenheart Menu as well as lets other classes/modules register their 
 * own menus under one Greenheart menu area.
 * 
 * 
 * There are a lot of Singletons on this Menu plugin...not sure what that means? There has to be a 
 * better way than all these singles but each part really does need to accumulate from everywhere.
 *   */
class HubMenu {
    public static $instance = null;

    private static $pages;

    public static function get_instance() : HubMenu
    {

        if ( 
            null == self::$instance 
        ) {

            self::$instance = new self;

        }
        return self::$instance;

    }
    /**
     * We create the main Greenheart Hub menu here. In addition to creating the main admin menu we provide an 
     * API for other classes to add submenu pages (or even top-level menu pages). 
     *  
     * */ 
    private function __construct() {
        AdminMenu::Create(
            AdminMenuPage::add('Greenheart Admin Menu','Greenheart Admin Menu', 'edit_pages', 'dashicons-admin-site', 15)
                ->setSlug( \GreenheartHub::text_domain )
                ->setMainMenu()
                ->setCallback('main_menu')
      );
    }
    /*
    Wrapper function for AdminMenu 
    */
    public static function Create( AdminMenuPage $page ) : void 
    {

        AdminMenu::Create( $page );
    }
}

HubMenu::get_instance();