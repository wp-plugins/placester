<?php

PL_Helper_Header::init();

class PL_Helper_Header {
    
    private static $page;
    private static $sub_pages = array();

    public function init() {
      add_action('admin_enqueue_scripts', array(__CLASS__, 'set_page' ) );
	  add_action('admin_head', array(__CLASS__, 'hide_sub_pages'));
    }

    public static function add_sub_page($parent_slug, $page_slug, $hook) {
    	self::$sub_pages[$page_slug] = $parent_slug;

   		// Fudge the highlighted subnav item when on a sub page
		add_action( "admin_head-$hook", array(__CLASS__, 'highlight_admin_menu') );
    }
    
	public static function hide_sub_pages() {
    	foreach(self::$sub_pages as $sub_page => $parent) {
  			remove_submenu_page('placester', $sub_page);
    	}
    }
    
    public static function highlight_admin_menu() {
    	global $plugin_page, $submenu_file;
    	$submenu_file = self::$sub_pages[$plugin_page];
    }

    public static function set_page ($hook) {
        self::$page = $hook;
    }

    public function pl_settings_subpages() {
    	global $settings_subpages;
    	$base_page = 'placester_settings';
    	return self::pl_subpages($base_page, $settings_subpages, 'Settings Pages');
    }

    public function pl_subpages($base_page, $subpages=array(), $title='') {
        global $submenu;
        $base_url = 'admin.php?page='.$base_page;
        
        ob_start();
        ?>
          <div class="settings_sub_nav">
            <ul>
              <li class="submenu-title"><?php echo $title.($title?':':'')?></li>
              <?php foreach ($subpages as $page_title => $page_url): ?>
                <li>
                  <?php
                    $current_page = ( !empty($page_url) ? strpos(self::$page, $page_url) : ($_REQUEST['page'] == $base_page) );
                  ?>
                  <a href="<?php echo $base_url . $page_url ?>" style="<?php echo $current_page ? 'color:#D54E21;' : '' ?>"><?php echo $page_title; ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php
        
        return ob_get_clean();
    }

    public static function placester_admin_header($title_postfix = '' ) {
        // placester_verified_check()
        global $wp_rewrite;
        global $submenu;

        $current_title = '';
        $menu = '';

        foreach ($submenu['placester'] as $i) {
            // Exclude settings submenu pages
            $check = explode('_', $i[2]);
            if (count($check) > 2 && $check[1] == 'settings') { continue; }
            
            $title = $i[0];
            $slug = $i[2];
            $style = '';

            if (strpos(self::$page, $slug)) {
                $style = 'nav-tab-active';
                $current_title = $title;
            }

            $id = str_replace(' ', '_', $title);
            
            // Handle custom links for listings
            if( strpos( $slug, 'edit.php?' ) === false ) {
                $menu .= "<a href='admin.php?page=$slug' style='font-size: 15px' class='nav-tab $style' id='$id'>$title</a>";
            } 
            else {
                $menu .= "<a href='$slug' style='font-size: 15px' class='nav-tab $style' id='$id'>$title</a>";
            }  
        }

        // Render header menu...
        ?>
            <div class='clear'></div>
            <!-- <div id="icon-options-general" class="icon32 placester_icon"><br /></div> -->
            <h2 id="placester-admin-menu" class="nav-tab-wrapper">
            <?php 
                echo $current_title; 
                echo $title_postfix; 
                echo "&nbsp;&nbsp;&nbsp;";
                echo $menu; 
            ?>
            </h2>
        <?php
    }

}

?>
