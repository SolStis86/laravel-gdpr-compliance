<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddGdprToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$models = config('gdpr.settings.user_model_fqns', 'App\User');

		if (! is_array($models)) {
			$models = [$models];
		}

		collect($models)
			->each(function ($model) {
				$table = (new $model())->getTable();

				Schema::table($table, function (Blueprint $table) {
					$table->dateTime('last_activity')->nullable()->default(null);
					$table->boolean('accepted_gdpr')->nullable()->default(null);
					$table->boolean('is_anonymized')->default(false);
				});
			});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		$models = config('gdpr.settings.user_model_fqns', 'App\User');

		if (! is_array($models)) {
			$models = [$models];
		}

		collect($models)
			->each(function ($model) {
				$table = (new $model())->getTable();

				Schema::table($table, function (Blueprint $table) {
					$table->dropColumn('last_activity');
					$table->dropColumn('accepted_gdpr');
					$table->dropColumn('is_anonymized');
				});
			});


    }
}
