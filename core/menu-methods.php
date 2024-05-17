<?php
namespace Greenheart\Hub;


class Methods extends AdminMenuPage {
    /* Site Options */

    public static function main_menu() {
        $AdminMenu = AdminMenu::get_instance();
        
        $AdminLinks = array_map( function($page, $index){
            #Skip the first page..as it is this page and we're doing it manually
            if($index!==0 && $page->ready && \current_user_can( $page->cap )){
                $htmlstring = '<a href="#" class="nav-tab" data-tab-slug="'.$page->slug.'"';
                $htmlstring.= ' onclick="MAHW_switchActiveTab(event);">'.$page->title.'</a>';
                
                return $htmlstring;
            } else {
                return '';
            }
        },$AdminMenu::$pages, array_keys($AdminMenu::$pages));

        $AdminBody = array_map( function($page, $index){
            #Skip the first page..as the first page is this page and will call this function in a death loop
            if($index!==0 && $page->ready && \current_user_can( $page->cap )){
                ob_start();
                call_user_func($page->callback);
                $htmlstring = ob_get_clean();
                $content = '<div class="tab-content" data-tab-slug="'.$page->slug.'">' . $htmlstring . '</div>';
                return $content;
            } else {
                return '';
            }
        },$AdminMenu::$pages, array_keys($AdminMenu::$pages));

        ?>
            <!-- Our admin page content should all be inside .wrap -->
            <script>
                const MAHW_switchActiveTab = (e) => {
                    console.log("clicked");
                    e.preventDefault();
                    let targetSlug = e.target.hasAttribute('data-tab-slug') ? e.target.dataset.tabSlug : false;
                    console.log(targetSlug);
                    let wrap = e.target.closest('.main-admin-hub-wrap');
                    let tabs = e.target.closest('.nav-tab-wrapper');
                    let content = wrap.querySelector('.admin-hub-content');
                    if(targetSlug){
                        tabs.children.forEach( child=>{
                            if(child.dataset.tabSlug === targetSlug){
                                child.classList.add('nav-tab-active');
                            } else {
                                child.classList.remove('nav-tab-active');
                            }
                        });
                        content.children.forEach( child=>{
                            if(child.dataset.tabSlug === targetSlug){
                                child.classList.add('tab-active');
                            } else {
                                child.classList.remove('tab-active');
                            }     
                        });
                    }
                }
            </script>
            <style>
                .main-admin-hub-wrap .tab-content {display: none;}
                .main-admin-hub-wrap .tab-content.tab-active {display: block;}
            </style>
            <div class="main-admin-hub-wrap">

                <h1><?php echo esc_html( \get_admin_page_title() ); ?></h1>
                <!-- Example tabs -->
                <nav class="nav-tab-wrapper">
                
                <a href="#" class="nav-tab nav-tab-active" data-tab-slug="<?php echo \GreenheartHub::text_domain?>" onclick="MAHW_switchActiveTab(event);">Site Options</a>
                <?php  foreach($AdminLinks as $link) echo $link; ?>
                </nav>

                <!-- DO CURRENT TAB -->
                <div class="admin-hub-content">
                    <div class="tab-content tab-active" data-tab-slug="<?php echo \GreenheartHub::text_domain?>">
                       <!-- Do manual Options page stuff here -->
                       <?php Options::textarea('banner_message', 'Banner Message'); ?>
                       <!-- Echo out the Options we've collected from other Plugins or Modules -->
                       <?php foreach( $AdminMenu::$options as $Option){
                            error_log("CALLING PROBLEM OPTION: " . print_r($Option, true));
                            switch($Option['callback']){
                                case "text":
                                    if(isset($Option['args']['helptext'])){
                                        echo '<p>'. $Option['args']['helptext'] . '</p>';
                                    }
                                    Options::text($Option['name'], $Option['title'], $Option['group']);
                                    break;
                                case "textarea":
                                    if(isset($Option['args']['helptext'])){
                                        echo '<p>'. $Option['args']['helptext'] . '</p>';
                                    }
                                    Options::textarea($Option['name'], $Option['title'], $Option['group']);
                                    break;
                                case "select":
                                    if(isset($Option['args']['helptext'])){
                                        echo '<p>'. $Option['args']['helptext'] . '</p>';
                                    }
                                    Options::select($Option['name'], $Option['title'], $Option['group'], $Option['options']);
                                    break;
                                default: break;
                            }
                       } ?>

                    </div>
                    <?php foreach($AdminBody as $content) echo $content; ?>

                </div>
            </div>
        <?php
    }
}