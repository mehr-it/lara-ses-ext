<?php


	namespace MehrIt\LaraSesExt\Events;


	use Carbon\Carbon;
	use Exception;

	class SesMessageBounced extends SesNotification
	{
		/**
		 * Gets all bounce data
		 * @return array All bounce data
		 */
		public function getBounceData(): array {
			return $this->notificationData;
		}

		/**
		 * Gets the type of bounce, as determined by Amazon SES
		 * @return string|null The type of bounce, as determined by Amazon SES
		 */
		public function getBounceType(): ?string {
			return $this->notificationData['bounceType'] ?? null;
		}

		/**
		 * Gets the subtype of the bounce, as determined by Amazon SES
		 * @return string|null The subtype of the bounce, as determined by Amazon SES
		 */
		public function getBounceSubType(): ?string {
			return $this->notificationData['bounceSubType'] ?? null;
		}

		/**
		 * Gets the list that contains information about the recipients of the original mail that bounced.
		 * @return string[][] The list that contains information about the recipients of the original mail that bounced.
		 */
		public function getBouncedRecipients(): array {

			$recipients = $this->notificationData['bouncedRecipients'] ?? [];

			if (!is_array($recipients))
				$recipients = [];

			return $recipients;

		}

		/**
		 * Gets the date and time at which the bounce was sent
		 * @return Carbon|null The date and time at which the bounce was sent
		 * @throws Exception
		 */
		public function getBounceTimestamp(): ?Carbon {
			$timeStr = $this->notificationData['timestamp'] ?? null;
			if (!$timeStr)
				return null;

			return new Carbon($timeStr);
		}

		/**
		 * Gets the unique ID for the bounce
		 * @return string|null The unique ID for the bounce
		 */
		public function getBounceFeedbackId(): ?string {
			return $this->notificationData['feedbackId'] ?? null;
		}

		/**
		 * Gets the IP address of the MTA to which Amazon SES attempted to deliver the email
		 * @return string|null The IP address of the MTA to which Amazon SES attempted to deliver the email
		 */
		public function getBounceRemoteMtaIp(): ?string {
			return $this->notificationData['remoteMtaIp'] ?? null;
		}

		/**
		 * Gets the value of the Reporting-MTA field from the DSN. This is the value of the MTA that attempted to perform the delivery, relay, or gateway operation described in the DSN
		 * @return string|null The value of the Reporting-MTA field from the DSN. This is the value of the MTA that attempted to perform the delivery, relay, or gateway operation described in the DSN
		 */
		public function getBounceReportingMta(): ?string {
			return $this->notificationData['reportingMTA'] ?? null;
		}

		/**
		 * @inheritDoc
		 */
		public function toArray(): array {
			return [
				'notificationType' => 'Bounce',
				'mail'             => $this->mailData,
				'bounce'           => $this->notificationData,
			];
		}
	}