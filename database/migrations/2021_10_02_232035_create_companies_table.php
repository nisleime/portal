<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj_cpf', 45)->unique();
            $table->string('corporate_name')->unique(); // razao
            $table->string('email')->nullable();
            $table->string('public_place')->nullable(); // logradouro
            $table->string('home_number', 35)->nullable(); // numero
            $table->string('complement')->nullable(); // complemento
            $table->string('district')->nullable(); // bairro
            $table->string('zip_code')->nullable(); // CEP
            $table->string('county')->nullable(); // municipio
            $table->string('uf', 2)->nullable(); // UF
            $table->string('phone_number', 30)->nullable(); // telefone
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
        Schema::dropIfExists('companies');
    }
}
