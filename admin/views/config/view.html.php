<?php
/**
 * @package		MiwoPolls
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die;

class MiwopollsViewConfig extends MiwopollsView {
	
	public function display($tpl = null) {
        $form = MForm::getInstance('config', MPATH_WP_PLG.'/miwopolls/admin/config.xml', array(), false, '/config');
        $params =MiwopollsHelper::getConfig();
        $form->bind($params);

       
            MToolBarHelper::title(MText::_('Configuration').':' , 'miwopolls' );
            MToolBarHelper::apply('applyConfig');
            MToolBarHelper::save('saveConfig');
            MToolBarHelper::cancel('cancelConfig');
       
        $this->form = $form;
			
		parent::display($tpl);				
	}

}