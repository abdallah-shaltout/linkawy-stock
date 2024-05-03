<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\Exceptions\ResourceNotFoundException;
use App\Http\Requests\Api\Category\IndexRequest;
use App\Http\Requests\Api\Category\StoreRequest;
use App\Http\Requests\Api\Category\UpdateRequest;
use App\Http\Requests\Api\Category\DeleteRequest;
use App\Http\Requests\Api\Category\ImportRequest;
use App\Imports\CategoryImport;
use App\Models\Category;
use App\Models\Product;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class CategoryController extends ApiBaseController
{
	protected $model = Category::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function update(...$args)
	{
		\DB::beginTransaction();

		// Geting id from hashids
		$xid = last(func_get_args());
		$convertedId = Hashids::decode($xid);
		$id = $convertedId[0];

		$this->validate();


		/** @var ApiModel $object */
		$object = Category::find($id);

		if (!$object) {
			throw new ResourceNotFoundException();
		}

		$object->fill(request()->all());

		$object->save();

		$meta = $this->getMetaData(true);

		\DB::commit();

		if (method_exists($this, 'updated')) {
			call_user_func([$this, 'updated'], $object);
		}

		return ApiResponse::make("Resource updated successfully", ["xid" => $object->xid], $meta);
	}

	public function updated(Category $category)
	{
		$childProducts = $category->products()->get();
		foreach ($childProducts as $childProduct) {
			if ($childProduct->sales_tax_type == 'inclusive') {
				$childProduct->details->update(['sales_price' => $category->unit_price * $childProduct->details->units_amount]);
			} else {
				$childProduct->details->update(['sales_price' => $category->unit_price * $childProduct->details->units_amount + ($category->unit_price * $childProduct->details->units_amount * $childProduct->sales_tax_rate / 100)]);
			}
		}
		return $category;
	}

	public function destroying(Category $category)
	{
		// Can not delete parent category
		$childCategoryCount = Category::where('parent_id', $category->id)->count();
		if ($childCategoryCount > 0) {
			throw new ApiException('Parent category cannot be deleted. Please delete child category first.');
		}

		// Category assigned to any product will not be deleted
		$productCount = Product::where('category_id', $category->id)->count();
		if ($productCount > 0) {
			throw new ApiException('Cateogry assigned to any product is not deletable.');
		}

		return $category;
	}

	public function import(ImportRequest $request)
	{
		if ($request->hasFile('file')) {
			Excel::import(new CategoryImport, request()->file('file'));
		}

		return ApiResponse::make('Imported Successfully', []);
	}
}
