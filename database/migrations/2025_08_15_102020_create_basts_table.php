<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('contract_client_id')->constrained('contract_clients')->onDelete('cascade');
            $table->foreignId('stamp_info_id')->constrained('stamp_infos')->onDelete('cascade');
            $table->json('document_attachments')->nullable(); // Kolom ini di ERD, tapi ada tabel terpisah. Mungkin ini ringkasan.
            $table->timestamp('timestamp');
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
        Schema::dropIfExists('basts');
    }
}
