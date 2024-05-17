<?php
namespace Greenheart\Hub;

Options::run();

class Options {
    public static function run(){
        \add_action('admin_init', [__CLASS__,'register_options']);
    }
    public static function register_options(){
        \register_setting('banner_message_options', 'banner_message');
        \add_action('update_option', [__CLASS__, 'add_option_hook'], 10, 3);
        \add_action('wp_update_nav_menu', [__CLASS__, 'add_menu_save_hook']);
    }   

    /**
     * Add each option I want to tie a static header rebuild to
     */
    public static function add_option_hook($option_name, $old_value, $new_value){
        if('banner_message' === $option_name) \do_action('greenheart_updated_header_inputs');
    }
    /**
     * Run this action whenever someone saves a menu
     */
    public static function add_menu_save_hook( $menu_id ){
        \do_action('greenheart_updated_header_inputs');
        \do_action('greenheart_updated_footer_inputs');
    }

    /* Helper Functions */
    public static function textarea(string $slug, string $title = '', string $group = '') 
    {
        if('' === $title) $title = ucwords(str_replace('_', ' ', $slug ));
        if('' === $group) $group = $slug . '_options';
        ?>
        <form method="post" action="options.php">
            <?php \settings_fields($group); ?>
            <label for="<?php echo $slug ?>"><?php echo $title ?></label><br>
            <textarea id="<?php echo $slug ?>" name="<?php echo $slug ?>" rows="5" cols="80"><?php echo \esc_textarea(\get_option($slug)); ?></textarea>
            <?php \submit_button('Save'); ?>
        </form>
        <?php
    }

    public static function text(string $slug, string $title = '', string $group = '') 
    {
        if('' === $title) $title = ucwords(str_replace('_', ' ', $slug ));
        if('' === $group) $group = $slug . '_options';
        ?>
        <form method="post" action="options.php">
            <?php \settings_fields($group); ?>
            <label for="<?php echo $slug ?>"><?php echo $title ?></label>
            <input type="text" id="<?php echo $slug ?>" name="<?php echo $slug ?>" value="<?php echo \esc_attr(\get_option($slug)); ?>">
            <?php \submit_button('Save'); ?>
        </form>
        <?php
    }

    public static function selectbox(string $slug, string $title = '', string $group = '', array $options = []) 
    {
        if('' === $title) $title = ucwords(str_replace('_', ' ', $slug ));
        if('' === $group) $group = $slug . '_options';
        ?>
        <form method="post" action="options.php">
            <?php \settings_fields($slug . '_options'); ?>
            <label for="<?php echo $slug ?>"><?php echo $title ?></label>
            <select id="<?php echo $slug ?>" name="<?php echo $slug ?>">
                <?php foreach ($options as $value => $label) { ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected(get_option($slug), $value); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>
            <?php \submit_button('Save'); ?>
        </form>
        <?php
    }
}
