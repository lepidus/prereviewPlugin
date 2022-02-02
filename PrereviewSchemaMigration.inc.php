<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class PrereviewSchemaMigration extends Migration {
        /**
         * Run the migrations.
         * @return void
         */
        public function up() {


			// prereview_settings
			Capsule::schema()->create('prereview_settings', function (Blueprint $table) {
				$table->bigInteger('publication_id');
				$table->string('setting_name', 255);
				$table->longText('setting_value')->nullable();
				$table->longText('status')->nullable();
				$table->longText('views')->nullable();
			});

		}
}
