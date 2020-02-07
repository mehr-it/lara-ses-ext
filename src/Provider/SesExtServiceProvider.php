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

			// register driver
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

			// register dummy driver
			$manager->extend('ses-ext-simulation', function () {

				return new SesExtTransport(
					new SesClient([
						// this are just the required options to allow creating a instance (it is never used)
						'version' => 'latest',
						'service' => 'email',
						'region'  => 'eu-central-1',
					]),
					$this->app->make('events'),
					$config['options'] ?? []
				);
			});
		}


	}