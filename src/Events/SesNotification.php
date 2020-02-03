<?php


	namespace MehrIt\LaraSesExt\Events;


	use Carbon\Carbon;
	use Illuminate\Foundation\Events\Dispatchable;
	use JsonSerializable;

	abstract class SesNotification implements JsonSerializable
	{
		use Dispatchable;

		protected $mailData;

		protected $notificationData;

		public function __construct(array $mailData, array $notificationData) {

			$this->mailData         = $mailData;
			$this->notificationData = $notificationData;

		}

		/**
		 * Gets the mail data of the notification
		 * @return array The mail data
		 */
		public function getMessageData(): array {
			return $this->mailData;
		}

		/**
		 * Gets the timestamp the original message was sent
		 * @return Carbon|null The timestamp the original message was sent
		 * @throws \Exception
		 */
		public function getMessageSentTimestamp(): ?Carbon {

			$timeStr = $this->mailData['timestamp'] ?? null;
			if (!$timeStr)
				return null;

			return new Carbon($timeStr);
		}

		/**
		 * Gets the message ID assigned by SES
		 * @return string|null The message ID assigned by SES
		 */
		public function getMessageId(): ?string {
			return  $this->mailData['messageId'] ?? null;
		}

		/**
		 * Gets the mail from address
		 * @return string|null The mail from address
		 */
		public function getMessageFrom(): ?string {
			return  $this->mailData['source'] ?? null;
		}

		/**
		 * Gets the Amazon Resource Name (ARN) of the identity that was used to send the email
		 * @return string|null The Amazon Resource Name (ARN) of the identity that was used to send the email
		 */
		public function getMessageSourceArn(): ?string {
			return $this->mailData['sourceArn'] ?? null;
		}

		/**
		 * Gets the originating public IP address of the client that performed the email sending request to Amazon SES
		 * @return string|null The originating public IP address of the client that performed the email sending request to Amazon SES
		 */
		public function getMessageSourceIp(): ?string {
			return $this->mailData['sourceIp'] ?? null;
		}

		/**
		 * Gets the AWS account ID of the account that was used to send the email
		 * @return string|null The AWS account ID of the account that was used to send the email
		 */
		public function getMessageSendingAccountId(): ?string {
			return $this->mailData['sendingAccountId'] ?? null;
		}

		/**
		 * Gets the list of email addresses that were recipients of the original mail
		 * @return string[] The list of email addresses that were recipients of the original mail
		 */
		public function getMessageDestination(): array {
			$dest = $this->mailData['destination'] ?? [];

			if (!is_array($dest))
				$dest = [];

			return $dest;
		}

		/**
		 * Indicates whether the headers are truncated in the notification
		 * @return bool|null Indicates whether the headers are truncated in the notification
		 */
		public function getMessageHeadersTruncated(): ?bool {
			return $this->mailData['headersTruncated'] ?? null;
		}

		/**
		 * Gets the list of the email's original headers. Each header in the list has a name field and a value field.
		 * @return string[] The list of the email's original headers. Each header in the list has a name field and a value field.
		 */
		public function getMessageHeaders(): array {
			$headers = $this->mailData['headers'] ?? [];

			if (!is_array($headers))
				$headers = [];

			return $headers;
		}

		/**
		 * Includes information about common email headers from the original email, including the From, To, and Subject fields. Within this object, each header is a key. The From and To fields are represented by arrays that can contain multiple values.
		 * @return string[] Includes information about common email headers from the original email, including the From, To, and Subject fields. Within this object, each header is a key. The From and To fields are represented by arrays that can contain multiple values.
		 */
		public function getMessageCommonHeaders(): array {
			$headers = $this->mailData['commonHeaders'] ?? [];

			if (!is_array($headers))
				$headers = [];

			return $headers;
		}

		/**
		 * Gets all the notification data as array
		 * @return array All the notification data as array
		 */
		public abstract function toArray(): array;

		/**
		 * @inheritDoc
		 */
		public function jsonSerialize() {
			return $this->toArray();
		}


	}