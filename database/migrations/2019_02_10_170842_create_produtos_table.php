<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('empresa_id')->unsigned();
            $table->foreign('empresa_id')->references('id')
            ->on('empresas')->onDelete('cascade');

            $table->integer('categoria_id')->unsigned();
            $table->foreign('categoria_id')->references('id')
            ->on('categorias')->onDelete('cascade');

            $table->integer('sub_categoria_id')->unsigned()->nullable();
            $table->foreign('sub_categoria_id')->references('id')
            ->on('sub_categorias')->onDelete('cascade');

            $table->integer('marca_id')->unsigned()->nullable();
            $table->foreign('marca_id')->references('id')->on('marcas')
            ->onDelete('cascade');

            $table->string('nome', 100);
            $table->string('cor', 20);
            $table->integer('referencia_balanca')->default(0);
            
            $table->decimal('valor_venda', 10,4)->default(0);
            $table->decimal('valor_compra', 10,4)->default(0);
            $table->boolean('reajuste_automatico')->default(0);
            $table->decimal('percentual_lucro', 10,2)->default(0);
            $table->string('NCM', 13)->default("");
            $table->string('codBarras', 13)->default("");
            $table->string('CEST', 10)->default("");
            $table->string('CST_CSOSN', 3)->default("");
            $table->string('CST_PIS', 3)->default("");
            $table->string('CST_COFINS', 3)->default("");
            $table->string('CST_IPI', 3)->default("");

            $table->string('CST_CSOSN_EXP', 3)->default("");

            $table->string('unidade_compra', 10);
            $table->string('conversao_unitaria')->default(1);
            $table->string('unidade_venda', 10);
            $table->boolean('composto')->default(false);
            $table->boolean('valor_livre');

            $table->decimal('perc_icms', 10,2)->default(0);
            $table->decimal('perc_pis', 10,2)->default(0);
            $table->decimal('perc_cofins', 10,2)->default(0);
            $table->decimal('perc_ipi', 10,2)->default(0);
            $table->decimal('perc_iss', 10,2)->default(0);
            $table->string('cListServ', 5);

            $table->string('CFOP_saida_estadual', 5);
            $table->string('CFOP_saida_inter_estadual', 5);

            $table->string('codigo_anp', 10);
            $table->string('descricao_anp', 95);
            $table->decimal('perc_glp', 5,2)->default(0);
            $table->decimal('perc_gnn', 5,2)->default(0);
            $table->decimal('perc_gni', 5,2)->default(0);
            $table->decimal('valor_partida', 10, 2)->default(0);
            $table->string('unidade_tributavel', 4)->default('');
            $table->decimal('quantidade_tributavel', 10, 2)->default(0);

            $table->string('imagem', 100);
            $table->integer('alerta_vencimento');
            $table->boolean('gerenciar_estoque');
            $table->integer('estoque_minimo')->default(0);
            $table->string('referencia', 25)->default('');

            $table->decimal('pRedBC', 5,2)->default(0);
            $table->string('cBenef',10)->default('');

            $table->decimal('largura', 6, 2)->default(0);
            $table->decimal('comprimento', 6, 2)->default(0);
            $table->decimal('altura', 6, 2)->default(0);
            $table->decimal('peso_liquido', 8, 3)->default(0);
            $table->decimal('peso_bruto', 8, 3)->default(0);
            
            $table->decimal('limite_maximo_desconto', 5, 2)->default(0);

            $table->string('referencia_grade', 20)->default('');
            $table->boolean('grade')->default(false);
            $table->string('str_grade', 20)->default('');

            $table->decimal('perc_icms_interestadual', 10,2)->default(0);
            $table->decimal('perc_icms_interno', 10,2)->default(0);
            $table->decimal('perc_fcp_interestadual', 10,2)->default(0);
            $table->boolean('inativo')->default(false);

            $table->string('renavam', 20)->default('');
            $table->string('placa', 9)->default('');
            $table->string('chassi', 30)->default('');
            $table->string('combustivel', 15)->default('');
            $table->string('ano_modelo', 9)->default('');
            $table->string('cor_veiculo', 20)->default('');

            $table->decimal('valor_locacao', 10,4)->default(0);

            $table->string('lote', 10)->default('');
            $table->string('vencimento', 10)->default('');
            $table->integer('origem')->default(0);

            $table->timestamps();
        });
    }

    // alter table produtos add column perc_glp decimal(5,2) default 0;
    // alter table produtos add column perc_gnn decimal(5,2) default 0;
    // alter table produtos add column perc_gni decimal(5,2) default 0;
    // alter table produtos add column valor_partida decimal(10, 2) default 0;
    // alter table produtos add column unidade_tributavel varchar(4) default '';
    // alter table produtos add column quantidade_tributavel decimal(10, 2) default 0;

    // alter table produtos add column perc_icms_interestadual decimal(10, 2) default 0;
    // alter table produtos add column perc_icms_interno decimal(10, 2) default 0;
    // alter table produtos add column perc_fcp_interestadual decimal(10, 2) default 0;


    // alter table produtos add column renavam varchar(20) default '';
    // alter table produtos add column placa varchar(9) default '';
    // alter table produtos add column chassi varchar(30) default '';
    // alter table produtos add column combustivel varchar(15) default '';
    // alter table produtos add column ano_modelo varchar(9) default '';
    // alter table produtos add column cor_veiculo varchar(20) default '';
    // alter table produtos add column reajuste_automatico boolean default 0;

    // alter table produtos add column valor_locacao decimal(10, 4) default 0;

    
    // alter table produtos add column lote varchar(10) default '';
    // alter table produtos add column vencimento varchar(10) default '';
    // alter table produtos add column origem integer default 0;

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
}
