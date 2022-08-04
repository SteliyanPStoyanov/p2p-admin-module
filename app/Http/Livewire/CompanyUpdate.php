<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorCompany;

class CompanyUpdate extends Component
{

    protected $listeners = ['companyUpdate' => '$refresh'];

    public Investor $investor;
    public $countries;
    public string $field;
    public string $name;
    public string $number;
    public string $address;
    public string $country_id;
    protected array $validationRules = [
        'name' => 'string|required|min:2|max:40',
        'number' => 'numeric|required',
        'address' => 'string|required|min:2|max:40',
        'country_id' => 'required|min:1',
    ];

    protected array $rules = [];

    /**
     * @param $investor
     */
    public function mount($investor)
    {
        $this->investor = $investor;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view(
            'livewire.company-update'
        );
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function companyField(string $field, string $value)
    {
        $this->countries = Country::all();

        $this->field = $field;
        $this->$field = $value;
    }


    public function submit()
    {
        $field = $this->field;

        // validation rules for field
        $this->rules = [$field => $this->validationRules[$field]];
        $this->validate();

        $data = [$field => $this->$field];

        $investorCompany = InvestorCompany::where('investor_id', $this->investor->investor_id)->first();

        $investorCompany->update($data);
        session()->flash('message', 'Company successfully updated.');
        $this->dispatchBrowserEvent('company-update');
    }

}
