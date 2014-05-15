<?php
/**
* @version		1.0.0
* @package		MiwoPolls
* @subpackage	MiwoPolls
* @copyright	2009-2011 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html

*
* Based on Apoll Component
* @copyright (C) 2009 - 2011 Hristo Genev All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.afactory.org
*/

// no direct access
defined('MIWI') or die('Restricted access');

class modMiwopollsHelper {

	function getPollOptions($poll_id) {
		$db	= MFactory::getDBO();

		$query = "SELECT o.id, o.text, o.ordering" .
			" FROM #__miwopolls_options AS o " .
			" WHERE o.poll_id = ".(int)$poll_id.
			" AND o.text <> ''" .
			" ORDER BY o.ordering"
			;
		
		$db->setQuery($query);

		if (!($options = $db->loadObjectList())) {
			echo "helper ".$db->stderr();
			return;
		}

		return $options;
	}
	
	// checks if user has voted (if cookie is set)
	function alreadyVoted($id) {
		$mainframe = MFactory::getApplication();
		
		if (MiwopollsHelper::is30()) {
			$cookieName	= MApplication::getHash($mainframe->getName().'poll'.$id);
		}
		else {
			$cookieName	= MUtility::getHash($mainframe->getName().'poll'.$id);
		}
		
		$voted 		= MRequest::getVar($cookieName, '0', 'COOKIE', 'INT');
		
		return $voted;
	}
	
	function userVoted($user_id, $poll_id) {
		$db	= MFactory::getDBO();
		$query = "SELECT date FROM #__miwopolls_votes WHERE poll_id=".(int) $poll_id." AND user_id=".(int)$user_id; 
		$db->setQuery($query);

		return $userVoted=($db->loadResult()) ? 1 : 0;	
	}
	
	function ipVoted($poll_id) {
		$db	= MFactory::getDBO();
		$ip = ip2long($_SERVER['REMOTE_ADDR']);
		$query = "SELECT ip FROM #__miwopolls_votes WHERE poll_id=".(int) $poll_id." AND ip = '".$ip."'";
		$db->setQuery($query); 
		
		return $ipVoted=($db->loadResult()) ? 1 : 0;	
	}
	
	function getResults($poll_id) {
        $db	= MFactory::getDBO();
		$query = "SELECT o.*, COUNT(v.id) AS hits, 
		(SELECT COUNT(id) FROM #__miwopolls_votes WHERE poll_id=".$poll_id.") AS votes 
		FROM #__miwopolls_options AS o 
		LEFT JOIN  #__miwopolls_votes AS v 
		ON (o.id = v.option_id AND v.poll_id = ".(int)$poll_id . ")
		WHERE o.poll_id=".(int)$poll_id ." 
		AND o.text <> '' 
		GROUP BY o.id 
		ORDER BY o.ordering";
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
    
    function getActivePolls() {
        $db    = MFactory::getDBO();
        $query = "SELECT id FROM #__miwopolls_polls WHERE published = 1";
        $db->setQuery($query);
        if ($ids = $db->loadResultArray()) {
            return $ids;
        } else {
            return false;
        }
    }
	
	function getItemid($poll_id) {
        $component 	= MComponentHelper::getComponent('com_miwopolls');
		$menus		= MApplication::getMenu('site', array());

        if (MiwopollsHelper::is15()) {
            $items	= $menus->getItems('componentid', $component->id);
        }
        else {
            $items	= $menus->getItems('component_id', $component->id);
        }

		$match 		= false;
		$item_id	= '';
		
		if (isset($items)) {
			foreach ($items as $item) {
				if ((@$item->query['view'] == 'poll') && (@$item->query['id'] == $poll_id)) {
					$itemid = $item->id;
					$match = true;
					break;
				}			
			}
		}
		
		if ($match) {
			$item_id = '&Itemid='.$itemid;
		}
		
		return $item_id;
	}
}