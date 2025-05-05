<?php

namespace Modules\CryptoExchange\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use Session, Cache, Config;
use App\Models\{
    Transaction,
    Wallet,
    EmailTemplate
};
use Modules\CryptoExchange\Datatables\CryptoExchangesDataTable;
use Modules\CryptoExchange\Exports\CryptoExchangesExport;
use Modules\CryptoExchange\Entities\CryptoExchange;

class CryptoExchangeController extends Controller
{
    protected $helper;
    protected $exchange;
    protected $email;


    public function __construct()
    {
        $this->helper = new Common();
        $this->cryptoExchange = new CryptoExchange();
        $this->email = new EmailController();
    }

    public function index(CryptoExchangesDataTable $dataTable)
    {
        if (!m_g_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU') && m_aic_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU')) {
            return view('addons::install', ['module' => 'Q1JZUFRPRVhDSEFOR0VfU0VDUkVU']);
        }
        
        $data = [];
        $data['menu'] = 'crypto_exchange';
        $data['sub_menu'] = 'crypto_exchanges';
        $data['exchanges_status'] = $this->cryptoExchange->select('status')->groupBy('status')->get();
        $data['exchanges_currency'] = CryptoExchange::with('fromCurrency', 'toCurrency')->groupBy('from_currency')->get();
        $data['from'] = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status'] = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['user'] = $user = isset(request()->user_id) ? request()->user_id : null;
        $data['getName'] = $this->cryptoExchange->getExchangesUserName($user);
        return $dataTable->render('cryptoexchange::admin.crypto_exchange.list', $data);
    }

    public function exchangesUserSearch(Request $request)
    {
        $search = $request->search;
        $user = $this->cryptoExchange->getExchangesUsersResponse($search);
        $res = [
            'status' => 'fail',
        ];
        if (count($user) > 0) {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }

    public function exchangeCsv()
    {
        return Excel::download(new CryptoExchangesExport(), 'crypto_exchanges_list_' . time() . '.xlsx');
    }

    public function exchangePdf()
    {
        $from = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user = isset(request()->user_id) ? request()->user_id : null;
        $crypto_exchanges = $this->cryptoExchange->getExchangesList($from, $to, $status, $currency, $user)->get();
        $data = [];
        $data['crypto_exchanges'] = $crypto_exchanges = collect($crypto_exchanges)->sortByDesc('id');
        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
            $data['date_range'] = 'N/A';
        }
        generatePDF('cryptoexchange::admin.crypto_exchange.exchanges_report_pdf', 'crypto_exchanges_report_', $data);
    }

    public function edit($id)
    {
        $data = [];
        $data['menu'] = 'crypto_exchange';
        $data['sub_menu'] = 'crypto_exchanges';
        $data['exchange'] = $exchange = CryptoExchange::find($id);
        $data['transaction'] = Transaction::select('transaction_type_id', 'status', 'transaction_reference_id', 'percentage', 'charge_percentage', 'charge_fixed', 'uuid', 'phone')
            ->where(['transaction_reference_id' => $exchange->id, 'uuid' => $exchange->uuid])
            ->whereIn('transaction_type_id', [Crypto_Buy, Crypto_Sell, Crypto_Swap])
            ->first();
        return view('cryptoexchange::admin.crypto_exchange.edit', $data);
    }

    public function update(Request $request)
    {
        $cryptoExchange = CryptoExchange::find($request->transaction_reference_id);
        if ($request->status == $cryptoExchange->status) {
            $this->helper->one_time_message('success', __('Transaction is already :x', ['x' => $request->status]));
            return redirect()->route('admin.crypto_exchanges.index');
        }
        if ($request->status == 'Pending') {
            $this->helper->one_time_message('success', __('Status not changed'));
            return redirect()->route('admin.crypto_exchanges.index');
        }
        $to_wallet = Wallet::where([
            'user_id'     => $cryptoExchange->user_id,
            'currency_id' => $cryptoExchange->to_currency,
        ])->select('balance')->first();
        $from_wallet = Wallet::where([
            'user_id'     => $cryptoExchange->user_id,
            'currency_id' => $cryptoExchange->from_currency,
        ])->select('balance')->first();
        if ($request->status == 'Success') {
            if($cryptoExchange->status == 'Pending') {
                if ($cryptoExchange->receive_via == 'wallet') {
                    if (!$to_wallet) {
                        $to_wallet = Wallet::createWallet($cryptoExchange->user_id, $cryptoExchange->to_currency);
                    }
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->to_currency,
                    ])->update([
                        'balance' => $to_wallet->balance + $cryptoExchange->get_amount,
                    ]);
                }
            } elseif ($cryptoExchange->status == 'Blocked') {
                if (!$to_wallet) {
                    $to_wallet = Wallet::createWallet($cryptoExchange->user_id, $cryptoExchange->to_currency);
                }
                if ($cryptoExchange->receive_via == 'wallet') {
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->to_currency,
                    ])->update([
                        'balance' => $to_wallet->balance + $cryptoExchange->get_amount,
                    ]);
                }
                if ($cryptoExchange->send_via == 'wallet') {
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->from_currency,
                    ])->update([
                        'balance' => $from_wallet->balance - $cryptoExchange->amount,
                    ]);
                }
            }
        }
        elseif ($request->status == 'Blocked') {
            if ($cryptoExchange->status == 'Success') {
                if ($cryptoExchange->receive_via == 'wallet') {
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->to_currency,
                    ])->update([
                        'balance' => $to_wallet->balance - $cryptoExchange->get_amount,
                    ]);
                }
                if ($cryptoExchange->send_via == 'wallet') {
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->from_currency,
                    ])->update([
                        'balance' => $from_wallet->balance + $cryptoExchange->amount,
                    ]);
                }
            } elseif ($cryptoExchange->status == 'Pending') {
                if ($cryptoExchange->send_via == 'wallet') {
                    Wallet::where([
                        'user_id'     => $cryptoExchange->user_id,
                        'currency_id' => $cryptoExchange->from_currency,
                    ])->update([
                        'balance' => $from_wallet->balance + $cryptoExchange->amount,
                    ]);
                }
            }
        }

        //Transferred entry update
        Transaction::where([
            'user_id'                  => $request->user_id,
            'transaction_reference_id' => $request->transaction_reference_id,
            'transaction_type_id'      => $request->transaction_type_id,
        ])->update([
            'status' => $request->status,
        ]);
        $cryptoExchange->status = $request->status;
        $cryptoExchange->save();

        $user_name = getColumnValue($cryptoExchange->user);

        //Email - Crypto Exchange

        if (checkAppMailEnvironment()) {

            $cryptoExchangeEmailTempEng = EmailTemplate::where(['temp_id' => 35, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
            $cryptoExchangeEmailTemp         = EmailTemplate::where([
                'temp_id'     => 35,
                'language_id' => settings('default_language'),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
            $cryptoExchangeSmsTempEng = EmailTemplate::where(['temp_id' => 36, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
            $cryptoExchangeSmsTemp    = EmailTemplate::where(['temp_id' => 36, 'language_id' => settings('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();
            // Crypto Exchange Mail
            if (!empty($cryptoExchangeEmailTemp->subject) && !empty($cryptoExchangeEmailTemp->body)){
                $cryptoExchangeEmailSub = str_replace('{uuid}', $cryptoExchange->uuid, $cryptoExchangeEmailTemp->subject);
                $cryptoExchangeEmailMsg = str_replace('{user}', $user_name, $cryptoExchangeEmailTemp->body);
            } else {
                $cryptoExchangeEmailSub = str_replace('{uuid}', $cryptoExchange->uuid, $cryptoExchangeEmailTempEng->subject);
                $cryptoExchangeEmailMsg = str_replace('{user}', $user_name, $cryptoExchangeEmailTempEng->body);
            }
            
            $cryptoExchangeEmailMsg = str_replace([            
               '{uuid}', 
               '{status}', 
               '{amount}', 
               '{added/subtracted}', 
               '{from/to}',
               '{soft_name}'
               ], [
                $cryptoExchange->uuid,  
                $cryptoExchange->status, 
                moneyFormat( optional($cryptoExchange->toCurrency)->symbol, formatNumber($cryptoExchange->get_amount, $cryptoExchange->to_currency)),
                'added',
                'to',
                ucwords(settings('name'))
               ], 
               $cryptoExchangeEmailMsg
            );


            if (!is_null($cryptoExchange->user_id)) {
                $this->email->sendEmail($cryptoExchange->user->email, $cryptoExchangeEmailSub, $cryptoExchangeEmailMsg);
            }
            elseif ( $cryptoExchange->verification_via == 'email' ) {
                $this->email->sendEmail($cryptoExchange->email_phone, $cryptoExchangeEmailSub, $cryptoExchangeEmailMsg);
            }
        }

        //SMS - Crypto Exchange
        if (checkAppSmsEnvironment()) {
            if (!empty($cryptoExchangeSmsTemp->subject) && !empty($cryptoExchangeSmsTemp->body)) {
                $cryptoExchangeSmsSub = str_replace('{uuid}', $cryptoExchange->uuid, $cryptoExchangeSmsTemp->subject);
                $cryptoExchangeSmsMsg = str_replace('{sender_id/receiver_id}', $user_name, $cryptoExchangeSmsTemp->body);    
            } else {
                $cryptoExchangeSmsSub = str_replace('{uuid}', $cryptoExchange->uuid, $cryptoExchangeSmsTempEng->subject);
                $cryptoExchangeSmsMsg = str_replace('{sender_id/receiver_id}', $user_name, $cryptoExchangeSmsTempEng->body);
            }   
            $cryptoExchangeSmsMsg = str_replace([            
               '{uuid}', 
               '{status}', 
               '{amount}', 
               '{added/subtracted}', 
               '{from/to}'
               ], [
                $cryptoExchange->uuid,  
                $cryptoExchange->status, 
                moneyFormat( optional($cryptoExchange->toCurrency)->symbol, formatNumber($cryptoExchange->get_amount, $cryptoExchange->to_currency)),
                'added',
                'to'
               ], 
               $cryptoExchangeSmsMsg
            );
            if (!empty($cryptoExchange->user->carrierCode) && !empty($cryptoExchange->user->phone)) {
                sendSMS($cryptoExchange->user->carrierCode . $cryptoExchange->user->phone, $cryptoExchangeSmsMsg);

            } else if ($cryptoExchange->verification_via == 'phone') {

                sendSMS($cryptoExchange->email_phone, $cryptoExchangeSmsMsg);
            }
        }
        
        $this->helper->one_time_message('success', __('Transaction updated successfully'));
        return redirect()->route('admin.crypto_exchanges.index');
    }
}
