<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Traits\Excludable;

use App\Models\{Currency,

    MerchantPayment,

    PaymentMethod,

    Merchant,

    User

};



class Transaction extends Model

{

    use Excludable;



    protected $table = 'transactions';



    protected $fillable = [

        'user_id',

        'end_user_id',

        'currency_id',

        'payment_method_id',

        'merchant_id',

        'bank_id',

        'file_id',

        'uuid',

        'refund_reference',

        'transaction_reference_id',

        'transaction_type_id',

        'user_type',

        'email',

        'phone',

        'subtotal',

        'percentage',

        'charge_percentage',

        'charge_fixed',

        'total',

        'note',

        'status',

    ];



    public static $cryptoTransactionsExcludes = ['merchant_id', 'bank_id', 'file_id', 'refund_reference', 'transaction_reference_id', 'email', 'phone', 'percentage', 'note'];



    public static $transactionTypes = [Deposit, Withdrawal, Transferred, Received, Exchange_From, Exchange_To, Request_From, Request_To, Payment_Sent, Payment_Received, Profile_payment];



    public function __construct()

    {

        if (config('referral.is_active')) {

            array_push(self::$transactionTypes, Referral_Award);

        }



        if (module('CryptoExchange') ) {

            array_push(self::$transactionTypes, Crypto_Swap, Crypto_Buy, Crypto_Sell);

        }

        if (module('Investment') ) {

            array_push(self::$transactionTypes, Investment);

        }



        if (module('BlockIo') ) {

            array_push(self::$transactionTypes, Crypto_Sent, Crypto_Received);

        }

    }



    // Start of relationships

    public function user()

    {

        return $this->belongsTo(User::class, 'user_id');

    }



    public function end_user()

    {

        return $this->belongsTo(User::class, 'end_user_id');

    }



    public function currency()

    {

        return $this->belongsTo(Currency::class, 'currency_id');

    }



    public function payment_method()

    {

        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');

    }

    public function profilePayment()
    {
        return $this->belongsTo(\Modules\MerchantPayLink\Entities\ProfilePayment::class, 'transaction_reference_id', 'id');
    }

    public function deposit()

    {

        return $this->belongsTo(Deposit::class, 'transaction_reference_id', 'id');

    }



    public function withdrawal()

    {

        return $this->belongsTo(Withdrawal::class, 'transaction_reference_id', 'id');

    }



    public function transfer()

    {

        return $this->belongsTo(Transfer::class, 'transaction_reference_id', 'id');

    }



    public function currency_exchange()

    {

        return $this->belongsTo(CurrencyExchange::class, 'transaction_reference_id', 'id');

    }



    public function crypto_exchange()

    {

        return $this->belongsTo( \Modules\CryptoExchange\Entities\CryptoExchange::class, 'transaction_reference_id', 'id');

    }



    public function invest()

    {

        return $this->belongsTo( \Modules\Investment\Entities\Invest::class, 'transaction_reference_id', 'id');

    }



    public function request_payment()

    {

        return $this->belongsTo(RequestPayment::class, 'transaction_reference_id', 'id');

    }



    public function merchant()

    {

        return $this->belongsTo(Merchant::class, 'merchant_id');

    }



    public function merchant_payment()

    {

        return $this->belongsTo(MerchantPayment::class, 'transaction_reference_id', 'id');

    }



    public function transaction_type()

    {

        return $this->belongsTo(TransactionType::class);

    }



    public function dispute()

    {

        return $this->hasOne(Dispute::class);

    }



    public function bank()

    {

        return $this->belongsTo(Bank::class, 'bank_id');

    }



    public function file()

    {

        return $this->belongsTo(File::class, 'file_id');

    }



    public function cryptoAssetApiLog()

    {

        return $this->hasOne(CryptoAssetApiLog::class, 'object_id')->whereIn('object_type', ["crypto_sent", "crypto_received", "crypto_exchange_from", "crypto_exchange_to", "fiat_exchange_from", "fiat_exchange_to"]);

    }



    // MobileMoney

    public function mobilemoney()

    {

        return $this->belongsTo(MobileMoney::class, 'mobilemoney_id');

    }

    // end of relationships



    //common functions - starts

    public function getTransactionsUsersEndUsersName($user, $type)

    {

        $getUserEndUserTransaction = $this->where(function ($q) use ($user)

        {

            $q->where(['user_id' => $user])->orWhere(['end_user_id' => $user]);

        });



        $userTransaction = $getUserEndUserTransaction->with(['user:id,first_name,last_name', 'end_user:id,first_name,last_name'])->first(['user_id', 'end_user_id']);



        if (!empty($userTransaction))

        {

            if ($userTransaction->user_id == $user)

            {

                return $userTransaction->user;

            }



            if ($userTransaction->end_user_id == $user)

            {

                return $userTransaction->end_user;

            }

        }

    }



    /**

     * [get transactions users response on search] [applied in 3 places]

     * @param  [string] $search   [query string]

     * @return [string] [distinct firstname and lastname]

     */

    public function getTransactionsUsersResponse($search, $type)

    {

        $getTransactionsUsers = $this->whereHas('user', function ($query) use ($search)

        {

            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })

        ->distinct('user_id');



        $getTrxUsers = $getTransactionsUsers->with(['user:id,first_name,last_name'])->get(['user_id'])->map(function ($transactionA)

        {

            $arr['user_id']    = $transactionA->user_id;

            $arr['first_name'] = $transactionA->user->first_name;

            $arr['last_name']  = $transactionA->user->last_name;

            return $arr;

        });



        $getTransactionsEndUsers = $this->whereHas('end_user', function ($query) use ($search)

        {

            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })

        ->distinct('end_user_id');



        $getTrxEndUsers = $getTransactionsEndUsers->with(['end_user:id,first_name,last_name'])->get(['end_user_id'])->map(function ($transactionB)

        {

            $arr['user_id']    = $transactionB->end_user_id;

            $arr['first_name'] = $transactionB->end_user->first_name;

            $arr['last_name']  = $transactionB->end_user->last_name;

            return $arr;

        });



        if ($getTrxUsers->isNotEmpty())

        {

            return $getTrxUsers->unique();

        }



        if ($getTrxEndUsers->isNotEmpty())

        {

            return $getTrxEndUsers->unique();

        }



        if ($getTrxUsers->isNotEmpty() && $getTrxEndUsers->isNotEmpty())

        {

            $getUniqueTransactionsUsers = ($getTrxUsers->merge($getTrxEndUsers))->unique();

            return $getUniqueTransactionsUsers;

        }

    }



    public function getTransactions($from, $to, $type, $wallet, $status)

    {

        $conditions = [];

        if (empty($from) || empty($to)) {

            $date_range = null;

        } else if (empty($from)) {

            $date_range = null;

        } else if (empty($to)) {

            $date_range = null;

        } else {

            $date_range = 'Available';

        }



        $conditions['transactions.user_id'] = auth()->user()->id;

        $whereInCondition = self::$transactionTypes;



        if (!empty($type) && $type != 'all') {

            $whereInCondition = [getPaymoneySettings('transaction_types')['mobile']['all'][getTransactionTypes($type)]];

        }



        if (!empty($wallet) && $wallet != 'all') {

            $conditions['transactions.currency_id'] = $wallet;

        }



        if (!empty($status) && $status != 'all') {

            $conditions['transactions.status'] = $status;

        }



        if (empty($date_range)) {

            $transaction = $this->with([

                'end_user:id,first_name,last_name,picture',

                'transaction_type:id,name',

                'payment_method:id,name',

                'bank:id,file_id,bank_name',

                'bank.file:id,filename',

                'merchant:id,business_name,logo',

                'currency:id,type,code',

                'dispute:id,transaction_id',

                'transfer:id,sender_id',

                'transfer.sender:id,first_name,last_name',

            ])

            ->where($conditions)

            ->whereIn('transactions.transaction_type_id', $whereInCondition)

            ->orderBy('transactions.id', 'desc')->select('transactions.*')

            ->paginate(15);

        }

        else

        {

            $from        = date('Y-m-d', strtotime($from));

            $to          = date('Y-m-d', strtotime($to));

            $transaction = $this->with([

                'end_user:id,first_name,last_name,picture',

                'transaction_type:id,name',

                'payment_method:id,name',

                'bank:id,file_id,bank_name',

                'bank.file:id,filename',

                'merchant:id,business_name,logo',

                'currency:id,code',

                'dispute:id,transaction_id',

                'transfer:id,sender_id',

                'transfer.sender:id,first_name,last_name',

            ])

            ->where($conditions)

            ->whereIn('transactions.transaction_type_id', $whereInCondition)

            ->whereDate('transactions.created_at', '>=', $from)

            ->whereDate('transactions.created_at', '<=', $to)

            ->orderBy('transactions.id', 'desc')

            ->select('transactions.*')

            ->paginate(15);

        }

        return $transaction;

    }



    /**

     * [Transactions Filtering Results]

     * @param  [null/date] $from     [start date]

     * @param  [null/date] $to       [end date]

     * @param  [string]    $status   [Status]

     * @param  [string]    $currency [currency]

     * @param  [string]    $type     [type]

     * @param  [null/id]   $user     [User ID]

     * @return [query]     [All Query Results]

     */

    public function getTransactionsList($from, $to, $status, $currency, $type, $user)

    {

        $conditions = [];



        if (!empty($from) && !empty($to))

        {

            $date_range = 'Available';

        }

        else

        {

            $date_range = null;

        }

        if (!empty($status) && $status != 'all')

        {

            $conditions['transactions.status'] = $status;

        }

        if (!empty($currency) && $currency != 'all')

        {

            $conditions['transactions.currency_id'] = $currency;

        }

        if (!empty($type) && $type != 'all')

        {

            $conditions['transaction_type_id'] = $type;

        }



        $transactions = $this->with([

            'user:id,first_name,last_name',

            'end_user:id,first_name,last_name',

            'currency:id,type,code',

            'transaction_type:id,name',

            'deposit.user:id,first_name,last_name',

            'withdrawal.user:id,first_name,last_name',

            'currency_exchange.user:id,first_name,last_name',

            'transfer.sender:id,first_name,last_name',

            'transfer.receiver:id,first_name,last_name',

            'request_payment.user:id,first_name,last_name',

            'request_payment.receiver:id,first_name,last_name',

            'cryptoAssetApiLog:id,object_id,network,payload'

        ])->where($conditions);



        //if user is not empty, check both user_id & end_user_id columns

        if (!empty($user))

        {

            $transactions->where(function ($q) use ($user)

            {

                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);

            });

        }

        if (!empty($date_range))

        {

            $transactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->select('transactions.*');

        }

        else

        {

            $transactions->select('transactions.*');

        }

        //

        return $transactions;

    }



    /**

     * [Get each user transactions list]

     * @param  [null/date] $from     [start date]

     * @param  [null/date] $to       [end date]

     * @param  [string]    $status   [Status]

     * @param  [string]    $currency [currency]

     * @param  [string]    $type     [type]

     * @param  [null/id]   $user     [User ID]

     * @return [query]     [All Query Results]

     */

    public function getEachUserTransactionsList($from, $to, $status, $currency, $type, $user)

    {

        $conditions = [];



        if (!empty($from) && !empty($to))

        {

            $date_range = 'Available';

        }

        else

        {

            $date_range = null;

        }

        if (!empty($status) && $status != 'all')

        {

            $conditions['transactions.status'] = $status;

        }

        if (!empty($currency) && $currency != 'all')

        {

            $conditions['transactions.currency_id'] = $currency;

        }

        if (!empty($type) && $type != 'all')

        {

            $conditions['transaction_type_id'] = $type;

        }



        //

        $transactions = $this->with([

            'user:id,first_name,last_name',

            'end_user:id,first_name,last_name',

            'currency:id,type,code',

            'transaction_type:id,name',

            'deposit.user:id,first_name,last_name',

            'withdrawal.user:id,first_name,last_name',

            'currency_exchange.user:id,first_name,last_name',

            'transfer.sender:id,first_name,last_name',

            'transfer.receiver:id,first_name,last_name',

            'request_payment.user:id,first_name,last_name',

            'request_payment.receiver:id,first_name,last_name',

        ])->where($conditions);

        //



        //if user is not empty, check both user_id & end_user_id columns

        if (!empty($user))

        {

            $transactions->where(function ($q) use ($user)

            {

                $q->where(['transactions.user_id' => $user]);

            });

        }

        //



        //

        if (!empty($date_range))

        {

            $transactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->select('transactions.*');

        }

        else

        {

            $transactions->select('transactions.*');

        }

        //

        return $transactions;

    }



    /**

     * [Revenues]

     * @return [void] [Total Charge of Each Transaction With Separate Currency Data]

     */

    public function getTotalCharge()

    {

        return $this->select('currency_id')

            ->addSelect(DB::raw('SUM(charge_percentage + charge_fixed) as total_charge'))

            ->groupBy('currency_id')

            ->get();

    }



    public function getRevenuesList($from, $to, $currency, $type)

    {

        $conditions = [];



        $revenueTransactionTypes = [Deposit, Withdrawal, Exchange_From, Transferred, Request_To, Payment_Received]; 



        if (module('CryptoExchange') ) {

            array_push($revenueTransactionTypes, Crypto_Swap, Crypto_Buy, Crypto_Sell);

        }

        if (module('MerchantPayLink') ) {
            array_push($revenueTransactionTypes, Profile_payment);
        }



        if (empty($from) || empty($to))

        {

            $date_range = null;

        }

        else if (empty($from))

        {

            $date_range = null;

        }

        else if (empty($to))

        {

            $date_range = null;

        }

        else

        {

            $date_range = 'Available';

        }

        if (!empty($currency) && $currency != 'all')

        {

            $conditions['transactions.currency_id'] = $currency;

        }

        if (!empty($type) && $type != 'all')

        {

            $conditions['transactions.transaction_type_id'] = $type;

        }



        $selectOptions = ['transactions.id', 'transactions.created_at', 'transactions.transaction_type_id', 'transactions.charge_percentage', 'transactions.charge_fixed',

            'transactions.currency_id'];



        $revenues = $this->with([

            'transaction_type:id,name',

            'currency:id,code',

        ])

        ->where($conditions)

        ->where(function ($query)

        {

            $query->where('charge_percentage', '>', 0);

            $query->orWhere('charge_fixed', '!=', 0);

        })

        ->where('status', 'Success')

        ->whereIn('transaction_type_id', $revenueTransactionTypes);



        if (!empty($date_range))

        {

            $revenues->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)

                ->select($selectOptions);

        }

        else

        {

            $revenues->select($selectOptions);

        }



        return $revenues;

    }



    /**

     *  DASHBOARD FUNCTIONALITIES

     */

    public function dashboardTransactionList()

    {

        $transaction = Transaction::with([

            'end_user:id,first_name,last_name,picture,status',

            'transaction_type:id,name',

            'payment_method:id,name',

            'bank:id,file_id,bank_name',

            'bank.file:id,filename',

            'merchant:id,business_name,logo',

            'currency:id,type,code',

            'dispute:id,transaction_id',

            'transfer:id,sender_id',

            'transfer.sender:id,first_name,last_name',

        ])->where(['transactions.user_id' => auth()->user()->id])->orderBy('transactions.id', 'desc')->take(10)->get();

        return $transaction;

    }



    public function lastThirtyDaysDeposit()

    {

        $getLastOneMonthDates = getLastOneMonthDates();

        $final                = [];

        $data_map             = array();

        $today                = date('Y-m-d');

        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));

        $data                 = $this->select(DB::raw('currency_id,SUM(total) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))

            ->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => Deposit, 'status' => 'Success'])

            ->groupBy('currency_id', 'day')->get();

        // $homeCurrency = Setting::where(['name' => 'default_currency', 'type' => 'general'])->select('value')->first();

        // $currencyRate = Currency::where(['id' => $homeCurrency->value])->select('rate')->first();



        $currencies       = getCurrencyIdOfTransaction($data);

        $currencyWithRate = Currency::whereIn('id', $currencies)->get();



        if (!empty($data))

        {

            $data_map = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);



            $dataArray = [];

            $i         = 0;

            foreach ($getLastOneMonthDates as $key => $value)

            {

                $date                   = explode('-', $value);

                $td                     = (int) $date[0];

                $tm                     = (int) $date[1];

                $dataArray[$i]['day']   = $date[0];

                $dataArray[$i]['month'] = $date[1];

                if (isset($data_map[$td][$tm]))

                {

                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);

                }

                else

                {

                    $dataArray[$i]['amount'] = 0;

                }

                $i++;

            }

            foreach ($dataArray as $key => $res)

            {

                $final[$key] = decimalFormat(abs($res['amount']));

            }

        }

        return $final;

    }



    public function lastThirtyDaysWitdrawal()

    {



        $getLastOneMonthDates = getLastOneMonthDates();

        $final                = [];

        $data_map             = [];

        $today                = date('Y-m-d');

        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));

        $data                 = $this->select(DB::raw('currency_id,SUM(total) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => Withdrawal, 'status' => 'Success'])->groupBy('currency_id', 'day')->get();

        $currencies           = getCurrencyIdOfTransaction($data);

        $currencyWithRate     = Currency::whereIn('id', $currencies)->get();

        if (!empty($data))

        {

            $data_map  = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);

            $dataArray = [];

            $i         = 0;

            foreach ($getLastOneMonthDates as $key => $value)

            {

                $date                   = explode('-', $value);

                $td                     = (int) $date[0];

                $tm                     = (int) $date[1];

                $dataArray[$i]['day']   = $date[0];

                $dataArray[$i]['month'] = $date[1];

                if (isset($data_map[$td][$tm]))

                {

                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);

                }

                else

                {

                    $dataArray[$i]['amount'] = 0;

                }

                $i++;

            }

            foreach ($dataArray as $key => $res)

            {

                $final[$key] = decimalFormat(abs($res['amount']));

            }

        }

        return $final;

    }



    public function lastThirtyDaysTransfer()

    {



        $getLastOneMonthDates = getLastOneMonthDates();

        $final                = [];

        $today                = date('Y-m-d');

        $previousDate         = date("Y-m-d", strtotime("-30 day", strtotime(date('d-m-Y'))));

        $data                 = $this->select(DB::raw('currency_id,SUM(subtotal) as amount,created_at as trans_date,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$previousDate, $today])->where(['transaction_type_id' => Transferred, 'status' => 'Success'])->groupBy('currency_id', 'day')->get();

        $currencies           = getCurrencyIdOfTransaction($data);

        $currencyWithRate     = Currency::whereIn('id', $currencies)->get();



        if (!empty($data))

        {

            $data_map  = generateAmountBasedOnDfltCurrency($data, $currencyWithRate);

            $dataArray = [];

            $i         = 0;

            foreach ($getLastOneMonthDates as $key => $value)

            {

                $date                   = explode('-', $value);

                $td                     = (int) $date[0];

                $tm                     = (int) $date[1];

                $dataArray[$i]['day']   = $date[0];

                $dataArray[$i]['month'] = $date[1];

                if (isset($data_map[$td][$tm]))

                {

                    $dataArray[$i]['amount'] = abs($data_map[$td][$tm]);

                }

                else

                {

                    $dataArray[$i]['amount'] = 0;

                }

                $i++;

            }

            foreach ($dataArray as $key => $res)

            {

                $final[$key] = decimalFormat(abs($res['amount']));



            }

        }

        return $final;

    }



    public function totalRevenue($from, $to)

    {

        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))

            ->whereBetween('created_at', [$from, $to])->whereIn('transaction_type_id', [Deposit, Withdrawal, Transferred])->groupBy('currency_id', 'day')->get();



        $currencies       = getCurrencyIdOfTransaction($data);

        $currencyWithRate = Currency::whereIn('id', $currencies)->get();

        $final            = 0;

        if (!empty($data))

        {

            $final = generateAmountForTotal($data, $currencyWithRate);

        }

        return $final;

    }



    public function totalDeposit($from, $to)

    {

        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,

                                            MONTH(created_at) as month,

                                            DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->where('transaction_type_id', Deposit)->groupBy('currency_id', 'day')->get();



        $currencies       = getCurrencyIdOfTransaction($data);

        $currencyWithRate = Currency::whereIn('id', $currencies)->get();

        $final            = 0;

        if (!empty($data))

        {

            $final = generateAmountForTotal($data, $currencyWithRate);

        }

        return $final;

    }



    public function totalWithdrawal($from, $to)

    {

        $data = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->where('transaction_type_id', Withdrawal)->groupBy('currency_id', 'day')->get();



        $currencies       = getCurrencyIdOfTransaction($data);

        $currencyWithRate = Currency::whereIn('id', $currencies)->get();

        $final            = 0;

        if (!empty($data))

        {

            $final = generateAmountForTotal($data, $currencyWithRate);

        }

        return $final;

    }



    public function totalTransfer($from, $to)

    {

        $data             = $this->select(DB::raw('currency_id,SUM(charge_percentage + charge_fixed) as total_charge,MONTH(created_at) as month,DAY(created_at) as day'))->whereBetween('created_at', [$from, $to])->whereIn('transaction_type_id', [Transferred, Request_To])->groupBy('currency_id', 'day')->get();

        $currencies       = getCurrencyIdOfTransaction($data);

        $currencyWithRate = Currency::whereIn('id', $currencies)->get();

        $final            = 0;

        if (!empty($data))

        {

            $final = generateAmountForTotal($data, $currencyWithRate);

        }

        return $final;

    }



    //Query for Mobile Application - starts

    public function getTransactionLists($type, $user_id)

    {

        $conditions = ['transactions.user_id' => $user_id];

        if ($type == 'allTransactions')

        {

            $whereInCondition = self::$transactionTypes;

        }



        $transaction = $this->with([

            'currency:id,type,code,symbol',

            'user:id,first_name,last_name,picture',

            'end_user:id,first_name,last_name,picture',

            'payment_method:id,name',

            'transaction_type:id,name',

            'merchant:id,business_name,logo', //fixed

            'bank:id,bank_name,file_id',

            'bank.file:id,filename',

        ])

            ->where($conditions)

            ->whereIn('transactions.transaction_type_id', $whereInCondition)

            ->orderBy('transactions.id', 'desc')

            ->select([

                'transactions.id as id',

                'transactions.user_id',

                'transactions.end_user_id',

                'transactions.currency_id',

                'transactions.payment_method_id',

                'transactions.merchant_id',

                'transactions.bank_id',

                'transactions.transaction_type_id',

                'transactions.subtotal as subtotal',

                'transactions.charge_percentage as charge_percentage',

                'transactions.charge_fixed as charge_fixed',

                'transactions.total as total',

                'transactions.status as status',

                'transactions.email as email',

                'transactions.phone as phone',

                'transactions.created_at as t_created_at',

            ])

            ->get();



        $transactions = [];

        for ($i = 0; $i < count($transaction); $i++)

        {

            if ($transaction[$i]->user_id)

            {

                $transactions[$i]['user_id']     = $transaction[$i]->user_id;

                $transactions[$i]['user_f_name'] = $transaction[$i]->user->first_name;

                $transactions[$i]['user_l_name'] = $transaction[$i]->user->last_name;

                $transactions[$i]['user_photo']  = $transaction[$i]->user->picture;

            }



            if ($transaction[$i]->end_user_id)

            {

                $transactions[$i]['end_user_id']     = $transaction[$i]->end_user_id;

                $transactions[$i]['end_user_f_name'] = $transaction[$i]->end_user->first_name;

                $transactions[$i]['end_user_l_name'] = $transaction[$i]->end_user->last_name;

                $transactions[$i]['end_user_photo']  = $transaction[$i]->end_user->picture;

            }



            $transactions[$i]['id']                  = $transaction[$i]->id;

            $transactions[$i]['transaction_type_id'] = $transaction[$i]->transaction_type_id;

            $transactions[$i]['transaction_type']    = $transaction[$i]->transaction_type->name;

            $transactions[$i]['curr_code']           = $transaction[$i]->currency->code;

            $transactions[$i]['curr_symbol']         = $transaction[$i]->currency->symbol;

            $transactions[$i]['charge_percentage']   = $transaction[$i]->charge_percentage;

            $transactions[$i]['charge_fixed']        = $transaction[$i]->charge_fixed;



            //formatNumber - starts

            $transactions[$i]['subtotal'] = moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->subtotal, $transaction[$i]->currency_id));



            $transactions[$i]['total'] = moneyFormat($transaction[$i]->currency->symbol, formatNumber($transaction[$i]->total, $transaction[$i]->currency_id));

            //formatNumber - ends



            $transactions[$i]['status']       = $transaction[$i]->status;

            $transactions[$i]['email']        = $transaction[$i]->email;

            $transactions[$i]['phone']        = $transaction[$i]->phone;

            $transactions[$i]['t_created_at'] = dateFormat($transaction[$i]->t_created_at, $user_id);



            if ($transaction[$i]->payment_method_id)

            {

                $transactions[$i]['payment_method_name'] = $transaction[$i]->payment_method->name;

                $transactions[$i]['payment_method_id']   = $transaction[$i]->payment_method_id;

                $transactions[$i]['company_name']        = settings('name');

                $transactions[$i]['company_logo']        = settings('logo');

            }



            if ($transaction[$i]->merchant_id)

            {

                $transactions[$i]['merchant_id']   = $transaction[$i]->merchant_id;

                $transactions[$i]['merchant_name'] = $transaction[$i]->merchant->business_name;

                $transactions[$i]['logo']          = $transaction[$i]->merchant->logo;

            }



            if ($transaction[$i]->bank_id)

            {

                $transactions[$i]['bank_id']   = $transaction[$i]->bank_id;

                $transactions[$i]['bank_name'] = $transaction[$i]->bank->bank_name;

                if ($transaction[$i]->bank->file_id)

                {

                    $transactions[$i]['bank_logo'] = $transaction[$i]->bank->file->filename;

                }

            }



        }

        // d($transactions, 1);

        return $transactions;

    }



    public function getTransactionDetails($tr_id, $user_id)

    {

        $conditions       = ['transactions.id' => $tr_id, 'transactions.user_id' => $user_id];

        $whereInCondition = self::$transactionTypes;



        $transaction = $this->with([

            'currency:id,type,code,symbol',

            'user:id,first_name,last_name,picture,email,formattedPhone',

            'end_user:id,first_name,last_name,picture,email,formattedPhone',

            'payment_method:id,name',

            'transaction_type:id,name',

            'merchant:id,business_name',

        ])

        ->where($conditions)

        ->whereIn('transactions.transaction_type_id', $whereInCondition)

        ->orderBy('transactions.id', 'desc')

        ->select([

            'transactions.id as id', //

            'transactions.user_id',  //

            'transactions.end_user_id',

            'transactions.currency_id',       //

            'transactions.payment_method_id', //

            'transactions.merchant_id as merchant_id',

            'transactions.transaction_type_id', //

            'transactions.transaction_reference_id as transaction_reference_id',

            'transactions.charge_percentage as charge_percentage',

            'transactions.charge_fixed as charge_fixed',

            'transactions.subtotal as subtotal',

            'transactions.total as total',

            'transactions.uuid as transaction_id',

            'transactions.status as status',

            'transactions.note as description',

            'transactions.email as email',

            'transactions.phone as phone',

            'transactions.created_at as t_created_at',

        ])->first();



        if (@$transaction->user_id)

        {

            $transaction->user_id     = @$transaction->user_id;

            $transaction->user_f_name = @$transaction->user->first_name;

            $transaction->user_l_name = @$transaction->user->last_name;

            $transaction->user_email  = @$transaction->user->email;

            $transaction->user_phone  = @$transaction->user->formattedPhone;

            $transaction->user_photo  = @$transaction->user->picture;

        }



        if (@$transaction->end_user_id)

        {

            $transaction->end_user_id     = @$transaction->end_user_id;

            $transaction->end_user_f_name = @$transaction->end_user->first_name;

            $transaction->end_user_l_name = @$transaction->end_user->last_name;

            $transaction->end_user_email  = @$transaction->end_user->email;

            $transaction->end_user_phone  = @$transaction->end_user->formattedPhone;

            $transaction->end_user_photo  = @$transaction->end_user->picture;

        }



        $transaction->curr_code   = @$transaction->currency->code;

        $transaction->curr_symbol = @$transaction->currency->symbol;



        //formatNumber - starts

        $transaction->total = moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->total, $transaction->currency_id));



        $transaction->subtotal = moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id));



        if ($transaction->currency->type != 'fiat')

        {

            $transaction->totalFees = (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0)) ? moneyFormat(optional($transaction->currency)->symbol, formatNumber(0, $transaction->currency_id)) :

            moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->charge_fixed, $transaction->currency_id));

        }

        else

        {

            $transaction->totalFees = (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0)) ? moneyFormat(optional($transaction->currency)->symbol, formatNumber(0, $transaction->currency_id)) : moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed, $transaction->currency_id));

        }

        //formatNumber - ends



        if (@$transaction->payment_method_id)

        {

            $transaction->payment_method_name = @$transaction->payment_method->name;

            $transaction->company_name        = settings('name');

        }



        if (@$transaction->merchant_id)

        {

            $transaction->merchant_name = @$transaction->merchant->business_name;

        }

        $transaction->type_id      = @$transaction->transaction_type->id;

        $transaction->type         = @$transaction->transaction_type->name;

        $transaction->t_created_at = dateFormat($transaction->t_created_at, $user_id);



        return $transaction;

    }



    public function eachUserTransactionGroupBy($groupBy, $userId)

    {

        return DB::select(DB::raw("SELECT transactions.user_id,transactions.end_user_id,transactions.currency_id,transactions.transaction_type_id,transactions.status,currencies.code,transaction_types.id,transaction_types.name 

        FROM transactions 

        LEFT JOIN currencies ON currencies.id = transactions.currency_id 

        LEFT JOIN transaction_types ON transaction_types.id = transactions.transaction_type_id 

        WHERE transactions.user_id = $userId OR transactions.end_user_id = $userId 

        GROUP BY $groupBy"));

    }



    public function getCryptoSentTransactions($from, $to, $status, $currency, $user)

    {

        $conditions = [];



        if (!empty($from) && !empty($to)) {

            $date_range = 'Available';

        } else {

            $date_range = null;

        }



        if (!empty($status) && $status != 'all') {

            $conditions['transactions.status'] = $status;

        }



        if (!empty($currency) && $currency != 'all') {

            $conditions['transactions.currency_id'] = $currency;

        }



        $cryptoSenttransactions = $this->with([

            'user:id,first_name,last_name',

            'end_user:id,first_name,last_name',

            'currency:id,code',

            'cryptoAssetApiLog:id,object_id,payload',

        ])

        ->where('transaction_type_id', Crypto_Sent)

        ->where($conditions);



        // If user is not empty, check both user_id & end_user_id columns

        if (!empty($user)) {

            $cryptoSenttransactions->where(function ($q) use ($user) {

                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);

            });

        }



        if (!empty($date_range)) {

            $cryptoSenttransactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)->exclude(self::$cryptoTransactionsExcludes);

        } else {

            $cryptoSenttransactions->exclude(self::$cryptoTransactionsExcludes);

        }

        return $cryptoSenttransactions;

    }



    public function getCryptoReceivedTransactions($from, $to, $currency, $user)

    {

        $conditions = [];

        if (!empty($from) && !empty($to)) {

            $date_range = 'Available';

        } else {

            $date_range = null;

        }

        if (!empty($currency) && $currency != 'all') {

            $conditions['transactions.currency_id'] = $currency;

        }



        $cryptoSenttransactions = $this->with([

            'user:id,first_name,last_name',

            'end_user:id,first_name,last_name',

            'currency:id,code',

            'cryptoAssetApiLog:id,object_id,payload',

        ])

        ->where('transaction_type_id', Crypto_Received)

        ->where($conditions);



        //if user is not empty, check both user_id & end_user_id columns

        if (!empty($user)) {

            $cryptoSenttransactions->where(function ($q) use ($user) {

                $q->where(['transactions.user_id' => $user])->orWhere(['transactions.end_user_id' => $user]);

            });

        }



        if (!empty($date_range)) {

            $cryptoSenttransactions->whereDate('transactions.created_at', '>=', $from)->whereDate('transactions.created_at', '<=', $to)

                ->exclude(self::$cryptoTransactionsExcludes);

        } else {

            $cryptoSenttransactions->exclude(self::$cryptoTransactionsExcludes);

        }



        return $cryptoSenttransactions;

    }

    public function createTransaction($data)
    {
        $transaction                           = new Transaction();
        $transaction->user_id                  = $data['user_id'];
        $transaction->currency_id              = $data['currency_id'];
        $transaction->payment_method_id        = $data['payment_method_id'];
        $transaction->merchant_id              = $data['merchant_id'] ?? null;
        $transaction->uuid                     = $data['uuid'];
        $transaction->transaction_reference_id = $data['transaction_reference_id'] ?? null;
        $transaction->transaction_type_id      = $data['transaction_type_id'] ?? Payment_Received;
        $transaction->subtotal                 = $data['subtotal'];
        $transaction->percentage               = $data['percentage'];
        $transaction->charge_percentage        = $data['charge_percentage'] ?? 0;
        $transaction->charge_fixed             = $data['charge_fixed'] ?? 0;
        $transaction->total                    = $data['total'];
        $transaction->status                   = $data['status'] ?? 'Pending';
        $transaction->note                     = $data['note'] ?? null;
        $transaction->save();

        return $transaction;
    }
}

