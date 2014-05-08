<?php 
/**
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die('Restricted access'); ?>

<?php if ($this->params->get( 'show_title', 1)) { ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php } ?>
<script type="text/javascript">
	function checkData() {
		var checked = false; 
		var error=0;
		var i=0;
		<?php  foreach ($this->options as $poll_option) { 	?>
		if(document.getElementById('voteid<?php echo $poll_option->id;?>').checked) {
		document.getElementById("miwopoll").submit();
		}else {
		error+= 1;
		}
		i+= 1;
		<?php } ?>
		if(i==error){
		alert("Please vote for any one.");
		}
	}
</script>
<div class="contentpane<?php echo $this->params->get('pageclass_sfx') ?>">

<?php if ($this->allowToVote) { ?>
<div id="poll_comp_form">
	<form action="<?php echo MRoute::_('index.php'); ?>" id="miwopoll"  method="post" name="poll_form2">
	<?php 
		$k=0; $i=0; $tabcnt = 0;
	    $tabclass = array ('sectiontableentry2', 'sectiontableentry1');
	    foreach ($this->options as $poll_option) { 
			?>
			<label for="voteid<?php echo $poll_option->id;?>" class="poll<?php echo $tabclass[$tabcnt]; ?>" style="display:block; padding:2px;">
				<input type="radio" name="voteid" id="voteid<?php echo $poll_option->id;?>" value="<?php echo $poll_option->id;?>" alt="<?php echo $poll_option->id;?>" />
				<?php echo $poll_option->text; ?>
			</label>
			<?php $tabcnt = 1 - $tabcnt; 
	    }
		?>
	    
	    <div style="padding:2px; text-align:left;">    
	    	<input type="button" name="task_button" onclick="checkData();" class="button-primary btn" value="<?php echo MText::_('COM_MIWOPOLLS_VOTE'); ?>" />
	    </div>    
	    
	    <input type="hidden" name="option" value="com_miwopolls" />
	    <input type="hidden" name="view" value="polls" />
	    <input type="hidden" name="task" value="vote" />
	    <input type="hidden" name="id" value="<?php echo $this->poll->id;?>" />
	    <?php echo MHtml::_('form.token'); ?>
	</form>
</div>
<br />

<?php 
	//if users are not allowed to vote for some reason (voted or not registered) show warning    
	} else { 
	    if ($this->params->get('show_component_msg')) { 
	    	echo "<p>".MText::_($this->msg)."</p>"; 
	    }
	} ?>

<?php if($this->params->get('show_dropdown')) { ?>
<form action="<?php echo MRoute::_('index.php');?>" method="post" name="poll" id="poll">
	<div class="contentpane<?php echo $this->params->get('pageclass_sfx') ?>">
		<label for="id">
			<?php echo MText::_('COM_MIWOPOLLS_VIEW_RESULTS'); ?>
			<?php echo $this->lists['polls']; ?>
		</label>
	</div>
</form>
<?php } ?>
<br />

<b><?php echo MText::_('COM_MIWOPOLLS_STATISTICS'); ?>:</b>
<br />
<br />
<?php     
    // set the correct view
    if ($this->params->get('show_what', '0') == '1') {
        echo $this->loadTemplate('pie');
    } else {
        echo $this->loadTemplate('chart');
    }
?>
<br />

<?php if ($this->params->get('show_voters') || $this->params->get('show_times')) { ?>
<table cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<?php if ($this->params->get('show_voters')) { ?>
		<tr>
			<td class="smalldark">
				<?php echo MText::_('COM_MIWOPOLLS_NUM_OF_VOTERS'); ?>
			</td>
			<td class="smalldark">
				&nbsp;
				<?php 
					if (isset($this->options[0])) {
						echo $this->options[0]->voters;
					}
				?>
		   </td>
		</tr>
		<?php } ?>
		
		<?php if ($this->params->get('show_times')) { ?>
		<tr>
			<td class="smalldark">
				<?php echo MText::_('COM_MIWOPOLLS_START'); ?>
			</td>
			<td class="smalldark">
				&nbsp;
				<?php echo $this->poll->publish_up; ?>
			</td>
		</tr>
		<tr>
			<td class="smalldark">
				<?php echo MText::_('COM_MIWOPOLLS_END'); ?>
			</td>
			<td class="smalldark">
				&nbsp;
				<?php echo $this->poll->publish_down; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php }


$comments = $this->params->get('show_comments', '0');

if ($comments != 0) {
	$jcomments = MPATH_SITE . '/components/com_jcomments/jcomments.php';
	$jomcomment = MPATH_SITE . '/plugins/content/jom_comment_bot.php';
	$jxcomments = MPATH_SITE . '/components/com_comments/comments.php';
	$jacomments1 = MPATH_SITE . '/components/com_jacomment/jacomment.php';
	$jacomments2 = MPATH_SITE . '/plugins/system/jacomment.php';
	
	if ($comments == 1 && file_exists($jcomments)) {
		require_once($jcomments);
		echo JComments::showComments($this->poll->id, 'com_miwopolls', $this->poll->title);
	}
	
	if ($comments == 2 && file_exists($jomcomment)) {
		require_once($jomcomment);
		echo jomcomment($this->poll->id, "com_miwopolls");
	}
	
	if ($comments == 3 && file_exists($jxcomments)) {
		$url = 'index.php?option=com_miwopolls&view=poll&id='.(int) $this->poll->id;
		$route = $url.':'.$this->poll->alias.'&Itemid='.MRequest::getInt('Itemid');
		
		MHtml::addIncludePath(MPATH_SITE.'/components/com_comments/helpers/html');
		MHtml::_('comments.comments', 'miwopolls', $this->poll->id, $url, $route, $this->poll->title);
	}
	
	if ($comments == 4 && file_exists($jacomments1) && file_exists($jacomments2) && !MRequest::getInt('print')) {
		$_jacCode = "#{jacomment(.*?) contentid=(.*?) option=(.*?) contenttitle=(.*?)}#i";
		$_jacCodeDisableid = "#{jacomment(\s)off.*}#i";
		$_jacCodeDisable = "#{jacomment(\s)off}#i";
		if (!preg_match($_jacCode, $this->poll->title) && !preg_match($_jacCodeDisable, $this->poll->title) && !preg_match($_jacCodeDisableid, $this->poll->title)) {
			echo '{jacomment contentid='.$this->poll->id.' option=com_miwopolls contenttitle='.$this->poll->title.'}';
		}
	}
}
?>

<br style="clear:both" />
</div>
