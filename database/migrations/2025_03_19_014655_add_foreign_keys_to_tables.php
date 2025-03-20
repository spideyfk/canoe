<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('fund_managers')->onDelete('cascade');
        });
        Schema::table('aliases', function (Blueprint $table) {
            $table->foreign('fund_id')->references('id')->on('funds')->onDelete('cascade');
        });
        Schema::table('company_fund', function (Blueprint $table) {
            $table->foreign('fund_id')->references('id')->on('funds')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });

        Schema::table('aliases', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
        });

        Schema::table('company_fund', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
            $table->dropForeign(['company_id']);
        });
    }
};
