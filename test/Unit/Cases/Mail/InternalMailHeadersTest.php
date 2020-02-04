<?php

	namespace MehrItLaraSesExtTest\Unit\Cases\Mail;

	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Message;
	use MehrIt\LaraSesExt\Mail\InternalMailHeaders;
	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;
	use Swift_Message;

	class InternalMailHeadersTest extends TestCase
	{

		public function testWithInternalHeader() {

			$swiftMessage = new Swift_Message('Foo subject', 'Bar body');
			$swiftMessage->setSender('myself@example.com');
			$swiftMessage->setTo('me@example.com');
			$swiftMessage->setBcc('you@example.com');

			$mailable = new InternalMailHeadersTestMailable();

			$mailable->build();

			$mailable->doRunCallbacks(new Message($swiftMessage));

			$this->assertSame('my-value-1', $swiftMessage->getHeaders()->get(SesExtTransport::INTERNAL_HEADER_PREFIX . 'my-header')->getFieldBodyModel());
			$this->assertSame('your-value', $swiftMessage->getHeaders()->get(SesExtTransport::INTERNAL_HEADER_PREFIX . 'your-header')->getFieldBodyModel());

		}


	}

	class InternalMailHeadersTestMailable extends Mailable
	{

		use InternalMailHeaders;

		/**
		 * Build the message.
		 *
		 * @return $this
		 */
		public function build() {

			$this->withInternalHeader('my-header', 'my-value-1');
			$this->withInternalHeader('your-header', 'your-value');

			return $this;
		}

		/**
		 * @param Message $message
		 */
		public function doRunCallbacks($message) {
			$this->runCallbacks($message);
		}
	}