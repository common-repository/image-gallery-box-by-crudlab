<?php
/*
  Plugin Name: Image Gallery Box by CRUDLab
  Description: Image Gallery Box is a great tool for viewing photos and for creating photo slides. 
  Author: <a href="http://crudlab.com/">CRUDLab</a>
  Version: 1.0.3
 */
$CLIGBPath = plugin_dir_path(__FILE__);
require_once $CLIGBPath . 'CLGBSettings.php';

class CLGalleryBox {

    private $CLGbSettings = null;
    public static $optionName = 'clgallerybox-options';
    private $db_version = '1.0.0';
    private $menuSlug = "cl-imggallery-box";
    private $settingsData = null;
    public static $defaultSettings = array(
        'box_style' => 1,
        'animation' => 'None',
        'show_title' => 1,
        'open_speed' => 50,
        'close_speed' => 50,
        'status' => 1,
    );

    public function __construct() {


        add_action('admin_menu', array($this, 'setup_menu'));
        $this->CLGbSettings = new CLGbSettings($this);

        add_action('wp_ajax_clgbactive', array($this, 'activePlugin'));
        add_action('wp_ajax_clgbphtml', array($this, 'getPopHtml'));

        if (get_option(CLGalleryBox::$optionName) == "") {
            update_option(CLGalleryBox::$optionName, serialize(CLGalleryBox::$defaultSettings));
            $this->settingsData = unserialize(get_option(CLGalleryBox::$optionName));
        } else {
            $this->settingsData = unserialize(get_option(CLGalleryBox::$optionName));
        }

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", array($this, 'settingsLink'));

        add_filter('wp_footer', array($this, 'addHtml'));
    }

    public function activePlugin() {
        $this->settingsData['status'] = (isset($_REQUEST['status']) && $_REQUEST['status'] == 'true') ? 1 : 0;
        update_option(CLGalleryBox::$optionName, serialize($this->settingsData));
        echo json_encode(array('status' => $this->settingsData['status']));
        wp_die();
    }

    public function getPopHtml() {
        $style = ((isset($_REQUEST['style']) && intval($_REQUEST['style'])) ? $_REQUEST['style'] : 1);
        global $CLIGBPath;
        $filename = $CLIGBPath . 'popups/style' . $style . '.php';
        if (file_exists($filename)) {
            include_once $filename;
        } else {
            include_once $filename = $CLIGBPath . 'popups/style1.php';
        }
        wp_die();
    }

    public function addHtml() {
        $sett = $this->getSettingsData();
        if ($sett['status'] == 1) {
            $animation = $this->CLGbSettings->getAnimation();
            $fani = $animation[$sett['animation']];
            ?>
            <div class="c-popup-wrap" style="display: none;">
                <div class="overlay"></div>
                <?php
                global $CLIGBPath;
                $filename = $CLIGBPath . 'popups/style' . $sett['box_style'] . '.php';
                if (file_exists($filename)) {
                    include_once $filename;
                } else {
                    include_once $filename = $CLIGBPath . 'popups/style1.php';
                }
                ?>
            </div>
            <script type="text/javascript">

                jQuery(document).ready(function ($) {
                    jQuery("body a[href$='.jpg'], body a[href$='.jpeg'], body a[href$='.png'], body a[href$='.gif']").crudgallery({
                        amiIn: "<?php echo $fani[0] ?>",
                        amiOut: "<?php echo $fani[1] ?>",
                        showtitle: <?php echo $sett['show_title']; ?>,
                    });
                });
            </script>
            <?php
            wp_register_script('cligb-cpopup', plugins_url('js/cpopup.js', __FILE__), array('jquery'),'v0.0.2');
            wp_register_style('cligb-popupstyle', plugins_url('dist/css/popstyles.css', __FILE__), array(), 'v0.0.2', 'all');
            wp_register_style('cligb-fontawsome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '20120208', 'all');
            wp_enqueue_script('cligb-cpopup');
            wp_enqueue_style('cligb-popupstyle');
            wp_enqueue_style('cligb-fontawsome');
        }
    }

    public function reloadSettings() {
        $this->settingsData = unserialize(get_option(CLGalleryBox::$optionName));
    }

    function settingsLink($links) {
        $settings_link = '<a href="admin.php?page=' . $this->menuSlug . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function getMenuSlug() {
        return $this->menuSlug;
    }

    public function setMenuSlug($menuSlug) {
        $this->menuSlug = $menuSlug;
    }

    public function getSettingsData() {
        return $this->settingsData;
    }

    public function setup_menu() {
        $set = $this->getSettingsData();
        if ($set['status'] == 0) {
            add_menu_page('CRUDLab Image Gallery Box', 'CL Image Gallery Box <span  class="update-plugins count-1" id="clgp_circ" style="background:#F00"><span class="plugin-count">&nbsp&nbsp</span></span>', 'manage_options', $this->menuSlug, array($this, 'admin_settings'), plugins_url('img/cgallery.png', __FILE__));
        } else {
            add_menu_page('CRUDLab Image Gallery Box', 'CL Image Gallery Box <span class="update-plugins count-1" id="clgp_circ" style="background:#0F0"><span class="plugin-count">&nbsp&nbsp</span></span>', 'manage_options', $this->menuSlug, array($this, 'admin_settings'), plugins_url('img/cgallery.png', __FILE__));
        }
    }

    function admin_settings() {
        $this->CLGbSettings->registerJSCSS();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->CLGbSettings->validateData()) {
                $this->CLGbSettings->saveData();
            }
        }
        $this->CLGbSettings->renderPage();
    }

}

global $clgallerybox;
$clgallerybox = new CLGalleryBox();
