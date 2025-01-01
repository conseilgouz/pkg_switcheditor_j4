<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor site
 * @copyright  Copyright (C) 2025 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv3
 */
namespace ConseilGouz\Module\Switcheditor\Site\Helper;
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Database\DatabaseInterface;

class SwitcheditorHelper
{

	/**
	 * static method to get the list of available editors
	 * 
	 * @return  mixed
	 */
	static public function getEditorOptions($params)
	{
		static $editors;
		if (is_null($editors))
		{
			$db = Factory::getContainer()->get(DatabaseInterface::class);
			$db->setQuery((string) $db->getQuery(true)
					->select('element, name')
					->from('#__extensions')
					->where($db->quoteName('type') . ' = ' . $db->Quote('plugin'))
					->where($db->quoteName('folder') . ' = ' . $db->Quote('editors'))
					->where($db->quoteName('enabled') . ' = 1')
			);
			$editors = $db->loadObjectList();
			// load the language files
			if (!empty($editors))
			{
				foreach ($editors as &$editor)
				{
					Factory::getApplication()->getLanguage()->load($editor->name . '.sys', JPATH_ADMINISTRATOR);
					$editor->name = Text::_($editor->name);
					// strip of any prefixed "Editor - " bits
					if ($params->get('compact',0) == 1) { // compact view : remove word editor
						if (strpos($editor->name,'-')) {
							list($tmp, $name) = explode('-', $editor->name, 2);
							if (isset($name) && !empty($name)) {
								$editor->name = trim($name);
							}
						}
					} else { // standard view
						if (false !== strpos('-', $editor->name)) {
							list($tmp, $name) = explode('-', $editor->name, 2);
							if (isset($name) && !empty($name)) {
								$editor->name = trim($name);
							}
						}
					}
				}
			}
			// add the "default"
			if (!is_array($editors))
			{
				$editors = array();
			}
			if ($params->get('compact',0) == 1) { // compact view : remove word editor
				array_unshift($editors, HTMLHelper::_('select.option', '', Text::_('MOD_SWITCHEDITOR_SELECT_EDITOR_COMPACT'), 'element', 'name'));
			} else  {
				array_unshift($editors, HTMLHelper::_('select.option', '', Text::_('MOD_SWITCHEDITOR_SELECT_EDITOR'), 'element', 'name'));
			}
		}
		return $editors;
	}

	/**
	 * static method to save the user's editor preferences
	 */
	static public function setEditor()
	{
		$user   = Factory::getApplication()->getIdentity();
		$editor = Factory::getApplication()->input->get('adEditor');
		if (!empty($editor) && !$user->guest)
		{
			$user->setParam('editor', $editor);
			return $user->save(true);
		}
		return false;
	}
// ==============================================    AJAX Request 	============================================================
	public static function getAjax() {
		// check session token
		$j = new Version();
		$version=substr($j->getShortVersion(), 0,1); 
		if (!Session::checkToken('get')) {
			if ($version >= "4") { // Joomla 4.X 
				echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
			} else {
				echo new \JResponseJson(null, Text::_('JINVALID_TOKEN'), true);
			}
		    return false;
		}
	    $input = Factory::getApplication()->input->request;
	    $user   = Factory::getApplication()->getIdentity();
	    $editor = $input->get('adEditor');
	    if (!empty($editor) && !$user->guest)
	    {
	        $user->setParam('editor', $editor);
	        return $user->save(true);
	    }
	    return false;
	    
	    return true;
	}
}
