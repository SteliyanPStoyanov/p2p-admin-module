<?php

namespace Modules\Admin\Forms;

use Kris\LaravelFormBuilder\Form;
use Modules\Core\Traits\ValidationTrait;

class SettingForm extends Form
{
    use ValidationTrait;

    /**
     * @return mixed|void
     */
    public function buildForm()
    {
        $nameConfigArr = [
            'label' => __('table.Name'),
            'rules' => $this->getConfiguration('requestRules.nameLong'),
        ];

        if ($this->getData('name_readonly')) {
            $nameConfigArr['attr'] = ['readonly' => 'readonly'];
        }

        $this
            ->add(
                'name',
                'text',
                $nameConfigArr
            )
            ->add(
                'description',
                'text',
                [
                    'label' => __('table.Description'),
                    'rules' => 'required|min:2|max:100',
                ]
            )
            ->add(
                'default_value',
                'text',
                [
                    'label' => __('table.DefaultValue'),
                    'rules' => 'required|min:2|max:100',
                ]
            );
    }
}
