<?php
/**
 * @package		MiwoVideos
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');


class MiwopollsAcl {

    public function __construct() {
		$this->config = MiwopollsHelper::getConfig();

        $this->user = MFactory::getUser();
        $this->actions = $this->getActions();
	}

    public function canAdmin() {
        return $this->actions->get('core.admin');
    }

    public function canManage() {
        return $this->actions->get('core.manage');
    }

    public function canCreate() {
        return $this->actions->get('core.create');
    }

    public function canEdit() {
        return $this->actions->get('core.edit');
    }

    public function canEditOwn($user_id = null) {
        $ret = false;

        if ($this->canEdit() or ($this->actions->get('core.edit.own') and $user_id == $this->user->get('id'))) {
            $ret = true;
        }

        return $ret;
    }

    public function canEditState() {
        return $this->actions->get('core.edit.state');
    }

    public function canDelete() {
        return $this->actions->get('core.delete');
    }

    public function canAutoPublish() {
        return $this->actions->get('miwopolls.autopublish');
    }

    public function canAccess($access) {
        $user = MFactory::getUser();

        if (!in_array($access, $user->getAuthorisedViewLevels())) {
            return false;
        }

        return true;
    }

    public function getActions($category_id = 0, $video_id = 0) {
        $acts = new MObject;
        $user = MFactory::getUser();

        $assetName = 'com_miwopolls';
        /*if (empty($video_id) and empty($category_id)) {
            $assetName = 'com_miwopolls';
        }
        elseif (empty($video_id)) {
            $assetName = 'com_miwopolls.category.'.(int) $category_id;
        }
        else {
            $assetName = 'com_miwopolls.video.'.(int) $video_id;
        }*/

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'miwopolls.autopublish'
        );

        foreach ($actions as $action) {
            $acts->set($action, $user->authorise($action, $assetName));
        }

        return $acts;
    }
}