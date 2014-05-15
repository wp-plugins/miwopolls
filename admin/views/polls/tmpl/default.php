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

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
	
	    <table width="100%">
        <tr>
            <td class="miwi_search">
                <?php echo MText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area search-query" onchange="document.adminForm.submit();" />
                <button onclick="this.form.submit();" class="button"><?php echo MText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_( 'Reset' ); ?></button>
            </td>
            <td class="miwi_filter">
                 <?php echo $this->lists['bulk_actions']; ?>
                    <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                    &nbsp;&nbsp;&nbsp;
				<?php echo $this->lists['state']; ?>
            </td>
        </tr>
    </table>			
			












	
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th width="5">
					<?php echo MText::_('#'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
				</th>
				<th  class="title">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_TITLE'), 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_PUBLISHED'), 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_START_DATE'), 'm.publish_up', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_END_DATE'), 'publish_down', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_VOTES'), 'votes', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_OPTIONS'), 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%" align="center">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_LAG'), 'm.lag', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo MHtml::_('grid.sort', MText::_('ID'), 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		$n=count($this->items);
		
		for ($i=0; $i < $n; $i++) {
			$row =$this->items[$i];

			$link 		= MRoute::_('index.php?option=com_miwopolls&controller=poll&task=edit&cid[]='.$row->id);

			$checked 	= MHtml::_('grid.checkedout', $row, $i);
			
			if( $row->published == 1){
			$published="<a href=\"#\" onclick=\"return listItemTask('cb$i','unpublish')\" title=\"Unpublish Item\"><img src=\"".MURL_MIWOPOLLS."/admin/assets/images/publish_y.png\" border=\"0\" alt=\"Published\"></a>";
			}else{
			$published="<a href=\"#\" onclick=\"return listItemTask('cb$i','publish')\" title=\"Publish Item\"><img src=\"".MURL_MIWOPOLLS."/admin/assets/images/publish_x.png\" border=\"0\" alt=\"Unpublished\"></a>";
			}
			
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
				<?php if (MTable::isCheckedOut($this->user->get('id'), $row->checked_out)) {
					echo $row->title;
				} else {
					?>
					<span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOPOLLS_EDIT_POLL');?>::<?php echo $row->title; ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $row->title; ?></a></span>
					<?php
				}
				?>
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>			
				<td align="center">
					<?php echo $row->publish_up;?>
				</td>			
				<td align="center">
					<?php echo $row->publish_down;?>
				</td>
				<td align="center">
					<a href="<?php echo MRoute::_('index.php?option=com_miwopolls&controller=votes&task=view&total='.$row->votes); ?>&id=<?php echo $row->id; ?>"><?php echo $row->votes; ?></a>
				</td>
				<td align="center">
					<?php echo $row->options; ?>
				</td>
				<td align="center">
					<?php echo $row->lag/60; ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_miwopolls" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo MHtml::_('form.token'); ?>
</form>