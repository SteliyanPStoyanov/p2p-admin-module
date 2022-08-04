<?php

namespace Modules\Core\Models;

use \JsonSerializable;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\BaseModelTrait;

abstract class BaseModel extends Model
{
    use BaseModelTrait;

    protected $historyClass = false;

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * BaseModel constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->casts = $this->traitCasts;
    }

    /**
     * @return bool|string
     */
    public function getHistoryClass()
    {
        return $this->historyClass;
    }

    /**
     * Set archived_at/archived_by
     */
    public function setArchivedFields()
    {
        $this->archived_at = Carbon::now();
        $this->archived_by = Auth::user()->administrator_id;
    }

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }
}
