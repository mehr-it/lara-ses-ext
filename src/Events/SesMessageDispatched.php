<?php


	namespace MehrIt\LaraSesExt\Events;


	use Illuminate\Foundation\Events\Dispatchable;
	use Illuminate\Support\Str;

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
		 * @var array
		 */
		protected $internalHeaders;

		/**
		 * Creates a new instance
		 * @param string $message The message as string
		 * @param string $sesMessageId The SES message id
		 * @param array $internalHeaders The internal header values
		 */
		public function __construct(string $message, string $sesMessageId, array $internalHeaders = []) {
			$this->message      = $message;
			$this->sesMessageId = $sesMessageId;
			$this->internalHeaders = $internalHeaders;
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

		/**
		 * Gets the internal header values
		 * @return array The internal header values
		 */
		public function getInternalHeaders(): array {
			return $this->internalHeaders;
		}

		/**
		 * Gets the value of the internal header with given name
		 * @param string $name The header name
		 * @return string|null The header value or null
		 */
		public function getInternalHeaderValue(string $name): ?string {
			return $this->internalHeaders[Str::lower($name)] ?? null;
		}


	}