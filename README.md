# lara-ses-ext
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mehr-it/lara-ses-ext.svg?style=flat-square)](https://packagist.org/packages/mehr-it/lara-ses-ext)
[![Build Status](https://travis-ci.org/mehr-it/lara-ses-ext.svg?branch=master)](https://travis-ci.org/mehr-it/lara-ses-ext)

This package implements an extended driver for sending mails via AWS SES in Laravel. It dispatches
a custom event on dispatch containing an exact copy of the passed message for archiving purposes.

Additionally a helper exists which transforms status notifications published by SES to 
Laravel events.

## Install

	composer require mehr-it/lara-ses-ext
	
This package uses Laravel's package auto-discovery, so the service provider will be loaded 
automatically.


## Driver configuration
Set the driver option in your `config/mail.php` configuration file to `"ses-ext"` and verify
that your `config/services.php` configuration file contains the following options:

	'ses' => [
        'key'    => 'your-ses-key',
        'secret' => 'your-ses-secret',
        'region' => 'ses-region',  // e.g. us-east-1
    ],
    
## Dispatched event
The `SesMessageDispatched` event is dispatched by the driver right after the message has been
passed to SES. It contains the message id assigned by SES and a copy of the raw message data
which has been passed.

You can use this information to a archive an exact copy of each sent message.

### Adding custom information
Sometimes it might be helpful to have additional information about the sent message, when receiving
the dispatched event. For example if the user ID of the recipient should be stored to the mail
archive.

With the `ses-ext` driver, this can be achieved by using the `InternalMailHeaders` trait in 
mailables and invoking the `withInternalHeader` method from within the `build` function:

    use InternalMailHeaders;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->view('emails.orders.shipped');
    
        $this->withInternalHeader('user-id', $userId);
    }

The internal headers are not sent to SES, but are removed right before sending the message.
    
When handling the `SesMessageDispatched` event, it is simple to retrieve the internal header
values again:

    $userId = $event->getInternalHeaderValue('user-id');

## SES notifications
AWS SES offers notifications to monitor the sending activity. A notification can be received for
bounces, complaints and deliveries. To receive notifications, SES has to be configured to sent
them to an SNS topic. SNS then can pass the notifications to your application, eg. via webhook
or a SQS queue. For further information, see the 
[AWS SES documentation](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/monitor-sending-activity-using-notifications.html).

The receiving of SNS notifications is not in the scope of this library, but the
`SesNotificationHandler` class helps to convert received SES notifications into Laravel events. 
Therefore the raw JSON notification string has to be passed to the handle method:

    $handler = app(SesNotificationHandler::class);
    
    $handler->handle('{"notificationType":"Delivery", "mail":{ ...} }');
    
The handle method parses the JSON and emits the corresponding event using the application
event dispatcher. One of the following events is dispatched based on the notification type:

* `SesMessageDelivered` - when a message has been delivered
* `SesMessageComplained` - when a complaint has been received
* `SesMessageBounced` - when a bounce has happened

The events' getters provide access to all notification information given by SES.


