<?php

namespace App\Http\Requests\Api\Product;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $company = company();
        $loggedUser = auth('api')->user();

        $rules = [
            'name'    => 'required',
            'slug'    => [
                'required',
                Rule::unique('products', 'slug')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ],
            'barcode_symbology'    => 'required',
            'item_code'    => [
                'required',
                Rule::unique('products', 'item_code')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ],
            'category_id'    => 'required',
            'unit_id'    => 'required',
        ];

        if ($this->product_type == 'single') {
            $rules['purchase_price'] = 'required|gt:0';
            $rules['sales_price'] = 'required||gt:0|gte:purchase_price';
        }

        // If purchase or sales includes tax
        if ($this->purchase_tax_type == 'inclusive' || $this->sales_tax_type == 'inclusive') {
            $rules['tax_id'] = 'required';
        }

        $convertedId = Hashids::decode($this->category_id);
        $id = $convertedId[0];
        if (Category::find($id)->unit_price) {
            $rules['units_amount'] = 'required';
        }

        if ($loggedUser->hasRole('admin')) {
            $rules['warehouse_id'] = 'required';
        }

        return $rules;
    }
}
