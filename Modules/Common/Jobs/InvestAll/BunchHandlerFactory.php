<?php

namespace Modules\Common\Jobs\InvestAll;

use \Exception;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Jobs\InvestAll\BunchHandler;

class BunchHandlerFactory
{
	const HANDLER_TACTIC_CART = 'CartTactic';
	const HANDLER_TACTIC_STRATEGY = 'StrategyTactic';
	const HANDLER_TACTIC_CART_SECONDARY = 'CartSecondaryTactic';
	const HANDLER_TACTIC_AMOUNT_FILTERS = 'AmountFiltersTactic';


	public static function build(InvestmentBunch $bunch): BunchHandler
	{
		$handlerName = self::getHandlerTacticName($bunch);
		if (empty($handlerName)) {
			throw new Exception("Failed to get Handler name for inv.bunch #" . $bunch->getId());
		}

		$classTactic = __NAMESPACE__ . '\\HandlerTactics\\' . $handlerName;
		if (!class_exists($classTactic)) {
			throw new Exception("Failed to find Handler:" . $classTactic);
		}

		$handler = new BunchHandler($bunch);
		$handler->setTactic(new $classTactic($bunch));
		return $handler;
	}


	private static function getHandlerTacticName(InvestmentBunch $bunch): ?string
	{
		// if bunch has strategy
		if (!empty($bunch->invest_strategy_id)) {
			return self::HANDLER_TACTIC_STRATEGY;
		}

		// if bunch has cart
		if (!empty($bunch->cart_id)) {
			return self::HANDLER_TACTIC_CART;
		}

        // if bunch has secondary cart
        if (!empty($bunch->cart_secondary_id)) {
            return self::HANDLER_TACTIC_CART_SECONDARY;
        }

        // if it's a manual bunch (amount/filters)
		if (!empty($bunch->amount)) {
			return self::HANDLER_TACTIC_AMOUNT_FILTERS;
		}

		return null;
	}
}
