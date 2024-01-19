<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    3rd September, 2020
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\Utilities\Component;


use Joomla\Input\Input;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;


/**
 * Some component helper
 * 
 * @since  3.0.11
 */
abstract class Helper
{
	/**
	 * The current option
	 *
	 * @var    string
	 * @since   3.0.11
	 */
	public static string $option;

	/**
	 * The component params list cache
	 *
	 * @var    Registry[]
	 * @since   3.0.11
	 */
	protected static array $params = [];

	/**
	 * Gets the parameter object for the component
	 *
	 * @param   string|null     $option  The option for the component.
	 *
	 * @return  Registry     A Registry object.
	 * @see     Registry
	 * @since   3.0.11
	 */
	public static function getParams(?string $option = null): Registry
	{
		// check that we have an option
		if (empty($option))
		{
			$option = self::getOption();
		}

		// get global value
		if (!isset(self::$params[$option]) || !self::$params[$option] instanceof Registry)
		{
			self::$params[$option] = ComponentHelper::getParams($option);
		}

		return self::$params[$option];
	}

	/**
	 * Gets the component option
	 *
	 * @param   string|null      $default  The default return value if none is found
	 *
	 * @return  string|null      A component option
	 * @since   3.0.11
	 */
	public static function getOption(string $default = 'empty'): ?string
	{
		if (empty(self::$option))
		{
			// get the option from the url input
			self::$option = (new Input)->getString('option', false);
		}

		if (self::$option)
		{
			 return self::$option;
		}

		return $default;
	}

	/**
	 * Gets the component code name
	 *
	 * @param   string|null    $option   The option for the component.
	 * @param   string|null    $default  The default return value if none is found
	 *
	 * @return  string|null    A component code name
	 * @since   3.0.11
	 */
	public static function getCode(?string $option = null, ?string $default = null): ?string
	{
		// check that we have an option
		if (empty($option))
		{
			$option = self::getOption();
		}
		// option with com_
		if (is_string($option) && strpos($option, 'com_') === 0)
		{
			return strtolower(trim(substr($option, 4)));
		}

		return $default;
	}

	/**
	 * Gets the component abstract helper class
	 *
	 * @param   string|null    $option   The option for the component.
	 * @param   string|null    $default  The default return value if none is found
	 *
	 * @return  string|null    A component helper name
	 *
	 * @since   3.0.11
	 */
	public static function get(string $option = null, string $default = null): ?string
	{
		// check that we have an option
		// and get the code name from it
		if (($code_name = self::getCode($option, false)) !== false)
		{
			// we build the helper class name
			$helper_name = '\\' . \ucfirst($code_name) . 'Helper';
			// check if class exist
			if (class_exists($helper_name))
			{
				return $helper_name;
			}
		}

		return $default;
	}

	/**
	 * Check if the helper class of this component has a method
	 *
	 * @param   string       $method  The method name to search for
	 * @param   string|null  $option  The option for the component.
	 *
	 * @return  bool    true if method exist
	 *
	 * @since   3.0.11
	 */
	public static function methodExists(string $method, string $option = null): bool
	{
		// get the helper class
		return ($helper = self::get($option, false)) !== false &&
			method_exists($helper, $method);
	}

	/**
	 * Check if the helper class of this component has a method, and call it with the arguments
	 *
	 * @param   string        $method     The method name to search for
	 * @param   array         $arguments  The arguments for function.
	 * @param   string|null   $option     The option for the component.
	 *
	 * @return  mixed    return whatever the method returns or null
	 * @since   3.2.0
	 */
	public static function _(string $method, array $arguments = [], ?string $option = null)
	{
		// get the helper class
		if (($helper = self::get($option, false)) !== false &&
			method_exists($helper, $method))
		{
			// we know this is not ideal...
			// so we need to move these
			// functions to their own classes
			return call_user_func_array([$helper, $method],  $arguments);
		}

		return null;
	}

}

