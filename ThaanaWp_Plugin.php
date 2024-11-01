<?php


include_once('ThaanaWp_LifeCycle.php');

class ThaanaWp_Plugin extends ThaanaWp_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'isEnabled' => array(__('Enable Thaana Keyboard:', 'my-awesome-plugin'), 'true', 'false'),
            'KeyboardType' => array(__('Keyboard Type:', 'my-awesome-plugin'), 'phonetic', 'phonetic-hh','typewriter'),
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Thaana WP';
    }

    protected function getMainPluginFileName() {
        return 'thaana-wp.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        function include_JTK()
        {
        ?>
        <!-- <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ); ?>css/custom_editor.css"> -->
        <script type='text/javascript' src='http://www.jawish.org/blog/uploads/jtk-4.2.1.pack.js'></script>
        <script>
        <?php $option = new ThaanaWp_Plugin();
            $type =  $option->getOption("KeyboardType"); 

            ?>
            thaanaKeyboard.defaultKeyboard = '<?php echo (empty($type))? "phonetic": $type ?>';
        </script>
        <?php
        }
        add_action('admin_head', 'include_JTK');

        add_filter( 'tiny_mce_before_init', 'myformatTinyMCE' );
        function myformatTinyMCE( $in ) {
             $option = new ThaanaWp_Plugin();
             $enabled =  $option->getOption("isEnabled"); 
            if($enabled == "true"){
                $in['directionality'] = "RTL";

                $in['setup'] = "function (editor) {
                    editor.on('keypress', function (e) {
                        thaanaKeyboard.value = '';
                        thaanaKeyboard.handleKey(e);
                        editor.insertContent(thaanaKeyboard.value);
                    });
                }";
            }
            
            return $in;
        }

        function my_theme_add_editor_styles() {
            add_editor_style( plugin_dir_url( __FILE__ ) . 'css/custom_editor.css' );
        }
        add_action( 'init', 'my_theme_add_editor_styles' );


    }


}
