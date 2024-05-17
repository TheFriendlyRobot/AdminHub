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

   # private static $pages;

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
     * We create the main Greenheart Hub menu here.  
     *  
     * */ 
    private function __construct() {
        $Instance = AdminMenu::get_instance();
        $Instance::Create(
            AdminMenuPage::add('Greenheart Admin Menu','Greenheart Admin Menu', 'edit_pages', 'dashicons-admin-site', 15)
                ->setSlug( \GreenheartHub::text_domain )
                ->setMainMenu()
                ->setCallback('main_menu')
      );
    }
}