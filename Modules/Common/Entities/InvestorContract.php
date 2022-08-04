<?php

namespace Modules\Common\Entities;

use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class InvestorContract extends BaseModel implements LoggerInterface
{
    protected $traitCasts = [
        'active' => 'boolean',
        'deleted' => 'boolean',
        'created_at' => 'datetime:d-m-Y H:i',
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

    /**
     * @var string
     */
    protected $table = 'investor_contract';

    /**
     * @var string
     */
    protected $primaryKey = 'investor_contract_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
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
}
