<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFornecedorTableContaPagars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conta_pagars', function (Blueprint $table) {
            $table->string('fornecedor') // Nome da coluna
            ->nullable() // Preenchimento não obrigatório
            ->after('id'); // Ordenado após a coluna "password"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conta_pagars', function (Blueprint $table) {
         $table->dropColumn('fornecedor');
        });
    }
}
