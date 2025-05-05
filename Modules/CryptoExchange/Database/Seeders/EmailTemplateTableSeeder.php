<?php

namespace Modules\CryptoExchange\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\EmailTemplate::insert(
        [
            // Crypto Email Notification to Admin
            [
                'temp_id'     => '34',
                'subject'     => 'Notice of Crypto Transaction!',
                'body'        => 'Hi,
                            <br><br>Amount {amount} was exchanged by {user}.

                            <br><br><b><u><i>Here’s a brief overview of the Exchange:</i></u></b>

                            <br><br><b><u>Created at:</u></b> {created_at}

                            <br><br><b><u>Transaction ID:</u></b> {uuid}

                            <br><br><b><u>From wallet:</u></b> {from_wallet}

                            <br><br><b><u>To wallet:</u></b> {to_wallet}

                            <br><br><b><u>Amount:</u></b> {amount}

                            <br><br><b><u>Fee (deducted from {from_wallet}):</u></b> {fee}

                            <br><br>If you have any questions, please feel free to reply to this email.
                            <br><br>Regards,
                            <br><b>{soft_name}</b>
                            ',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '34',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Crypto Status Updated Email Notification to User

            [
                'temp_id'     => '35',
                'subject'     => 'Status of Crypto Transaction #{uuid} has been updated!',
                'body'        => 'Hi {user},

                            <br><br><b>
                            Transaction of Crypto Transaction #{uuid} has been updated to {status} by system administrator!</b>

                            <br><br>
                            {amount} is {added/subtracted} {from/to} your account.

                            <br><br>If you have any questions, please feel free to reply to this email.

                            <br><br>Regards,
                            <br><b>{soft_name}</b>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],

            [
                'temp_id'     => '35',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Crypto Status Updated SMS Notification to User
            [
                'temp_id'     => '36',
                'subject'     => 'Status of Crypto Transaction #{uuid} has been updated!',
                'body'        => 'Hi {sender_id/receiver_id},
                                <br><br><b>
                                Crypto Transaction #{uuid} has been updated to {status} by system administrator!</b>
                                <br><br>
                                {amount} is {added/subtracted} {from/to} your account.
                            ',
                'lang'        => 'en',
                'type'        => 'sms',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'sms',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'sms',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'sms',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'sms',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'sms',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'sms',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '36',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'sms',
                'language_id' => 8,
            ],

        ]);
    }
}
