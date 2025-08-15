<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('contract_client_id')->constrained('contract_clients')->onDelete('cascade');
            $table->foreignId('stamp_info_id')->constrained('stamp_infos')->onDelete('cascade');
            $table->enum('project_type', ['project', 'termin', 'montly']);
            $table->decimal('contract_value', 15, 2);
            $table->decimal('tax_value', 15, 2);
            $table->json('faktur_files')->nullable();
            $table->string('faktur_no')->nullable();
            $table->string('discount_value')->default(0);
            $table->integer('termin');
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
        Schema::dropIfExists('invoices');
    }
}
