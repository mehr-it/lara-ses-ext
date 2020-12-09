<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Provider;


	use Illuminate\Mail\MailManager;
	use MehrIt\LaraSesExt\SesNotificationHandler;
	use MehrIt\LaraSesExt\Transport\SesExtSimulationTransport;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesExtServiceProviderTest extends TestCase
	{
		/**
		 * @inheritDoc
		 */
		protected function getEnvironmentSetUp($app) {

			$app['config']->set('mail.mailers.ses-ext-mailer', ['transport' => 'ses-ext']);
			$app['config']->set('mail.mailers.ses-ext-simulation-mailer', ['transport' => 'ses-ext-simulation']);
		}

		protected function useSesExtMailer($app) {
			$app['config']->set('mail.default', 'ses-ext-mailer');

		}

		protected function useSesSimulationExtMailer($app) {
			$app['config']->set('mail.default', 'ses-ext-simulation-mailer');

		}

		public function testSesNotificationHandlerRegistration() {

			$handler = app(SesNotificationHandler::class);
			$this->assertInstanceOf(SesNotificationHandler::class, $handler);
			$this->assertSame($handler, app(SesNotificationHandler::class));

		}

		/**
		 * @environment-setup useSesExtMailer
		 */
		public function testSesExtTransportRegistered() {

			/** @var MailManager $manager */
			$manager = app('mail.manager');

			$this->assertInstanceOf(SesExtTransport::class, $manager->getSwiftMailer()->getTransport());

		}

		/**
		 * @environment-setup useSesSimulationExtMailer
		 */
		public function testSesExtSimulationTransportRegistered() {

			/** @var MailManager $manager */
			$manager = app('mail.manager');

			$this->assertInstanceOf(SesExtSimulationTransport::class, $manager->getSwiftMailer()->getTransport());

		}

	}