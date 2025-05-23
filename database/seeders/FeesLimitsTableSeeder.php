<?php



namespace Database\Seeders;



use App\Models\FeesLimit;

use Illuminate\Database\Seeder;



class FeesLimitsTableSeeder extends Seeder

{



    public function run()

    {

        $data = [

            // USD

            // Deposit

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 2,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 4,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 6,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 1,

                'payment_method_id'   => 7,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],



            // withdrawal

            [

                'currency_id'         => 1,

                'transaction_type_id' => 2,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 2,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 2,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 1,

                'transaction_type_id' => 2,

                'payment_method_id'   => 8,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Transferred

            [

                'currency_id'         => 1,

                'transaction_type_id' => 3,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Exchange_From

            [

                'currency_id'         => 1,

                'transaction_type_id' => 5,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Request_To

            [

                'currency_id'         => 1,

                'transaction_type_id' => 8,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],



            // GBP

            // Deposit

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 2,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 4,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 6,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 1,

                'payment_method_id'   => 7,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],



            // withdrawal

            [

                'currency_id'         => 2,

                'transaction_type_id' => 2,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 2,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 2,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 2,

                'transaction_type_id' => 2,

                'payment_method_id'   => 8,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Transferred

            [

                'currency_id'         => 2,

                'transaction_type_id' => 3,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Exchange_From

            [

                'currency_id'         => 2,

                'transaction_type_id' => 5,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Request_To

            [

                'currency_id'         => 2,

                'transaction_type_id' => 8,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],



            // EUR

            // Deposit

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 2,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 4,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 6,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 1,

                'payment_method_id'   => 7,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],



            // withdrawal

            [

                'currency_id'         => 3,

                'transaction_type_id' => 2,

                'payment_method_id'   => 1,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 2,

                'payment_method_id'   => 3,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 2,

                'payment_method_id'   => 5,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            [

                'currency_id'         => 3,

                'transaction_type_id' => 2,

                'payment_method_id'   => 8,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Transferred

            [

                'currency_id'         => 3,

                'transaction_type_id' => 3,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Exchange_From

            [

                'currency_id'         => 3,

                'transaction_type_id' => 5,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],

            // Request_To

            [

                'currency_id'         => 3,

                'transaction_type_id' => 8,

                'payment_method_id'   => null,

                'charge_percentage'   => 0.00000000,

                'charge_fixed'        => 0.00000000,

                'min_limit'           => 1.00000000,

                'max_limit'           => null,

                'has_transaction'     => 'Yes',

            ],
            
            // Profile Payment
            [
                'currency_id'         => 6,
                'transaction_type_id' => 13,
                'payment_method_id'   => 9,
                'charge_percentage'   => 10.00000000,
                'charge_fixed'        => 2.00000000,
                'min_limit'           => 1.00000000,
                'max_limit'           => null,
                'has_transaction'     => 'Yes',
            ],

        ];

        FeesLimit::insert($data);

    }

}

