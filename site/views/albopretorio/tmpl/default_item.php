<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014 Alessandro Pasotti http://www.itopen.it All rights reserved.
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

// member method access
$this->loadHelper('route');

?>
<tr>
    <td><a class="hasTooltip" title="<?php echo JText::_('COM_ALBOPRETORIO_DOCUMENT_DETAILS'); ?>" href="<?php echo AlbopretorioHelperRoute::getAffissioneRoute($this->item->slug, $this->item->catid) ?>"><i class="icon icon-stack"></i> <?php echo $this->item->name; ?></a></td>
    <td><?php echo $this->item->document_number; ?></td>
    <td><?php echo $this->item->official_number; ?></td>
    <td><?php echo JHTML::_('date', $this->item->document_date, JText::_('DATE_FORMAT_LC3')); ?></td>
    <td><?php echo JHTML::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC3')); ?></td>
    <td><?php echo JHTML::_('date', $this->item->publish_down, JText::_('DATE_FORMAT_LC3')); ?></td>
    <td><?php
        $category = JCategories::getInstance('Albopretorio')->get($this->item->catid);
        echo AlbopretorioHelper::getCategoryPath($category);
	?>
	</td>
</tr>
