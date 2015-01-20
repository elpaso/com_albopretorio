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

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'affissione.cancel' || document.formvalidator.isValid(document.id('albopretorio-form'))) {
			Joomla.submitform(task, document.getElementById('albopretorio-form'));
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_albopretorio&layout=edit&view=affissione&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="albopretorio-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_ALBOPRETORIO_NEW_AFFISSIONE', true) : JText::_('COM_ALBOPRETORIO_EDIT_AFFISSIONE', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="form-vertical">
                    <?php echo $this->form->getControlGroup('itopen'); ?>
					<?php echo $this->form->getControlGroup('description'); ?>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachments', JText::_('COM_ALBOPRETORIO_FIELDSET_ATTACHMENTS', true) ); ?>
        <?php
        //$this->fieldset = 'attachments';
        //echo JLayoutHelper::render('joomla.edit.fieldset', $this);
        for($i=0; $i<12; $i++){ ?>
        <div class="row-fluid">
			<div class="span12">
				<div class="form-inline">
					<?php $fname = 'filename'.$i; ?>
                    <?php
                    if ($this->item->$fname) {
                        echo '<div class="alert alert-info" role="alert"><h3>' . $this->item->$fname; ?></h3>
                        <div class="checkbox">
                            <label><?php echo  JText::_('COM_ALBOPRETORIO_REMOVE'); ?>
                                <input name="<?php echo 'jform[' . $fname . '_remove]'; ?>" type="checkbox">
                            </label>
                        </div>
                    </div>
                    <?php }
                    echo $this->form->getControlGroup($fname);
                    ?>
				</div>
                <hr />
			</div>
		</div>
        <?php } ?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'official', JText::_('COM_ALBOPRETORIO_FIELDSET_OFFICIAL', true) ); ?>
        <?php

        // Official field set

        // Autoincrement?
        jimport('joomla.application.component.helper');

        $fields = array('document_date', 'document_number');

        $html = array();
        foreach ($fields as $f)
        {
            $field = $this->form->getField($f);
            $html[] = $field->renderField();
        }

        $autoincrement_official_number = JComponentHelper::getParams('com_albopretorio')->get('autoincrement_official_number');
        if ( $autoincrement_official_number == '1'){
            $this->form->setFieldAttribute('official_number', 'readonly', 'true');
        }
        $field = $this->form->getField('official_number');
        $html[] = $field->renderField();

        echo implode('', $html);


        ?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publication', JText::_('COM_ALBOPRETORIO_FIELDSET_PUBLICATION', true) ); ?>
        <?php
        $this->fieldset = 'publication';
        echo JLayoutHelper::render('joomla.edit.fieldset', $this);
        ?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php
        $this->fields =  array(
            array('created', 'created_time'),
            array('created_by', 'created_user_id'),
            'created_by_alias',
            array('modified', 'modified_time'),
            array('modified_by', 'modified_user_id'),
            'version',
            'hits',
            'id'
        );

        echo JHtml::_('bootstrap.addTab', 'myTab', 'options', JText::_('COM_ALBOPRETORIO_FIELDSET_OPTIONS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php $this->set('ignore_fieldsets', array('jbasic')); ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>


		<?php if ($assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
			<?php echo $this->loadTemplate('associations'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
