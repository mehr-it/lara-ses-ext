<?php


	namespace MehrIt\LaraSesExt\Provider;


	use Aws\Ses\SesClient;
	use Illuminate\Mail\TransportManager;
	use Illuminate\Support\Arr;
	use Illuminate\Support\ServiceProvider;
	use MehrIt\LaraSesExt\SesNotificationHandler;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;

	class SesExtServiceProvider extends ServiceProvider
	{
		public $singletons = [
			SesNotificationHandler::class => SesNotificationHandler::class,
		];

		public function boot() {
			/** @var TransportManager $manager */
			$manager = $this->app['swift.transport'];

			$manager->extend('ses-ext', function () {
				$config = array_merge(
					$this->app->make('config')->get('services.ses', []),
					[
						'version' => 'latest',
						'service' => 'email',
					]
				);

				if (!empty($config['key']) && !empty($config['secret']))
					$config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);

				return new SesExtTransport(
					new SesClient($config),
					$this->app->make('events'),
					$config['options'] ?? []
				);
			});
		}


	}