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

defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	?>
	<div class="tab-pane" id="params-<?php echo $name;?>">
	<?php
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="alert alert-info">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label; ?></div>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
			<?php endforeach; ?>
	</div>
<?php endforeach; ?>
