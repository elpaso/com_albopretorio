<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2007-2015 Alessandro Pasotti http://www.itopen.it All rights reserved.
* @license      GNU/GPL

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$canOrder	= $user->authorise('core.edit.state', 'com_albopretorio.category');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_albopretorio&task=albopretorio.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
$assoc		= JLanguageAssociations::isEnabled();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_albopretorio&view=albopretorio'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_ALBOPRETORIO_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_ALBOPRETORIO_SEARCH_IN_TITLE'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
					</th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_DOCUMENT_NUMBER', 'a.document_number', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_OFFICIAL_NUMBER', 'a.official_number', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_DOCUMENT_DATE', 'a.document_date', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_PUBLISH_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_SUSPEND_DATE', 'a.publish_down', $listDirn, $listOrder); ?>
                    </th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<?php if ($assoc) : ?>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.ordering');
				$canCreate  = $user->authorise('core.create',     'com_albopretorio.category.' . $item->catid);
				// ABP: TODO: needs some more love from an helper... canEdit should take into account pub dates and special user permissions
				$canEdit    = $user->authorise('core.edit',       'com_albopretorio.category.' . $item->catid);
				$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
				$canChange  = $user->authorise('core.edit.state', 'com_albopretorio.category.' . $item->catid) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order" />
						<?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'albopretorio.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
							<?php
							// Create dropdown items
							$action = $archived ? 'unarchive' : 'archive';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'albopretorio');

							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'albopretorio');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
							?>
						</div>
					</td>
					<td class="has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'albopretorio.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_albopretorio&task=affissione.edit&view=affissioni&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
									<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
							<span class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</span>
							<div class="small">
								<?php echo $this->escape($item->category_title); ?>
							</div>
						</div>
					</td>
                    <td class="nowrap small hidden-phone">
                        <?php echo $this->escape($item->document_number); ?>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php echo $this->escape($item->official_number); ?>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php echo JHtml::_('date', $item->document_date, JText::_('DATE_FORMAT_LC3')); ?>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC3')); ?>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php echo JHtml::_('date', $item->publish_down, JText::_('DATE_FORMAT_LC3')); ?>
                    </td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<?php if ($assoc) : ?>
					<td class="hidden-phone">
						<?php if ($item->association) : ?>
							<?php echo JHtml::_('albopretorio.association', $item->id); ?>
						<?php endif; ?>
					</td>
					<?php endif;?>
					<td class="small hidden-phone">
						<?php if ($item->language == '*'):?>
							<?php echo JText::alt('JALL', 'language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif;?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
