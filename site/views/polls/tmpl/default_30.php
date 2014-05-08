<?php 
/**
* @package		MiwoPolls
* @copyright	2009-2011 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
<form action="<?php echo MRoute::_(MFactory::getURI()->toString()); ?>" method="post" name="adminForm">
	<div id="filter-bar" class="button-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo MText::_('Search in title');?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo MText::_(' '); ?>" value="<?php echo $this->lists['search'];?>" title="<?php echo MText::_('Search in title'); ?>" />
		</div>
		<div class="button-group pull-left">
			<button type="submit" class="button hasTooltip" title="<?php echo MText::_('MSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="button hasTooltip" title="<?php echo MText::_('MSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="button-group pull-right">
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>

	<br/>
	<table class="table table-striped" width="100%" border="0" cellspacing="0" cellpadding="0">
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
					<?php echo MFactory::getDate($row->publish_up)->format($this->params->get('date_format')); ?>
				</td>
				<?php } ?>	
				<?php if($this->params->get('show_end_date')) { ?>
				<td>
					<?php echo MFactory::getDate($row->publish_down)->format($this->params->get('date_format')); ?>
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
	<div>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div class="pagecounter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>

	<input type="hidden" name="option" value="com_miwopolls" />
	<input type="hidden" name="view" value="polls" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo MHtml::_('form.token'); ?>
</form>
<br />