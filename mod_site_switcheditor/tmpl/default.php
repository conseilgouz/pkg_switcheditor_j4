<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor site
 * @copyright  Copyright (C) 2024 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv3
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;

// Joomla 4.0 custom field conflict 
if (($app->input->get('option') == 'com_fields') && ($app->input->get('view') == 'field') && ($app->input->get('layout') == 'edit')) return;
$value = Factory::getUser()->getParam('editor');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('switcheditor','media/switcheditor/css/switcheditor_j4.css');
if (!is_file(JPATH_ROOT . '/media/switcheditor/js/switcheditor.js')) {
	echo '<div class="alert alert-danger" role="alert">';
	echo Text::_('MOD_SWITCHEDITOR_NOJS');
	echo '</div>';
	return;
}
$wa->registerAndUseScript('switcheditor','media/switcheditor/js/switcheditor.js');

$document 		= Factory::getDocument();
$document->addScriptOptions('mod_switcheditor', array('auto' => $params->get('auto','0'),'automsg' => Text::_("MOD_SWITCHEDITOR_SWITCHING")));

?>
<noscript>
    <div class="alert alert-danger" role="alert">
        <?php echo Text::_('MOD_SWITCHEDITOR_NOSCRIPT'); ?>
    </div>
</noscript>

<div class="adEditorFormBox btn-group">
		<?php echo str_replace(' id="adEditor"', '', HTMLHelper::_('select.genericlist', $options,'adEditor', ' class="adEditor chzn-done" data-chosen="done"','element', 'name',$value)); ?>
</div>
