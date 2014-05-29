<?php
/*
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.installer.installer');
mimport('framework.filesystem.file');

class com_MiwopollsInstallerScript {

    private $_current_version = null;
    private $_is_new_installation = true;

    public function preflight($type, $parent) {
        $db = MFactory::getDBO();
        $db->setQuery('SELECT option_value FROM #__options WHERE option_name = "miwopolls"');
        $config = $db->loadResult();

        if (!empty($config)) {
            $this->_is_new_installation = false;

            $miwopolls_xml = MPATH_WP_PLG.'/miwopolls/miwopolls.xml';
            $a_miwopolls_xml = MPATH_WP_PLG.'/miwopolls/a_miwopolls.xml';

            if (MFile::exists($miwopolls_xml)) {
                $xml = MFactory::getXML($miwopolls_xml);
                $this->_current_version = (string)$xml->version;
            }
			else if (MFile::exists($a_miwopolls_xml)) {
                $xml = MFactory::getXML($a_miwopolls_xml);
                $this->_current_version = (string)$xml->version;
            }
        }
    }
	
	public function postflight($type, $parent) {
		$status = new MObject();
        $db = MFactory::getDBO();
		
		require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/miwopolls.php');

        if (MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwopolls/languages'), MPath::clean(MPATH_MIWI . '/languages'), null, true)) {
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/languages/admin'));
        }
        if (MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwopolls/languages'), MPath::clean(MPATH_MIWI . '/languages'), null, true)) {
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/languages/site'));
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/languages'));
        }
        if (MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwopolls/media'), MPath::clean(MPATH_MIWI . '/media/miwopolls'), null, true)) {
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/media'));
        }
        if (MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwopolls/modules'), MPath::clean(MPATH_MIWI . '/modules'), null, true)) {
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/modules'));
        }
        if (MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwopolls/plugins'), MPath::clean(MPATH_MIWI . '/plugins'), null, true)) {
            MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwopolls/plugins'));
        }

		//@TODO Delete this code next version(Current Version 1.0.1)
		if ($type == 'upgrade') {
			return;
		}
		########

        if ($this->_is_new_installation == true) {
			$this->_installMiwopolls();
		}
        else {
            $this->_updateMiwopolls();
        }
	}

    protected function _installMiwopolls() {
		
		$config = new stdClass();
		// General
		$config->only_registered 		= '0';
        $config->one_vote_per_user 		= '1';
        $config->ip_check 				= '1';
        $config->show_component_msg 	= '1';
        $config->allow_voting 			= '1';
        $config->show_comments 			= '1';
        
        // Results
        $config->show_what 				= '1';
        $config->show_hits 				= '1';
        $config->show_voters 			= '1';
        $config->show_times 			= '1';
        $config->show_dropdown 			= '1';
		
        // Pie Options
        $config->opacity 				= '90';
        $config->bg_color 				= 'ffffff';
        $config->circle_color		 	= '505050';
        $config->pieX 					= '100%';
        $config->pieY 					=	'400';
        $config->start_angle 			= '55';
        $config->radius 				= '150';
        $config->gradient 				= '1';
        $config->no_labels 				= '1';
        $config->show_zero_votes 		= '1';
        $config->animation_type 		= 'bounce';
        $config->bounce_dinstance 		= '30';
        $config->bg_image 				= '-1';
        $config->bg_image_x 			= 'left';
        $config->bg_image_y 			= 'top';
        $config->font_size 				= '11';
        $config->font_color 			= '404040';
        $config->title_lenght 			= '30';
		
		// Default Option
        $config->chartX 				= '100%';
        $config->optionsFontSize 		= '12';
        $config->barHeight 				= '15';
        $config->barBorder 				= '1px solid #000000';
        $config->bgBarColor 			= 'f5f5f5';
        $config->bgBarBorder 			= '1px solid #cccccc';
		
		$reg = new MRegistry($config);
        $config = $reg->toString();

        $db = MFactory::getDbo();
        $db->setQuery('INSERT INTO `#__options` (option_name, option_value) VALUES ("miwopolls", '.$db->Quote($config).')');
        $db->query();
		
		$this->addPage();
		
	  if (empty($this->_current_version)) {
            return;
        }

        if ($this->_current_version = '1.0.0') {
            return;
        }
			
    }

    protected function _updateMiwopolls() {
        if (empty($this->_current_version)) {
            return;
        }

        if ($this->_current_version = '1.0.0') {
            return;
        }
    }
	
	
	public function uninstall($parent) {
		$db  = MFactory::getDBO();
		$src = __FILE__;
	}

	public function addPage(){
        $page_content="<!-- MiwoPolls Shortcode. Please do not remove to widgets work properly. -->[miwopolls]<!-- MiwoPolls Shortcode End. -->";
        add_option("miwopolls_page_id",'','','yes');

        $miwopolls_post  = array();
        $_tmp_page      = null;

        $id = get_option("miwopolls_page_id");

        if (!empty($id) && $id > 0) {
            $_tmp_page = get_post($id);
        }

        if ($_tmp_page != null){
            $miwopolls_post['ID']            = $id;
            $miwopolls_post['post_status']   = 'publish';

            wp_update_post($miwopolls_post);
        }
        else{
            $miwopolls_post['post_title']    = 'Miwopolls';
            $miwopolls_post['post_content']  = $page_content;
            $miwopolls_post['post_status']   = 'publish';
            $miwopolls_post['post_author']   = 1;
            $miwopolls_post['post_type']     = 'page';
            $miwopolls_post['comment_status']= 'closed';

			
            $id = wp_insert_post($miwopolls_post);
            update_option('miwopolls_page_id',$id);
        }
    }
	
}