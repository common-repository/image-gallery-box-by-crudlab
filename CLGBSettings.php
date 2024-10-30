<?php
if (!defined('ABSPATH'))
    exit;

class CLGBSettings {

    /**
     *
     * @var CLGalleryBox
     */
    private $parrent = null;

    public function __construct(CLGalleryBox &$parrent) {
        $this->parrent = $parrent;
    }

    public function addJSCSS() {
        add_action('wp_enqueue_scripts', array($this, 'registerJSCSS'));
    }

    public function registerJSCSS() {
        wp_register_style('cligb-bootstrap', plugins_url('dist/css/vendor/bootstrap.min.css', __FILE__), array(), '20120208', 'all');
        wp_register_style('cligb-style-flat', plugins_url('dist/css/flat-ui.css', __FILE__), array(), '20120208', 'all');
        wp_register_style('cligb-fontawsome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '20120208', 'all');
        wp_register_style('cligb-popupstyle', plugins_url('dist/css/popstyles.css', __FILE__), array(), '20120208', 'all');
        wp_register_style('cligb-style', plugins_url('dist/css/style.css', __FILE__), array(), '20120208', 'all');

        wp_enqueue_style('cligb-bootstrap');
        wp_enqueue_style('cligb-style-flat');
        wp_enqueue_style('cligb-fontawsome');
        wp_enqueue_style('cligb-style');
        wp_enqueue_style('cligb-popupstyle');

//        wp_register_script('cligb-src-falt', plugins_url('js/flat-ui.min.js', __FILE__), array('jquery'));
        wp_register_script('cligb-srcipt', plugins_url('js/scripts.js', __FILE__), array('jquery'));
        wp_register_script('cligb-btswitch', plugins_url('js/bootstrap-switch.min.js', __FILE__), array('jquery'));
        wp_register_script('cligb-select2', plugins_url('js/select2.min.js', __FILE__), array('jquery'));
        wp_register_script('clgp-radiocheck', plugins_url('js/radiocheck.js', __FILE__), array('jquery'));

//        wp_enqueue_script('cligb-src-falt');
        wp_enqueue_script('cligb-btswitch');
        wp_enqueue_script('cligb-select2');
        wp_enqueue_script('clgp-radiocheck');
        wp_enqueue_script('cligb-srcipt');
    }

    public function validateData() {
        return true;
    }

    public function saveData() {
        update_option(CLGalleryBox::$optionName, serialize(array(
            'box_style' => sanitize_text_field($_POST['box_style']),
            'animation' => sanitize_text_field($_POST['animation']),
            'show_title' => sanitize_text_field($_POST['show_title']),
//            'open_speed' => intval($_POST['open_speed']),
//            'close_speed' => intval($_POST['close_speed']),
            'status' => (isset($_POST['status']) && $_POST['status'] == 'on') ? 1 : 0,
        )));
        $this->parrent->reloadSettings();
    }

    public function getAnimation() {
        return array(
            'None' => array('none', 'none'),
//            'Zoom' => array('zoomOut', 'zoomIn'),
            'Rotate' => array('rotateIn', 'rotateOut'),
            'Horizontal move' => array('bounceInLeft', 'bounceOutRight'),
            'Move from top' => array('fadeInDown', 'fadeOutDown'),
            '3d unfold' => array('flipInY', 'flipOutY'),
            'Zoom-out' => array('zoomIn', 'zoomOut')
        );
    }

    public function renderPage() {
        global $CLIGBPath;
        $obj = $this->parrent->getSettingsData();
        ?>
        <div class="c-popup-wrap" style="display: none;">
            <div class="overlay"></div>
        </div>
        <form method="post">
            <div class="plugins-wrap">
                <div class="col-left">
                    <div class="row-wrap">
                        <h4>Image Gallery Box By CRUDLab</h4>
                        <div class="small">
                            Image Gallery Box is a great tool for viewing photos and for creating photo slides. 
                        </div>
                    </div>
                    <div class="row-wrap">
                        <div class="where pull-left">
                            <div class="small ">
                                <strong>Image Box Style</strong>
                            </div>
                        </div>
                        <div class="options-wrap pull-left">
                            <div class="block">
                                <div class="block">
                                    <select class="form-control select select-primary" name="box_style" data-toggle="select">
                                        <?php for ($i = 1; $i <= 6; $i++) { ?>
                                            <option <?php echo ($i == $obj['box_style']) ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>">Style <?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row-wrap"  style="background: none repeat scroll 0 0 rgba(0, 0, 0, 0.1)">
                        <div id="boxpophtml" class="block" style="text-align: center; padding-top: 30px;padding-bottom: 30px;">
                            <?php include_once $CLIGBPath . 'popups/style' . $obj['box_style'] . '.php'; ?>
                        </div>
                    </div>
                    <div class="row-wrap">
                        <div class="where pull-left">
                            <div class="small" style="font-size: 14px;">
                                <strong>Image Box Animation</strong>
                            </div>
                        </div>
                        <div class="options-wrap pull-left">
                            <div class="block">
                                <div class="block">
                                    <select class="form-control select select-primary" name="animation" data-toggle="select">
                                        <?php foreach ($this->getAnimation() as $key => $value) { ?>
                                            <option <?php echo $obj['animation'] == $key ? 'selected="selected"' : ''; ?>  value="<?php echo $key; ?>" data-aniin="<?php echo $value[0]; ?>" data-aniout="<?php echo $value[1]; ?>"><?php echo $key; ?></option>
                                        <?php } ?>
                                    </select>
                                    <a class="inline-block btn-preview" style="margin-left: 10px; font-size: 14px; text-decoration: underline;" href="javascript://">Preview</a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row-wrap">
                        <div class="block">
                            <div class="where pull-left">
                                <div class="small ">
                                    <strong>Show Title</strong>
                                </div>
                            </div>
                            <div class="options-wrap pull-left">
                                <label class="radio inline-block">
                                    <input type="radio" <?php echo ($obj['show_title'] == 1) ? 'checked' : ''; ?> data-toggle="radio" value="1"  name="show_title" class="custom-radio">
                                    <span class="icons">
                                        <span class="icon-unchecked"></span>
                                        <span class="icon-checked"></span>
                                    </span>
                                    Yes
                                </label>
                                <label class="radio inline-block">
                                    <input type="radio" <?php echo ($obj['show_title'] == 0) ? 'checked' : ''; ?>  data-toggle="radio" value="0" name="show_title" class="custom-radio"><span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span>
                                    No
                                </label>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <?php /*
                    <div class="row-wrap">
                        <div class="block">
                            <div class="col-md-6">
                                <div class="where pull-left align-left">
                                    <div class="small" style="font-size: 14px;">
                                        <strong>Opening Speed:</strong>
                                    </div>
                                </div>
                                <div class="options-wrap col-md-8" style="padding: 0;">
                                    <div class="block">
                                        <div class="block">
                                            <div class="input-group">
                                                <input type="text" value="<?php echo $obj['open_speed']; ?>" name="open_speed" class="form-control col-md-3">
                                                <span class="input-group-addon">Seconds</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="where pull-left align-left">
                                    <div class="small" style="font-size: 14px;">
                                        <strong>Closing Speed:</strong>
                                    </div>
                                </div>
                                <div class="options-wrap col-md-8" style="padding: 0;">
                                    <div class="block">
                                        <div class="block">
                                            <div class="input-group">
                                                <input type="text"  value="<?php echo $obj['close_speed']; ?>" name="close_speed" class="form-control col-md-3">
                                                <span class="input-group-addon">Seconds</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                     */ ?>
                    <div class="row-wrap">
                        <button class="btn btn-success pull-left">Save Settings</button>
                        <div class="pull-right">
                            <input type="checkbox" data-toggle="switch" name="status" <?php echo ($obj['status'] == 1) ? 'checked' : ''; ?> />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </form>
        <?php
    }

}
