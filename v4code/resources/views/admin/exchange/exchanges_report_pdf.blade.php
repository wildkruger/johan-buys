@extends('admin.pdf.app')

@section('title', __('Exchange List pdf'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('Date') }}</td>
                <td>{{ __('User') }}</td>
                <td>{{ __('Amount') }}{{ __('') }}</td>
                <td>{{ __('Fees') }}</td>
                <td>{{ __('Total') }}</td>
                <td>{{ __('Rate') }}</td>
                <td>{{ __('From') }}</td>
                <td>{{ __('To') }}</td>
                <td>{{ __('Status') }}</td>
            </tr>
            
            @foreach ($exchanges as $exchange)
                <tr class="table-body">
                    <td>{{ dateFormat($exchange->created_at) }}</td>
                    <td>{{ getColumnValue($exchange) }}</td>
                    <td>{{ formatNumber($exchange->amount) }} </td>
                    <td>{{ $exchange->fee == 0 ? '-' : formatNumber($exchange->fee) }}</td>
                    <td>{{ '-' . formatNumber($exchange->amount + $exchange->fee) }}</td>
                    <td>{{ moneyFormat($exchange->tc_symbol, formatNumber($exchange->exchange_rate, $exchange->to_currecny_id)) }}</td>
                    <td>{{ $exchange->fc_code }} </td>
                    <td>{{ $exchange->tc_code }}</td>
                    <td>{{ getStatus($exchange->status) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
