<?php

namespace App\Http\Livewire;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Common\Entities\Investor;
use Modules\Common\Repositories\InvestorRepository;

/**
 * Header Univested Amount Component(visible on all Profile pages)
 *
 * Livewire do wallet ping every 1.5 sec when investor investing
 * Sometimes investing could be delayed, so we don't need to check for wallet so often.
 * So we do first 3 times wallet check per 1.5 sec
 * Then we increase waiting period from 1.5 sec to 5 sec
 * If next 3 ping per 5 seconds will not have wallet change, we will increase waiting period to 15 sec.
 * So all next wallet checks will be done every 15 sec, while wallet uninvested amount has been changed,
 * If amount changed, we start from begin: 3x1.5 -> 3x5 -> 3x15.
 * If amount is changing consistantly we are doing ping every 1,5 by default
 */
class WalletUninvested extends Component
{
    protected $listeners = ['postAdded' => '$refresh'];

    public $sum;
    public $bunch;
    public int $count = 0;
    public int $waitTime = 0;

    /**
     * @param InvestorRepository $investorRepository
     * @return Application|Factory|View|RedirectResponse
     */
    public function render(InvestorRepository $investorRepository)
    {
        $investor = self::getInvestor($investorRepository);
        $wallet = $investor->wallet();
        if (!empty($wallet->wallet_id)) {
            $this->sum = $wallet->uninvested;
            $this->bunch = $investor->getInvestmentBunch();
        }

        $config = config('profile.wallet_uninvested');
        $this->waitTime = $config['default_delay'];

        if ($this->isNotEmptyBunch() && $this->isProcessingBunch() ) {
            if (
                $this->count >= $config['default_border_count']
                && $this->count < (2 * intval($config['default_border_count']))
            ) {
                $this->waitTime = $config['short_delay'];
            } else {
                if ($this->count >= (2 * intval($config['default_border_count']))) {
                    $this->waitTime = $config['long_delay'];
                }
            }

            $this->count++;

            $this->dispatchBrowserEvent('invest-status', ['waitTime' => $this->waitTime]);
        }

        if ($this->isSecondaryMarket() && $this->isBunchFinished()) {
            return redirect()->route('profile.cart-secondary.buySuccess');
        }

        return $this->getWalletUninvested();
    }

    private function isNotEmptyBunch(): bool
    {
        return !empty($this->bunch);
    }

    private function isProcessingBunch(): bool
    {
        return $this->bunch->finished == 0;
    }

    private function getWalletUninvested(): View
    {
        return view(
            'livewire.wallet-uninvested'
        );
    }

    private function isSecondaryMarket(): bool
    {
        if(isset($this->bunch->cart_secondary_id) && $this->bunch->cart_secondary_id) {
            return true;
        }

        return false;
    }

    private function isBunchFinished(): bool
    {
        return $this->bunch->finished;
    }

    /**
     * @param InvestorRepository $investorRepository
     * @return Investor|null
     */
    public function getInvestor(InvestorRepository $investorRepository): ?Investor
    {
        return $investorRepository->getById(Auth::guard('investor')->user()->investor_id);
    }
}
