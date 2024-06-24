<?php

namespace APP\plugins\generic\pidManager\classes\Igsn;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IgsnSchemaMigration extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        error_log('IgsnSchemaMigration->up()');

        # igsn
        if (!Schema::hasTable('igsns')) {
            $this->createTableIgsns();
        } else {
            $this->updateTableIgsns();
        }

        // igsn_settings
        if (!Schema::hasTable('igsn_settings')) {
            $this->createTableIgsnSettings();
        } else {
            $this->updateTableIgsnSettings();
        }
    }

    private function createTableIgsns()
    {
        Schema::create('igsns', function (Blueprint $table) {
            $table->bigInteger('igsn_id')->autoIncrement();
            $table->string('igsn_identification', 255);
            $table->bigInteger('publication_id');
            $table->bigInteger('context_id');
        });
    }

    private function updateTableIgsns()
    {
//        Schema::table('igsns', function (Blueprint $table) {
//            $table->bigInteger('igsn_id')->autoIncrement();
//            $table->string('igsn_identification', 255);
//            $table->bigInteger('publication_id');
//            $table->bigInteger('context_id');
//        });
    }

    private function createTableIgsnSettings()
    {
        Schema::create('igsn_settings', function (Blueprint $table) {
            $table->bigIncrements('igsn_setting_id');
            $table->bigInteger('igsn_id');
            $table->string('locale', 14)->default('');
            $table->string('setting_name', 255);
            $table->mediumText('setting_value')->nullable();
            $table->index(['igsn_id'], 'igsn_settings_id');
            $table->unique(['igsn_id', 'locale', 'setting_name'], 'igsn_settings_pkey');
        });
    }

    private function updateTableIgsnSettings()
    {
//        Schema::table('igsn_settings', function (Blueprint $table) {
//            $table->bigIncrements('igsn_setting_id');
//            $table->bigInteger('igsn_id');
//            $table->string('locale', 14)->default('');
//            $table->string('setting_name', 255);
//            $table->mediumText('setting_value')->nullable();
//            $table->index(['igsn_id'], 'igsn_settings_id');
//            $table->unique(['igsn_id', 'locale', 'setting_name'], 'igsn_settings_pkey');
//        });
    }
}
