<?php
/*
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.application.component.controller');

if (!class_exists('MiwisoftController')) {
    if (interface_exists('MController')) {
        abstract class MiwisoftController extends MControllerLegacy {}
    }
    else {
        class MiwisoftController extends MController {}
    }
}

class MiwopollsController extends MiwisoftController {

	public function __construct() {
		parent::__construct();

		$this->registerTask('add', 			'edit');
		$this->registerTask('apply', 		'save');
		$this->registerTask('unpublish', 	'publish');
		$this->registerTask('deleteVotes', 	'deleteVotes');
		$this->registerTask('importPolls', 	'importPolls');

			if(!is_admin()){
            $_lang = MFactory::getLanguage();
            $_lang->load('com_miwopolls', ABSPATH, 'en-GB', true);
            $_lang->load('com_miwopolls', MPATH_WP_CNT.'/miwi', $_lang->getDefault(), true);
            $_lang->load('com_miwopolls', ABSPATH, null, true);
        }
			
	}

    public function display($cachable = false, $urlparams = false) {
		if (MFactory::getApplication()->isAdmin()) {
			$controller = MRequest::getVar('controller', 'polls');

			if (isset($_GET['view']) and ($_GET['view'] == "config")){
			$controller = MRequest::getVar('controller', 'config');
			}
			
		}
		else {
			$controller = MRequest::getVar('view', 'polls');
		}
		
		MRequest::setVar('view', $controller);

        parent::display($cachable, $urlparams);
	}
	
	public function edit() {
		MRequest::setVar('view', 'poll');
		MRequest::setVar('edit', true);
		MRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	
	public function preview() {
		MRequest::setVar('tmpl', 'component');
		MRequest::setVar('view', 'poll');

		parent::display();
	}

	public function save() {
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$db	= MFactory::getDBO();

		// save the apoll parent information
		$row = MTable::getInstance('Poll', 'Table');
		
		$post = MRequest::get('post');
		if (!$row->bind($post)) {
			MError::raiseError(500, $row->getError());
		}
		
		$isNew = ($row->id == 0);

		//reset the poll, erases hits and voters
		if ($optionReset = MRequest::getVar('reset')) {
			$model = $this->getModel('polls');
			$model->resetVotes((int)$row->id);
		}
		
		if (!$row->check()) {
			MError::raiseError(500, $row->getError());
		}

		if (!$row->store()) {
			MError::raiseError(500, $row->getError());
		}
		$row->checkin();
		
		// put all poll options and their colors and ordering in arrays
		$options 	= MArrayHelper::getValue($post, 'polloption', array(), 'array');
		$colors 	= MArrayHelper::getValue($post, 'color', array(), 'array');
		$orderings	= MArrayHelper::getValue($post, 'ordering', array(), 'array');
	
		//options represented by id=>text
 		foreach ($options as $i => $text) {
			// turns ' into &#039;
			$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

			if ($isNew) {
				if ($text != ''){
					$obj = new stdClass();
					$obj->poll_id  = (int)$row->id;
					$obj->text     = $text;
					$obj->color    = substr($colors[$i], -6);
					$obj->ordering = $orderings[$i];
					$db->insertObject('#__miwopolls_options', $obj);
				}
			} 
			else {
				if ($text != ''){
					$obj = new stdClass();
					$obj->id  	  	= (int)$i;
					$obj->text 	   	= $text;
					$obj->color	   	= substr($colors[$i], -6);
					$obj->ordering	= $orderings[$i];
					$db->updateObject('#__miwopolls_options', $obj, 'id');
				}
				else {
					//If there are empty options delete them so we don't waste database space
					$model = $this->getModel('poll');
					if (!$model->deleteOption($i)) {
						MError::raiseError(500, $model->getError());
					}	
				}
			}
		}
		
		// Are there any new options that are added
		if (MRequest::getVar('is_there_extra')) {
			$extra_options	 = MArrayHelper::getValue($post, 'polloptionextra', array(), 'array');
			$extra_ordering	 = MArrayHelper::getValue($post, 'extra_ordering', array(), 'array');
			$extra_colors 	 = MArrayHelper::getValue($post, 'extra_colors', array(), 'array');
			
			//Insert in the database the newly created options
			foreach ($extra_options as $k => $text) {
				if ($text != ''){
					$obj = new stdClass();
					$obj->poll_id  = (int)$row->id;
					$obj->text     = $text;
					$obj->color    = substr($extra_colors[$k], -6);
					$obj->ordering = $extra_ordering[$k];
					$db->insertObject('#__miwopolls_options', $obj);	
				}				
			}
		}
		
		switch (MRequest::getCmd('task')) {
			case 'apply':
				$msg = MText::_('COM_MIWOPOLLS_POLL_SAVED');
				$link = 'index.php?option=com_miwopolls&controller=poll&task=edit&cid[]='.$row->id;
				break;
			case 'save':
			default:
				$msg = MText::_('COM_MIWOPOLLS_POLL_SAVED');
				$link = 'index.php?option=com_miwopolls';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function remove() {
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$db	= MFactory::getDBO();
		$cid = MRequest::getVar('cid', array(), '', 'array');

		MArrayHelper::toInteger($cid);
		$msg = '';

		for ($i=0, $n=count($cid); $i < $n; $i++) {
			$apoll = MTable::getInstance('poll', 'Table');
			if (!$apoll->delete( $cid[$i] )) {
				$msg .= $apoll->getError();
				$tom = "error";
			}
			else {
				$msg = MText::_('COM_MIWOPOLLS_POLL_DELETED');
				$tom = "";
			}
		}
		
		$this->setRedirect('index.php?option=com_miwopolls', $msg, $tom);
	}
	
	public function deleteVotes() {
		// Check for request forgeries
		MRequest::checkToken() or mexit( 'Invalid Token' );
		
		$poll_id = MRequest::getVar('poll_id', 0, 'POST', 'INT');
		$model = $this->getModel('votes');
	
		if($model->deleteVotes()) {
			$msg = MText::_("COM_MIWOPOLLS_DELETED_VOTES_YES");
			$tom = "";
		} else {
			$msg = MText::_("COM_MIWOPOLLS_DELETED_VOTES_NO");
			$tom = "error";
		}
		
		$this->setRedirect('index.php?option=com_miwopolls&controller=votes&task=view&id='.$poll_id, $msg, $tom);
	}

	public function publish() {
		$mainframe = MFactory::getApplication();

		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$user = MFactory::getUser();
		
		$cid = MRequest::getVar( 'cid', array(), '', 'array' );
		$publish = (MRequest::getCmd('task') == 'publish' ? 1 : 0);
		
		$table = MTable::getInstance('poll', 'Table');
		MArrayHelper::toInteger($cid);

		if (!$table->publish($cid, $publish, $user->get('id'))) {
			$table->getError();
		}

		if (count($cid ) < 1) {
			$action = $publish ? 'publish' : 'unpublish';
			MError::raiseError(500, MText::_('Select an item to' .$action, true));
		}

		$mainframe->redirect('index.php?option=com_miwopolls');
	}

	public function cancel() {
		// Check for request forgeries
		MRequest::checkToken() or mexit( 'Invalid Token' );

		$id	= MRequest::getVar('id', 0, '', 'int');
		$row = MTable::getInstance('poll', 'Table');

		$row->checkin($id);
		
		$this->setRedirect( 'index.php?option=com_miwopolls' );
	}

	public function resetVotes() {
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');
		
		$model = $this->getModel('polls');
		
		if ($model->resetVotes()) {
			$msg = MText::_("COM_MIWOPOLLS_DELETED_POLL_VOTES_YES");
			$tom = "";
		}
		else {
			$msg = MText::_("VCOM_MIWOPOLLS_DELETED_POLL_VOTES_NO");
			$tom = "error";
		}
		
		$this->setRedirect('index.php?option=com_miwopolls&controller=polls', $msg, $tom);
	}


	 // SaveConfig changes
    function saveConfig() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');
		$model = $this->getModel('config');
        $model->save();

        $this->setRedirect('index.php?page=miwopolls', MText::_('COM_MIWOPOLLS_CONFIG_SAVED'));
    }

    // ApplyConfig changes
    function applyConfig() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');
		$model = $this->getModel('config');
        $model->save();

        $this->setRedirect('index.php?page=miwopolls&view=config', MText::_('COM_MIWOPOLLS_CONFIG_SAVED'));
    }

    // CancelConfig saving changes
    function cancelConfig() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->setRedirect('index.php?page=miwopolls', MText::_('COM_MIWOPOLLS_CONFIG_NOT_SAVED'));

    }			
	public function vote()	{
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$mainframe 	= MFactory::getApplication();
		$poll_id	= MRequest::getInt('id', 0);
		$option_id	= MRequest::getInt('voteid', 0);
		$poll 		= MTable::getInstance('Poll', 'Table');
		
		if (!$poll->load($poll_id) || $poll->published != 1) {
			MError::raiseWarning(404, MText::_('ALERTNOTAUTH'));
			return;
		}

        $model = $this->getModel('Poll');

					
			$params = new MRegistry($poll->params);
			$cookieName	= MApplication::getHash($mainframe->getName().'poll'.$poll_id );
			







		
		$voted_cookie = MRequest::getVar($cookieName, '0', 'COOKIE', 'INT');
        $voted_ip = $model->ipVoted($poll, $poll_id);
		
		if ($params->get('ip_check') and ($voted_cookie or $voted_ip or !$option_id)) {
			if ($voted_cookie || $voted_ip) {
				$msg = MText::_('COM_MIWOPOLLS_ALREADY_VOTED');
				$tom = "error";
			}

			if (!$option_id){
				$msg = MText::_('COM_MIWOPOLLS_NO_SELECTED');
				$tom = "error";
			}
		}
		else {
			if ($model->vote($poll_id, $option_id)) {
				//Set cookie showing that user has voted
							
				$session	= MFactory::getSession();
                $session->set('miwopollscookie',$cookieName);
                $session->set('miwopollscookielag',$poll->lag);
			
            }

			$msg = MText::_('COM_MIWOPOLLS_THANK_YOU');
			$tom = "";
			
			if (MFactory::getUser()->id != 0) {
				MPluginHelper::importPlugin('miwopolls');
				$dispatcher = MDispatcher::getInstance();
				$dispatcher->trigger('onAfterVote', array($poll, $option_id));
			}
		}

		// set Itemid id for links
		$menu	 = MFactory::getApplication()->getMenu();
		$items   = $menu->getItems('link', 'index.php?option=com_miwopolls');

		$itemid  = isset($items[0]) ? '&Itemid='.$items[0]->id : '';

		$this->setRedirect(MRoute::_('index.php?option=com_miwopolls&view=poll&id='. $poll_id.':'.$poll->alias.$itemid, false), $msg, $tom);
	}
}
