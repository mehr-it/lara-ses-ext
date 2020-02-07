<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Transport;


	use Aws\Ses\SesClient;
	use Illuminate\Support\Facades\Event;
	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use MehrIt\LaraSesExt\Transport\SesExtSimulationTransport;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;
	use Swift_Message;

	class SesExtSimulationTransportTest extends TestCase {

		public function testSend() {
			$message = new Swift_Message('Foo subject', 'Bar body');
			$message->setSender('myself@example.com');
			$message->setTo('me@example.com');
			$message->setBcc('you@example.com');

			$messageBody = $message->toString();

			Event::fake();

			$builder = $this->getMockBuilder(SesClient::class);
			if (method_exists($builder, 'addMethods')) {
				$client = $builder->addMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}
			else {
				$client = $builder->setMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}

			$transport = new SesExtSimulationTransport($client, Event::getFacadeRoot());


			$client->expects($this->never())
				->method('sendRawEmail');

			$transport->send($message);

			$messageId = $message->getHeaders()->get('X-SES-Message-ID')->getFieldBody();

			$this->assertRegExp('/^[0-9a-f]{16}-[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}-[0]{6}$/', $messageId);


			Event::assertDispatched(SesMessageDispatched::class, function (SesMessageDispatched $event) use ($messageBody, $messageId) {

				$this->assertSame('myself@example.com', $event->getSenderAddress());
				$this->assertSame('', $event->getMailFromAddress());
				$this->assertSame('me@example.com', $event->getMailToAddress());
				$this->assertSame('Foo subject', $event->getSubject());

				$this->assertSame($messageId, $event->getSesMessageId());
				$this->assertSame($messageBody, $event->getMessage());

				return true;
			});
		}

		public function testSend_withNamedAndMultipleAddress() {
			$message = new Swift_Message('Foo subject', 'Bar body');
			$message->setSender('myself@example.com', 'My name');
			$message->setTo(['me@example.com' => 'My name', 'you@example.com' => 'Your name', 'other@example.com']);
			$message->setFrom(['meAndYou@example.com' => 'My name']);
			$message->setBcc('you@example.com');

			$messageBody = $message->toString();

			Event::fake();

			$builder = $this->getMockBuilder(SesClient::class);
			if (method_exists($builder, 'addMethods')) {
				$client = $builder->addMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}
			else {
				$client = $builder->setMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}

			$client->expects($this->never())
					->method('sendRawEmail');

			$transport = new SesExtSimulationTransport($client, Event::getFacadeRoot());

			$transport->send($message);

			$messageId = $message->getHeaders()->get('X-SES-Message-ID')->getFieldBody();

			$this->assertRegExp('/^[0-9a-f]{16}-[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}-[0]{6}$/', $messageId);


			Event::assertDispatched(SesMessageDispatched::class, function (SesMessageDispatched $event) use ($messageBody, $messageId) {

				$this->assertSame('myself@example.com', $event->getSenderAddress());
				$this->assertSame('meAndYou@example.com', $event->getMailFromAddress());
				$this->assertSame('me@example.com', $event->getMailToAddress());
				$this->assertSame('Foo subject', $event->getSubject());

				$this->assertSame($messageId, $event->getSesMessageId());
				$this->assertSame($messageBody, $event->getMessage());

				return true;
			});
		}

			public function testSend_withInternalHeaders() {
			$message = new Swift_Message('Foo subject', 'Bar body');
			$message->setSender('myself@example.com');
			$message->setTo('me@example.com');
			$message->setBcc('you@example.com');

			$messageBody = $message->toString();


			// add internal headers
			$message->getHeaders()->addTextHeader(SesExtTransport::INTERNAL_HEADER_PREFIX . 'my-header', 'v1');
			$message->getHeaders()->addTextHeader(strtoupper(SesExtTransport::INTERNAL_HEADER_PREFIX . 'your-header'), 'v2');


			Event::fake();

			$builder = $this->getMockBuilder(SesClient::class);
			if (method_exists($builder, 'addMethods')) {
				$client = $builder->addMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}
			else {
				$client = $builder->setMethods(['sendRawEmail'])
					->disableOriginalConstructor()
					->getMock();
			}

				$client->expects($this->never())
					->method('sendRawEmail');

				$transport = new SesExtSimulationTransport($client, Event::getFacadeRoot());

				$transport->send($message);

				$messageId = $message->getHeaders()->get('X-SES-Message-ID')->getFieldBody();

				$this->assertRegExp('/^[0-9a-f]{16}-[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}-[0]{6}$/', $messageId);


			Event::assertDispatched(SesMessageDispatched::class, function (SesMessageDispatched $event) use ($messageBody, $messageId) {

				$this->assertSame($messageId, $event->getSesMessageId());
				$this->assertSame($messageBody, $event->getMessage());
				$this->assertSame('myself@example.com', $event->getSenderAddress());
				$this->assertSame('', $event->getMailFromAddress());
				$this->assertSame('me@example.com', $event->getMailToAddress());
				$this->assertSame('Foo subject', $event->getSubject());

				$internalHeaders = $event->getInternalHeaders();
				$this->assertSame('v1', $internalHeaders['my-header']);
				$this->assertSame('v2', $internalHeaders['your-header']);
				$this->assertCount(2, $internalHeaders);

				return true;
			});
		}

	}
