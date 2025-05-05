<?php

namespace Modules\CryptoExchange\Datatables;


use App\Http\Helpers\Common;
use Modules\CryptoExchange\Entities\ExchangeDirection;
use Yajra\DataTables\Services\DataTable;

class ExchangeDirectionsDataTable extends DataTable
{

    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('from_currency_id', function ($exDirection) {
                return isset($exDirection->fromCurrency->code) ? "<strong>" . optional($exDirection->fromCurrency)->code . "</strong>" : '-';
            })->editColumn('to_currency_id', function ($exDirection) {
                return isset($exDirection->toCurrency->code) ? "<strong>" . optional($exDirection->toCurrency)->code . "</strong>" : '-';
            })->editColumn('exchange_rate', function ($exDirection) {
                return ( $exDirection->exchange_from == 'local') ? formatNumber($exDirection->exchange_rate, $exDirection->to_currency_id) : '-';
            })->editColumn('min_amount', function ($exDirection) {
                return  formatNumber($exDirection->min_amount, $exDirection->from_currency_id);
            })->editColumn('max_amount', function ($exDirection) {
                return formatNumber($exDirection->max_amount, $exDirection->from_currency_id);
            })->editColumn('type', function ($exDirection) {
                return ucwords(str_replace("_", " ", $exDirection->type));
            })->editColumn('status', function ($exDirection) {
                if (optional($exDirection->toCurrency)->status == 'Inactive' || optional($exDirection->fromCurrency)->status == 'Inactive' ) {
                    return getStatusLabel('Inactive');
                }
                return getStatusLabel($exDirection->status);
            })->addColumn('action', function ($exDirection) {
                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_crypto_direction')) ? '<a href="' . route('admin.crypto_direction.edit', $exDirection->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
                $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_crypto_direction')) ? '<a href="' . route('admin.crypto_direction.delete', $exDirection->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>' : '';
                return $edit . $delete;
            })
            ->rawColumns(['from_currency_id','to_currency_id','status','action'])
            ->make(true);
    }

    public function query()
    {
        $query = ExchangeDirection::with(['fromCurrency','toCurrency']);
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'exchange_directions.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
        ->addColumn(['data' => 'type', 'name' => 'exchange_directions.type', 'title' => __('Type')])
        ->addColumn(['data' => 'from_currency_id', 'name' => 'fromCurrency.code', 'title' => __('From')])
        ->addColumn(['data' => 'to_currency_id', 'name' => 'toCurrency.code', 'title' => __('To')])
        ->addColumn(['data' => 'exchange_rate', 'name' => 'exchange_directions.exchange_rate', 'title' => __('Exchange Rate')])
        ->addColumn(['data' => 'min_amount', 'name' => 'exchange_directions.min_amount', 'title' => __('Min Amount')])
        ->addColumn(['data' => 'max_amount', 'name' => 'exchange_directions.max_amount', 'title' => __('Max Amount')])
        ->addColumn(['data' => 'status', 'name' => 'exchange_directions.status', 'title' => __('Status')])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false,
        'searchable' => false])
        ->parameters(dataTableOptions());
    }
}
