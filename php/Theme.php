<?php

namespace MP;

use MP\Modules\Acf_Config;
use MP\Modules\Disabled_Comments;
use SimpleXMLElement;
use WP_Post;

class Theme
{
    /**
     * @var null|Theme
     */
    protected static ?Theme $instance       = null;

    public static string    $theme_path     = '';

    public static string    $theme_uri      = '';

    public static string    $domain         = 'site-domain';

    private bool            $show_admin_bar = false;

    public static string    $nonce_action   = 'site-domain-ajax';

    private static array    $CSS            = [
        'frontend' => [
            'src'      => 'assets/css/frontend.min.css',
            'deps'     => [],
            'template' => [
                'all'
            ]
        ]
    ];

    private static array    $JS             = [
        'frontend' => [
            'src'      => 'assets/js/frontend.min.js',
            'deps'     => [],
            'template' => [
                'all'
            ]
        ]
    ];

    private static array    $ADMIN_JS       = [
        'admin' => [
            'src'  => 'assets/js/admin.min.js',
            'deps' => [],
        ],
    ];

    private static array    $ADMIN_CSS      = [
        'admin' => [
            'src'  => 'assets/css/admin.min.css',
            'deps' => [],
        ],
    ];

    
    /**
     * Return an instance of this class.
     */
    public static function instance(): ?Theme
    {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        self::$theme_path = trailingslashit(get_template_directory());
        self::$theme_uri  = trailingslashit(get_template_directory_uri());

        $this->disabled_admin_bar();

        $this->include_modules();

        add_action('wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], PHP_INT_MAX);
        add_action('admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ]);

        /**
         * Disable gutenberg
         */
        add_filter('gutenberg_can_edit_post', "__return_false", 5);
        add_filter('use_block_editor_for_post', "__return_false", 5);

        /**
         * Filters whether to attempt to guess a redirect URL for a 404 request.
         */
        add_filter('do_redirect_guess_404_permalink', '__return_false');

        /**
         * Remove oembed link
         */
        remove_action('wp_head', 'wp_oembed_add_discovery_links');

        /**
         * Removes svg from front
         */
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

        add_action('init', [ $this, 'init' ]);
        add_action('after_setup_theme', [ $this, 'setup' ]);

        /**
         * Support svg
         */
        add_filter('upload_mimes', [ $this, 'svg_upload_allow' ]);
        add_filter('wp_check_filetype_and_ext', [ $this, 'fix_svg_mime_type' ], 10, 5);

        /**
         * Adds svg tag to acf attachment
         */
        add_filter('acf/load_attachment', [ $this, 'get_svg_content' ], 10, 3);

        add_filter('wp_get_attachment_metadata', [ $this, 'add_width_height_to_svg' ], 10, 2);

        /**
         * Show template name in admin table
         */
        add_filter('display_post_states', [ $this, 'special_page_mark' ], 10, 2);

        /**
         * Disable plugin updates
         */
        add_filter('site_transient_update_plugins', [ $this, 'disable_update_plugins' ]);

        /**
         *  Add defer to script tag
         */
        add_filter('script_loader_tag', [ $this, 'add_async_attribute' ], 10, 2);

        /**
         * Add mega menu
         */
        add_filter('walker_nav_menu_start_el', [ $this, 'add_mega_menu' ], 10, 4);

        if ( wp_doing_ajax() ) {
            $this->add_ajax();
        }
    }

    /**
     * Add mega menu
     *
     * @param $item_output
     * @param $item
     * @param $depth
     * @param $args
     *
     * @return mixed|string
     */
    public function add_mega_menu( $item_output, $item, $depth, $args )
    {
        foreach ( glob(self::$theme_path . 'template-parts/mega-menu/mega-menu-*.php') as $file_menu ) {
            $file_info = pathinfo($file_menu);

            if ( $file_info['extension'] === 'php' ) {
                $class = 'has-' . $file_info['filename'];
                if ( in_array($class, $item->classes) ) {
                    $item->classes[] = 'has-mega-menu';
                    ob_start();
                    load_template($file_menu, false);
                    $mega_menu   = ob_get_clean();
                    $item_output .= $mega_menu;
                }
            }
        }

        return $item_output;
    }

    /**
     * Include theme modules
     *
     * @return void
     */
    private function include_modules()
    {
        Acf_Config::instance();
        Disabled_Comments::instance();
    }

    /**
     * Add defer to script tag
     *
     * @param $tag
     * @param $handle
     *
     * @return string
     */
    public function add_async_attribute( $tag, $handle )
    {
        if ( !is_admin() ) {
            return str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    /**
     * Filters the default post display states used in the posts list table.
     *
     * @param string[] $post_states An array of post display states.
     * @param WP_Post  $post        The current post object.
     */
    public function special_page_mark( $post_states, $post )
    {
        $template_file = get_page_template_slug($post);
        $all           = wp_get_theme()->get_page_templates();

        if ( isset($all[ $template_file ]) ) {
            $post_states['template'] = esc_html($all[ $template_file ]) . ' template';
        }

        return $post_states;
    }

    /**
     * Filters the attachment meta data.
     *
     * @param array $data          Array of metadata for the given attachment.
     * @param int   $attachment_id Attachment post ID.
     *
     * @since 2.1.0
     *
     */
    public function add_width_height_to_svg( $data, $attachment_id )
    {
        if ( wp_attachment_is('svg', $attachment_id) && class_exists('SimpleXMLElement') ) {
            $attached_path = get_attached_file($attachment_id);
            $svg           = trim(file_get_contents($attached_path));

            if ( !empty($svg) ) {
                try {
                    $xml            = new SimpleXMLElement($svg);
                    $attr           = $xml->attributes();
                    $viewbox        = explode(' ', $attr->viewBox);
                    $data['width']  = isset($attr->width) && preg_match('/\d+/', $attr->width,
                                                                        $value) ? (int) $value[0] : ( count($viewbox) == 4 ? (int) $viewbox[2] : null );
                    $data['height'] = isset($attr->height) && preg_match('/\d+/', $attr->height,
                                                                         $value) ? (int) $value[0] : ( count($viewbox) == 4 ? (int) $viewbox[3] : null );
                } catch ( \Exception $e ) {
                }
            }
        }

        return $data;
    }

    /**
     * Filters the attachment $response after it has been loaded.
     *
     * @param array       $response   Array of loaded attachment data.
     * @param WP_Post     $attachment Attachment object.
     * @param array|false $meta       Array of attachment metadata, or false if there is none.
     */
    public function get_svg_content( $response, $attachment, $meta ): array
    {
        $is_svg = $response['mime_type'] === 'image/svg+xml';

        if ( $is_svg ) {
            $file = get_attached_file($response['ID']);
            if ( file_exists($file) ) {
                $response['svg'] = file_get_contents($file);
            }
        }

        return $response;
    }

    /**
     * @param array $attachment Acf attachment data
     *
     * @return string
     */
    public static function get_acf_svg_or_img( $attachment ): string
    {
        if ( isset($attachment['svg']) ) {
            return $attachment['svg'];
        }

        return sprintf('<img src="%s" alt="%s">', $attachment['url'], $attachment['alt']);
    }

    public function disabled_admin_bar(): void
    {
        if ( !$this->show_admin_bar ) {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    public function fix_svg_mime_type( $data, $file, $filename, $mimes, $real_mime = '' )
    {
        $dosvg = in_array($real_mime, [ 'image/svg', 'image/svg+xml' ]);

        if ( $dosvg ) {
            if ( current_user_can('manage_options') ) {
                $data['ext']  = 'svg';
                $data['type'] = 'image/svg+xml';
            } else {
                $data['ext']  = false;
                $data['type'] = false;
            }
        }

        return $data;
    }

    public function svg_upload_allow( $mimes )
    {
        $mimes['svg'] = 'image/svg+xml';

        return $mimes;
    }

    public function setup(): void
    {
        add_theme_support('title-tag');
        add_theme_support('menus');
        add_theme_support('post-thumbnails');

        add_theme_support('html5', [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ]);

        register_nav_menus([
                               'header_menu'               => __('Header menu', self::$domain),
                               'header_menu_left'          => __('Header menu left', self::$domain),
                               'header_menu_right'         => __('Header menu right', self::$domain),
                               'header_menu_mobile'        => __('Header menu mobile', self::$domain),
                               'header_menu_mobile_bottom' => __('Header menu mobile bottom', self::$domain),
                           ]);
    }

    public function init(): void
    {
        remove_post_type_support('page', 'editor');
        $this->disable_emojis();
    }

    /**
     * Disable emojis
     *
     * @return void
     */
    public function disable_emojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('tiny_mce_plugins', [ $this, 'disable_emojis_tinymce' ]);
        add_filter('wp_resource_hints', [ $this, 'disable_emojis_remove_dns_prefetch' ], 10, 2);
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param array $plugins
     *
     * @return array Difference betwen the two arrays
     */
    public function disable_emojis_tinymce( $plugins ): array
    {
        if ( is_array($plugins) ) {
            return array_diff($plugins, [ 'wpemoji' ]);
        } else {
            return [];
        }
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array  $urls          URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     *
     * @return array Difference betwen the two arrays.
     */
    public function disable_emojis_remove_dns_prefetch( $urls, $relation_type )
    {
        if ( 'dns-prefetch' == $relation_type ) {
            /** This filter is documented in wp-includes/formatting.php */
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

            $urls = array_diff($urls, [ $emoji_svg_url ]);
        }

        return $urls;
    }

    /**
     * Register scripts
     *
     * @return void
     */
    public function wp_enqueue_scripts(): void
    {
        $this->enqueue_style();
        $this->enqueue_script();

        /**
         * Disable block library css
         *
         * @return void
         */
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('classic-theme-styles');

        wp_localize_script('frontend', 'theme_data', [
            'url'   => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::$nonce_action)
        ]);
    }

    /**
     * Enqueue style
     *
     * @return void
     */
    public function enqueue_style(): void
    {
        global $template;

        $file_name = basename($template);

        foreach ( self::$CSS as $handle => $data ) {
            if ( in_array($file_name, $data['template']) || in_array('all', $data['template']) ) {
                wp_enqueue_style($handle,
                                 !str_contains($data['src'], 'http') ? self::$theme_uri . $data['src'] : $data['src'],
                                 $data['deps'],
                                 file_exists(get_stylesheet_directory() . '/' . $data['src']) ? filemtime(get_stylesheet_directory() . '/' . $data['src']) : '');
            }
        }
    }

    /**
     * Enqueue script
     *
     * @return void
     */
    public function enqueue_script(): void
    {
        global $template;

        $file_name = basename($template);

        foreach ( self::$JS as $handle => $data ) {
            if ( in_array($file_name, $data['template']) || in_array('all', $data['template']) ) {
                $in_footer = $data['in_footer'] ?? true;
                wp_enqueue_script($handle,
                                  !str_contains($data['src'], 'http') ? self::$theme_uri . $data['src'] : $data['src'],
                                  $data['deps'],
                                  file_exists(get_stylesheet_directory() . '/' . $data['src']) ? filemtime(get_stylesheet_directory() . '/' . $data['src']) : '',
                                  $in_footer);
            }
        }
    }

    /**
     * Invert text color depending on its background color
     *
     * @param $hex
     *
     * @return string
     */
    public static function contrast_color( $hex ): string
    {
        $hex = trim($hex, ' #');

        $size = strlen($hex);
        if ( $size == 3 ) {
            $parts = str_split($hex);
            $hex   = '';
            foreach ( $parts as $row ) {
                $hex .= $row . $row;
            }
        }

        $dec = hexdec($hex);
        $rgb = [
            0xFF & ( $dec >> 0x10 ),
            0xFF & ( $dec >> 0x8 ),
            0xFF & $dec
        ];

        $contrast = ( round($rgb[0] * 299) + round($rgb[1] * 587) + round($rgb[2] * 114) ) / 1000;

        return ( $contrast >= 125 ) ? '#000' : '#fff';
    }

    private function add_ajax()
    {
        $ajax_callbacks = [];

        foreach ( $ajax_callbacks as $action => $callback ) {
            if ( method_exists($this, $callback) ) {
                add_action('wp_ajax_' . $action, [ $this, $callback ]);
                add_action('wp_ajax_nopriv_' . $action, [ $this, $callback ]);
            }
        }
    }

    public static function is_not_empty_acf_group( $group ): bool
    {
        $group = (array) $group;

        foreach ( $group as $value ) {
            if ( is_scalar($value) && !empty($value) ) {
                return true;
            } elseif ( is_array($value) || is_object($value) ) {
                return self::is_not_empty_acf_group($value);
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return string|void
     */
    public static function get_img_url( $name )
    {
        if ( file_exists(self::$theme_path . 'src/img/temp-img/' . $name) ) {
            return self::$theme_uri . 'src/img/temp-img/' . $name;
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function disable_update_plugins( $value )
    {
        if ( !is_object($value) )
            return $value;

        foreach ( self::DISABLED_UPDATE_PLUGIN as $plugin ) {
            unset($value->response[ $plugin ]);
        }

        return $value;
    }

    public function admin_enqueue_scripts( $hook_suffix )
    {
        foreach ( self::$ADMIN_JS as $handle => $data ) {
            $in_footer = $data['in_footer'] ?? true;
            wp_enqueue_script($handle . '_' . self::$domain,
                              !str_contains($data['src'], 'http') ? self::$theme_uri . $data['src'] : $data['src'],
                              $data['deps'],
                              file_exists(get_stylesheet_directory() . '/' . $data['src']) ? filemtime(get_stylesheet_directory() . '/' . $data['src']) : '',
                              $in_footer);
        }

        wp_localize_script('admin_' . self::$domain, 'map', [
            'nonce' => wp_create_nonce(self::$nonce_action)
        ]);

        foreach ( self::$ADMIN_CSS as $handle => $data ) {
            wp_enqueue_style($handle . '_' . self::$domain,
                             !str_contains($data['src'], 'http') ? self::$theme_uri . $data['src'] : $data['src'],
                             $data['deps'],
                             file_exists(get_stylesheet_directory() . '/' . $data['src']) ? filemtime(get_stylesheet_directory() . '/' . $data['src']) : '');
        }
    }
}