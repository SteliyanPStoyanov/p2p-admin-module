<?php

namespace Modules\Common\Entities;

use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class ContractTemplate extends BaseModel implements LoggerInterface
{
    /**
     * @var array
     */
    protected $traitCasts = [
        'active' => 'boolean',
        'deleted' => 'boolean',
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y H:i',
        'deleted_at' => 'datetime:d-m-Y H:i',
        'enabled_at' => 'datetime:d-m-Y H:i',
        'disabled_at' => 'datetime:d-m-Y H:i',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'enabled_by' => 'integer',
        'disabled_by' => 'integer',
    ];

    public const USER_AGREEMENT_VARS = [
        'Investor.investor_id',
        'Current.date',
        'ContractTemplate.version',
        'ContractTemplate.created_at',
        'ContractTemplate.start_date',
    ];

    public const ASSIGNMENT_AGREEMENT_VARS = [
        'ContractTemplate.version',
        'ContractTemplate.created_at',
        'LoanContract.created_at',
        'Loan.loan_id',
        'Loan.interest_rate_percent',
        'Loan.final_payment_date',
        'Investor.investor_id',
        'Transaction.created_at',
        'Transaction.transaction_id',
        'Originator.name',
        'Originator.country.name',
        'Originator.pin',
        'Afranga.pin',
        'Investment.amount',
        'Buyback',
    ];

    public const TYPE_INVESTOR = 'investor';
    public const TYPE_LOAN = 'loan';
    public const TYPE_COOKIE_PRIVACY = 'cookie';
    public const TYPE_REFER_A_FRIEND = 'refer-a-friend';

    public const AGREEMENT_LANGUAGE = 'Bulgarian';

    /**
     * @var string
     */
    protected $table = 'contract_template';

    /**
     * @var string
     */
    protected $primaryKey = 'contract_template_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'name',
        'version',
        'text',
        'variables',
        'start_date',
    ];

    public static function getTypes()
    {
        return [
            self::TYPE_INVESTOR,
            self::TYPE_LOAN,
            self::TYPE_COOKIE_PRIVACY,
            self::TYPE_REFER_A_FRIEND,
        ];
    }
}
