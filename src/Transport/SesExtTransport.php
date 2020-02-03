<?php


	namespace MehrIt\LaraSesExt\Transport;


	use Aws\Ses\SesClient;
	use Illuminate\Contracts\Events\Dispatcher;
	use Illuminate\Mail\Transport\SesTransport;
	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use Swift_Mime_SimpleMessage;


	class SesExtTransport extends SesTransport
	{
		protected $events;

		public function __construct(SesClient $ses, Dispatcher $events, $options = []) {
			parent::__construct($ses, $options);

			$this->events = $events;
		}


		/**
		 * @inheritDoc
		 */
		public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null) {

			$this->beforeSendPerformed($message);

			$rawMessage = $message->toString();

			$result = $this->ses->sendRawEmail(
				array_merge(
					$this->options, [
						'Source'     => key($message->getSender() ?: $message->getFrom()),
						'RawMessage' => [
							'Data' => $rawMessage,
						],
					]
				)
			);
			$sesMessageId = $result->get('MessageId');

			$message->getHeaders()->addTextHeader('X-SES-Message-ID', $sesMessageId);

			$this->sendPerformed($message);

			// dispatch sent event
			$this->events->dispatch(new SesMessageDispatched($rawMessage, $sesMessageId));

			return $this->numberOfRecipients($message);
		}


	}