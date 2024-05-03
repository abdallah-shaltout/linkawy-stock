<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Sales\IndexRequest;
use App\Http\Requests\Api\Sales\StoreRequest;
use App\Http\Requests\Api\Sales\UpdateRequest;
use App\Http\Requests\Api\Sales\DeleteRequest;
use App\Models\Order;
use App\Traits\OrderTraits;
use Examyou\RestAPI\ApiResponse;

class SalesController extends ApiBaseController
{
	use OrderTraits;

	protected $model = Order::class;
	private $query = null;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function __construct()
	{
		parent::__construct();

		$this->query = call_user_func($this->model . "::query");
		$this->orderType = "sales";
	}

	public function getSummary()
	{
		$this->validate();

		$warehouse = warehouse();

		$results = $this->parseRequest()
			->addIncludes()
			->addFilters()
			->addOrdering()
			->addPaging()
			->modifyIndex($this->query)
			->with('user:id,name,user_type')
			->with('staffMember:id,name,user_type')
			->get()
			->toArray();

		// query sales order grouped by staff_user_id and count of orders and sum of total
		$statsResults = Order::selectRaw('id,order_date,invoice_number,total,payment_status,user_id,staff_user_id')->with('user:id,name,user_type')
			->with('staffMember:id,name,user_type')->selectRaw('COUNT(id) as orders_count')
			->selectRaw('SUM(total) as total')->groupBy('staff_user_id');

		$statsResults = $this->modifyIndex($statsResults)->get()->toArray();

		$results = [
			'sales' => $results,
			'staff_stats' => $statsResults
		];

		$meta = $this->getMetaData();

		return ApiResponse::make(null, $results, $meta);
	}
}
