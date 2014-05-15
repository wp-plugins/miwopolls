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

defined('MIWI') or die('Restricted access');
?>

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
			<?php echo MText::_('COM_MIWOPOLLS_VIEW_RESULTS_FOR').':'; ?>
			<?php echo $this->lists['polls']; ?>
            </td>
        </tr>
    </table>		
			












	</table>
	
	<table class="wp-list-table widefat" align="center" width="90%" cellspacing="2" cellpadding="2" border="0" >
		<thead>
			<tr>
				<th width="1%"><?php echo MText::_('#'); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->votes); ?>);" /></th>
				<th width="10%"><?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_DATE'), 'v.date', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
				<th><?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_USER'), 'u.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th><?php echo MHtml::_('grid.sort', MText::_('IP'), 'ip', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th width="80%"><?php echo MHtml::_('grid.sort', MText::_('COM_MIWOPOLLS_OPTION'), 'o.text', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
			</tr>
		</thead>
		<?php
		$i = 0;
		foreach ($this->votes as $vote) { 
			$checkBox = MHtml::_('grid.id', $i++, $vote->id);
		?>
			<tr class="row<?php echo $i%2; ?>">
				<td valign="top" height="30"><?php echo $i; ?></td>
				<td valign="top"><?php echo $checkBox; ?></td>
				<td valign="top"><?php echo $vote->date; ?></td>
				<td valign="top"><?php echo $vote->name; ?></td>
				<td valign="top"><?php echo $vote->ip; ?></td>
				<td valign="top"><?php echo $vote->text; ?></td>
			</tr>

		<?php } ?>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>	
	</table>

	<input type="hidden" name="option" value="com_miwopolls" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="poll_id" value="<?php echo $this->poll_id; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo MHtml::_('form.token'); ?>
</form>