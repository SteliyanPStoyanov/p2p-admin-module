<?php

use Modules\Core\Services\CacheService;

//////////////////////////////////////////////////

Artisan::command('project:init', function () {

	if (isProdOrStage()) {
		$this->info('------- !!! IMPORTANT !!! -------');
		$this->error('-- You are on stage/production --');
		$this->error('--    Run commands manually    --');
		$this->info('---------------------------------');
		return ;
	}

	$actions = [
		'cmd-before' => [
		],
		'artisan' => [
			'clear:all-simple' => 'All cache is cleared',
			'migrate:fresh --seed' => 'DB re-created & seeds imported',
			'session:flush' => 'Redis/Session is cleared',
			'storage:link' => 'Storage link is set',
			'horizon:install' => 'Instaled Horison a tool for queues monitoring',
		],
		'cmd-after' => [
			'composer dump-autoload 2>/dev/null' => 'Update composer autoload file',
		],
	];

	$onlyForDev = [
		'migrate:fresh --seed',
		'session:flush',
	];

	$this->info('Starting clean project set-up');
	$operationCount = count($actions, COUNT_RECURSIVE) - count(array_keys($actions));
	$bar = $this->output->createProgressBar($operationCount);
	$bar->start();

	foreach ($actions as $type => $commands) {
		foreach ($commands as $command => $description) {

			if (isProdOrStage() && in_array($command, $onlyForDev)) {
				sleep(1);
				$bar->advance();
				$this->error(' - SKIPPED: ' . $description);
				continue;
			}

			switch ($type) {
				case 'artisan':
					Artisan::call($command);
					break;
				case 'cmd-before':
				case 'cmd-after':
					exec($command);
					break;
			}

			// for fast commands we need to sleep a bit, to show progress bar
			if (!preg_match('/(migrate)/', $command)) {
				sleep(1);
			}

			$bar->advance();
			$this->info(' - ' . $description);
		}
	}

	Artisan::call('optimize');

	$bar->finish();
	$this->info('-----------------------');
	$this->info('Project is ready for use');
	$this->info('');

})->describe('Running project setup commands');


//////////////////////////////////////////////////

Artisan::command('redis:flushall', function () {
    Session::flush();
	Redis::flushAll();
})->describe('Will remove all user session');


//////////////////////////////////////////////////

Artisan::command('session:flush', function () {
    Redis::select(config('database.redis.session.database'));
    Redis::flushdb();
})->describe('Will remove all user session');


//////////////////////////////////////////////////

Artisan::command('jobs:flush', function () {
    Artisan::call("queue:failed");
    echo Artisan::output();

    Artisan::call("queue:flush");
})->describe('Will remove all failed jobs');

//////////////////////////////////////////////////



Artisan::command('clear:all', function () {
	Artisan::call('view:clear');
	Artisan::call('route:clear');
	Artisan::call('optimize');
	Artisan::call('config:clear');
})->describe('Will remove all user session');

//////////////////////////////////////////////////

Artisan::command('clear:all-simple', function () {
	Artisan::call('view:clear');
	Artisan::call('config:clear');
	Artisan::call('route:clear');
})->describe('Will remove all user session');
