<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_payments', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('currency_id')->unsigned()->index()->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('payment_method_id')->unsigned()->index()->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('cascade')->onDelete('cascade');

            $table->string('gateway_reference', 50)->nullable();
            $table->json('payer_details')->nullable();
            
            $table->string('uuid', 13)->nullable();
            $table->decimal('charge_percentage', 20, 8)->nullable()->default(0.00000000);;
            $table->decimal('charge_fixed', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('amount', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('total', 20, 8)->nullable()->default(0.00000000);
            $table->string('status', 11)->default('Success')->comment('Pending, Success, Refund, Blocked');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_payments');
    }
}
