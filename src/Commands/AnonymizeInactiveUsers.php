<?php

namespace Dialect\Gdpr\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class AnonymizeInactiveUsers extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'gdpr:anonymizeInactiveUsers';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize inactive users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$models = config('gdpr.settings.user_model_fqns', 'App\User');

    	if (! is_array($models)) {
			$models = [$models];
		}

    	collect($models)
			->each(function ($model) {
				$user = new $model();

				$anonymizableUsers = $user::where('last_activity', '!=', null)
					->where('is_anonymized', 0)
					->where('last_activity', '<=', Carbon::now()->subMonths(config('gdpr.settings.ttl')))->get();

				foreach ($anonymizableUsers as $user) {
					$user->anonymize();
					$user->update([
						'is_anonymized' => true,
					]);
				}
			});


    }
}
