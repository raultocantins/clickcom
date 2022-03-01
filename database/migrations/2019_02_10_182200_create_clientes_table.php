<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('empresa_id')->unsigned();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');

            $table->string('razao_social', 100);
            $table->string('nome_fantasia', 80);
            $table->string('cpf_cnpj', 19)->default("000.000.000-00");
            $table->string('rua', 80);
            $table->string('ie_rg', 20);
            $table->string('numero', 10);
            $table->string('bairro', 50);
            $table->string('telefone', 20);
            $table->string('celular', 20)->default("00 00000 0000");
            $table->string('email', 40)->default("null");
            $table->string('cep', 10)->default("null");
            $table->integer('consumidor_final');
            $table->integer('contribuinte');

            $table->integer('cidade_id')->unsigned();
            $table->foreign('cidade_id')->references('id')->on('cidades')->onDelete('cascade');

            $table->decimal('limite_venda', 10,2)->default(0);

            $table->string('rua_cobranca', 100);
            $table->string('numero_cobranca', 10);
            $table->string('bairro_cobranca', 30);
            $table->string('cep_cobranca', 9);

            $table->integer('cidade_cobranca_id')->nullable()->unsigned();
            $table->foreign('cidade_cobranca_id')->references('id')
            ->on('cidades')->onDelete('cascade');

            $table->integer('cod_pais')->default(1058);
            $table->string('id_estrangeiro', 30)->default("");

            $table->integer('grupo_id')->default(0);
            $table->integer('acessor_id')->default(0);

            $table->string('contador_nome', 30)->default('');
            $table->string('contador_telefone', 15)->default('');
            $table->string('contador_email', 60)->default('');

            $table->integer('funcionario_id')->default(0);
            $table->string('observacao')->default('');
            $table->string('data_aniversario', 5)->default('');


            // $table->string('cidade', 10)->default("null");

            // alter table clientes add column funcionario_id integer default 0;
            // alter table clientes add column observacao varchar(255) default '';
            // alter table clientes add column contador_email varchar(60) default '';
            // alter table clientes add column data_aniversario varchar(5) default '';

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
        Schema::dropIfExists('clientes');
    }
}
