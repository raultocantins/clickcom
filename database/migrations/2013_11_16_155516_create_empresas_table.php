<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 80);
            $table->string('rua', 50);
            $table->string('telefone', 15);
            $table->string('email', 50);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('cidade', 30);
            $table->string('cnpj', 18);
            $table->text('permissao');
            $table->boolean('status')->default(1);
            $table->boolean('tipo_representante')->default(0);
            $table->integer('perfil_id')->default(0);
            $table->string('mensagem_bloqueio', 255)->default('');

            // alter table empresas add column tipo_representante boolean default 0;
            // alter table empresas add column perfil_id integer default 0;
            // alter table empresas add column mensagem_bloqueio varchar(255) default '';

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
        Schema::dropIfExists('empresas');
    }
}
