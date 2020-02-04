<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Events;


	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesMessageDispatchedTest extends TestCase
	{

		public function testConstructorGetters() {

			$internalHeaders = [
				'my-header'   => 'v1',
				'your-header' => 'v2',
			];

			$event = new SesMessageDispatched('my-message', '12309-3214', $internalHeaders);

			$this->assertSame('my-message', $event->getMessage());
			$this->assertSame('12309-3214', $event->getSesMessageId());
			$this->assertSame($internalHeaders, $event->getInternalHeaders());

		}

		public function testGetInternalHeaderValue() {

			$internalHeaders = [
				'my-header'   => 'v1',
				'your-header' => 'v2',
			];

			$event = new SesMessageDispatched('my-message', '12309-3214', $internalHeaders);

			$this->assertSame('v1', $event->getInternalHeaderValue('my-header'));
			$this->assertSame('v2', $event->getInternalHeaderValue('your-header'));
			$this->assertSame(null, $event->getInternalHeaderValue('not-existing'));

		}

	}