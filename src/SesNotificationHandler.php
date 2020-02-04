<?php


	namespace MehrIt\LaraSesExt;


	use Illuminate\Contracts\Events\Dispatcher;
	use InvalidArgumentException;
	use MehrIt\LaraSesExt\Events\SesMessageBounced;
	use MehrIt\LaraSesExt\Events\SesMessageComplained;
	use MehrIt\LaraSesExt\Events\SesMessageDelivered;
	use MehrIt\LaraSesExt\Exception\UnknownSesNotificationTypeException;

	/**
	 * Handles SES notifications
	 * @package MehrIt\LaraSesExt
	 */
	class SesNotificationHandler
	{
		/**
		 * @var Dispatcher
		 */
		protected $dispatcher;

		/**
		 * Creates a new instance
		 * @param Dispatcher $dispatcher The event dispatcher
		 */
		public function __construct(Dispatcher $dispatcher) {
			$this->dispatcher = $dispatcher;
		}


		/**
		 * Handles the given SES notification and dispatches the corresponding event
		 * @param string $body The SES notification as string
		 * @throws UnknownSesNotificationTypeException
		 */
		public function handle(string $body): void {

			$this->dispatcher->dispatch($this->resolve($body));
		}

		/**
		 * Resolves the event for the given SES notification
		 * @param string $body The SES notification as string
		 * @return SesMessageBounced|SesMessageComplained|SesMessageDelivered The event
		 * @throws UnknownSesNotificationTypeException
		 */
		public function resolve(string $body) {
			// decode data
			$data = json_decode($body, true);
			if (JSON_ERROR_NONE !== json_last_error())
				throw new InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());

			// dispatch event
			$type = $data['notificationType'] ?? null;
			switch ($type) {
				case 'Bounce':
					return new SesMessageBounced((array)($data['mail'] ?? []), (array)($data['bounce'] ?? []));

				case 'Complaint':
					return new SesMessageComplained((array)($data['mail'] ?? []), (array)($data['complaint'] ?? []));

				case 'Delivery':
					return new SesMessageDelivered((array)($data['mail'] ?? []), (array)($data['delivery'] ?? []));

				default:
					throw new UnknownSesNotificationTypeException("Unknown SES notification type \"$type\".");
			}
		}

	}