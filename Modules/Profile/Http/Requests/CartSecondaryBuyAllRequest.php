<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class CartSecondaryBuyAllRequest extends BaseRequest implements ListSearchInterface
{

    public function rules(): array
    {
        $this->maxPrincipalPrice();

        return [
            'cartId' => 'required|numeric',
            'cart.*.cartLoanId' => 'required|numeric',
            'cart.*.outstandingInvestment' => 'nullable|numeric',
            'cart.*.principal' => 'nullable|numeric|max_principal_price:cart.*.outstandingInvestment',

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
            'cart.*.principal.max_principal_price' => [trans('common.PrincipalMaxPriceDiscountBuy')],
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
