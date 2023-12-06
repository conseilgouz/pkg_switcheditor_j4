<?php
/**
 * @package    Switch Editor
 * @copyright  Copyright (C) 2023 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Version;

class pkg_SwitchEditorInstallerScript
{
	protected $db;
	private $min_joomla_version      = '4.0.0';
	private $min_php_version         = '8.0';
	private $installerName = 'switcheditorinstaller';
	
	public function __construct()
	{
		$this->db = Factory::getDbo();
	}
    function preflight($type, $parent)
    {
		// To prevent installer from running twice if installing multiple extensions
		if ( ! file_exists($this->dir . '/' . $this->installerName . '.xml'))
		{
			return true;
		}

		if ( ! $this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();

			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();

			return false;
		}
		$obsloteFiles = ["SwitchEditorHelper.php"];
		foreach ($obsloteFiles as $file) {
			$f = JPATH_ADMINISTRATOR . '/modules/mod_switcheditor/src/Helper/' . $file;
			if (@is_file($f)) {
				File::delete($f);
			}
			$f = JPATH_ROOT . '/modules/mod_switcheditor/src/Helper/' . $file;
			if (@is_file($f)) {
				File::delete($f);
			}
		}


	}

	public function postflight($type, $parent)
	{
		if ('uninstall' == $type)
		{
			return;
		}
		$j = new Version();
		$version=substr($j->getShortVersion(), 0,1); 
		// remove obsolete files
		$obsloteFiles = ["helper.php","mod_switcheditor.php"];
		foreach ($obsloteFiles as $file) {
			$f = JPATH_ADMINISTRATOR . '/modules/mod_switcheditor/' . $file;
			if (@is_file($f)) {
				File::delete($f);
			}
			$f = JPATH_ROOT . '/modules/mod_switcheditor/' . $file;
			if (@is_file($f)) {
				File::delete($f);
			}
		}
		// plugin not used anymore
		$query = $this->db->getQuery(true)
			->delete('#__extensions')
			->where($this->db->quoteName('element') . ' like "switcheditor" AND '
					.$this->db->quoteName('type'). 'like "plugin"');
		$this->db->setQuery($query);
		$this->db->execute();
		$f = JPATH_ROOT . '/plugins/system/switcheditor';
		if (@is_dir($f)) {
			Folder::delete($f);
		}
		// update admin module if not enabled : access = registered, position = status, enable it
		$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from('#__modules')
				->where($this->db->quoteName('module') . '="mod_switcheditor"')
				->where($this->db->quoteName('client_id') . '=1')
				->where($this->db->quoteName('published') . '=0'); // not yet published
		$this->db->setQuery($query);
		$id = $this->db->loadResult();
		if ($id) {
			$id = (int) $id;
			// update the module position & publication
			$query = $this->db->getQuery(true)
					->update('#__modules')
					->set($this->db->quoteName('published') . '=1')
					->set($this->db->quoteName('position') . '= "status"')
					->set($this->db->quoteName('access') . '=2') // registred mini
					->where($this->db->quoteName('id') . '=' . $id);
			$this->db->setQuery($query);
			$this->db->execute();
			// remove any previous module menu entries
			$query = $this->db->getQuery(true)->delete('#__modules_menu')->where($this->db->quoteName('moduleid') . '=' . $id);
			$this->db->setQuery($query);
	        $this->db->execute();
			// insert a new module menu entry
			$query = $this->db->getQuery(true)->insert('#__modules_menu')->values($id . ', 0');
			$this->db->setQuery($query);
			$this->db->execute(); 
		}
		// update site module if not enabled : access = registered, enable it
		$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from('#__modules')
				->where($this->db->quoteName('module') . '="mod_switcheditor"')
				->where($this->db->quoteName('client_id') . '=0')
				->where($this->db->quoteName('published') . '=0'); // not yet published
		$this->db->setQuery($query);
		$id = $this->db->loadResult();
		if ($id) {
			$id = (int) $id;
			// update the module position & publication
			$query = $this->db->getQuery(true)
					->update('#__modules')
					->set($this->db->quoteName('published') . '=1')
					->set($this->db->quoteName('access') . '=2') // registred mini
					->where($this->db->quoteName('id') . '=' . $id);
			$this->db->setQuery($query);
			$this->db->execute();
			// enable module on all menus
			$menu = new stdClass();
			$menu->menuid = 0;
			$menu->moduleid=$id;
			// Insert the object into the modules_menu table.
			$result = $this->db->insertObject('#__modules_menu', $menu);
		}
		// SwitchEditor is now on Github
		$query = $this->db->getQuery(true)
			->delete('#__update_sites')
			->where($this->db->quoteName('location') . ' like "%conseilgouz.com/updates/pkg_switcheditor%"');
		$this->db->setQuery($query);
		$this->db->execute();
	}
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		if (version_compare(JVERSION, $this->min_joomla_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf(
					'NOT_COMPATIBLE_UPDATE',
					'<strong>' . JVERSION . '</strong>',
					'<strong>' . $this->min_joomla_version . '</strong>'
				),
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, 'l'))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf(
					'NOT_COMPATIBLE_PHP',
					'<strong>' . PHP_VERSION . '</strong>',
					'<strong>' . $this->min_php_version . '</strong>'
				),
				'error'
			);

			return false;
		}

		return true;
	}
	private function uninstallInstaller()
	{
		if ( ! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
			return;
		}
		$this->delete([
			JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
			JPATH_PLUGINS . '/system/' . $this->installerName,
		]);
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		Factory::getCache()->clean('_system');
	}
	
}
