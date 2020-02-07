<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Provider;


	use Illuminate\Mail\TransportManager;
	use MehrIt\LaraSesExt\SesNotificationHandler;
	use MehrIt\LaraSesExt\Transport\SesExtSimulationTransport;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesExtServiceProviderTest extends TestCase
	{

		public function testSesNotificationHandlerRegistration() {

			$handler = app(SesNotificationHandler::class);
			$this->assertInstanceOf(SesNotificationHandler::class, $handler);
			$this->assertSame($handler, app(SesNotificationHandler::class));

		}

		public function testSesExtDriverRegistered() {

			/** @var TransportManager $manager */
			$manager = app('swift.transport');

			$this->assertInstanceOf(SesExtTransport::class, $manager->driver('ses-ext'));

		}

		public function testSesExtSimulationDriverRegistered() {

			/** @var TransportManager $manager */
			$manager = app('swift.transport');

			$this->assertInstanceOf(SesExtSimulationTransport::class, $manager->driver('ses-ext-simulation'));

		}

	}