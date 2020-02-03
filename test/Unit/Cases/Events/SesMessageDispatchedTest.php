<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Events;


	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesMessageDispatchedTest extends TestCase
	{

		public function testConstructorGetters() {

			$event = new SesMessageDispatched('my-message', '12309-3214');

			$this->assertSame('my-message', $event->getMessage());
			$this->assertSame('12309-3214', $event->getSesMessageId());

		}

	}