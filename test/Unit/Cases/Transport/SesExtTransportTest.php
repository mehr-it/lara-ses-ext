<?php

	namespace MehrItLaraSesExtTest\Unit\Cases\Transport;

	use Aws\Ses\SesClient;
	use Illuminate\Support\Facades\Event;
	use Illuminate\Support\Str;
	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;
	use Swift_Message;

	class SesExtTransportTest extends TestCase
	{


		public function testSend() {
			$message = new Swift_Message('Foo subject', 'Bar body');
			$message->setSender('myself@example.com');
			$message->setTo('me@example.com');
			$message->setBcc('you@example.com');

			$messageBody = $message->toString();

			Event::fake();

			$client    = $this->getMockBuilder(SesClient::class)
				->addMethods(['sendRawEmail'])
				->disableOriginalConstructor()
				->getMock();
			$transport = new SesExtTransport($client, Event::getFacadeRoot());

			// Generate a messageId for our mock to return to ensure that the post-sent message
			// has X-SES-Message-ID in its headers
			$messageId        = Str::random(32);
			$sendRawEmailMock = new sendRawEmailMock($messageId);
			$client->expects($this->once())
				->method('sendRawEmail')
				->with($this->equalTo([
					'Source'     => 'myself@example.com',
					'RawMessage' => ['Data' => (string)$message],
				]))
				->willReturn($sendRawEmailMock);

			$transport->send($message);
			$this->assertEquals($messageId, $message->getHeaders()->get('X-SES-Message-ID')->getFieldBody());


			Event::assertDispatched(SesMessageDispatched::class, function (SesMessageDispatched $event) use ($messageBody, $messageId) {

				$this->assertSame($messageId, $event->getSesMessageId());
				$this->assertSame($messageBody, $event->getMessage());

				return true;
			});
		}

	}

	class sendRawEmailMock
	{
		protected $getResponse;

		public function __construct($responseValue) {
			$this->getResponse = $responseValue;
		}

		/**
		 * Mock the get() call for the sendRawEmail response.
		 * @param  [type] $key [description]
		 * @return [type]      [description]
		 */
		public function get($key) {
			return $this->getResponse;
		}
	}