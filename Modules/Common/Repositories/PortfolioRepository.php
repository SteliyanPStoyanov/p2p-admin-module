<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Core\Repositories\BaseRepository;

class PortfolioRepository extends BaseRepository
{

    /**
     * @param int $investorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $investorId)
    {
        return Portfolio::where('investor_id', '=', $investorId)->get();
    }

    /**
     * @param int $investorId
     * @param string $type
     * @param int $currency
     *
     * @return mixed
     */
    public function getPortfolio(int $investorId, string $type, int $currency)
    {
        return Portfolio::where(
            [
                'investor_id' => $investorId,
                'currency_id' => $currency,
                'type' => $type,
            ]
        )->first();
    }

    /**
     * @param array $ranges
     * @param null|int $portfolioId
     *
     * @return array
     */
    public function getActualMaturities(array $ranges, ?int $portfolioId)
    {
        return DB::select(
            DB::raw(
                "
                SELECT
                    portfolio_id,
                   (
                       SELECT count(investment.loan_id) FROM investment
                       JOIN loan ON loan.loan_id = investment.loan_id
                       WHERE loan.final_payment_date BETWEEN :range1_from AND :range1_to
                       AND investment.investor_id = p.investor_id
                       AND loan.status = :status_active
                       AND loan.unlisted = :unlisted
                       LIMIT 1
                   ) AS range1,
                   (
                       SELECT count(investment.loan_id) FROM investment
                       JOIN loan ON loan.loan_id = investment.loan_id
                       WHERE loan.final_payment_date BETWEEN :range2_from AND :range2_to
                       AND investment.investor_id = p.investor_id
                       AND loan.status = :status_active
                       AND loan.unlisted = :unlisted
                       LIMIT 1
                   ) AS range2,
                   (
                       SELECT count(investment.loan_id) FROM investment
                       JOIN loan ON loan.loan_id = investment.loan_id
                       WHERE loan.final_payment_date BETWEEN :range3_from AND :range3_to
                       AND investment.investor_id = p.investor_id
                       AND loan.status = :status_active
                       AND loan.unlisted = :unlisted
                       LIMIT 1
                   ) AS range3,
                   (
                       SELECT count(investment.loan_id) FROM investment
                       JOIN loan ON loan.loan_id = investment.loan_id
                       WHERE loan.final_payment_date > :range4_from
                       AND investment.investor_id = p.investor_id
                       AND loan.status = :status_active
                       AND loan.unlisted = :unlisted
                       LIMIT 1
                   ) AS range4,
                   (
                       SELECT count(investment.loan_id) FROM investment
                       JOIN loan ON loan.loan_id = investment.loan_id
                       WHERE loan.final_payment_date < CURRENT_DATE
                       AND investment.investor_id = p.investor_id
                       AND loan.status = :status_active
                       AND loan.unlisted = :unlisted
                       LIMIT 1
                   ) AS range5
                FROM
                    portfolio AS p
                WHERE
                    p.type = :maturity_type
                    " . (is_int($portfolioId) ? " AND p.portfolio_id = " . $portfolioId : "") . "

                AND
                    (
                        DATE(p.ranges_updated_at) < CURRENT_DATE
                        OR p.ranges_updated_at IS NULL
                    )
                GROUP BY
                    portfolio_id
            "
            ),
            [
                'status_active' => Loan::STATUS_ACTIVE,
                'unlisted' => 0,
                'maturity_type' => Portfolio::PORTFOLIO_TYPE_MATURITY,
                'range1_from' => $ranges['range1']['from'],
                'range1_to' => $ranges['range1']['to'],
                'range2_from' => $ranges['range2']['from'],
                'range2_to' => $ranges['range2']['to'],
                'range3_from' => $ranges['range3']['from'],
                'range3_to' => $ranges['range3']['to'],
                'range4_from' => $ranges['range4']['from'],
            ]
        );
    }

    /**
     * @param $portfolioData
     *
     * @return bool
     */
    public function updateMaturityRanges($portfolioData)
    {
        return Portfolio::where('portfolio_id', $portfolioData->portfolio_id)
            ->update(
                [
                    'range1' => $portfolioData->range1,
                    'range2' => $portfolioData->range2,
                    'range3' => $portfolioData->range3,
                    'range4' => $portfolioData->range4,
                    'range5' => $portfolioData->range5,
                    'ranges_updated_at' => Carbon::now(),
                ]
            );
    }

    /**
     * @param array $data
     *
     * @return Portfolio
     */
    public function createPortfolio(array $data)
    {
        $portfolio = new Portfolio();
        $portfolio->fill($data);
        $portfolio->save();

        $portfolioTypeQuality = $portfolio->replicate()->fill(
            [
                'type' => Portfolio::PORTFOLIO_TYPE_QUALITY
            ]
        );

        $portfolioTypeQuality->save();

        return $portfolio;
    }
}
