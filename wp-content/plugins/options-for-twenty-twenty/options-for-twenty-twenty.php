<?php
/*
 * Plugin Name: Options for Twenty Twenty
 * Version: 1.5.5
 * Plugin URI: https://webd.uk/product/options-for-twenty-twenty-upgrade/
 * Description: Adds powerful customizer options to modify all aspects of the default Wordpress theme Twenty Twenty
 * Author: Webd Ltd
 * Author URI: https://webd.uk
 * Text Domain: options-for-twenty-twenty
 */



if (!defined('ABSPATH')) {
    exit('This isn\'t the page you\'re looking for. Move along, move along.');
}



if (!class_exists('options_for_twenty_twenty_class')) {

	class options_for_twenty_twenty_class {

        public static $version = '1.5.5';

		function __construct() {

            add_action('customize_register', array($this, 'oftt_customize_register'), 999);
            add_action('wp_head' , array($this, 'oftt_header_output'));
            add_action('customize_controls_enqueue_scripts', array($this, 'oftt_enqueue_customizer_css'));
            add_action('customize_preview_init', array($this, 'oftt_enqueue_customize_preview_js'));
            add_action('customize_controls_enqueue_scripts', array($this, 'oftt_enqueue_customize_controls_js'));

            if (is_admin()) {

                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'oftt_add_plugin_action_links'));
                add_action('admin_notices', 'ofttCommon::admin_notices');
                add_action('wp_ajax_dismiss_oftt_notice_handler', 'ofttCommon::ajax_notice_handler');
                add_action('customize_controls_enqueue_scripts', 'ofttCommon::enqueue_customize_controls_js');

            } else {

                add_action('wp_footer', array($this, 'oftt_fix_focused_toggle'));
                add_filter('get_post_metadata', array($this, 'oftt_force_template'), 10, 4);
                add_action('wp_enqueue_scripts', array($this, 'oftt_enqueue_scripts'));

            }

            add_action('customize_register', 'webd_customize_register');

		}

		function oftt_add_plugin_action_links($links) {

			$settings_links = ofttCommon::plugin_action_links(admin_url('customize.php'));

			return array_merge($settings_links, $links);

		}

        function oftt_customize_register($wp_customize) {

            $section_description = ofttCommon::control_section_description();
            $upgrade_nag = ofttCommon::control_setting_upgrade_nag();




            $wp_customize->add_section('oftt_general', array(
                'title'     => __('General Options', 'options-for-twenty-twenty'),
                'description'  => __('Use these options to customise the overall site design.', 'options-for-twenty-twenty') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('disable_font', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('disable_font', array(
                'label'         => __('Disable Font', 'options-for-twenty-twenty'),
                'description'   => __('Speed up the site by disabling the WOFF2 font Inter Var.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'disable_font',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('force_page_template', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('force_page_template', array(
                'label'         => __('Force Page Template', 'options-for-twenty-twenty'),
                'description'   => __('Force all pages to use the same template.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'force_page_template',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Default Template', 'options-for-twenty-twenty'),
                    'templates/template-cover.php' => __('Cover Template', 'options-for-twenty-twenty'),
                    'templates/template-full-width.php' => __('Full Width Template', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('force_post_template', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('force_post_template', array(
                'label'         => __('Force Post Template', 'options-for-twenty-twenty'),
                'description'   => __('Force all posts to use the same template.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'force_post_template',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Default Template', 'options-for-twenty-twenty'),
                    'templates/template-cover.php' => __('Cover Template', 'options-for-twenty-twenty'),
                    'templates/template-full-width.php' => __('Full Width Template', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('force_fixed_background', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('force_fixed_background', array(
                'label'         => __('Force Fixed Background', 'options-for-twenty-twenty'),
                'description'   => __('Generates a workaround in CSS for Apple devices to keep the "parallax" effect in iOS.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'force_fixed_background',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_mobile_background', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_mobile_background', array(
                'label'         => __('Hide Mobile Background', 'options-for-twenty-twenty'),
                'description'   => __('Hides the background image on small screens as long pages result in a thin slice of the background image being shown which is undesirable.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'hide_mobile_background',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('text_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'text_color', array(
                'label'         => __('Text Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the default text colour.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
            	'settings'      => 'text_color'
            )));

            $wp_customize->add_setting('remove_link_underlines', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_link_underlines', array(
                'label'         => __('Remove Link Underlines', 'options-for-twenty-twenty'),
                'description'   => __('Remove the line below links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'remove_link_underlines',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('social_menu_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_menu_color', array(
                'label'         => __('Social Menu Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the color of the social menu logos in the expanded / mobile menu and the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
            	'settings'      => 'social_menu_color'
            )));

            $wp_customize->add_setting('social_menu_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_menu_background_color', array(
                'label'         => __('Social Menu Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the background color of the social menu logos in the expanded / mobile menu and the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
            	'settings'      => 'social_menu_background_color'
            )));

            $taxonomies = array(
                'blog' => 'Posts / Blog Page'
            );

            foreach(get_taxonomies(array('public' => 'true'), 'objects') as $taxonomy) {

                $taxonomies[$taxonomy->name] = __('Taxonomy: ', 'options-for-twenty-twenty') . ': ' . $taxonomy->label;

            }

            unset($taxonomies['post_format']);

            $wp_customize->add_setting('archive_grid_template', array(
                'default'       => array(),
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_multiple_options'
            ));
            $wp_customize->add_control(new webd_Customize_Control_Checkbox_Multiple($wp_customize, 'archive_grid_template', array(
                'label'         => __('Archive Grid Template', 'options-for-twenty-twenty'),
                'description'   => __('Show posts in a grid format on taxonomy pages.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_general',
                'settings'      => 'archive_grid_template',
                'choices'       => $taxonomies
            )));



            $wp_customize->add_section('oftt_navigation', array(
                'title'        => __('Nav Options', 'options-for-twenty-twenty'),
                'description'  => __('Use these options to customise the navigation.', 'options-for-twenty-twenty') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('navigation_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('navigation_width', array(
                'label'         => __('Navigation Width', 'options-for-twenty-twenty'),
                'description'   => __('Change the width of the site\'s navigation.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'navigation_width',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('100% (full width)', 'options-for-twenty-twenty'),
                    '128rem' => '128rem (1280px)',
                    '120rem' => '120rem (1200px)',
                    '100rem' => '100rem (1000px)',
                    '76.8rem' => '76.8rem (768px)',
                    '58rem' => '58rem (580px)'
                )
            ));

            $wp_customize->add_setting('transparent_navigation', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('transparent_navigation', array(
                'label'         => __('Transparent Navigation', 'options-for-twenty-twenty'),
                'description'   => __('Remove the background color behind the site navigation.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'transparent_navigation',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('nav_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_background_color', array(
                'label'         => __('Nav Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the background color of the site navigation.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'nav_background_color'
            )));

            $wp_customize->add_setting('site_title', array(
                'default'           => '',
                'transport'         => 'refresh',
                'sanitize_callback' => 'wp_kses_post'
            ));
            $wp_customize->add_control('site_title', array(
                'label'         => __('Site Title', 'options-for-twenty-twenty'),
                'description'   => __('Change the site tile in the navigation bar.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_title',
                'type'          => 'text'
            ));

            $wp_customize->add_setting('site_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_title_text_transform', array(
                'label'         => __('Site Title Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the site title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('site_title_font_size', array(
                'default'           => 24,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_title_font_size', array(
                'label'         => __('Site Title Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the site title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 50,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('site_title_font_weight', array(
                'default'           => 700,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_title_font_weight', array(
                'label'         => __('Site Title Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the site title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('site_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_title_color', array(
                'label'         => __('Site Title Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the site title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'site_title_color'
            )));

            $wp_customize->add_setting('hide_site_description', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_site_description', array(
                'label'         => __('Hide Site Description', 'options-for-twenty-twenty'),
                'description'   => __('Hide the site description in the nav bar (below the site title).', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'hide_site_description',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('site_description_below_title', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('site_description_below_title', array(
                'label'         => __('Site Description Below Title', 'options-for-twenty-twenty'),
                'description'   => __('Keep the site description below the site title on large screens.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_description_below_title',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('site_description_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_description_text_transform', array(
                'label'         => __('Site Description Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the site description.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_description_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('site_description_font_size', array(
                'default'           => 18,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_description_font_size', array(
                'label'         => __('Site Description Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the site description.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_description_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 50,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('site_description_font_weight', array(
                'default'           => 500,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_description_font_weight', array(
                'label'         => __('Site Description Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the site description.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'site_description_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('site_description_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_description_color', array(
                'label'         => __('Site Description Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the site description.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'site_description_color'
            )));

            $wp_customize->add_setting('nav_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('nav_text_transform', array(
                'label'         => __('Navigation Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'nav_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('nav_font_size', array(
                'default'           => 18,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('nav_font_size', array(
                'label'         => __('Navigation Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the menu links on the desktop horizontal menu.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'nav_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 50,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('mobile_nav_font_size', array(
                'default'           => 24,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('mobile_nav_font_size', array(
                'label'         => __('Mobile Nav Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the menu links on the expanded / mobile menu.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'mobile_nav_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 50,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('nav_font_weight', array(
                'default'           => 500,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('nav_font_weight', array(
                'label'         => __('Navigation Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'nav_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('nav_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_color', array(
                'label'         => __('Navigation Link Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'nav_color'
            )));

            $wp_customize->add_setting('nav_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_hover_color', array(
                'label'         => __('Navigation Link Hover Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font hover color of the menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'nav_hover_color'
            )));

            $wp_customize->add_setting('submenu_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'submenu_background_color', array(
                'label'         => __('Submenu Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the background color of submenus.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'submenu_background_color'
            )));

            $wp_customize->add_setting('submenu_link_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'submenu_link_color', array(
                'label'         => __('Submenu Link Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the submenu links on the desktop horizontal menu.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'submenu_link_color'
            )));

            $wp_customize->add_setting('submenu_link_hover_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'submenu_link_hover_color', array(
                'label'         => __('Submenu Link Hover Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font hover color of the submenu links on the desktop horizontal menu.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
            	'settings'      => 'submenu_link_hover_color'
            )));

            $wp_customize->add_setting('toggle_use_dashicon', array(
                'default'       => '',
                'type'          => 'theme_mod',
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('toggle_use_dashicon', array(
                'label'         => __('Use Hamburger Dashicon', 'sidemenu'),
                'description'   => __('Use a hamburger Dashicon on the button used to open the Expanded / Mobile menu.', 'sidemenu'),
                'section'       => 'oftt_navigation',
                'settings'      => 'toggle_use_dashicon',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_toggle_text', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_toggle_text', array(
                'label'         => __('Hide Toggle Text', 'options-for-twenty-twenty'),
                'description'   => __('Hide the words "Menu" and "Search" below the icons in the navigation.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'hide_toggle_text',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_expanded_social_menu', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_expanded_social_menu', array(
                'label'         => __('Hide Expanded Social Menu', 'options-for-twenty-twenty'),
                'description'   => __('Hide the social menu in the expanded / mobile menu.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_navigation',
                'settings'      => 'hide_expanded_social_menu',
                'type'          => 'checkbox'
            ));



            $wp_customize->add_section('oftt_header', array(
                'title'     => __('Header Options', 'options-for-twenty-twenty'),
                'description'  => __('Use these options to customise the header.', 'options-for-twenty-twenty') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('hide_archive_headers', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_archive_headers', array(
                'label'         => __('Hide Archive Headers', 'options-for-twenty-twenty'),
                'description'   => __('Hide all tag, category and search headers.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_archive_headers',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('archive_title_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('archive_title_width', array(
                'label'         => __('Archive Title Width', 'options-for-twenty-twenty'),
                'description'   => __('Change the width of the tag, category and search header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_title_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-twenty'),
                    '128rem' => '128rem (1280px)',
                    '120rem' => '120rem (1200px)',
                    '' => '100rem (1000px)',
                    '76.8rem' => '76.8rem (768px)',
                    '58rem' => '58rem (580px)'
                )
            ));

            $wp_customize->add_setting('hide_archive_title_prefix', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_archive_title_prefix', array(
                'label'         => __('Hide Archive Title Prefix', 'options-for-twenty-twenty'),
                'description'   => __('Hide the word "Tag" or "Category" in the archive title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_archive_title_prefix',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('archive_title_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('archive_title_text_align', array(
                'label'         => __('Archive Title Alignment', 'options-for-twenty-twenty'),
                'description'   => __('Align the archive title to the left, center or right.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    'left' => __('Left', 'options-for-twenty-twenty'),
                    '' => __('Center', 'options-for-twenty-twenty'),
                    'right' => __('Right', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('archive_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('archive_title_text_transform', array(
                'label'         => __('Archive Title Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the archive title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('archive_title_font_size', array(
                'default'           => 32,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('archive_title_font_size', array(
                'label'         => __('Archive Title Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the archive title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 80,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('archive_title_font_weight', array(
                'default'           => 700,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('archive_title_font_weight', array(
                'label'         => __('Archive Title Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the archive title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('archive_title_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archive_title_color', array(
                'label'         => __('Archive Title Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the archive title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
            	'settings'      => 'archive_title_color'
            )));

            $wp_customize->add_setting('archive_description_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('archive_description_width', array(
                'label'         => __('Archive Description Width', 'options-for-twenty-twenty'),
                'description'   => __('Change the width of the tag, category and search header description.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_description_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-twenty'),
                    '128rem' => '128rem (1280px)',
                    '120rem' => '120rem (1200px)',
                    '100rem' => '100rem (1000px)',
                    '76.8rem' => '76.8rem (768px)',
                    '' => '58rem (580px)'
                )
            ));

            $wp_customize->add_setting('archive_description_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('archive_description_text_align', array(
                'label'         => __('Archive Description Alignment', 'options-for-twenty-twenty'),
                'description'   => __('Align the archive description to the left, center or right.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_description_text_align',
                'type'          => 'select',
                'choices'       => array(
                    'left' => __('Left', 'options-for-twenty-twenty'),
                    '' => __('Center', 'options-for-twenty-twenty'),
                    'right' => __('Right', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('hide_all_headers', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_all_headers', array(
                'label'         => __('Hide Post / Page Headers', 'options-for-twenty-twenty'),
                'description'   => __('Hide all post / page headers on the site.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_all_headers',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('transparent_header', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('transparent_header', array(
                'label'         => __('Transparent Header', 'options-for-twenty-twenty'),
                'description'   => __('Remove the background color behind the site header.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'transparent_header',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('header_featured_image', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('header_featured_image', array(
                'label'         => __('Featured Image Header', 'options-for-twenty-twenty'),
                'description'   => __('Use the featured image as the header background.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_featured_image',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('header_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('header_width', array(
                'label'         => __('Post / Page Header Width', 'options-for-twenty-twenty'),
                'description'   => __('Change the width of post / page headers.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-twenty'),
                    '128rem' => '128rem (1280px)',
                    '120rem' => '120rem (1200px)',
                    '' => '100rem (1000px)',
                    '76.8rem' => '76.8rem (768px)',
                    '58rem' => '58rem (580px)'
                )
            ));

            $wp_customize->add_setting('hide_post_categories', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_post_categories', array(
                'label'         => __('Hide Post Categories', 'options-for-twenty-twenty'),
                'description'   => __('Prevent the categories from showing in the post header.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_post_categories',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('category_links_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'category_links_color', array(
                'label'         => __('Category Links Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the post category links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
            	'settings'      => 'category_links_color'
            )));

            $wp_customize->add_setting('archive_header_top_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('archive_header_top_padding', array(
                'label'         => __('Archive Header Top Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding above the header title on archive pages.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_header_top_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('archive_header_bottom_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('archive_header_bottom_padding', array(
                'label'         => __('Archive Header Bottom Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding below the header title on archive pages.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_header_bottom_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('header_top_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('header_top_padding', array(
                'label'         => __('Header Top Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding above the header title on pages and single posts.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_top_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('header_bottom_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('header_bottom_padding', array(
                'label'         => __('Header Bottom Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding below the header title on pages and single posts.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_bottom_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('hide_all_header_titles', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_all_header_titles', array(
                'label'         => __('Hide All Header Titles', 'options-for-twenty-twenty'),
                'description'   => __('Hide all header titles on the site.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_all_header_titles',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('header_title_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('header_title_text_align', array(
                'label'         => __('Header Title Alignment', 'options-for-twenty-twenty'),
                'description'   => __('Align the header title to the left, center or right.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    'left' => __('Left', 'options-for-twenty-twenty'),
                    '' => __('Center', 'options-for-twenty-twenty'),
                    'right' => __('Right', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('header_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('header_title_text_transform', array(
                'label'         => __('Header Title Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('page_title_font_size', array(
                'default'           => 84,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_title_font_size', array(
                'label'         => __('Page Title Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the page header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'page_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 100,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('post_title_font_size', array(
                'default'           => 84,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('post_title_font_size', array(
                'label'         => __('Post Title Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the post header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'post_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 100,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('archive_post_title_font_size', array(
                'default'           => 64,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('archive_post_title_font_size', array(
                'label'         => __('Archive Post Title Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the post header title on archive pages.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'archive_post_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 32,
                    'max'   => 128,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('header_title_font_weight', array(
                'default'           => 800,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('header_title_font_weight', array(
                'label'         => __('Header Title Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('header_title_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_title_color', array(
                'label'         => __('Header Title Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the header title.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
            	'settings'      => 'header_title_color'
            )));

            $wp_customize->add_setting('hide_excerpt', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_excerpt', array(
                'label'         => __('Hide Excerpt', 'options-for-twenty-twenty'),
                'description'   => __('Hide excerpts on single posts.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'hide_excerpt',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('excerpt_font_size', array(
                'default'           => 32,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('excerpt_font_size', array(
                'label'         => __('Excerpt Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the post header excerpt.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'excerpt_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 100,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('header_post_meta_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('header_post_meta_align', array(
                'label'         => __('Header Post Meta Alignment', 'options-for-twenty-twenty'),
                'description'   => __('Align the header post meta to the left, center or right.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_post_meta_align',
                'type'          => 'select',
                'choices'       => array(
                    'normal' => __('Left', 'options-for-twenty-twenty'),
                    '' => __('Center', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('header_post_meta', array(
                'default'       => array(
				    'author',
				    'post-date',
				    'comments',
				    'sticky'
				),
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_multiple_options'
            ));
            $wp_customize->add_control(new webd_Customize_Control_Checkbox_Multiple($wp_customize, 'header_post_meta', array(
                'label'         => __('Header Post Meta', 'options-for-twenty-twenty'),
                'description'   => __('Choose which meta items are shown in the header.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_header',
                'settings'      => 'header_post_meta',
                'choices'       => array(
                    'author' => __('Author', 'options-for-twenty-twenty'),
                    'categories' => __('Categories', 'options-for-twenty-twenty'),
                    'comments' => __('Comments', 'options-for-twenty-twenty'),
                    'post-date' => __('Post Date', 'options-for-twenty-twenty'),
                    'sticky' => __('Sticky', 'options-for-twenty-twenty'),
                    'tags' => __('Tags', 'options-for-twenty-twenty')
                )
            )));



            $wp_customize->add_section('oftt_content', array(
                'title'     => __('Content Options', 'options-for-twenty-twenty'),
                'description'  => __('Use these options to customise the content.', 'options-for-twenty-twenty') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('inject_breadcrumbs', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('inject_breadcrumbs', array(
                'label'         => __('Inject Breadcrumbs', 'options-for-twenty-seventeen'),
                'description'   => sprintf(wp_kses(__('Inject <a href="%s">Yoast SEO</a> or <a href="%s">Breadcrumb NavXT</a> breadcrumbs above page content.', 'options-for-twenty-twenty'), array('a' => array('href' => array()))), esc_url(admin_url('plugin-install.php?s=wordpress-seo&tab=search&type=term')), esc_url(admin_url('plugin-install.php?s=breadcrumb-navxt&tab=search&type=term'))),
                'section'       => 'oftt_content',
                'settings'      => 'inject_breadcrumbs',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_all_featured_images', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_all_featured_images', array(
                'label'         => __('Hide All Featured Images', 'options-for-twenty-twenty'),
                'description'   => __('Hide all featured images on the site.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'hide_all_featured_images',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('featured_images_to_top', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('featured_images_to_top', array(
                'label'         => __('Featured Images to Top', 'options-for-twenty-twenty'),
                'description'   => __('Move featured images to the top of the page.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'featured_images_to_top',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('full_width_featured_images', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('full_width_featured_images', array(
                'label'         => __('Full Width Featured Images', 'options-for-twenty-twenty'),
                'description'   => __('Make featured images fill the width of the page.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'full_width_featured_images',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('content_width', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('content_width', array(
                'label'         => __('Content Width', 'options-for-twenty-twenty'),
                'description'   => __('Change the width of the site\'s content.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'content_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-twenty'),
                    '128rem' => '128rem (1280px)',
                    '120rem' => '120rem (1200px)',
                    '100rem' => '100rem (1000px)',
                    '76.8rem' => '76.8rem (768px)',
                    '' => '58rem (580px)'
                )
            ));

            $wp_customize->add_setting('content_link_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_link_color', array(
                'label'         => __('Content Link Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of links in the content.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
            	'settings'      => 'content_link_color'
            )));

            $wp_customize->add_setting('content_link_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_link_hover_color', array(
                'label'         => __('Content Link Hover Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font hover color of links in the content.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
            	'settings'      => 'content_link_hover_color'
            )));

            $wp_customize->add_setting('bottom_post_meta', array(
                'default'       => array(
				    'tags'
				),
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_multiple_options'
            ));
            $wp_customize->add_control(new webd_Customize_Control_Checkbox_Multiple($wp_customize, 'bottom_post_meta', array(
                'label'         => __('Bottom Post Meta', 'options-for-twenty-twenty'),
                'description'   => __('Choose which meta items are shown below the post.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'bottom_post_meta',
                'choices'       => array(
                    'author' => __('Author', 'options-for-twenty-twenty'),
                    'categories' => __('Categories', 'options-for-twenty-twenty'),
                    'comments' => __('Comments', 'options-for-twenty-twenty'),
                    'post-date' => __('Post Date', 'options-for-twenty-twenty'),
                    'sticky' => __('Sticky', 'options-for-twenty-twenty'),
                    'tags' => __('Tags', 'options-for-twenty-twenty')
                )
            )));

            $wp_customize->add_setting('hr_width', array(
                'default'           => 0,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('hr_width', array(
                'label'         => __('Separator Border Width', 'options-for-twenty-twenty'),
                'description'   => __('Set the width of the post separator. Increasing this value will allow you to style the color and style of the separator.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'hr_width',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 0,
                    'max'   => 10,
                    'step'  => 1
                )
            ));

            $wp_customize->add_setting('hr_color', array(
                'default'       => '#6d6d6d',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hr_color', array(
                'label'         => __('Separator Border Colour', 'options-for-twenty-twenty'),
                'description'   => __('Set the colour of the post separator.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
            	'settings'      => 'hr_color'
            )));

            $wp_customize->add_setting('hr_style', array(
                'default'       => 'solid',
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('hr_style', array(
                'label'         => __('Separator Border Style', 'options-for-twenty-twenty'),
                'description'   => __('Set a border style for the post separator.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'hr_style',
                'type'          => 'select',
                'choices'       => array(
                    'dotted' => __('Dotted', 'options-for-twenty-twenty'),
                    'dashed' => __('Dashed', 'options-for-twenty-twenty'),
                    'solid' => __('Solid', 'options-for-twenty-twenty'),
                    'double' => __('Double', 'options-for-twenty-twenty'),
                    'groove' => __('3D Groove', 'options-for-twenty-twenty'),
                    'ridge' => __('3D Ridge', 'options-for-twenty-twenty'),
                    'inset' => __('3D Inset', 'options-for-twenty-twenty'),
                    'outset' => __('3D Outset', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('hide_pagination', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_pagination', array(
                'label'         => __('Hide Pagination', 'options-for-twenty-twenty'),
                'description'   => __('Hide the previous and next post links on single post pages.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
                'settings'      => 'hide_pagination',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('button_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'button_background_color', array(
                'label'         => __('Button Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font background color of buttons.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_content',
            	'settings'      => 'button_background_color'
            )));



            $wp_customize->add_section('oftt_footer', array(
                'title'     => __('Footer Options', 'options-for-twenty-twenty'),
                'description'  => __('Use these options to customise the footer.', 'options-for-twenty-twenty') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('push_footer_to_bottom', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('push_footer_to_bottom', array(
                'label'         => __('Push Footer to Bottom', 'options-for-twenty-twenty'),
                'description'   => __('Make sure the footer is at the bottom of the browser window when there isn\'t enough content.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'push_footer_to_bottom',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('footer_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_background_color', array(
                'label'         => __('Footer Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the background color of the site footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
            	'settings'      => 'footer_background_color'
            )));

            $wp_customize->add_setting('footer_padding', array(
                'default'           => 30,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_padding', array(
                'label'         => __('Footer Padding / Margin', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding / margin of the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 31,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('footer_top_padding', array(
                'default'           => 30,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_top_padding', array(
                'label'         => __('Footer Top Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding of the footer top.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_top_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 31,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('footer_widgets_margin', array(
                'default'           => 50,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_widgets_margin', array(
                'label'         => __('Footer Widgets Margin', 'options-for-twenty-twenty'),
                'description'   => __('Change the margin of the footer widgets.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_widgets_margin',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 51,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('footer_text_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_text_color', array(
                'label'         => __('Footer Text Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the default footer text colour.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
            	'settings'      => 'footer_text_color'
            )));

            $wp_customize->add_setting('footer_link_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_link_color', array(
                'label'         => __('Footer Link Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of links in the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
            	'settings'      => 'footer_link_color'
            )));

            $wp_customize->add_setting('footer_link_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_link_hover_color', array(
                'label'         => __('Footer Link Hover Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font hover color of links in the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
            	'settings'      => 'footer_link_hover_color'
            )));

            $wp_customize->add_setting('footer_nav_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('footer_nav_text_transform', array(
                'label'         => __('Footer Nav Font Case', 'options-for-twenty-twenty'),
                'description'   => __('Change the font case of the footer menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_nav_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-twenty'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-twenty'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-twenty'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('footer_nav_font_size', array(
                'default'           => 24,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_nav_font_size', array(
                'label'         => __('Footer Nav Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the footer menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_nav_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6,
                    'max'   => 50,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('footer_nav_font_weight', array(
                'default'           => 700,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_nav_font_weight', array(
                'label'         => __('Footer Nav Font Weight', 'options-for-twenty-twenty'),
                'description'   => __('Change the font weight of the footer menu links.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_nav_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('hide_footer_social_menu', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_footer_social_menu', array(
                'label'         => __('Hide Footer Social Menu', 'options-for-twenty-twenty'),
                'description'   => __('Hide the social menu in the footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'hide_footer_social_menu',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('footer_social_menu_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('footer_social_menu_align', array(
                'label'         => __('Footer Social Menu Alignment', 'options-for-twenty-twenty'),
                'description'   => __('Align the icons in the footer social menu to the left, center or right.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_social_menu_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-twenty'),
                    'center' => __('Center', 'options-for-twenty-twenty'),
                    'right' => __('Right', 'options-for-twenty-twenty')
                )
            ));

            $wp_customize->add_setting('footer_widgets_top_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_widgets_top_padding', array(
                'label'         => __('Footer Widgets Top Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding above the footer widgets.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_widgets_top_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('footer_widgets_bottom_padding', array(
                'default'           => 81,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_widgets_bottom_padding', array(
                'label'         => __('Footer Widgets Bottom Padding', 'options-for-twenty-twenty'),
                'description'   => __('Change the padding below the footer widgets.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'footer_widgets_bottom_padding',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 161,
                    'step'  => 2
                ),
            ));

            $wp_customize->add_setting('center_footer_widgets', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('center_footer_widgets', array(
                'label'         => __('Center Footer Widgets', 'options-for-twenty-twenty'),
                'description'   => __('Center footer widgets when using a single footer widget area.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'center_footer_widgets',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_site_footer', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_site_footer', array(
                'label'         => __('Hide Site Footer', 'options-for-twenty-twenty'),
                'description'   => __('Hides the website footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'hide_site_footer',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('remove_copyright', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_copyright', array(
                'label'         => __('Hide Copyright', 'options-for-twenty-twenty'),
                'description'   => __('Hides the copyright text displayed in the website footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'remove_copyright',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('remove_powered_by_wordpress', array(
                'default'       => false,
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_powered_by_wordpress', array(
                'label'         => __('Hide Powered by WordPress', 'options-for-twenty-twenty'),
                'description'   => __('Hides the "Powered by WordPress" text displayed in the website footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'remove_powered_by_wordpress',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('remove_powered_by_font_size', array(
                'default'           => 18,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('remove_powered_by_font_size', array(
                'label'         => __('"Remove Powered by" Font Size', 'options-for-twenty-twenty'),
                'description'   => __('Change the font size of the "Powered by WordPress" text.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'remove_powered_by_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 4,
                    'max'   => 30,
                    'step'  => 2
                ),
            ));

            if (get_theme_mod('remove_to_the_top') == 1) { set_theme_mod('remove_to_the_top' ,'hide'); }

            $wp_customize->add_setting('remove_to_the_top', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'ofttCommon::sanitize_options'
            ));
            $wp_customize->add_control('remove_to_the_top', array(
                'label'         => __('Hide "To The Top"', 'options-for-twenty-twenty'),
                'description'   => __('Hides the "To The Top" link displayed in the website footer.', 'options-for-twenty-twenty'),
                'section'       => 'oftt_footer',
                'settings'      => 'remove_to_the_top',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Do not hide', 'options-for-twenty-twenty'),
                    'hide' => __('Hide', 'options-for-twenty-twenty'),
                    'center' => __('Hide (center copyright)', 'options-for-twenty-twenty'),
                    'right' => __('Hide (right align copyright)', 'options-for-twenty-twenty')
                )
            ));



            $wp_customize->add_setting('hex_primary_color', array(
                'default'       => '',
                'type'          => 'theme_mod',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hex_primary_color', array(
                'label'         => __('Hex Primary Colour', 'options-for-twenty-twenty'),
                'description'   => __('Set the hue of the primary colour using a HEX control.', 'options-for-twenty-twenty'),
                'section'       => 'colors',
            	'settings'      => 'hex_primary_color'
            )));



            $wp_customize->add_setting('cover_nav_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cover_nav_background_color', array(
                'label'         => __('Cover Nav Background Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the background color of the site navigation on the Cover Template.', 'options-for-twenty-twenty'),
                'section'       => 'cover_template_options',
            	'settings'      => 'cover_nav_background_color'
            )));

            $wp_customize->add_setting('alt_cover_logo', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'alt_cover_logo', array(
                'mime_type'     => 'image',
                'label'         => __('Alternate Cover Logo', 'options-for-twenty-twenty'),
                'description'   => __('Choose a different logo for use on the Cover Template.', 'options-for-twenty-twenty'),
                'section'       => 'cover_template_options',
                'settings'      => 'alt_cover_logo'
            )));

            $wp_customize->add_setting('cover_nav_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cover_nav_color', array(
                'label'         => __('Cover Nav Link Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font color of the menu links on the Cover Template.', 'options-for-twenty-twenty'),
                'section'       => 'cover_template_options',
            	'settings'      => 'cover_nav_color'
            )));

            $wp_customize->add_setting('cover_nav_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cover_nav_hover_color', array(
                'label'         => __('Cover Nav Link Hover Color', 'options-for-twenty-twenty'),
                'description'   => __('Change the font hover color of the menu links on the Cover Template.', 'options-for-twenty-twenty'),
                'section'       => 'cover_template_options',
            	'settings'      => 'cover_nav_hover_color'
            )));



            $control_label = __('Archive Cover Template', 'options-for-twenty-twenty');
            $control_description = __('Use the Cover Template for taxonomy pages.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'archive_cover_template', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('True Parallax Background Image', 'options-for-twenty-twenty');
            $control_description = __('Turn on "true parallax" scrolling for the background image.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'true_parallax_background_image', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Inject Sidebar', 'options-for-twenty-twenty');
            $control_description = __('Inject a sidebar into the theme by choosing where you want it shown.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'inject_sidebar', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Alignment', 'options-for-twenty-twenty');
            $control_description = __('Choose to show the sidebar on the right or the left.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_alignment', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Width', 'options-for-twenty-twenty');
            $control_description = __('Set the width of the injected sidebar.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_width', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Horizontal Padding', 'options-for-twenty-twenty');
            $control_description = __('Set the width of the padding to the left and right of widgets in the injected sidebar.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_horizontal_padding', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Widget Padding', 'options-for-twenty-twenty');
            $control_description = __('Set the height of the padding in-between widgets in the injected sidebar.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_widget_padding', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Title Font Size', 'options-for-twenty-twenty');
            $control_description = __('Set the font size of widget titles in the injected sidebar.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_title_font_size', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sidebar Text Font Size', 'options-for-twenty-twenty');
            $control_description = __('Set the font size of widget text in the injected sidebar.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sidebar_text_font_size', 'oftt_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Limit Search to Post Type', 'options-for-twenty-twenty');
            $control_description = __('Limit the site search to a specific post type.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'search_post_type', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Custom Toggle Dashicon', 'options-for-twenty-twenty');
            $control_description = sprintf(wp_kses(__('Choose your own <a href="%s">dashicon</a> for the button that opens the expanded / mobile menu.', 'options-for-twenty-twenty'), array('a' => array('href' => array()))), esc_url('https://developer.wordpress.org/resource/dashicons/'));
            ofttCommon::add_hidden_control($wp_customize, 'nav_toggle_dashicon', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Animate Close Menu Icon', 'options-for-twenty-twenty');
            $control_description = __('Animate the close menu toggle on the expanded menu.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'animate_close_toggle', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Expanded Menu Top Widgets', 'options-for-twenty-twenty');
            $control_description = __('Inject a widget area above the expanded menu.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'expanded_menu_widgets_1', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Expanded Menu Bottom Widgets', 'options-for-twenty-twenty');
            $control_description = __('Inject a widget area below the expanded menu.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'expanded_menu_widgets_2', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Mobile Menu Top Widgets', 'options-for-twenty-twenty');
            $control_description = __('Inject a widget area above the mobile menu.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'mobile_menu_widgets_1', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Mobile Menu Bottom Widgets', 'options-for-twenty-twenty');
            $control_description = __('Inject a widget area below the mobile menu.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'mobile_menu_widgets_2', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sticky Desktop Menu', 'options-for-twenty-twenty');
            $control_description = __('Fix the site navigation to the top of the page on large screens.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sticky_desktop_menu', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sticky Mobile Menu', 'options-for-twenty-twenty');
            $control_description = __('Fix the site navigation to the top right on scroll down on small screens.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sticky_mobile_menu', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Menu Button Background Color', 'options-for-twenty-twenty');
            $control_description = __('Set the background color of the sticky menu button.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'menu_button_background_color', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Sticky Menu Dropshadow', 'options-for-twenty-twenty');
            $control_description = __('Add a dropshadow to the sticky menus.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sticky_menu_dropshadow', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Animate Dropshadow', 'options-for-twenty-twenty');
            $control_description = __('Give a 3D animation to the dropshadow on sticky menus.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'sticky_menu_shadow_animate', 'oftt_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Footer Order', 'options-for-twenty-twenty');
            $control_description = __('Order the sections in the site\'s footer.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'footer_order', 'oftt_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Footer Sidebars', 'options-for-twenty-twenty');
            $control_description = __('Add a third or fourth widget ready sidebar to the footer area of the theme.', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'footer_sidebars', 'oftt_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Replace "Powered by" Text', 'options-for-twenty-twenty');
            $control_description = __('Provide alternate text to replace "Proudly powered by Wordpress".', 'options-for-twenty-twenty');
            ofttCommon::add_hidden_control($wp_customize, 'replace_powered_by_wordpress', 'oftt_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Enable Privacy Link', 'options-for-twenty-twenty');
            $control_description = sprintf(wp_kses(__('Injects link to the privacy policy page set in <a href="%s">Privacy Settings</a>.', 'options-for-twenty-twenty'), array('a' => array('href' => array()))), esc_url(admin_url('options-privacy.php')));
            ofttCommon::add_hidden_control($wp_customize, 'privacy_link', 'oftt_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Background Slider', 'options-for-twenty-twenty');
            $control_description = sprintf(
                wp_kses(__('Places a <a href="%s">MetaSlider</a> slider in the background of the Cover Template.', 'options-for-twenty-twenty'), array('a' => array('href' => array()))),
                esc_url(add_query_arg( array(
                    's' => 'ml-slider',
                    'tab' => 'search',
                    'type' => 'term'
                ), admin_url('plugin-install.php')))
            );
            ofttCommon::add_hidden_control($wp_customize, 'slider_cover', 'cover_template_options', $control_label, $control_description . ' ' . $upgrade_nag);

        }

        function oftt_header_output() {

?>
<!--Customizer CSS-->
<style type="text/css">
.powered-by-wordpress a {
    color: <?= sanitize_hex_color(twentytwenty_get_color_for_area('content', 'accent')); ?>;
}
<?php


            if (get_theme_mod('disable_font')) {

?>
@supports ( font-variation-settings: normal ) {
	body {
	font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;
	}
}
@supports ( font-variation-settings: normal ) {
	input,
	textarea,
	button,
	.button,
	.faux-button,
	.faux-button.more-link,
	.wp-block-button__link,
	.wp-block-file__button {
		font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;
	}
}
@supports ( font-variation-settings: normal ) {
	.has-drop-cap:not(:focus)::first-letter,
	.entry-content .wp-block-archives,
	.entry-content .wp-block-categories,
	.entry-content .wp-block-latest-posts,
	.entry-content .wp-block-latest-comments,
	.entry-content .wp-block-cover-image p,
	.entry-content .wp-block-pullquote {
		font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;
	}
}
@supports ( font-variation-settings: normal ) {
	.entry-content h1,
	.entry-content h2,
	.entry-content h3,
	.entry-content h4,
	.entry-content h5,
	.entry-content h6,
	.entry-content cite,
	.entry-content figcaption,
	.entry-content table,
	.entry-content address,
	.entry-content .wp-caption-text,
	.entry-content .wp-block-file {
		font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;
	}
}
@supports ( font-variation-settings: normal ) {
	.widget-content cite,
	.widget-content figcaption,
	.widget-content .wp-caption-text {
		font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;
	}
}
<?php

            }


            if (get_theme_mod('force_fixed_background') && set_url_scheme(get_background_image())) {

        		$size = get_theme_mod('background_size', get_theme_support('custom-background', 'default-size'));

        		if (in_array($size, array('contain', 'cover'), true)) {
?>
@supports (-webkit-overflow-scrolling: touch) {
    body.custom-background {
        background-size: 0 0;
    }
    body.custom-background:before {
        content: "";
        top: 0; 
        left: 0; 
        bottom: 0;
        right: 0;
        background-image: inherit;
        background-size: <?= $size; ?>;
        background-position: inherit;
        background-repeat: inherit;
        position: fixed;
        z-index: -1;
    }
}
<?php

        		}

            }

            if (get_theme_mod('hide_mobile_background') && set_url_scheme(get_background_image())) {

?>
@media (max-width: 1000px) {
    body.custom-background {
        background-image: none;
    }
}
<?php

            }

            ofttCommon::generate_css('body, .entry-title a, :root .has-primary-color', 'color', 'text_color');

            $mod = get_theme_mod('navigation_width');

            if ($mod) {

?>
#site-header, #breadcrumbs {
    max-width: <?= $mod; ?>;
    margin: 0 auto;
}
<?php

            }

            ofttCommon::generate_css('#site-header', 'background-color', 'transparent_navigation', '', '', 'transparent');
            ofttCommon::generate_css('#site-header, .menu-modal-inner, .search-modal-inner', 'background-color', 'nav_background_color');

            if (get_theme_mod('site_title')) {

                add_filter('twentytwenty_site_logo', array($this, 'oftt_site_title'), 10, 4);

            }

            ofttCommon::generate_css('.site-description', 'display', 'hide_site_description', '', '', 'none');
            ofttCommon::generate_css('.header-titles', 'display', 'site_description_below_title', '', '', 'block');
            ofttCommon::generate_css('.site-description', 'text-transform', 'site_description_text_transform');
            ofttCommon::generate_css('.site-description', 'font-size', 'site_description_font_size', '', '', (get_theme_mod('site_description_font_size') / 10) . 'rem');
            ofttCommon::generate_css('.site-description', 'font-weight', 'site_description_font_weight');
            ofttCommon::generate_css('.site-description, .overlay-header .site-description', 'color', 'site_description_color');
            ofttCommon::generate_css('.site-title a', 'text-transform', 'site_title_text_transform');

            $mod = get_theme_mod('site_title_font_size');

            if ($mod) {

?>
.site-title {
    font-size: <?= ($mod * 0.0875); ?>rem;
}
@media (min-width: 700px) {
    .site-title {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('.site-title a', 'font-weight', 'site_title_font_weight');
            ofttCommon::generate_css('.site-title a', 'color', 'site_title_color');
            ofttCommon::generate_css('ul.primary-menu, .modal-menu a', 'text-transform', 'nav_text_transform');
            ofttCommon::generate_css('ul.primary-menu', 'font-size', 'nav_font_size', '', '', (get_theme_mod('nav_font_size') / 10) . 'rem');

            $mod = get_theme_mod('mobile_nav_font_size');

            if ($mod) {

?>
.modal-menu > li > a, .modal-menu > li > .ancestor-wrapper > a {
    font-size: <?= ($mod * 0.2 / 2.4); ?>rem;
}
@media (min-width: 700px) {
    .modal-menu > li > a, .modal-menu > li > .ancestor-wrapper > a {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('ul.primary-menu, .modal-menu > li > a, .modal-menu > li > .ancestor-wrapper > a', 'font-weight', 'nav_font_weight');
            ofttCommon::generate_css('body:not(.overlay-header) .primary-menu > li > a, body:not(.overlay-header) #site-header .toggle, body:not(.overlay-header) .toggle-inner .toggle-text, .modal-menu a, .modal-menu ul li a, body:not(.overlay-header) .primary-menu > li > .icon', 'color', 'nav_color');
            ofttCommon::generate_css('body:not(.overlay-header) .primary-menu > li > a:hover, body:not(.overlay-header) #site-header .toggle:hover, body:not(.overlay-header) .toggle-inner .toggle-text:hover, .modal-menu a:hover, .modal-menu ul li a:hover', 'color', 'nav_hover_color');
            ofttCommon::generate_css('body:not(.overlay-header) .primary-menu ul, .primary-menu ul', 'background-color', 'submenu_background_color');
            ofttCommon::generate_css('body:not(.overlay-header) .primary-menu > li > ul:after, .primary-menu > li > ul:after', 'border-bottom-color', 'submenu_background_color');
            ofttCommon::generate_css('body:not(.overlay-header) .primary-menu ul ul:after, .primary-menu ul ul:after', 'border-left-color', 'submenu_background_color');
            ofttCommon::generate_css('.primary-menu ul a', 'color', 'submenu_link_color');
            ofttCommon::generate_css('.primary-menu ul a:hover', 'color', 'submenu_link_hover_color');

            if (get_theme_mod('remove_link_underlines')) {

?>
a,
.primary-menu li.current-menu-item > a, .primary-menu li.current-menu-item > .link-icon-wrapper > a,
button:focus, button:hover,
.header-inner .toggle:focus .toggle-text, .header-inner .toggle:hover .toggle-text,
.site-title a:hover, .site-title a:focus,
.footer-menu a:hover, .footer-menu a:focus,
.entry-title a:focus, .entry-title a:hover,
.comment-author .url,
.post-meta a:focus, .post-meta a:hover,
.primary-menu a:hover, .primary-menu a:focus, .primary-menu .current_page_ancestor,
.modal-menu a:focus, .modal-menu a:hover, .modal-menu li.current-menu-item > .ancestor-wrapper > a, .modal-menu li.current_page_ancestor > .ancestor-wrapper > a,
#site-footer a:focus, #site-footer a:hover {
    text-decoration: none;
}
.entry-categories a {
    border: none;
}
<?php

            }

            ofttCommon::generate_css('.menu-modal .social-icons a, .footer-top .social-icons a', 'color', 'social_menu_color');
            ofttCommon::generate_css('.menu-modal .social-icons a, .footer-top .social-icons a', 'background-color', 'social_menu_background_color');

            $mod = get_theme_mod('archive_grid_template');

            if ($mod && is_array($mod)) {

                add_filter('post_thumbnail_html', array($this, 'oftt_change_grid_image_size'), 10, 5);

                foreach ($mod as $taxonomy) {

                    switch ($taxonomy) {

                        case 'blog':

                            break;

                        case 'category':

                            break;

                        case 'post_tag':

                            $taxonomy = 'tag';
                            break;

                        default:

                            $taxonomy = 'tax-' . $taxonomy;
                            break;

                    }

            		$accent = sanitize_hex_color(twentytwenty_get_color_for_area('content', 'accent'));

                    if (!$accent) {

                        $accent = '#cd2653';

                    }

?>
.<?= $taxonomy; ?>:not(.template-cover) #site-content, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner {
	display: flex;
    flex-wrap: wrap;
	padding: 0;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap {
	width: 100%;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>header {
    flex: 0 1 auto;
	width: 100%;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article {
	display: flex;
	flex-direction: column;
    flex: 0 1 auto;
	width: 100%;
	padding: 1em;
}
@media (min-width: 700px) {
    .<?= $taxonomy; ?>:not(.template-cover) #site-content>article, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article,
    .<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article {
        width: 50%;
    }
}
@media (min-width: 1000px) {
    .<?= $taxonomy; ?>:not(.template-cover) #site-content>article, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article,
    .<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article {
        width: 25%;
    }
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article>header, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article>header,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article>header, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article>header {
	width: 100%;
	order: 2;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article>header>.section-inner, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article>header>.section-inner,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article>header>.section-inner, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article>header>.section-inner {
	width: auto;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article h2.entry-title, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article h2.entry-title,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article h2.entry-title, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article h2.entry-title {
	font-size: 1.2em;
	padding-top: 0.5em;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article:not(.has-post-thumbnail)>header::after, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article:not(.has-post-thumbnail)>header::after,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article:not(.has-post-thumbnail)>header::after, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article:not(.has-post-thumbnail)>header::after {
	display: block;
	content: '';
	padding-bottom: 65%;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article:not(.has-post-thumbnail)>header, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article:not(.has-post-thumbnail)>header,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article:not(.has-post-thumbnail)>header, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article:not(.has-post-thumbnail)>header {
    background-color: <?= $accent; ?>;
    flex: 1 1 auto;
    position: relative;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article:not(.has-post-thumbnail)>header>.section-inner, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article:not(.has-post-thumbnail)>header>.section-inner,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article:not(.has-post-thumbnail)>header>.section-inner, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article:not(.has-post-thumbnail)>header>.section-inner {
    position: absolute;
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article:not(.has-post-thumbnail) h2.entry-title a, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article:not(.has-post-thumbnail) h2.entry-title a,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article:not(.has-post-thumbnail) h2.entry-title a, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article:not(.has-post-thumbnail) h2.entry-title a {
	color: white;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article:not(.has-post-thumbnail) h2.entry-title, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article:not(.has-post-thumbnail) h2.entry-title,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article:not(.has-post-thumbnail) h2.entry-title, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article:not(.has-post-thumbnail) h2.entry-title {
	flex: 0 1 auto;
	font-size: 2.5em;
	padding: 0 1rem;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article .post-meta-wrapper, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article .post-meta-wrapper,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article .post-meta-wrapper, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article .post-meta-wrapper {
	display: none;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content .featured-media, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap .featured-media,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner .featured-media, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner .featured-media {
	width: 100%;
	margin: 0;
	order: 1;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content .featured-media::after, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap .featured-media::after,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner .featured-media::after, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner .featured-media::after {
	display: block;
	content: '';
	padding-bottom: 65%;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content .featured-media-inner, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap .featured-media-inner,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner .featured-media-inner, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner .featured-media-inner {
	position: static;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content .featured-media .featured-media-inner img, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap .featured-media .featured-media-inner img,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner .featured-media .featured-media-inner img, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner .featured-media .featured-media-inner img {
	position: absolute;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content .featured-media figcaption, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap .featured-media figcaption,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner .featured-media figcaption, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner .featured-media figcaption {
	display: none;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content>article>.post-inner.thin, .<?= $taxonomy; ?>:not(.template-cover) #site-content>article>.post-inner>article>.section-inner,
.<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article>.post-inner.thin, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap>article>.post-inner>article>.section-inner,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article>.post-inner.thin, .<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner>article>.section-inner,
.<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article>.post-inner.thin, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner>article>.section-inner {
	display: none;
}
.<?= $taxonomy; ?>:not(.template-cover) #site-content hr, .<?= $taxonomy; ?>:not(.template-cover) #site-content>.infinite-wrap hr,
.<?= $taxonomy; ?>.template-cover #site-content>article>.post-inner hr, .<?= $taxonomy; ?>.template-cover #site-content>.infinite-wrap>article>.post-inner hr {
	display: none;
}
.<?= $taxonomy; ?>.footer-top-visible:not(.template-cover) .footer-nav-widgets-wrapper {
	margin-top: 0;
}
<?php

                }

            }

            if (get_theme_mod('toggle_use_dashicon')) {

                add_filter('wp_footer', array($this, 'oftt_toggle_use_dashicon'));

?>
.nav-toggle .toggle-icon {
    height: auto;
}
.nav-toggle .toggle-inner {
    padding-top: 0.2rem;
}
.nav-toggle .toggle-inner .dashicons, .dashicons-before:before {
    width: 23px;
    height: 23px;
    font-size: 23px;
}
<?php

            }

            ofttCommon::generate_css('.toggle .toggle-text', 'display', 'hide_toggle_text', '', '', 'none');
            ofttCommon::generate_css('.menu-bottom', 'display', 'hide_expanded_social_menu', '', '', 'none');

            ofttCommon::generate_css('.archive-header', 'display', 'hide_archive_headers', '', '', 'none');
            ofttCommon::generate_css('#site-content .archive-header-inner', 'max-width', 'archive_title_width');
            ofttCommon::generate_css('.archive .archive-title .color-accent', 'display', 'hide_archive_title_prefix', '', '', 'none');
            ofttCommon::generate_css('h1.archive-title', 'text-align', 'archive_title_text_align');
            ofttCommon::generate_css('h1.archive-title', 'text-transform', 'archive_title_text_transform');

            $mod = get_theme_mod('archive_title_font_size');

            if ($mod) {

?>
.archive-title {
    font-size: <?= ($mod * 0.24 / 3.2); ?>rem;
}
@media (min-width: 700px) {
    .archive-title {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('h1.archive-title', 'font-weight', 'archive_title_font_weight');
            ofttCommon::generate_css('h1.archive-title, h1.archive-title .color-accent', 'color', 'archive_title_color');
            ofttCommon::generate_css('#site-content .archive-header-inner .archive-subtitle', 'max-width', 'archive_description_width');
            ofttCommon::generate_css('div.archive-subtitle', 'text-align', 'archive_description_text_align');
            ofttCommon::generate_css('header.entry-header', 'display', 'hide_all_headers', '', '', 'none');
            ofttCommon::generate_css('.singular .entry-header', 'background-color', 'transparent_header', '', '', 'transparent');
            ofttCommon::generate_css('#site-content .entry-header-inner, .post-meta-wrapper', 'max-width', 'header_width');

            if (get_theme_mod('header_featured_image')) {

                add_action('wp_footer', array($this, 'oftt_move_featured_image_to_header'));

?>
figure.featured-media {
    display: none;
}
.singular .entry-header {
    background-size: cover;
}
<?php

            }

            if (get_theme_mod('hide_post_categories')) {

                add_filter('twentytwenty_show_categories_in_entry_header', array($this, 'oftt_hide_post_categories'));

            }

            ofttCommon::generate_css('.singular:not(.overlay-header) .entry-header a', 'color', 'category_links_color');

            $mod = get_theme_mod('archive_header_top_padding');

            if ($mod) {

?>
.archive-header {
    padding-top: <?= ((absint($mod) - 1) / 20); ?>rem;
}
@media (min-width: 700px) {
    .archive-header {
        padding-top: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('archive_header_bottom_padding');

            if ($mod) {

?>
.archive-header {
    padding-bottom: <?= ((absint($mod) - 1) / 20); ?>rem;
}
@media (min-width: 700px) {
    .archive-header {
        padding-bottom: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('header_top_padding');

            if ($mod) {

?>
.singular .entry-header {
    padding-top: <?= ((absint($mod) - 1) / 20); ?>rem;
}
@media (min-width: 700px) {
    .singular .entry-header {
        padding-top: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('header_bottom_padding');

            if ($mod) {

?>
.singular .entry-header {
    padding-bottom: <?= ((absint($mod) - 1) / 20); ?>rem;
}
@media (min-width: 700px) {
    .singular .entry-header {
        padding-bottom: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('header.entry-header h1.entry-title, header.entry-header h2.entry-title', 'visibility', 'hide_all_header_titles', '', '', 'hidden');
            ofttCommon::generate_css('h1.entry-title, h2.entry-title', 'text-align', 'header_title_text_align');
            ofttCommon::generate_css('h1.entry-title, h2.entry-title', 'text-transform', 'header_title_text_transform');

            $mod = get_theme_mod('header_title_font_size');

            if ($mod) {

                set_theme_mod('page_title_font_size', $mod);
                set_theme_mod('post_title_font_size', $mod);
                remove_theme_mod('header_title_font_size');

            }

            $mod = get_theme_mod('post_title_font_size');

            if ($mod) {

?>
h1.entry-title, h2.entry-title {
    font-size: <?= ($mod * 0.36 / 8.4); ?>rem;
}
@media (min-width: 700px) {
    h1.entry-title, h2.entry-title {
        font-size: <?= ($mod * 0.64 / 8.4); ?>rem;
    }
}
@media (min-width: 1220px) {
    h1.entry-title, h2.entry-title {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('archive_post_title_font_size');

            if ($mod) {

?>
.archive .post h2.entry-title {
    font-size: <?= ($mod * 0.36 / 6.4); ?>rem;
}
@media (min-width: 700px) {
    .archive .post h2.entry-title {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('page_title_font_size');

            if ($mod) {

?>
.page h1.entry-title, h2.entry-title {
    font-size: <?= ($mod * 0.36 / 8.4); ?>rem;
}
@media (min-width: 700px) {
    .page h1.entry-title, h2.entry-title {
        font-size: <?= ($mod * 0.64 / 8.4); ?>rem;
    }
}
@media (min-width: 1220px) {
    .page h1.entry-title, h2.entry-title {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('h1.entry-title, h2.entry-title', 'font-weight', 'header_title_font_weight');
            ofttCommon::generate_css('h1.entry-title, h2.entry-title, .entry-title a', 'color', 'header_title_color');
            ofttCommon::generate_css('.intro-text', 'display', 'hide_excerpt', '', '', 'none');

            $mod = get_theme_mod('excerpt_font_size');

            if ($mod) {

?>
.singular .intro-text {
    font-size: <?= ($mod * 0.2 / 3.2); ?>rem;
}
@media (min-width: 700px) {
    .singular .intro-text {
        font-size: <?= ($mod * 0.26 / 3.2); ?>rem;
    }
}
@media (min-width: 1000px) {
    .singular .intro-text {
        font-size: <?= ($mod * 0.28 / 3.2); ?>rem;
    }
}
@media (min-width: 1220px) {
    .singular .intro-text {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('header_post_meta_align');

            if ($mod) {

?>
.post-meta-single-top .post-meta {
    justify-content: <?= $mod; ?>;
    
}
.post-meta-wrapper {
    max-width: inherit;
}
<?php

            }

            if (get_theme_mod('header_post_meta')) {

                add_filter('twentytwenty_post_meta_location_single_top', array($this, 'oftt_header_post_meta'));

            }

            if (get_theme_mod('inject_breadcrumbs')) {

                add_action('wp_footer', array($this, 'oftt_inject_breadcrumbs'));

?>
#breadcrumbs {
    background: white;
    padding: 3.15rem 2rem;
}
@media (min-width: 700px) {
    #breadcrumbs {
        padding: 3.8rem 4rem;
    }
}
@media (min-width: 1000px) {
    #breadcrumbs {
        padding: 2.8rem 4rem;
    }
}
<?php

            }

            ofttCommon::generate_css('.featured-media', 'display', 'hide_all_featured_images', '', '', 'none');

            if (get_theme_mod('featured_images_to_top')) {

?>
body.singular.has-post-thumbnail article.post, body.singular.has-post-thumbnail article.page {
	display: flex;
	flex-direction: column;
}
body.singular.has-post-thumbnail article.post>*:not(.entry-header):not(.featured-media), body.singular.has-post-thumbnail article.page>*:not(.entry-header):not(.featured-media) {
	order: 3;
}
body.singular.has-post-thumbnail .entry-header {
	order: 2;
}
body.singular.has-post-thumbnail .featured-media {
	order: 1;
}
<?php

                if (get_theme_mod('transparent_navigation')) {

?>
body.singular.has-post-thumbnail {
	margin-top: 0 !important;
}
<?php

                }

            }

            if (get_theme_mod('full_width_featured_images')) {

?>
body.singular.has-post-thumbnail .featured-media-inner {
	max-width: none;
}
body.singular.has-post-thumbnail .featured-media img {
	width: 100%;
}
<?php

            }

            if (!is_page_template(array('templates/template-full-width.php'))) {

                ofttCommon::generate_css('.entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.is-style-wide), #site-content-wrapper', 'max-width', 'content_width');

                $mod = get_theme_mod('content_width');

                if ($mod) {

?>
@media (min-width: 660px) {
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: static;
        max-width: 26rem;
	}
	.entry-content > .wp-block-image figure.alignleft {
        margin-left: 0;
        margin-right: 1em;
	}
	.entry-content > .wp-block-image figure.alignright {
        margin-right: 0;
        margin-left: 1em;
	}
}
@media (min-width: 1000px) {
	.entry-content > .wp-block-pullquote.alignleft, .entry-content > .wp-block-pullquote.alignright {
        position: relative;
        max-width: 26rem;
	}
	.entry-content > .wp-block-pullquote.alignleft {
        right: auto;
        margin-left: 2rem;
	}
	.entry-content > .wp-block-pullquote.alignright {
        left: auto;
        margin-right: 2rem;
	}
<?php

                    if ($mod != '100%' && floatval($mod) >= (100 + 52)) {

?>
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: static;
        max-width: 26rem;
	}
	.entry-content > .wp-block-image figure.alignleft {
        margin-left: calc(( 100vw - <?= $mod; ?> - 8rem ) / -2);
        margin-right: 1em;
	}
	.entry-content > .wp-block-image figure.alignright {
        margin-right: calc(( 100vw - <?= $mod; ?> - 8rem ) / -2);
        margin-left: 1em;
	}
<?php

                    } else {

?>
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: static;
        max-width: 26rem;
	}
	.entry-content > .wp-block-image figure.alignleft {
        right: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-left: 0;
        margin-right: 1em;
	}
	.entry-content > .wp-block-image figure.alignright {
        left: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-right: 0;
        margin-left: 1em;
	}
<?php

                    }

?>
}
<?php

                    if ($mod != '100%' && floatval($mod) >= (128 + 52)) {

?>
@media (min-width: 1280px) {
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: static;
        max-width: 26rem;
	}
	.entry-content > .wp-block-image figure.alignleft {
        margin-left: calc(( 100vw - <?= $mod; ?> - 8rem ) / -2);
        margin-right: 1em;
	}
	.entry-content > .wp-block-image figure.alignright {
        margin-right: calc(( 100vw - <?= $mod; ?> - 8rem ) / -2);
        margin-left: 1em;
	}
}
<?php

                    } else {

?>
@media (min-width: 1280px) {
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: static;
        max-width: 26rem;
	}
	.entry-content > .wp-block-image figure.alignleft {
        right: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-left: 0;
        margin-right: 1em;
	}
	.entry-content > .wp-block-image figure.alignright {
        left: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-right: 0;
        margin-left: 1em;
	}
}
<?php

                    }

                    if ($mod != '100%') {

?>
@media (min-width: <?= (floatval($mod) + 62) * 10; ?>px) {
	.entry-content > .wp-block-pullquote.alignleft, .entry-content > .wp-block-pullquote.alignright {
        position: absolute;
        max-width: calc((100% - <?= $mod; ?>) / 2 - 6rem);
	}
	.entry-content > .wp-block-pullquote.alignleft {
        right: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 2rem);
        margin-left: -31rem;
	}
	.entry-content > .wp-block-pullquote.alignright {
        left: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 2rem);
        margin-right: -31rem;
	}
	.entry-content > .wp-block-image figure.alignleft, .entry-content > .wp-block-image figure.alignright {
        position: absolute;
        max-width: calc((100% - <?= $mod; ?>) / 2 - 4rem);
	}
	.entry-content > .wp-block-image figure.alignleft {
        right: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-left: -29rem;
        margin-right: -2rem;
	}
	.entry-content > .wp-block-image figure.alignright {
        left: calc((100vw - <?= $mod; ?>) / 2 + <?= $mod; ?> + 4rem);
        margin-right: -29rem;
        margin-left: -2rem;
	}
}

<?php

                    }

                }

            }

            ofttCommon::generate_css('a', 'color', 'content_link_color');
            ofttCommon::generate_css('a:hover', 'color', 'content_link_hover_color');

            if (get_theme_mod('bottom_post_meta')) {

                add_filter('twentytwenty_post_meta_location_single_bottom', array($this, 'oftt_bottom_post_meta'));

            }

$mod = get_theme_mod('hr_width');

if ($mod) {

?>
.entry-content hr, hr.styled-separator {
    border-top: <?= absint($mod); ?>px <?= (get_theme_mod('hr_style') ? get_theme_mod('hr_style') : 'solid'); ?> <?= (get_theme_mod('hr_color') ? get_theme_mod('hr_color') : '#6d6d6d'); ?>;
    position: static;
}
.entry-content hr::before, .entry-content hr::after, hr.styled-separator::before, hr.styled-separator::after {
    display: none;
}
<?php

}

            ofttCommon::generate_css('.pagination-single', 'display', 'hide_pagination', '', '', 'none');
            ofttCommon::generate_css('button, .button, .faux-button, .wp-block-button__link, .wp-block-file .wp-block-file__button, input[type="button"], input[type="reset"], input[type="submit"], .bg-accent, .bg-accent-hover:hover, .bg-accent-hover:focus, :root .has-accent-background-color, .comment-reply-link', 'background-color', 'button_background_color');

            if (get_theme_mod('push_footer_to_bottom')) {

                add_filter('wp_footer', array($this, 'oftt_push_footer_to_bottom'));

            }

            ofttCommon::generate_css('.footer-nav-widgets-wrapper, #site-footer', 'background-color', 'footer_background_color');
            ofttCommon::generate_css('.footer-menu a', 'text-transform', 'footer_nav_text_transform');

            $mod = get_theme_mod('footer_nav_font_size');

            if ($mod) {

?>
.footer-menu {
    font-size: <?= ($mod * 0.18 / 2.4); ?>rem;
}
@media (min-width: 700px) {
    .footer-menu {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
@media (min-width: 1000px) {
    .footer-menu {
    font-size: <?= ($mod * 0.12 / 2.4); ?>rem;
    }
}
@media (min-width: 1220px) {
    .footer-menu {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('.footer-menu', 'font-weight', 'footer_nav_font_weight');
            ofttCommon::generate_css('.footer-social-wrapper, .footer-top:not(.has-footer-menu)', 'display', 'hide_footer_social_menu', '', '', 'none');
            ofttCommon::generate_css('.footer-social', 'justify-content', 'footer_social_menu_align');

            $mod = get_theme_mod('footer_widgets_top_padding');

            if ($mod) {

?>
.footer-widgets-outer-wrapper {
    padding-top: <?= ((absint($mod) - 1) / 80 * 3); ?>rem;
}
@media (min-width: 700px) {
    .footer-widgets-outer-wrapper {
        padding-top: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            $mod = get_theme_mod('footer_widgets_bottom_padding');

            if ($mod) {

?>
.footer-widgets-outer-wrapper {
    padding-bottom: <?= ((absint($mod) - 1) / 80 * 3); ?>rem;
}
@media (min-width: 700px) {
    .footer-widgets-outer-wrapper {
        padding-bottom: <?= ((absint($mod) - 1) / 10); ?>rem;
    }
}
<?php

            }

            if (get_theme_mod('center_footer_widgets')) {

?>
@media (min-width: 700px) {
    .footer-widgets {
        margin: 0 auto;
    }
}
<?php

            }

            $mod = get_theme_mod('footer_padding');

            if ($mod) {

?>
#site-footer {
    padding: <?= ((absint($mod) - 1) / 10); ?>rem 0;
}
.footer-top-hidden #site-footer {
    margin-top: <?= ((absint($mod) - 1) / 10); ?>rem;
}
@media (min-width: 700px) {
    .footer-top-hidden #site-footer {
        margin-top: <?= ((absint($mod) - 1) / 30 * 4); ?>rem;
        padding: <?= ((absint($mod) - 1) / 30 * 4); ?>rem 0;
    }
}
<?php

            }

            $mod = get_theme_mod('footer_top_padding');

            if ($mod) {

?>
.footer-top {
    padding: <?= ((absint($mod) - 1) / 10); ?>rem 0;
}
@media (min-width: 700px) {
    .footer-top {
        padding: <?= ((absint($mod) - 1) / 30 * 3.7); ?>rem 0;
    }
}
<?php

            }

            $mod = get_theme_mod('footer_widgets_margin');

            if ($mod) {

?>
.footer-top-visible .footer-nav-widgets-wrapper, .footer-top-hidden #site-footer {
    margin-top: <?= ((absint($mod) - 1) / 10); ?>rem;
}
@media (min-width: 700px) {
    .footer-top-visible .footer-nav-widgets-wrapper, .footer-top-hidden #site-footer {
        margin-top: <?= ((absint($mod) - 1) / 50 * 8); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('.footer-nav-widgets-wrapper, #site-footer', 'color', 'footer_text_color');
            ofttCommon::generate_css('.footer-menu a, .footer-widgets a, .footer-copyright a, .powered-by-wordpress a', 'color', 'footer_link_color');
            ofttCommon::generate_css('.footer-menu a:hover, .footer-widgets a:hover, .footer-copyright a:hover, .powered-by-wordpress a:hover', 'color', 'footer_link_hover_color');
            ofttCommon::generate_css('#site-footer', 'display', 'hide_site_footer', '', '', 'none');
            ofttCommon::generate_css('.footer-widgets-outer-wrapper', 'border-bottom', 'hide_site_footer', '', '', 'none');
            ofttCommon::generate_css('.footer-copyright', 'display', 'remove_copyright', '', '', 'none');

            if (get_theme_mod('remove_powered_by_wordpress') && !get_theme_mod('replace_powered_by_wordpress')) {

?>
.powered-by-wordpress {
    display: none;
}
<?php

            }

            $mod = get_theme_mod('remove_powered_by_font_size');

            if ($mod) {

?>
.powered-by-wordpress {
    font-size: <?= ($mod * 1.6 / 18); ?>rem;
}
@media (min-width: 700px) {
    .powered-by-wordpress {
        font-size: <?= ($mod / 10); ?>rem;
    }
}
<?php

            }

            ofttCommon::generate_css('.to-the-top', 'display', 'remove_to_the_top', '', '', 'none');

            $mod = get_theme_mod('remove_to_the_top');

            if ($mod === 'center') {

?>
.footer-credits {
    margin: 0 auto;
}
<?php

            } elseif ($mod === 'right') {

?>
.footer-credits {
    margin: 0 0 0 auto;
}
<?php

            }

            ofttCommon::generate_css('.overlay-header #site-header', 'background-color', 'cover_nav_background_color');

            if (is_page_template(array('templates/template-cover.php')) && get_theme_mod('alt_cover_logo')) {

                add_filter('get_custom_logo', array($this, 'oftt_alt_cover_logo'), 10, 2);

            }

            ofttCommon::generate_css('.overlay-header .primary-menu > li > a, .overlay-header #site-header .toggle, .overlay-header .toggle-inner .toggle-text', 'color', 'cover_nav_color');
            ofttCommon::generate_css('.overlay-header .primary-menu > li > a:hover, .overlay-header #site-header .toggle:hover, .overlay-header .toggle-inner .toggle-text:hover', 'color', 'cover_nav_hover_color');

?>
</style> 
<!--/Customizer CSS-->
<?php

        }

        function oftt_enqueue_customize_preview_js() {

            wp_enqueue_script('oftt-customize-preview', plugin_dir_url( __FILE__ ) . 'js/customize-preview.js', array('jquery','customize-preview'), ofttCommon::plugin_version(), true);

        }

        function oftt_enqueue_customize_controls_js() {

            wp_enqueue_script('oftt-customize-controls', plugin_dir_url( __FILE__ ) . 'js/customize-controls.js', array('jquery','customize-controls'), ofttCommon::plugin_version(), true);


        }

        function oftt_enqueue_customizer_css() {

            wp_enqueue_style('oftt-customizer-css', plugin_dir_url( __FILE__ ) . 'css/theme-customizer.css', array(), ofttCommon::plugin_version());

        }

        function oftt_fix_focused_toggle() {

?>
<script type="text/javascript">
    (function () {
        document.getElementsByClassName('close-nav-toggle')[0].addEventListener('focus', () => {
            document.getElementsByClassName('close-nav-toggle')[0].blur();
        });
    }());
</script>
</script>
<?php

        }

        public function oftt_force_template($check, $object_id, $meta_key, $single) {

            $page_template = get_theme_mod('force_page_template');
            $post_template = get_theme_mod('force_post_template');

            if ($single == true && $page_template && $meta_key === '_wp_page_template' && get_post_type($object_id) == 'page' && !get_post_meta($object_id, 'oftt_override_force_template', true)) {

                $check = $page_template;

            } elseif ($single == true && $post_template && $meta_key === '_wp_page_template' && get_post_type($object_id) == 'post' && !get_post_meta($object_id, 'oftt_override_force_template', true)) {

                $check = $post_template;

            }

            return $check;

        }

        function oftt_site_title($html, $args, $classname, $contents) {

            foreach (array(array('>', '<'), array('"', '"')) as $title_wrapper) {

                $html = str_replace($title_wrapper[0] . get_bloginfo('name') . $title_wrapper[1], $title_wrapper[0] . get_theme_mod('site_title') . $title_wrapper[1], $html);

            }

            return $html;

        }

        public function oftt_change_grid_image_size($html, $post_id, $post_thumbnail_id, $size, $attr) {

            $mod = get_theme_mod('archive_grid_template');

            if ($mod && is_array($mod)) {

                foreach ($mod as $taxonomy) {

                    if ((is_archive() && is_tax($taxonomy)) || (is_home() && $taxonomy == 'blog') || (is_category() && $taxonomy == 'category') || (is_tag() && $taxonomy == 'post_tag')) {

                        $html = '<a href="' . esc_url(get_permalink()) . '">' . wp_get_attachment_image($post_thumbnail_id, 'large', false, $attr) . '</a>';

                    }

                }

            }

            return $html;

        }

        function oftt_toggle_use_dashicon() {

?>
<script type="text/javascript">
    (function () {
        var navToggles = document.querySelectorAll('.nav-toggle .toggle-icon');
            Array.prototype.forEach.call(navToggles, function(navToggle) {
            navToggle.innerHTML = '<span alt="Menu" class="dashicons dashicons-dashicons dashicons-menu"></span>';
        });
    }());
</script>
<?php

        }

        function oftt_enqueue_scripts() {

            if (get_theme_mod('toggle_use_dashicon')) {

                wp_enqueue_style('dashicons');

            }

        }

        function oftt_move_featured_image_to_header() {

?>
<script type="text/javascript">
    (function () {
        if (typeof document.querySelectorAll('article.has-post-thumbnail')[0] !== 'undefined') {
            document.querySelectorAll('article.has-post-thumbnail').forEach(function(featuredArticle) {
      	        var theHeader = featuredArticle.querySelectorAll('header.entry-header')[0],
                    theImageWrapper = featuredArticle.querySelectorAll('figure.featured-media')[0];
                if (typeof theHeader !== 'undefined' && typeof theImageWrapper !== 'undefined') {
                    var theImage = theImageWrapper.getElementsByTagName('IMG')[0];
                    if (typeof theImage !== 'undefined') {
                        theHeader.style.backgroundImage = 'url("' + theImage.src + '")';
                    }
                }
                theImageWrapper.parentNode.removeChild(theImageWrapper);
            });
        }
    }());
</script>
<?php

        }

        function oftt_hide_post_categories($show_categories) {

            return false;

        }

        function oftt_header_post_meta($args) {

            return get_theme_mod('header_post_meta');

        }

        public function oftt_inject_breadcrumbs() {

            $breadcrumbs = apply_filters('oftt_breadcrumbs', false);

            if ($breadcrumbs || function_exists('bcn_display') || function_exists('yoast_breadcrumb')) {

                if ($breadcrumbs) {

                    echo '<div id="breadcrumbs">' . $breadcrumbs . '</div>';

                } elseif (function_exists('bcn_display')) {

                    echo('<div id="breadcrumbs">');
                    bcn_display();
                    echo('</div>');

                } elseif (function_exists('yoast_breadcrumb')) {

                    yoast_breadcrumb('<div id="breadcrumbs">','</div>');

                }

?>
<script type="text/javascript">
    var breadcrumbs = document.getElementById('breadcrumbs'),
    referenceNode;
    if (document.body.classList.contains('template-cover') == true) {
        referenceNode = document.getElementsByClassName('cover-header')[0];
    } else {
        referenceNode = document.getElementById('site-header');
    }
    referenceNode.parentNode.insertBefore(breadcrumbs, referenceNode.nextSibling);
</script>
<?php

            }

        }

        function oftt_bottom_post_meta($args) {

            return get_theme_mod('bottom_post_meta');

        }

        function oftt_push_footer_to_bottom() {

?>
<script type="text/javascript">
    (function () {
        if (!document.getElementsByClassName('blocks-gallery-item').length && document.getElementById('site-content')) {
            var browserHeight = window.innerHeight,
                contentHeight = document.getElementById('site-header').clientHeight + document.getElementById('site-content').clientHeight + document.getElementById('site-footer').clientHeight;
            if (typeof document.getElementsByClassName('footer-nav-widgets-wrapper')[0] !== 'undefined') {
                contentHeight += document.getElementsByClassName('footer-nav-widgets-wrapper')[0].clientHeight;
                contentHeight += parseInt(window.getComputedStyle(document.getElementsByClassName('footer-nav-widgets-wrapper')[0]).getPropertyValue('margin-top'));
            } else {
                contentHeight += parseInt(window.getComputedStyle(document.getElementById('site-footer')).getPropertyValue('margin-top'));
            }
            if (typeof document.getElementsByClassName('admin-bar')[0] !== 'undefined') {
            	var wpadminbar_timer = setTimeout(function wpadminbar_check() {
                    if (document.getElementById('wpadminbar') == null) {
            			wpadminbar_timer = setTimeout(wpadminbar_check, 50);
            		} else {
                        contentHeight += document.getElementById('wpadminbar').clientHeight;
                        if (browserHeight > contentHeight) {
                            document.getElementById('site-content').style.height = (document.getElementById('site-content').clientHeight + browserHeight - contentHeight) + 'px'
                        }
            		}
            	}, 50);
            } else if (browserHeight > contentHeight) {
                document.getElementById('site-content').style.height = (document.getElementById('site-content').clientHeight + browserHeight - contentHeight) + 'px'
            }
        }
    }());
</script>
<?php

        }

        function oftt_alt_cover_logo($html, $blog_id) {

            $html = '';
            $switched_blog = false;
 
            if (is_multisite() && !empty($blog_id) && get_current_blog_id() !== (int) $blog_id) {

                switch_to_blog( $blog_id );
                $switched_blog = true;

            }

            $custom_logo_id = get_theme_mod('alt_cover_logo');
 
            if ($custom_logo_id) {

                $custom_logo_attr = array(
                    'class' => 'custom-logo',
                );

                $image_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);

                if (empty($image_alt)) {

                    $custom_logo_attr['alt'] = get_bloginfo('name', 'display');

                }

                $html = sprintf(
                    '<a href="%1$s" class="custom-logo-link" rel="home">%2$s</a>',
                    esc_url( home_url( '/' ) ),
                    wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr )
                );

            } elseif (is_customize_preview()) {

                $html = sprintf(
                    '<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo"/></a>',
                    esc_url(home_url('/'))
                );

            }
 
            if ($switched_blog) {

                restore_current_blog();

            }

            return $html;

        }

	}

    if (!class_exists('ofttCommon')) {

        require_once(dirname(__FILE__) . '/includes/class-oftt-common.php');

    }

    if (ofttCommon::is_theme_being_used('twentytwenty')) {

	    $options_for_twenty_twenty_object = new options_for_twenty_twenty_class();

    } else {

        if (is_admin()) {

            $themes = wp_get_themes();

            if (!isset($themes['twentytwenty'])) {

                add_action('admin_notices', 'oftt_wrong_theme_notice');

            }

        }

    }

    function oftt_wrong_theme_notice() {

?>

<div class="notice notice-error">

<p><strong><?php _e('Options for Twenty Twenty Plugin Error', 'options-for-twenty-twenty'); ?></strong><br />
<?php
        printf(
            __('This plugin requires the default Wordpress theme Twenty Twenty to be active or live previewed in order to function. Your theme "%s" is not compatible.', 'options-for-twenty-twenty'),
            get_template()
        );
?>

<a href="<?= add_query_arg('search', 'twentytwenty', admin_url('theme-install.php')); ?>" title="<?= __('Twenty Twenty', 'options-for-twenty-twenty'); ?>"><?php
        _e('Please install and activate or live preview the Twenty Twenty theme (or a child theme thereof)', 'options-for-twenty-twenty');
?></a>.</p>

</div>

<?php

    }

}

?>
