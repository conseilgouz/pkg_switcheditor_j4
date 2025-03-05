<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor site
 * @copyright  Copyright (C) 2023 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */

namespace ConseilGouz\Module\Switcheditor\Site\Field;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\String\StringHelper;

// Prevent direct access
defined('_JEXEC') || die;

class VersionField extends FormField
{
	/**
	 * Element name
	 *
	 * @var   string
	 */
	protected $_name = 'Version';

	function getInput()
	{
		$return = '';
		// Load language
		$extension = $this->def('extension');

		$version = '';

		$jinput = Factory::getApplication()->input;
		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->createQuery();
		$query
			->select($db->quoteName('manifest_cache'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . '=' . $db->Quote($extension) .' AND '.$db->quoteName('client_id') . '= 1');
		$db->setQuery($query, 0, 1);
		$row = $db->loadAssoc();
		$tmp = json_decode($row['manifest_cache']);
		$version = $tmp->version;
		
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		$css = '';
		$css .= ".version {display:block;text-align:right;color:brown;font-size:10px;}";
		$css .= ".readonly.plg-desc {font-weight:normal;}";
		$css .= "fieldset.radio label {width:auto;}";
		$wa->addInlineStyle($css);
		$margintop = $this->def('margintop');
		if (StringHelper::strlen($margintop)) {
			$js = "document.addEventListener('DOMContentLoaded', function() {
			vers = document.querySelector('.version');
			parent = vers.parentElement.parentElement;
			parent.style.marginTop = '".$margintop."';
			})";
			$wa->addInlineScript($js);
		}
		$return .= '<span class="version">' . Text::_('JVERSION') . ' ' . $version . "</span>";

		return $return;
	}
	public function def($val, $default = '')
	{
	    return ( isset( $this->element[$val] ) && (string) $this->element[$val] != '' ) ? (string) $this->element[$val] : $default;
	}
	
}
