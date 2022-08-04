<?php

namespace Modules\Core\Database\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CustomEloquentCollection extends Collection
{
    private $customMethods = [
        'delete' => 1,
        'disable' => 1,
        'enable' => 1,
    ];

    public function __call($method, $arguments) {
        if (array_key_exists($method, $this->customMethods)) {
            if (empty($this->items)) {
                return false;
            }

            $action = 'set' . ucfirst($method) . 'Fields';
            foreach ($this->items as $key => $item) {
                $this->{$action}($item);
            }

            return true;
        }

        return call_user_func_array([$this, $method], $arguments);
    }

    private function setDeleteFields($item) {
        $item->deleted_at = Carbon::now();
        $item->deleted = 1;
        $item->save();
    }

    private function setDisableFields($item) {
        $item->updated_at = Carbon::now();
        $item->active = 0;
        $item->save();
    }

    private function setEnableFields($item) {
        $item->updated_at = Carbon::now();
        $item->active = 1;
        $item->save();
    }
}
