<?php 
/**
* @version		1.0.0
* @package		MiwoPolls
* @subpackage	MiwoPolls Search
* @copyright	2011 Miwisoft LLC, www.miwisoft.com
* @license		GNU GPL
*/

//No Permision
defined('MIWI') or die( 'Restricted access' );

class plgSearchMiwopolls extends MPlugin {

	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}
	
	function onContentSearchAreas() {
	
		static $areas = array('miwopolls' => 'Polls');
		return $areas;
	}
	
	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null,$context = null ) {
		
        if ($context != 'miwopolls') {
            return array();
        }
		
		$db	= MFactory::getDBO();

		if (is_array( $areas )) {
			if (!array_intersect($areas, array_keys(self::onContentSearchAreas()))) {
				return array();
			}
		}

		$limit = $this->params->get('search_limit', 50);

		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$text = $db->Quote('%'.$db->getEscaped($text, true).'%', false);
		
		$query	= 'SELECT id AS ID, title AS post_title, alias, publish_up AS post_date'
		. ' FROM #__miwopolls_polls'
		. ' WHERE (title LIKE '.$text.' OR alias LIKE '.$text.') AND published = 1'
		. ' GROUP BY id'
		. ' ORDER BY title'
		;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
		if (empty($rows)) {
			return array();
		}

		foreach($rows as $key => $row) {
			$rows[$key]->href = MRoute::_('index.php?option=com_miwopolls&amp;view=poll&amp;id='. $row->ID . ":" . $row->alias . self::getItemid($row->ID));
		}

		return $rows;
	}
	
	function getItemid($poll_id) {
        $component 	= MComponentHelper::getComponent('com_miwopolls');
		$menus		= MApplication::getMenu('site', array());
		$items		= $menus->getItems('component_id', $component->id);
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