<?php


	namespace MehrIt\LaraSesExt\Events;


	use Illuminate\Foundation\Events\Dispatchable;

	class SesMessageDispatched
	{
		use Dispatchable;

		/**
		 * @var string
		 */
		protected $message;

		/**
		 * @var string
		 */
		protected $sesMessageId;

		/**
		 * Creates a new instance
		 * @param string $message The message as string
		 * @param string $sesMessageId The SES message id
		 */
		public function __construct(string $message, string $sesMessageId) {
			$this->message      = $message;
			$this->sesMessageId = $sesMessageId;
		}

		/**
		 * Gets the message data
		 * @return string The message data
		 */
		public function getMessage(): string {
			return $this->message;
		}

		/**
		 * The SES message id
		 * @return string The SES message id
		 */
		public function getSesMessageId(): string {
			return $this->sesMessageId;
		}
	}