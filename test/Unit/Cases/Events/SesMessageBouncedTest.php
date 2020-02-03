<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Events;


	use MehrIt\LaraSesExt\Events\SesMessageBounced;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesMessageBouncedTest extends TestCase
	{
		public function testConstructGetters() {

			$body = '{
      "notificationType":"Bounce",
      "mail":{
         "timestamp":"2016-01-27T13:59:38.237Z",
         "messageId":"0000014644fe5ef6-9a483358-9170-4cb4-a269-f5dcdf415321-000000",
         "source":"john@example.com",
         "sourceArn": "arn:aws:ses:us-west-2:888888888888:identity/example.com",
         "sourceIp": "127.0.3.0",
         "sendingAccountId":"123456789012",
         "destination":[
            "jane@example.com"
         ], 
          "headersTruncated":false,
          "headers":[ 
           { 
              "name":"From",
              "value":"\"John Doe\" <john@example.com>"
           },
           { 
              "name":"To",
              "value":"\"Jane Doe\" <jane@example.com>"
           },
           { 
              "name":"Message-ID",
              "value":"custom-message-ID"
           }
          ],
          "commonHeaders":{ 
            "from":[ 
               "John Doe <john@example.com>"
            ],
            "date":"Wed, 27 Jan 2016 14:58:45 +0000",
            "to":[ 
               "Jane Doe <jane@example.com>"
            ],
            "messageId":"custom-message-ID",
            "subject":"Hello"
          }
       },
      "bounce":{
          "bounceType":"Permanent",
          "reportingMTA":"dns; email.example.com",
          "bouncedRecipients":[
             {
                "emailAddress":"jane@example.com",
                "status":"5.1.1",
                "action":"failed",
                "diagnosticCode":"smtp; 550 5.1.1 <jane@example.com>... User"
             }
          ],
          "bounceSubType":"General",
          "timestamp":"2016-01-27T14:59:38.237Z",
          "feedbackId":"00000138111222aa-33322211-cccc-cccc-cccc-ddddaaaa068a-000000",
          "remoteMtaIp":"127.0.2.0"
       }
   }';

			$bodyData = json_decode($body, true);


			$event = new SesMessageBounced($bodyData['mail'], $bodyData['bounce']);

			$this->assertSame('0000014644fe5ef6-9a483358-9170-4cb4-a269-f5dcdf415321-000000', $event->getMessageId());
			$this->assertSame(strtotime('2016-01-27T13:59:38.237Z'), $event->getMessageSentTimestamp()->getTimestamp());
			$this->assertSame('john@example.com', $event->getMessageFrom());
			$this->assertSame('arn:aws:ses:us-west-2:888888888888:identity/example.com', $event->getMessageSourceArn());
			$this->assertSame('127.0.3.0', $event->getMessageSourceIp());
			$this->assertSame('123456789012', $event->getMessageSendingAccountId());
			$this->assertSame(['jane@example.com'], $event->getMessageDestination());
			$this->assertSame(false, $event->getMessageHeadersTruncated());
			$this->assertSame([
				[
					'name'  => 'From',
					'value' => '"John Doe" <john@example.com>',
				],
				[
					'name'  => 'To',
					'value' => '"Jane Doe" <jane@example.com>',
				],
				[
					'name'  => 'Message-ID',
					'value' => 'custom-message-ID',
				],
			], $event->getMessageHeaders());
			$this->assertSame([
				'from'      => [
					'John Doe <john@example.com>'
				],
				'date'      => 'Wed, 27 Jan 2016 14:58:45 +0000',
				'to'        => [
					'Jane Doe <jane@example.com>',
				],
				'messageId' => 'custom-message-ID',
				'subject'   => 'Hello',
			], $event->getMessageCommonHeaders());

			$this->assertSame(strtotime('2016-01-27T14:59:38.237Z'), $event->getBounceTimestamp()->getTimestamp());
			$this->assertSame('Permanent', $event->getBounceType());
			$this->assertSame('General', $event->getBounceSubType());
			$this->assertSame('dns; email.example.com', $event->getBounceReportingMta());
			$this->assertSame('00000138111222aa-33322211-cccc-cccc-cccc-ddddaaaa068a-000000', $event->getBounceFeedbackId());
			$this->assertSame('127.0.2.0', $event->getBounceRemoteMtaIp());
			$this->assertSame([
				[
					'emailAddress'   => 'jane@example.com',
					'status'         => '5.1.1',
					'action'         => 'failed',
					'diagnosticCode' => 'smtp; 550 5.1.1 <jane@example.com>... User',
				]
			], $event->getBouncedRecipients());

		}

		public function testToArray() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageBounced($mailData, $notificationData);

			$this->assertSame([
				'notificationType' => 'Bounce',
				'mail'             => $mailData,
				'bounce'           => $notificationData,
			], $event->toArray());

		}

		public function testJsonSerialize() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageBounced($mailData, $notificationData);

			$this->assertSame([
				'notificationType' => 'Bounce',
				'mail'             => $mailData,
				'bounce'           => $notificationData,
			], $event->jsonSerialize());

		}

		public function testGetMessageData() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageBounced($mailData, $notificationData);

			$this->assertSame($mailData, $event->getMessageData());

		}

		public function testGetDeliveryData() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageBounced($mailData, $notificationData);

			$this->assertSame($notificationData, $event->getBounceData());

		}
	}