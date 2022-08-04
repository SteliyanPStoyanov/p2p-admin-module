<?php

namespace Modules\Core\Forms;

class FormValuesLibrary
{
	const DEFAULT_POSITIVE_ARGUMENT = 1;
	const DEFAULT_NEGATIVE_ARGUMENT = 0;

	/**
	 * [getBinarySelectValues description]
	 * @param  string $zeroLabel
	 * @param  string $oneLabel
	 * @return array
	 */
	public static function getBinarySelectValues(
		$zeroLabel = '0',
		$oneLabel = '1'
	): array
	{
		return [
			self::DEFAULT_POSITIVE_ARGUMENT => $zeroLabel,
			self::DEFAULT_NEGATIVE_ARGUMENT => $oneLabel,
		];
	}

	/**
	 * [getDefaultSelectValues description]
	 * Mostly used in forms for default select
	 * @param  string $zeroLabel
	 * @param  string $oneLabel
	 * @return array
	 */
	public static function getDefaultSelectValues(
		$zeroLabel = 'Default',
		$oneLabel = 'Custom'
	): array
	{
		return self::getBinarySelectValues($zeroLabel, $oneLabel);
	}

	/**
	 * [getActiveSelectValues description]
	 * Mostly used in forms for active select
	 * @param  string $zeroLabel
	 * @param  string $oneLabel
	 * @return array
	 */
	public static function getActiveSelectValues(
		$zeroLabel = 'Active',
		$oneLabel = 'Disabled'
	): array
	{
		return self::getBinarySelectValues($zeroLabel, $oneLabel);
	}

	/**
	 * [getYesNoSelectValues description]
	 * Mostly used in forms for simple  selects
	 * @param  string $zeroLabel
	 * @param  string $oneLabel
	 * @return array
	 */
	public static function getYesNoSelectValues(
		$zeroLabel = 'Yes',
		$oneLabel = 'No'
	): array
	{
		return self::getBinarySelectValues(
			trans('messages.' . $zeroLabel),
			trans('messages.' . $oneLabel)
		);
	}
}
