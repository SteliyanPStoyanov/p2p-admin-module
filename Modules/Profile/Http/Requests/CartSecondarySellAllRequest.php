<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Modules\Admin\Entities\Setting;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class CartSecondarySellAllRequest extends BaseRequest implements ListSearchInterface
{

    public function rules(): array
    {
        $premiumLimit = (int)\SettingFacade::getSettingValue(
            Setting::PREMIUM_LIMIT_VALUE_KEY
        );

        $this->maxPrincipalPrice();
        $this->maxSalePrice();

        return [
            'cart.*.cartLoanId' => 'nullable|numeric',
            'cart.*.discount' => 'nullable|numeric|min:-' . $premiumLimit . '|max:' . $premiumLimit,
            'cart.*.outstandingInvestment' => 'nullable|numeric',
            'cart.*.principal' => 'nullable|numeric|max_principal_price:cart.*.outstandingInvestment',
            'cart.*.salePrice' => 'nullable|numeric|max_sale_price:cart.*.outstandingInvestment,cart.*.discount',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'cart.*.discount.max' => ['numeric' => trans('common.DiscountMax', ['max' => ':max'])],
            'cart.*.discount.min' => ['numeric' => trans('common.DiscountMin', ['min' => ':min'])],
            'cart.*.principal.max_principal_price' => [trans('common.PrincipalMaxPrice')],
            'cart.*.salePrice.max_sale_price' => [trans('common.PrincipalMaxPriceDiscount')],
        ];
    }

    public function maxPrincipalPrice()
    {
        FacadesValidator::extendDependent(
            'max_principal_price',
            function ($attribute, $value, $parameters, $validator) {
                $data = $validator->getData();
                $flatten = $this->flattenArray($data);

                $principalValue = $flatten[$attribute];
                $outstandingInvestmentValue = $flatten[$parameters[0]];

                return $principalValue <= $outstandingInvestmentValue;
            }
        );
    }

    public function maxSalePrice()
    {
        FacadesValidator::extendDependent(
            'max_sale_price',
            function ($attribute, $value, $parameters, $validator) {
                $data = $validator->getData();

                $flatten = $this->flattenArray($data);

                $principalValue = $flatten[$attribute];
                $discountValue = $flatten[$parameters[1]];
                $outstandingInvestmentValue = $flatten[$parameters[0]];


                return $principalValue <= Calculator::getMaxSaleAmount($outstandingInvestmentValue, $discountValue);
            }
        );
    }

    /**
     * @param $data
     * @return array
     */
    public function flattenArray($data): array
    {
        $flatten = [];
        foreach ($data as $keyCart => $arrayCart) {
            if (is_array($arrayCart)) {
                foreach ($arrayCart as $keyItem => $arrayItem) {
                    foreach ($arrayItem as $keyFiled => $arrayFiled) {
                        $flatten[$keyCart . '.' . $keyItem . '.' . $keyFiled] = $arrayFiled;
                    }
                }
            }
        }
        return $flatten;
    }
}
