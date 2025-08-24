<?php
/**
 * @file classes/Igsn/IgsnSchemaMigration.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnSchemaMigration
 * @brief Migration class for IGSN
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IgsnSchemaMigration extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    $this->createTableIgsns();
    $this->createTableIgsnSettings();
  }

  /**
   * Create table igsn.
   *
   * @return void
   */
  private function createTableIgsns(): void
  {
    if (!Schema::hasTable('igsns')) {
      Schema::create('igsns', function (Blueprint $table) {
        $table->bigInteger('igsn_id')->autoIncrement();
        $table->string('igsn_identification', 255);
        $table->bigInteger('publication_id');
        $table->bigInteger('context_id');
      });
    }
  }

  /**
   * Create table igsn_settings.
   */
  private function createTableIgsnSettings(): void
  {
    if (!Schema::hasTable('igsn_settings')) {
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
  }
}
