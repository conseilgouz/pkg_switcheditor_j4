<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2021 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Version;

// Joomla 4.0 custom field conflict 
if (($app->input->get('option') == 'com_fields') && ($app->input->get('view') == 'field') && ($app->input->get('layout') == 'edit')) return;
$value = Factory::getUser()->getParam('editor');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('switcheditor','media/switcheditor/css/switcheditor_j4.css');
$wa->registerAndUseScript('switcheditor','media/switcheditor/js/switcheditor.js');

?>
<div class="adEditorFormBox btn-group">
	<form name="adEditorFormSite">
		<?php echo str_replace(' id="adEditor"', '', HTMLHelper::_('select.genericlist', $options,'adEditor', ' class="adEditor chzn-done" data-chosen="done"','element', 'name',$value)); ?>
		<input type="hidden" name="task" value="switch" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
