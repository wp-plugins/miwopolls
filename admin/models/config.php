<?php
/**
 * @package        MiwoPolls
 * @copyright      2009-2014 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('_MEXEC') or die;

class MiwopollsModelConfig extends MiwopollsModel {

    public function __construct() {
        parent::__construct('config');
    }

    // Save configuration
    function save() {
        $config = MiwopollsHelper::getConfig();

        // General
        $config->set('only_registered', MRequest::getCmd('only_registered', 0));
        $config->set('one_vote_per_user', MRequest::getCmd('one_vote_per_user', '1'));
        $config->set('ip_check', MRequest::getCmd('ip_check', 1));
        $config->set('show_component_msg', MRequest::getCmd('show_component_msg', 1));
        $config->set('allow_voting', MRequest::getCmd('allow_voting', 1));
        $config->set('show_comments', MRequest::getCmd('show_comments', 1));
        
        // Results
        $config->set('show_what', MRequest::getCmd('show_what', 1));
        $config->set('show_hits', MRequest::getCmd('show_hits', 1));
        $config->set('show_voters', MRequest::getCmd('show_voters', 1));
        $config->set('show_times', MRequest::getCmd('show_times', 1));
        $config->set('show_dropdown', MRequest::getCmd('show_dropdown', 1));
		
        // Pie Options
        $config->set('opacity', MRequest::getCmd('opacity', 90));
        $config->set('bg_color', MRequest::getCmd('bg_color', 'ffffff'));
        $config->set('circle_color', MRequest::getCmd('circle_color', '505050'));
        $config->set('pieX', MRequest::getCmd('pieX', '100%'));
        $config->set('pieY', MRequest::getCmd('pieY', '400'));
        $config->set('start_angle', MRequest::getCmd('start_angle', 55));
        $config->set('radius', MRequest::getCmd('radius', 150));
        $config->set('gradient', MRequest::getCmd('gradient', 1));
        $config->set('no_labels', MRequest::getCmd('no_labels', 1));
        $config->set('show_zero_votes', MRequest::getCmd('show_zero_votes', 1));
        $config->set('animation_type', MRequest::getCmd('animation_type', 'bounce'));
        $config->set('bounce_dinstance', MRequest::getCmd('bounce_dinstance', 30));
        $config->set('bg_image', MRequest::getCmd('bg_image', '-1'));
        $config->set('bg_image_x', MRequest::getCmd('bg_image_x', 'left'));
        $config->set('bg_image_y', MRequest::getCmd('bg_image_y', 'top'));
        $config->set('font_size', MRequest::getCmd('font_size', 11));
        $config->set('font_color', MRequest::getCmd('font_color', '404040'));
        $config->set('title_lenght', MRequest::getCmd('title_lenght', 30));
		
		// Default Option
        $config->set('chartX', MRequest::getCmd('chartX', '100%'));
        $config->set('optionsFontSize', MRequest::getCmd('optionsFontSize', 12));
        $config->set('barHeight', MRequest::getCmd('barHeight', 15));
        $config->set('barBorder', MRequest::getCmd('barBorder', '1px solid #000000'));
        $config->set('bgBarColor', MRequest::getCmd('bgBarColor', 'f5f5f5'));
        $config->set('bgBarBorder', MRequest::getCmd('bgBarBorder', '1px solid #cccccc'));



        MiwopollsHelper::storeConfig($config);

        $this->cleanCache('_system');
    }
}