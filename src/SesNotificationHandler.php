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

			// decode data
			$data = json_decode($body, true);
			if (JSON_ERROR_NONE !== json_last_error())
				throw new InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());

			// dispatch event
			$type = $data['notificationType'] ?? null;
			switch($type) {
				case 'Bounce':
					$this->dispatcher->dispatch(new SesMessageBounced((array)($data['mail'] ?? []), (array)($data['bounce'] ?? [])));
					break;

				case 'Complaint':
					$this->dispatcher->dispatch(new SesMessageComplained((array)($data['mail'] ?? []), (array)($data['complaint'] ?? [])));
					break;

				case 'Delivery':
					$this->dispatcher->dispatch(new SesMessageDelivered((array)($data['mail'] ?? []), (array)($data['delivery'] ?? [])));
					break;

				default:
					throw new UnknownSesNotificationTypeException("Unknown SES notification type \"$type\".");
			}

		}

	}