<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Core\Repositories\BaseRepository;
use Throwable;

class ArchiveRepository extends BaseRepository
{
    /**
     * @return int
     *
     * @throws Exception
     */
    public function wallets()
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
                INSERT INTO
                    wallet_history
                    (
                        wallet_id,
                        investor_id,
                        currency_id,
                        date,
                        total_amount,
                        invested,
                        uninvested,
                        deposit,
                        withdraw,
                        income,
                        interest,
                        late_interest,
                        bonus,
                        active,
                        deleted,
                        created_at,
                        created_by,
                        updated_at,
                        updated_by,
                        deleted_at,
                        deleted_by,
                        enabled_at,
                        enabled_by,
                        disabled_at,
                        disabled_by,
                        blocked_amount,
                        archived_at,
                        archived_by
                    )
                    SELECT
                        wallet_id,
                        investor_id,
                        currency_id,
                        date,
                        total_amount,
                        invested,
                        uninvested,
                        deposit,
                        withdraw,
                        income,
                        interest,
                        late_interest,
                        bonus,
                        active,
                        deleted,
                        created_at,
                        created_by,
                        updated_at,
                        updated_by,
                        deleted_at,
                        deleted_by,
                        enabled_at,
                        enabled_by,
                        disabled_at,
                        disabled_by,
                        blocked_amount,
                        :archived_at AS archived_at,
                        :archived_by AS archived_by
                    FROM
                        wallet
                    WHERE
                        (
                            date < CURRENT_DATE
                            OR date IS NULL
                        )',
                [
                    'archived_at' => $now,
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            );

            if ($affectedRows < 0) {
                Log::channel('daily_archiver')->error('!!! wallets - NO history inserts done');
            }

            DB::table('wallet')->update(['date' => $now]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Wallets not archived! ' . $exception->getMessage());
        }


        return $affectedRows;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function portfolios()
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
                INSERT INTO
                    portfolio_history
                    (
                        portfolio_id,
                        investor_id,
                        currency_id,
                        type,
                        date,
                        range1,
                        range2,
                        range3,
                        range4,
                        range5,
                        ranges_updated_at,
                        active,
                        deleted,
                        created_at,
                        created_by,
                        updated_at,
                        updated_by,
                        deleted_at,
                        deleted_by,
                        enabled_at,
                        enabled_by,
                        disabled_at,
                        disabled_by,
                        archived_at,
                        archived_by
                    )
                    SELECT
                        portfolio.*,
                        :archived_at AS archived_at,
                        :archived_by AS archived_by
                    FROM
                        portfolio
                    WHERE
                        (
                            date < CURRENT_DATE
                            OR date IS NULL
                        )',
                [
                    'archived_at' => $now,
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            );

            if ($affectedRows < 0) {
                Log::channel('daily_archiver')->error('!!! portfolios - NO history inserts done');
            }

            DB::table('portfolio')->update(['date' => $now]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Portfolios not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function investorInstallments()
    {
        DB::beginTransaction();

        try {
            $affectedRows = DB::affectingStatement(
                '
            INSERT INTO
                investor_installment_history
                (
                    investor_installment_id,
                    loan_id,
                    investment_id,
                    investor_id,
                    installment_id,
                    currency_id,
                    days,
                    remaining_principal,
                    principal,
                    accrued_interest,
                    interest,
                    late_interest,
                    interest_percent,
                    total,
                    paid,
                    paid_at,
                    active,
                    deleted,
                    created_at,
                    created_by,
                    updated_at,
                    updated_by,
                    deleted_at,
                    deleted_by,
                    enabled_at,
                    enabled_by,
                    disabled_at,
                    disabled_by,
                    archived_at,
                    archived_by
                )
                SELECT
                    investor_installment.*,
                    :archived_at AS archived_at,
                    :archived_by AS archived_by
                FROM
                    investor_installment
                JOIN
                    loan AS l on investor_installment.loan_id = l.loan_id
                WHERE
                    l.status != :status_active
            ',
                [
                    'archived_at' => Carbon::now(),
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                    'status_active' => Loan::STATUS_ACTIVE
                ]
            );

            DB::table('investor_installment')
                ->join('loan', 'loan.loan_id', '=', 'investor_installment.loan_id')
                ->where([['loan.status', '!=', Loan::STATUS_ACTIVE]])
                ->delete();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Installments not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function registrationAttempts()
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
            INSERT INTO
                registration_attempt_history
                (
                    id,
                    datetime,
                    email,
                    ip,
                    device,
                    last,
                    active,
                    deleted,
                    created_at,
                    created_by,
                    updated_at,
                    updated_by,
                    deleted_at,
                    deleted_by,
                    enabled_at,
                    enabled_by,
                    disabled_at,
                    disabled_by,
                    archived_at,
                    archived_by
                )
                SELECT
                    ra.*,
                    :archived_at AS archived_at,
                    :archived_by AS archived_by
                FROM
                    registration_attempt AS ra
                WHERE
                    ra.datetime < :today
            ',
                [
                    'archived_at' => $now,
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                    'today' => $now->toDateString(),
                ]
            );

            DB::table('registration_attempt')
                ->where([['datetime', '<', $now->toDateString()]])
                ->delete();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Registration attempts not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function loginAttempts()
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
            INSERT INTO
                login_attempt_history
                (
                    id,
                    datetime,
                    email,
                    ip,
                    device,
                    last,
                    active,
                    deleted,
                    created_at,
                    created_by,
                    updated_at,
                    updated_by,
                    deleted_at,
                    deleted_by,
                    enabled_at,
                    enabled_by,
                    disabled_at,
                    disabled_by,
                    archived_at,
                    archived_by
                )
                SELECT
                    ra.*,
                    :archived_at AS archived_at,
                    :archived_by AS archived_by
                FROM
                    login_attempt AS ra
                WHERE
                    ra.datetime < :today
            ',
                [
                    'archived_at' => $now,
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                    'today' => $now->toDateString(),
                ]
            );

            DB::table('login_attempt')
                ->where([['datetime', '<', $now->toDateString()]])
                ->delete();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Login attempts not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function blockedIps()
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
            INSERT INTO
                blocked_ip_history
                (
                    id,
                    ip,
                    blocked_till,
                    created_at,
                    created_by,
                    reason,
                    last,
                    active,
                    deleted,
                    updated_at,
                    updated_by,
                    deleted_at,
                    deleted_by,
                    enabled_at,
                    enabled_by,
                    disabled_at,
                    disabled_by,
                    archived_at,
                    archived_by
                )
                SELECT
                    bi.*,
                    :archived_at AS archived_at,
                    :archived_by AS archived_by
                FROM
                    blocked_ip AS bi
                WHERE
                    bi.blocked_till < :now
            ',
                [
                    'archived_at' => $now,
                    'now' => $now,
                    'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            );

            DB::table('blocked_ip')
                ->where([['blocked_till', '<', $now]])
                ->delete();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Blocked IPs not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }

    /**
     * Removes loans which are not exists in Afranga OR which is already de-activated
     *
     * @return int
     *
     * @throws Exception|\Throwable
     */
    public function unlistedLoans()
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $affectedRows = DB::affectingStatement(
                '
                UPDATE
                    unlisted_loan AS ul
                SET
                    handled = 1,
                    status = :not_exists,
                    updated_at = :now,
                    updated_by = :updated_by
                WHERE
                      handled = 0
                AND
                      lender_id NOT IN (SELECT l.lender_id FROM loan AS l)
            ',
                [
                    'now' => $now,
                    'not_exists' => UnlistedLoan::STATUS_NOT_EXISTS,
                    'updated_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            );

            $affectedRows += DB::affectingStatement(
                '
                UPDATE
                    unlisted_loan AS ul
                SET
                    handled = 1,
                    status = :already_unlisted,
                    updated_at = :now,
                    updated_by = :updated_by
                WHERE
                      handled = 0
                AND
                      lender_id IN (SELECT l.lender_id FROM loan AS l WHERE l.status != :status_active)
            ',
                [
                    'now' => $now,
                    'already_unlisted' => UnlistedLoan::STATUS_ALREADY_UNLISTED,
                    'status_active' => Loan::STATUS_ACTIVE,
                    'updated_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            );

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw new Exception('Unlisted loans not archived! ' . $exception->getMessage());
        }

        return $affectedRows;
    }
}
