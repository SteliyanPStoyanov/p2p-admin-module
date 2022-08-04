<?php

namespace Modules\Admin\Forms;

use Kris\LaravelFormBuilder\Form;
use Modules\Core\Traits\ValidationTrait;

class AdministratorForm extends Form
{
    use ValidationTrait;

    public function buildForm()
    {
        $this
            ->add('username', 'text', [
                'label' => __('table.AdminUserName'),
                'rules' => 'required|min:5|unique:administrator',
            ])
            ->add('first_name', 'text', [
                'label' => __('table.Name'),
                'rules' => $this->getConfiguration('requestRules.firstName'),
            ])
            ->add('middle_name', 'text', [
                'label' => __('table.MiddleName'),
                'rules' => $this->getConfiguration('requestRules.middleNameNullable'),
            ])
            ->add('last_name', 'text', [
                'label' => __('table.LastName'),
                'rules' => $this->getConfiguration('requestRules.lastName'),
            ])
            ->add('phone', 'text', [
                'label' => __('table.Phone'),
                'rules' => $this->getConfiguration('requestRules.phone'),
            ])
            ->add('avatar', 'file', [
                'label' => __('table.AdminAvatar'),
                 'label_attr' => ['class' => 'custom-file-label'],
                 'wrapper' => ['class' => 'custom-file'],
                 'attr' => ['class' => 'custom-file-input'],
            ])
            ->add('email', 'email', [
                'label' => __('table.Email'),
                'rules' => $this->getConfiguration('requestRules.email'),
            ]);

        $this->add('password', 'repeated', [
            'type' => 'password',
            'second_name' => 'password_confirmation',
            'first_options' => [
                'value' => '',
            ],
            'second_options' => [
                'value' => '',
            ],
            'rules' => 'required|confirmed|min:2',
        ]);
    }
}
