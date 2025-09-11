<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('contract_employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nip')->nullable();
            $table->date('start_on');
            $table->date('ends_on');
            $table->integer('thp')->default(0);
            $table->integer('daily_wages')->default(0);
            $table->string('account_number');
            $table->string('bank_id');
            $table->string('account_holder_name');
            $table->string('no_bpjstk')->nullable();
            $table->string('no_bpjskes')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('placement_id')->constrained('placements')->onDelete('cascade');
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
    public function down(): void
    {
        Schema::dropIfExists('contract_employees');
    }
};
