<?php

namespace Modules\Common\Entities;

use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class LoanContract extends BaseModel implements LoggerInterface
{
    /**
     * @var string
     */
    protected $table = 'loan_contract';

    /**
     * @var string
     */
    protected $primaryKey = 'loan_contract_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'loan_id',
        'investor_id',
        'investment_id',
        'contract_template_id',
        'file_id',
        'data',
    ];

    public function file()
    {
        return $this->belongsTo(
            File::class,
            'file_id',
            'file_id'
        );
    }

    public function template()
    {
        return $this->belongsTo(
            ContractTemplate::class,
            'contract_template_id',
            'contract_template_id'
        );
    }

    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    public function investment()
    {
        return $this->belongsTo(
            Investment::class,
            'investment_id',
            'investment_id'
        );
    }
}
