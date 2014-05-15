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


// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');
require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/miwopolls.php');

$tabclass_arr = array ('sectiontableentry2', 'sectiontableentry1');

$menu	 	= MFactory::getApplication()->getMenu();
$items		= $menu->getItems('link', 'index.php?option=com_miwopolls&view=poll');
$itemid 	= isset($items[0]) ? '&Itemid='.$items[0]->id : '';
$details 	= "";

$poll_id 	= $params->get('id', 1);
//if Show random poll is selected
if (!$poll_id) {
    $ids = modMiwopollsHelper::getActivePolls();
    
    if (count($ids) > 1) {
        $poll_id = $ids[array_rand($ids)];
    } else {
        $poll_id = $ids[0];
    }
}

$results = modMiwopollsHelper::getResults($poll_id);

//print_r($results); exit; 
//get the component params	
MTable::addIncludePath(MPATH_WP_PLG.'/miwopolls/admin/tables');
$poll = MTable::getInstance('Poll', 'Table');

if (!$poll->load($poll_id)) {
	return;
}

//merge them with the module params
$pollParams = new MRegistry($poll->params);
$params = clone($params);
$params->merge($pollParams);

$slug = ($poll->alias=='')? $poll->id : $poll->id.":".$poll->alias;

// check if cookie is set showing that the user has voted for this poll
$voted = modMiwopollsHelper::alreadyVoted($poll_id);

// check if the registrated user has voted and if user has logged in
$user = MFactory::getUser();
$userVoted = modMiwopollsHelper::userVoted($user->id, $poll_id);
$guest = $user->guest;

// check if this ip has been recorder
$ipVoted = modMiwopollsHelper::ipVoted($poll_id);

//reset the var display_poll
$display_poll = 0;

//check the start time and the end of poll
$mainframe = MFactory::getApplication();
$date = MFactory::getDate();
//$date->setOffset($mainframe->getCfg('offset'));
$now   = $date->toSql(); 

if (($now > $poll->publish_up) && ($now < $poll->publish_down)) {
	$display_submit = 1;

	// if only registered users can vote
	if ($params->get('only_registered')) {
		//if the user is not a guest
		if(!$guest) {
			//if only one vote is allowed per logged user
			if($params->get('one_vote_per_user')) {
				//check if user has voted
				if ($userVoted) {
				//display the poll with disabled options
					$display_submit = 0;
					$msg = MText::_("MOD_MIWOPOLLS_ALREADY_VOTED");
					$details = MText::_("MOD_MIWOPOLLS_ONLY_ONE_VOTE_PER_USER");
				//user has not voted yet
				} else {
				//display the poll
                    $display_poll = 1;
                    $display_submit = 1;
					$msg = "";
				}
			// if loggedin user are allowed to vote unlimited times
			} else {
				// Check the cookie
				if($voted) {
					$display_poll = 0;
					$display_submit = 0;
					$msg = MText::_("MOD_MIWOPOLLS_ALREADY_VOTED");
					$details = MText::sprintf("MOD_MIWOPOLLS_ONLY_ONE_VOTE_PER_HOUR", $poll->lag / 60);

                //hm check the ip please but only if allowed to do that
                } elseif ($params->get('ip_check')) {
    				if($ipVoted) {
    					//display the poll with disabled options
    					$display_poll = 0;
    					$display_submit = 0;
    					$msg 		= MText::_("MOD_MIWOPOLLS_ALREADY_VOTED");
    					$details 	= MText::_("MOD_MIWOPOLLS_ONLY_ONE_VOTE_PER_IP");
    				//if user's ip has not been logged
    				}
				//if user has not voted
				} else {
					//display the poll
					$display_poll = 1;
					$display_submit = 1;
					$msg = "";
				}
			}
		//if the user has not logged in
		} else {
		//display message please log in
			$display_poll = 1;
			$display_submit = 0;

            $return = MRequest::getURI();
            $return = base64_encode($return);
            $user_option = MiwopollsHelper::is15() ? 'com_user' : 'com_users';
            $link = 'index.php?option='.$user_option.'&view=login&return='.$return;

            $msg = MText::sprintf('MOD_MIWOPOLLS_PLEASE_REGISTER_TO_VOTE', '<a href="'.$link.'">', '</a>');
		}
	// if anybody can vote
	} else {
		//if user has voted, according to the cookie check
		if($voted) {
			//display the poll with disabled options or the results
			$display_poll = 0;
			$display_submit = 0;
			$msg = MText::_("MOD_MIWOPOLLS_ALREADY_VOTED");
			$details = MText::sprintf("MOD_MIWOPOLLS_ONLY_ONE_VOTE_PER_HOUR", $poll->lag/60);
		//if user has not voted according to the cookie
		} else {
			//hm check the ip please but only if allowed to do that
			if ($params->get('ip_check')) {
				if($ipVoted) {
					//display the poll with disabled options
					$display_poll = 0;
					$display_submit = 0;
					$msg 		= MText::_("MOD_MIWOPOLLS_ALREADY_VOTED");
					$details 	= MText::_("MOD_MIWOPOLLS_ONLY_ONE_VOTE_PER_IP");
				//if user's ip has not been logged
				} else {
					//display the poll
					$display_poll = 1;
					$display_submit = 1;
					$msg = "";
				}
			//if we are not allowed to do the ip check
			} else {
				//display the poll
				$display_poll = 1;
				$display_submit = 1;
				$msg = "";
			}
		}
	}

} else {
	$display_submit = 0;
	$msg = MText::_("MOD_MIWOPOLLS_VOTING_HAS_NOT_STARTED");
	$publish_up = MFactory::getDate($poll->publish_up);
	$details = MText::_("MOD_MIWOPOLLS_IT_WILL_START_ON").": ".$publish_up->format($params->get('msg_date_format'));
}

//if deadline has passed change the msg
if($now > $poll->publish_down) { 
	$display_poll = 0;
	$msg = MText::_("MOD_MIWOPOLLS_VOTING_HAS_ENDED");
	$publish_down = MFactory::getDate($poll->publish_down);
	$details = MText::_("MOD_MIWOPOLLS_ON").": ".$publish_down->format($params->get('msg_date_format'));
}

$disabled = ($display_submit)? '':'disabled="disabled"';
//if show messages is set to no, reset all messages
//if(!$params->get('show_msg')) {$msg ='<br />';}

if ($poll && $poll->id) {
	$layout = MModuleHelper::getLayoutPath('mod_miwopolls');
	$tabcnt = 0;
	$options = modMiwopollsHelper::getPollOptions($poll_id);
	$itemid = modMiwopollsHelper::getItemid($poll_id);
	require($layout);
}