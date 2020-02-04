<?php


	namespace MehrItLaraSesExtTest\Unit\Cases;


	use Illuminate\Support\Facades\Event;
	use InvalidArgumentException;
	use MehrIt\LaraSesExt\Events\SesMessageBounced;
	use MehrIt\LaraSesExt\Events\SesMessageComplained;
	use MehrIt\LaraSesExt\Events\SesMessageDelivered;
	use MehrIt\LaraSesExt\Exception\UnknownSesNotificationTypeException;
	use MehrIt\LaraSesExt\SesNotificationHandler;

	class SesNotificationHandlerTest extends TestCase
	{


		public function testDeliveryNotification() {

			$body = '{
      "notificationType":"Delivery",
      "mail":{
         "timestamp":"2016-01-27T14:59:38.237Z",
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
           },
           { 
              "name":"Subject",
              "value":"Hello"
           },
           { 
              "name":"Content-Type",
              "value":"text/plain; charset=\"UTF-8\""
           },
           { 
              "name":"Content-Transfer-Encoding",
              "value":"base64"
           },
           { 
              "name":"Date",
              "value":"Wed, 27 Jan 2016 14:58:45 +0000"
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
      "delivery":{
         "timestamp":"2016-01-27T14:59:38.237Z",
         "recipients":["jane@example.com"],
         "processingTimeMillis":546,     
         "reportingMTA":"a8-70.smtp-out.amazonses.com",
         "smtpResponse":"250 ok:  Message 64111812 accepted",
         "remoteMtaIp":"127.0.2.0"
      } 
   }';

			Event::fake();

			$handler = new SesNotificationHandler(Event::getFacadeRoot());

			$handler->handle($body);

			Event::assertDispatched(SesMessageDelivered::class, function(SesMessageDelivered $event) {

				$this->assertSame('0000014644fe5ef6-9a483358-9170-4cb4-a269-f5dcdf415321-000000', $event->getMessageId());
				$this->assertSame('250 ok:  Message 64111812 accepted', $event->getDeliverySmtpResponse());

				return true;
			});
			Event::assertNotDispatched(SesMessageComplained::class);
			Event::assertNotDispatched(SesMessageBounced::class);

		}

		public function testComplaintNotification() {

			$body = '{
      "notificationType":"Complaint",
      "complaint":{
         "userAgent":"AnyCompany Feedback Loop (V0.01)",
         "complainedRecipients":[
            {
               "emailAddress":"richard@example.com"
            }
         ],
         "complaintFeedbackType":"abuse",
         "arrivalDate":"2016-01-27T14:59:38.237Z",
         "timestamp":"2016-01-27T14:59:38.237Z",
         "feedbackId":"000001378603177f-18c07c78-fa81-4a58-9dd1-fedc3cb8f49a-000000"
      },
      "mail":{
         "timestamp":"2016-01-27T14:59:38.237Z",
         "messageId":"000001378603177f-7a5433e7-8edb-42ae-af10-f0181f34d6ee-000000",
         "source":"john@example.com",
         "sourceArn": "arn:aws:ses:us-west-2:888888888888:identity/example.com",
         "sourceIp": "127.0.3.0",
         "sendingAccountId":"123456789012",
         "destination":[
            "jane@example.com",
            "mary@example.com",
            "richard@example.com"
         ], 
          "headersTruncated":false,
          "headers":[ 
           { 
             "name":"From",
             "value":"\"John Doe\" <john@example.com>"
           },
           { 
             "name":"To",
             "value":"\"Jane Doe\" <jane@example.com>, \"Mary Doe\" <mary@example.com>, \"Richard Doe\" <richard@example.com>"
           },
           { 
             "name":"Message-ID",
             "value":"custom-message-ID"
           },
           { 
             "name":"Subject",
             "value":"Hello"
           },
           { 
             "name":"Content-Type",
             "value":"text/plain; charset=\"UTF-8\""
           },
           { 
             "name":"Content-Transfer-Encoding",
             "value":"base64"
           },
           { 
             "name":"Date",
             "value":"Wed, 27 Jan 2016 14:05:45 +0000"
           }
         ],
         "commonHeaders":{ 
           "from":[ 
              "John Doe <john@example.com>"
           ],
           "date":"Wed, 27 Jan 2016 14:05:45 +0000",
           "to":[ 
              "Jane Doe <jane@example.com>, Mary Doe <mary@example.com>, Richard Doe <richard@example.com>"
           ],
           "messageId":"custom-message-ID",
           "subject":"Hello"
         }
      }
   }';

			Event::fake();

			$handler = new SesNotificationHandler(Event::getFacadeRoot());

			$handler->handle($body);

			Event::assertDispatched(SesMessageComplained::class, function(SesMessageComplained $event) {

				$this->assertSame('000001378603177f-7a5433e7-8edb-42ae-af10-f0181f34d6ee-000000', $event->getMessageId());
				$this->assertSame('abuse', $event->getComplaintFeedbackType());

				return true;
			});
			Event::assertNotDispatched(SesMessageDelivered::class);
			Event::assertNotDispatched(SesMessageBounced::class);

		}

		public function testBounceNotification() {

			$body = '   {
       "notificationType":"Bounce",
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
       },
       "mail":{
          "timestamp":"2016-01-27T14:59:38.237Z",
          "source":"john@example.com",
          "sourceArn": "arn:aws:ses:us-west-2:888888888888:identity/example.com",
          "sourceIp": "127.0.3.0",
          "sendingAccountId":"123456789012",
          "messageId":"00000138111222aa-33322211-cccc-cccc-cccc-ddddaaaa0680-000000",
          "destination":[
            "jane@example.com",
            "mary@example.com",
            "richard@example.com"],
          "headersTruncated":false,
          "headers":[ 
           { 
             "name":"From",
             "value":"\"John Doe\" <john@example.com>"
           },
           { 
             "name":"To",
             "value":"\"Jane Doe\" <jane@example.com>, \"Mary Doe\" <mary@example.com>, \"Richard Doe\" <richard@example.com>"
           },
           { 
             "name":"Message-ID",
             "value":"custom-message-ID"
           },
           { 
             "name":"Subject",
             "value":"Hello"
           },
           { 
             "name":"Content-Type",
             "value":"text/plain; charset=\"UTF-8\""
           },
           { 
             "name":"Content-Transfer-Encoding",
             "value":"base64"
           },
           { 
             "name":"Date",
             "value":"Wed, 27 Jan 2016 14:05:45 +0000"
           }
          ],
          "commonHeaders":{ 
             "from":[ 
                "John Doe <john@example.com>"
             ],
             "date":"Wed, 27 Jan 2016 14:05:45 +0000",
             "to":[ 
                "Jane Doe <jane@example.com>, Mary Doe <mary@example.com>, Richard Doe <richard@example.com>"
             ],
             "messageId":"custom-message-ID",
             "subject":"Hello"
           }
        }
    }';

			Event::fake();

			$handler = new SesNotificationHandler(Event::getFacadeRoot());

			$handler->handle($body);

			Event::assertDispatched(SesMessageBounced::class, function(SesMessageBounced $event) {

				$this->assertSame('00000138111222aa-33322211-cccc-cccc-cccc-ddddaaaa0680-000000', $event->getMessageId());
				$this->assertSame('Permanent', $event->getBounceType());

				return true;
			});
			Event::assertNotDispatched(SesMessageDelivered::class);
			Event::assertNotDispatched(SesMessageComplained::class);

		}



		public function testUnknownNotification() {

			$body = '   {
       "notificationType":"Unknown",
       "mail":{
          "timestamp":"2016-01-27T14:59:38.237Z",
          "source":"john@example.com",
          "sourceArn": "arn:aws:ses:us-west-2:888888888888:identity/example.com",
          "sourceIp": "127.0.3.0",
          "sendingAccountId":"123456789012",
          "messageId":"00000138111222aa-33322211-cccc-cccc-cccc-ddddaaaa0680-000000",
          "destination":[
            "jane@example.com",
            "mary@example.com",
            "richard@example.com"],
          "headersTruncated":false,
          "headers":[ 
           { 
             "name":"From",
             "value":"\"John Doe\" <john@example.com>"
           },
           { 
             "name":"To",
             "value":"\"Jane Doe\" <jane@example.com>, \"Mary Doe\" <mary@example.com>, \"Richard Doe\" <richard@example.com>"
           },
           { 
             "name":"Message-ID",
             "value":"custom-message-ID"
           },
           { 
             "name":"Subject",
             "value":"Hello"
           },
           { 
             "name":"Content-Type",
             "value":"text/plain; charset=\"UTF-8\""
           },
           { 
             "name":"Content-Transfer-Encoding",
             "value":"base64"
           },
           { 
             "name":"Date",
             "value":"Wed, 27 Jan 2016 14:05:45 +0000"
           }
          ],
          "commonHeaders":{ 
             "from":[ 
                "John Doe <john@example.com>"
             ],
             "date":"Wed, 27 Jan 2016 14:05:45 +0000",
             "to":[ 
                "Jane Doe <jane@example.com>, Mary Doe <mary@example.com>, Richard Doe <richard@example.com>"
             ],
             "messageId":"custom-message-ID",
             "subject":"Hello"
           }
        }
    }';

			Event::fake();

			$handler = new SesNotificationHandler(Event::getFacadeRoot());

			$this->expectException(UnknownSesNotificationTypeException::class);

			$handler->handle($body);


		}


		public function testInvalidJson() {

			$body = 'this is not json';

			Event::fake();

			$handler = new SesNotificationHandler(Event::getFacadeRoot());

			$this->expectException(InvalidArgumentException::class);

			$handler->handle($body);


		}

	}