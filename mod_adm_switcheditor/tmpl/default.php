<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2024 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Joomla 4.0 custom field conflict 
if (($app->input->get('option') == 'com_fields') && ($app->input->get('view') == 'field') && ($app->input->get('layout') == 'edit')) return;

$value = Factory::getUser()->getParam('editor');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('switcheditor','media/switcheditor/css/switcheditor_j4.css');
$wa->registerAndUseScript('switcheditor','media/switcheditor/js/switcheditor.js');
$document 		= Factory::getDocument();
$document->addScriptOptions('mod_switcheditor', array('auto' => $params->get('auto','0'),'automsg' => Text::_("MOD_SWITCHEDITOR_SWITCHING")));
?>
<div class="adEditorFormBox btn-group">
		<?php echo str_replace(' id="adEditor"', '', HTMLHelper::_('select.genericlist', $options,'adEditor', ' class="adEditor chzn-done" data-chosen="done"','element', 'name',$value)); ?>
</div>
