<?php

namespace Modules\Admin\Forms;

use Kris\LaravelFormBuilder\Form;

class RoleForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text', [
                'label' => __('table.Name'),
                'rules' => 'required|min:5|unique:role',
            ])
            ->add('priority', 'number', [
                'label' => __('table.Priority'),
                'rules' => 'required|int|min:1|max:' . \Auth::user()->getMaxPriority(),
            ])
            ->add('guard_name', 'hidden', [
                'disabled' => true,
                'value' => 'web',
            ]);
    }
}
