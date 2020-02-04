<?php


	namespace MehrIt\LaraSesExt\Transport;


	use Aws\Ses\SesClient;
	use Illuminate\Contracts\Events\Dispatcher;
	use Illuminate\Mail\Transport\SesTransport;
	use Illuminate\Support\Str;
	use MehrIt\LaraSesExt\Events\SesMessageDispatched;
	use Swift_Mime_SimpleMessage;


	class SesExtTransport extends SesTransport
	{
		const INTERNAL_HEADER_PREFIX = 'x-internal-';

		/**
		 * @var Dispatcher
		 */
		protected $events;


		public function __construct(SesClient $ses, Dispatcher $events, $options = []) {
			parent::__construct($ses, $options);

			$this->events = $events;
		}


		/**
		 * @inheritDoc
		 */
		public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null) {

			// extract internal headers
			$internalHeaders = $this->extractInternalHeaders($message);

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
			$this->events->dispatch(new SesMessageDispatched($rawMessage, $sesMessageId, $internalHeaders));

			return $this->numberOfRecipients($message);
		}

		/**
		 * Removes all internal headers from the message and returns their values
		 * @param Swift_Mime_SimpleMessage $message The message
		 * @return array The header values
		 */
		protected function extractInternalHeaders(Swift_Mime_SimpleMessage $message): array {

			$internalHeaderValues = [];

			$headers = $message->getHeaders();

			$headerNames = $headers->listAll();
			foreach ($headerNames as $currHeader) {

				$currHeaderLower = Str::lower($currHeader);

				if (Str::startsWith($currHeaderLower, self::INTERNAL_HEADER_PREFIX)) {
					$internalValue = $message->getHeaders()->get($currHeader)->getFieldBodyModel();

					$internalHeaderValues[Str::substr($currHeaderLower, Str::length(self::INTERNAL_HEADER_PREFIX))] = $internalValue;

					$headers->removeAll($currHeader);
				}
			}

			return $internalHeaderValues;
		}

	}