<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uploaded_documents', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('cuti_id')->constrained('cuti')->onDelete('cascade');
            $table->string('filename'); 
            $table->string('filepath'); 
            $table->timestamps();
        });        
    }

    public function down()
    {
        Schema::dropIfExists('uploaded_documents');
    }
};
