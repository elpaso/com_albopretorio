<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014 Alessandro Pasotti http://www.itopen.it All rights reserved.
* @license      GNU/AGPL

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

defined ( '_JEXEC' ) or die ();

JHtml::_ ( 'bootstrap.tooltip' );
JHtml::_ ( 'behavior.multiselect' );
JHtml::_ ( 'formbehavior.chosen', 'select' );

$app = JFactory::getApplication ();
$user = JFactory::getUser ();
$userId = $user->get ( 'id' );
$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$archived = $this->state->get ( 'filter.published' ) == 2 ? true : false;
$trashed = $this->state->get ( 'filter.published' ) == - 2 ? true : false;
$canOrder = $user->authorise ( 'core.edit.state', 'com_albopretorio.category' );
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_albopretorio&task=albopretorio.saveOrderAjax&tmpl=component';
	JHtml::_ ( 'sortablelist.sortable', 'articleList', 'adminForm', strtolower ( $listDirn ), $saveOrderingUrl );
}
$sortFields = $this->getSortFields ();
$assoc = JLanguageAssociations::isEnabled ();
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
<form action="" method="post" name="adminForm" id="adminForm">
	<fieldset class="filters btn-toolbar clearfix">
		<div class="btn-toolbar" role="toolbar">
		<div class="btn-group">
			<label class="filter-search-lbl element-invisible"
				for="filter-search">
					<?php echo JText::_('COM_ALBOPRETORIO_NAME_FILTER_LABEL').'&#160;'; ?>
				</label> <input type="text" name="filter_search" id="filter-search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="inputbox" onchange="document.adminForm.submit();"
				title="<?php echo JText::_('COM_ALBOPRETORIO_FILTER_SEARCH_DESC'); ?>"
				placeholder="<?php echo JText::_('COM_ALBOPRETORIO_NAME_FILTER_LABEL'); ?>" />
				<button class="btn btn-primary" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<?php if($this->state->get('filter.search')): ?>
				<a class="btn btn-primary" href="javascript:jQuery('#filter-search').val('');jQuery('#adminForm').submit();"><?php echo JText::_('COM_ALBOPRETORIO_FILTER_SEARCH_CLEAR'); ?></a>
				<?php endif; ?>
		</div>
		<?php
		$chromePath = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/pagination.php';
		if (file_exists($chromePath))
		{
			?>
		<div class="btn-group pull-right">
			<label for="limit" class="element-invisible">
				<?php echo JText::_('COM_ALBOPRETORIO_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

			<?php
		} ?>
		</div>
	</fieldset>

	<table class="table table-striped table-bordered table-hover albopretorio"
		id="articleList">
		<thead>
			<tr>
				<th class="title">
						<?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
				<th width="10%" class="hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_DOCUMENT_NUMBER', 'a.document_number', $listDirn, $listOrder); ?>
                    </th>
				<th width="10%" class=" hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_OFFICIAL_NUMBER', 'a.official_number', $listDirn, $listOrder); ?>
                    </th>
				<th width="10%" class="hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_DOCUMENT_DATE', 'a.document_date', $listDirn, $listOrder); ?>
                    </th>
				<th width="10%" class="hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_PUBLISH_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
                    </th>
				<th width="10%" class="hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_SUSPEND_DATE', 'a.publish_down', $listDirn, $listOrder); ?>
                    </th>
				<th class="hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_ALBOPRETORIO_HEADING_CATEGORY', 'a.category_title', $listDirn, $listOrder); ?>
                    </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11"><div class="pagination bottom">
    			<?php echo $this->pagination->getResultsCounter(); ?>
				<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
            <?php
			foreach ( $this->items as $item ) {
				$this->item = $item;
				echo $this->loadTemplate ( 'item' );
			}
			?>
            </tbody>
	</table>
	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> <input type="hidden"
		name="filter_order" value="<?php echo $listOrder; ?>" /> <input
		type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
