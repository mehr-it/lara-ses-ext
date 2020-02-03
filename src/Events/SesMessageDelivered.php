<?php


	namespace MehrIt\LaraSesExt\Events;


	use Carbon\Carbon;
	use Exception;

	class SesMessageDelivered extends SesNotification
	{

		/**
		 * Gets all delivery data
		 * @return array All delivery data
		 */
		public function getDeliveryData(): array {
			return $this->notificationData;
		}

		/**
		 * Gets the delivery timestamp
		 * @return Carbon|null The delivery timestamp
		 * @throws Exception
		 */
		public function getDeliveryTimestamp(): ?Carbon {
			$timeStr = $this->notificationData['timestamp'] ?? null;
			if (!$timeStr)
				return null;

			return new Carbon($timeStr);
		}

		/**
		 * Gets the time in milliseconds between when Amazon SES accepted the request from the sender to passing the message to the recipient's mail server.
		 * @return int|null The time in milliseconds between when Amazon SES accepted the request from the sender to passing the message to the recipient's mail server.
		 */
		public function getDeliveryProcessingTimeMillis(): ?int {
			$millis = $this->notificationData['processingTimeMillis'] ?? null;
			if ($millis === null)
				return null;

			return (int)$millis;
		}

		/**
		 * Gets the list of the intended recipients of the email to which the delivery notification applies
		 * @return string[] The list of the intended recipients of the email to which the delivery notification applies
		 */
		public function getDeliveryRecipients(): array {

			$recipients = $this->notificationData['recipients'] ?? [];

			if (!is_array($recipients))
				$recipients = [];

			return $recipients;

		}

		/**
		 * Gets the SMTP response message of the remote ISP that accepted the email from Amazon SES
		 * @return string|null The SMTP response message of the remote ISP that accepted the email from Amazon SES
		 */
		public function getDeliverySmtpResponse(): ?string {
			return $this->notificationData['smtpResponse'] ?? null;
		}

		/**
		 * Gets the host name of the Amazon SES mail server that sent the mail
		 * @return string|null The host name of the Amazon SES mail server that sent the mail
		 */
		public function getDeliveryReportingMta(): ?string {
			return $this->notificationData['reportingMTA'] ?? null;
		}

		/**
		 * Gets the IP address of the MTA to which Amazon SES delivered the email
		 * @return string|null The IP address of the MTA to which Amazon SES delivered the email
		 */
		public function getDeliveryRemoteMtaIp(): ?string {
			return $this->notificationData['remoteMtaIp'] ?? null;
		}

		/**
		 * @inheritDoc
		 */
		public function toArray(): array {
			return [
				'notificationType' => 'Delivery',
				'mail'             => $this->mailData,
				'delivery'         => $this->notificationData,
			];
		}


	}