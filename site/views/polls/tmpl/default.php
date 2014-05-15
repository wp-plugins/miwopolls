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

defined('MIWI') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
	function tableOrdering(order, dir, task) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit(task);
}
</script>

<h1><?php echo $this->params->get('page_title'); ?></h1>
<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php 
				echo MText::_('Filter'); ?>:
				<input type="text" name="search" value="<?php echo $this->lists['search'];?>" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo MText::_('Go'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo MText::_( 'Reset' ); ?></button>
				
			</td>
			<td nowrap="nowrap">
				<?php
			echo MText::_('COM_MIWOPOLLS_DISPLAY_NUM') .'&nbsp;';
			echo $this->pagination->getLimitBox();
		?>
			</td>
		</tr>
	</table>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<td width="5" height="20" style="" class="sectiontableheader">
					<?php echo MText::_('Num'); ?>
				</td>
				<td class="sectiontableheader">
					<?php echo MHtml::_('grid.sort', MText::_('Title'), 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td>
					<?php if($this->params->get('show_start_date')) { ?>
				<td width="10%" class="sectiontableheader" style="text-align:center;" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_START'), 'm.publish_up', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td><?php } ?>						
				<?php if($this->params->get('show_end_date')) { ?>
				<td width="10%" class="sectiontableheader" style="text-align:center;" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_END'), 'm.publish_down', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td><?php } ?>					
				<?php if($this->params->get('show_status')) { ?>
				<td width="5%" class="sectiontableheader" style="text-align:center;" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_STATUS'), 'status', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td><?php } ?>				
				<?php if($this->params->get('show_num_voters')) { ?>
				<td width="5%" class="sectiontableheader" style="text-align:center;" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_VOTES'), 'voters', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td><?php } ?>
					<?php if($this->params->get('show_num_options')) { ?>
				<td width="5%" class="sectiontableheader" style="text-align:center;" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_OPTIONS'), 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td><?php } ?>
			</tr>
		</thead>

		<tbody>
		<?php
		$k = 0;
		$n = count($this->items);
		for ($i=0; $i < $n; $i++) {
			$row =$this->items[$i];
			
			//find the Itemid that correspondents to the link if any.
			$component 	= MComponentHelper::getComponent('com_miwopolls');
			$menus		= MApplication::getMenu('site', array());
			
			if (MiwopollsHelper::is15()) {
				$menu_items	= $menus->getItems('componentid', $component->id);
			}
			else {
				$menu_items	= $menus->getItems('component_id', $component->id);
			}
			
			$itemid		= null;
			
			if (isset($menu_items)) {
				foreach ($menu_items as $item) {
					if ((@$item->query['view'] == 'poll') && (@$item->query['id'] == $row->id)) {
						$itemid = '&Itemid='.$item->id;
						break;
					}			
				}
			}
			
			$link = MRoute::_('index.php?option=com_miwopolls&view=poll&id='.$row->slug.$itemid); 
		
		?>
			<tr class="sectiontableentry<?php echo $k + 1; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<span class="hasTip" title="<?php echo MText::_('COM_MIWOPOLLS_VIEW');?>">
						<a href="<?php echo $link;?>"><?php echo $row->title; ?></a>
					</span>
				</td>
				<?php if ($this->params->get('show_start_date')) { ?>
				<td align="center">
					<?php echo MFactory::getDate($row->publish_up)->toFormat($this->params->get('date_format')); ?>
				</td>
				<?php } ?>	
				<?php if($this->params->get('show_end_date')) { ?>
				<td>
					<?php echo MFactory::getDate($row->publish_down)->toFormat($this->params->get('date_format')); ?>
				</td>
				<?php } ?>				
				<?php if ($this->params->get('show_status')) { ?>
				<td align="center" style="text-transform:capitalize;">
					<?php 
					if ($this->params->get('show_status_as')) { ?>
						<img src="<?php echo MUri::base(); ?>media/miwopolls/images/poll-<?php echo $row->status; ?>.gif" />
					<?php 
					} else {
						echo MText::_('COM_MIWOPOLLS_'.$row->status);
					} ?>
				</td>			
				<?php 
				}
				if ($this->params->get('show_num_voters')) { ?>
				<td align="center">
					<?php echo $row->voters; ?>
				</td>
				<?php } ?>
				<?php if($this->params->get('show_num_options')) { ?>
				<td align="center">
					<?php echo $row->numoptions; ?>
				</td>
				<?php } ?>
			</tr>
			<?php $k = 1 - $k;
		}	?>
		</tbody>
	</table>
	
	 <?php if ($this->pagination->total > $this->pagination->limit) { ?>
        <div align="center" class="pagination">
            <?php echo $this->pagination->getListFooter(); ?>
        </div>
    <?php } ?>
			






	<input type="hidden" name="option" value="com_miwopolls" />
	<input type="hidden" name="view" value="polls" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo MHtml::_('form.token'); ?>
</form>
<br />