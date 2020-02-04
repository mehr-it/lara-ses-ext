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
		 * @var string
		 */
		protected $senderAddress;

		/**
		 * @var string
		 */
		protected $mailFromAddress;

		/**
		 * @var string
		 */
		protected $mailToAddress;

		/**
		 * @var string
		 */
		protected $subject;

		/**
		 * Creates a new instance
		 * @param string $message The message as string
		 * @param string $sesMessageId The SES message id
		 * @param string $senderAddress The sender address
		 * @param string $mailFromAddress The mail from address
		 * @param string $mailToAddress The mail to address
		 * @param string $subject The subject
		 * @param array $internalHeaders The internal header values
		 */
		public function __construct(string $message, string $sesMessageId, string $senderAddress, string $mailFromAddress, string $mailToAddress, $subject, array $internalHeaders = []) {
			$this->message         = $message;
			$this->sesMessageId    = $sesMessageId;
			$this->internalHeaders = $internalHeaders;
			$this->mailFromAddress = $mailFromAddress;
			$this->mailToAddress   = $mailToAddress;
			$this->subject         = $subject;
			$this->senderAddress   = $senderAddress;
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

		/**
		 * Gets the (first) mail from address
		 * @return string The (first) mail from address
		 */
		public function getMailFromAddress(): string {
			return $this->mailFromAddress;
		}

		/**
		 * Gets the (first) mail to address
		 * @return string The (first) mail to address
		 */
		public function getMailToAddress(): string {
			return $this->mailToAddress;
		}

		/**
		 * Gets the subject
		 * @return string The subject
		 */
		public function getSubject(): string {
			return $this->subject;
		}

		/**
		 * Gets the sender address
		 * @return string The sender address
		 */
		public function getSenderAddress(): string {
			return $this->senderAddress;
		}





	}