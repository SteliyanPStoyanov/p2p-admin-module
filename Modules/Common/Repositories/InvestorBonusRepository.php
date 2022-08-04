<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestorBonus;
use Modules\Core\Repositories\BaseRepository;

class InvestorBonusRepository extends BaseRepository
{
    /**
     * @param array $data
     *
     * @return InvestorBonus
     */
    public function create(array $data)
    {
        $investorBonus = new InvestorBonus();
        $investorBonus->fill($data);
        $investorBonus->save();

        return $investorBonus;
    }

    public function updateHandledInvestorBonus(int $investorBonusId)
    {
        InvestorBonus::where('investor_bonus_id', '=', $investorBonusId)->update(
            ['handled' => 1, 'handled_at' => Carbon::now()]
        );
    }

    /**
     * @param int $investorBonusId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Collection|InvestorBonus[]
     */
    public function getById(int $investorBonusId)
    {
        return InvestorBonus::where('investor_id', $investorBonusId)->get();
    }

    /**
     * @return Collection
     */
    public function getUnhandledBonus(): Collection
    {
        $builder = DB::table('investor_bonus');
        $builder->select(DB::raw('*'));

        $where[] = ['handled' ,'=', 0];
        $where[] = ['date' ,'<', Carbon::today()->format('Y-m-d')];

        $builder->where($where);

        return InvestorBonus::hydrate($builder->get()->all());
    }

}
