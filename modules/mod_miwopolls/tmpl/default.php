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
 
$document = MFactory::getDocument();	 
$document->addStyleDeclaration("div#poll_loading_".$poll->id." {
	background: url(media/system/images/mootree_loader.gif) 0% 50% no-repeat;
	width:100%;
	height:20px; 
	padding: 4px 0 0 20px; 
}
");
?>

<div class="poll<?php echo $params->get('moduleclass_sfx'); ?>" style="border:none; padding:1px;">

<?php if ($params->get('show_poll_title')) : ?>
    <h4><?php echo $poll->title; ?></h4>
<?php endif; ?>

<div id="polldiv_<?php echo $poll->id;?>">

<?php if ($display_poll) { ?>
<form action="<?php echo MRoute::_('index.php');?>" method="post" name="poll_vote_<?php echo $poll->id;?>" id="poll_vote_<?php echo $poll->id;?>">
<?php for ($i = 0, $n = count($options); $i < $n; $i ++) { ?>
	<label for="mod_voteid<?php echo $options[$i]->id;?>" class="<?php echo $tabclass_arr[$tabcnt].$params->get('moduleclass_sfx'); ?>" style="display:block; padding:2px;">
		<input type="radio" name="voteid" id="mod_voteid<?php echo $options[$i]->id;?>" value="<?php echo $options[$i]->id;?>" alt="<?php echo $options[$i]->id;?>" <?php echo $disabled; ?> />
			<?php echo $options[$i]->text; ?>
	</label>
	<?php $tabcnt = 1 - $tabcnt; } 
			
			//show messages box
			if($params->get('show_msg')) : 
				echo '<div id="mod_poll_messages_'.$poll->id.'" style="margin:5px;">'.MText::_($msg);
				if($params->get('show_detailed_msg')) echo " ".$details;
				echo '</div>';
			endif;
	?>
	<div style="padding:2px;" id="poll_buttons_<?php echo $poll->id;?>" >	
	<input type="submit" id="submit_vote_<?php echo $poll->id; ?>" name="task_button" class="<?php echo (MiwopollsHelper::is30() ? 'btn button-primary' : 'button'); ?>" value="<?php echo MText::_('MOD_MIWOPOLLS_VOTE'); ?>" <?php echo $disabled; ?> />
	</div>	
	<div id="poll_loading_<?php echo $poll->id;?>" style="display:none;"><?php echo MText::_('MOD_MIWOPOLLS_PROCESSING'); ?>
	</div>		

	<input type="hidden" name="option" value="com_miwopolls" />
	<input type="hidden" name="id" value="<?php echo $poll->id;?>" />
	<?php if ($params->get('ajax')) { ?>
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="view" value="poll" />
	<?php } else { ?>
	<input type="hidden" name="task" value="vote" />
	<?php }; 
	echo "<div>".MHTML::_('form.token')."</div>";  ?>
</form>

<?php if($params->get('ajax')) {
// add mootools
if (!MiwopollsHelper::is30()) {
	MHTML::_('behavior.mootools');
}

$updateValue = '';
$poll_bars_color = $params->get('poll_bars_color');

for ($i = 0; $i < count($results); $i++) {
	if ($params->get('only_one_color')) {
		$background_color = $poll_bars_color;
	}
	else {
		$background_color = "' + options.item($i).attributes[2].nodeValue + '";	
	}

	$updateValue .= "<div style=\"width:100%\"><div style=\"padding: 3px;\">' + text.item($i).firstChild.nodeValue + ' - ' + options.item($i).attributes[1].nodeValue + '%</div><div class=\"poll_module_bar_holder\" id=\"poll_module_bar_holder".$i."\" style=\"width: 98%; height: 10px; padding:1px; border:1px solid #".$params->get('poll_bars_border_color').";\"><div id=\"poll_module_bar'+options.item($i).attributes[0].nodeValue+'\" style=\"background:#$background_color; width:' + options.item($i).attributes[1].nodeValue + '%; height:10px;\"></div></div></div>";
}

if ($params->get('show_total')) {
	$updateValue .= "<br /><b>".MText::_('MOD_MIWOPOLLS_TOTAL_VOTES')."</b>: ' + voters.item(0).firstChild.nodeValue + '";
}

$js = "
/* <![CDATA[ */
window.addEvent('load', function()
    {
		$('poll_vote_".$poll->id."').addEvent('submit', function(e) {
				// Prevent the submit event
			    e.stop();

				var options = $('poll_vote_".$poll->id."').getElements('input[name=voteid]');

				var nothing_selected = 1;
				
				options.each(function(item, index){
					if(item.checked==1) {
						nothing_selected = 0;
					}
				});
				
				if (nothing_selected) {
					alert('".MText::_('Please select an option')."');
					return false;
				}
				else {
					$('submit_vote_".$poll->id."').disabled = 1;
					$('poll_loading_".$poll->id."').setStyle('display', '');
					
					// Update the page
					this.set('send', {
					onComplete: function(response, responseXML)
					{						
						// get the XML nodes
						var root    = responseXML.documentElement;
						var options = root.getElementsByTagName('option');
						var text    = root.getElementsByTagName('text');
						var voters  = root.getElementsByTagName('voters');
						
						// update the page element
						";
						if (MiwopollsHelper::is30()) {
							$js .= "jQuery('polldiv_".$poll->id."').hide();";
						} else {
							$js .= "var slide = new Fx.Slide('polldiv_".$poll->id."').hide();";
						}
						
						$js .= "$('polldiv_".$poll->id."').innerHTML = '".$updateValue."';
						slide.slideIn();
					}
					}).send();
				}
        });  
    });/* ]]> */";

$document->addScriptDeclaration($js);

}
//If user has voted 
	} else { 
	
		foreach ($results as $row) :
			$percent = ($row->votes)? round((100*$row->hits)/$row->votes, 1):0;
			$width = ($percent)? $percent:2; 
			if($params->get('only_one_color')) 
				$background_color = $params->get('poll_bars_color');
			else 
				$background_color = $row->color; ?>
			
			<div>
				<div style="padding:3px;"><?php echo $row->text." - ".$percent; ?>%</div>
				<div style="height:10px; padding:1px; border:1px solid #<?php echo $params->get('poll_bars_border_color'); ?>;">
					<div style="width: <?php echo $width; ?>%; height:10px;background:#<?php echo $background_color; ?>;"></div>
				</div>
			</div>
<?php  endforeach;
			if($params->get('show_total')) 
				echo "<br /><b>".MText::_('MOD_MIWOPOLLS_TOTAL_VOTES')."</b>: ".$row->votes;
			
			if($params->get('show_msg')) : 
				echo '<div id="mod_poll_messages_'.$poll->id.'" style="margin:5px;">'.MText::_($msg);
				if($params->get('show_detailed_msg')) { 
					echo " ".$details;
				}
				echo '</div>';
			endif;
 } ?>

<!-- End of #polldiv -->
</div>
<?php if (($params->get('show_view_details')) || ($params->get('rel_article_window'))) { ?>
<div id="poll_links" style="padding-top:5px; ">

	<?php if ($params->get('show_view_details')) : ?>
	<a class="poll_result_link" href="<?php echo MRoute::_('index.php?option=com_miwopolls&view=poll&id='.$slug.$itemid); ?>"><?php echo MText::_('MOD_MIWOPOLLS_VIEW_DETAILS'); ?></a><br />
	<?php endif; ?>
	
	<?php if ($params->get('show_rel_article')) : ?>
	<a class="poll_result_link" target="<?php echo $params->get('rel_article_window'); ?>" href="<?php echo MRoute::_($params->get('rel_article')); ?>">
		<?php echo MText::_('MOD_MIWOPOLLS_READ_RELATED_ARTICLE'); ?> >></a>
	<?php endif; ?>
<?php } ?>

</div>
</div>