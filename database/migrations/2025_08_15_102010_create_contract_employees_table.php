<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('nip')->nullable();
            $table->date('start_on');
            $table->date('ends_on');
            $table->foreignId('placement_id')->constrained('placements')->onDelete('cascade');
            $table->decimal('thp', 15, 2)->default(0); // Total Home Pay
            $table->decimal('daily_wages', 15, 2)->default(0);
            $table->string('account_number');
            $table->string('bank_id'); // Asumsi nama bank atau ID bank
            $table->string('account_holder_name');
            $table->string('no_bpjstk')->nullable();
            $table->string('no_bpjskes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_employees');
    }
}
