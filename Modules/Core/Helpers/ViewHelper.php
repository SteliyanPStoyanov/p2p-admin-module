<?php

namespace Modules\Core\Helpers;

use Illuminate\Http\Request;

/**
 * This class is created for representing of custom view functions
 */
class ViewHelper
{
	const ACTIVE_MENU_ITEM_CLASS = 'active';

	/**
	 * Function is used for menu item checks, if active OR not
	 *
	 * Blade usage: {{ ViewHelper::setActive('home') }}
	 *
	 * @param string $aim - main url part(controller)
	 * @param int|null $position - url segment number
	 * @return string
	 */
    public static function setActive(string $aim, $position = null): string
    {
    	$pattern = explode("*", $aim)[0];
    	$request = request();

    	if (strpos($request->path(), $pattern) !== false) {
    		return self::ACTIVE_MENU_ITEM_CLASS;
    	}

    	return '';
		// if (!empty($position)) {
		// 	if ($aim == Request::segment($position)) {
		// 		return self::ACTIVE_MENU_ITEM_CLASS;
		// 	}

		// 	return '';
		// }

		// if (Request::is($aim)) {
		// 	return self::ACTIVE_MENU_ITEM_CLASS;
		// }

		// return '';
    }
}
