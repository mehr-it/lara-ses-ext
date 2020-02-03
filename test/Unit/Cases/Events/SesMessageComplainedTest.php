<?php


	namespace MehrItLaraSesExtTest\Unit\Cases\Events;


	use MehrIt\LaraSesExt\Events\SesMessageComplained;
	use MehrItLaraSesExtTest\Unit\Cases\TestCase;

	class SesMessageComplainedTest extends TestCase
	{
		public function testConstructGetters() {

			$body = '{
      "notificationType":"Complaint",
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
       "complaint":{
         "userAgent":"AnyCompany Feedback Loop (V0.01)",
         "complainedRecipients":[
            {
               "emailAddress":"richard@example.com"
            }
         ],
         "complaintFeedbackType":"abuse",
         "arrivalDate":"2016-01-27T18:59:38.237Z",
         "timestamp":"2016-01-27T14:59:38.237Z",
         "feedbackId":"000001378603177f-18c07c78-fa81-4a58-9dd1-fedc3cb8f49a-000000"
      }
   }';

			$bodyData = json_decode($body, true);


			$event = new SesMessageComplained($bodyData['mail'], $bodyData['complaint']);

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

			$this->assertSame(strtotime('2016-01-27T14:59:38.237Z'), $event->getComplaintTimestamp()->getTimestamp());
			$this->assertSame(strtotime('2016-01-27T18:59:38.237Z'), $event->getComplaintArrivalDate()->getTimestamp());
			$this->assertSame([['emailAddress' => 'richard@example.com']], $event->getComplainedRecipients());
			$this->assertSame('abuse', $event->getComplaintFeedbackType());
			$this->assertSame('000001378603177f-18c07c78-fa81-4a58-9dd1-fedc3cb8f49a-000000', $event->getComplaintFeedbackId());
			$this->assertSame('AnyCompany Feedback Loop (V0.01)', $event->getComplaintUserAgent());

		}

		public function testToArray() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageComplained($mailData, $notificationData);

			$this->assertSame([
				'notificationType' => 'Complaint',
				'mail'             => $mailData,
				'complaint'         => $notificationData,
			], $event->toArray());

		}

		public function testJsonSerialize() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageComplained($mailData, $notificationData);

			$this->assertSame([
				'notificationType' => 'Complaint',
				'mail'             => $mailData,
				'complaint'        => $notificationData,
			], $event->jsonSerialize());

		}

		public function testGetMessageData() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageComplained($mailData, $notificationData);

			$this->assertSame($mailData, $event->getMessageData());

		}

		public function testGetDeliveryData() {

			$mailData         = ['messageId' => 'my-message-id'];
			$notificationData = ['timestamp' => '2016-01-27T14:59:38.237Z'];

			$event = new SesMessageComplained($mailData, $notificationData);

			$this->assertSame($notificationData, $event->getComplaintData());

		}
	}