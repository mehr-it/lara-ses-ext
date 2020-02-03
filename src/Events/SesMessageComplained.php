<?php


	namespace MehrIt\LaraSesExt\Events;


	use Carbon\Carbon;
	use Exception;

	class SesMessageComplained extends SesNotification
	{
		/**
		 * Gets all complaint data
		 * @return array All complaint data
		 */
		public function getComplaintData(): array {
			return $this->notificationData;
		}

		/**
		 * Gets the list that contains information about recipients that may have been responsible for the complaint
		 * @return string[] The list that contains information about recipients that may have been responsible for the complaint
		 */
		public function getComplainedRecipients(): array {

			$recipients = $this->notificationData['complainedRecipients'] ?? [];

			if (!is_array($recipients))
				$recipients = [];

			return $recipients;

		}

		/**
		 * Gets the date and time when the ISP sent the complaint notification
		 * @return Carbon|null The date and time when the ISP sent the complaint notification
		 * @throws Exception
		 */
		public function getComplaintTimestamp(): ?Carbon {
			$timeStr = $this->notificationData['timestamp'] ?? null;
			if (!$timeStr)
				return null;

			return new Carbon($timeStr);
		}

		/**
		 * Gets the unique ID associated with the complaint
		 * @return string|null The unique ID associated with the complaint
		 */
		public function getComplaintFeedbackId(): ?string {
			return $this->notificationData['feedbackId'] ?? null;
		}

		/**
		 * Gets the complaint sub type
		 * @return string|null Either null or "OnAccountSuppressionList". If the value is "OnAccountSuppressionList", Amazon SES accepted the message, but didn't attempt to send it because it was on the account-level suppression list
		 */
		public function getComplaintSubType(): ?string {
			return $this->notificationData['complaintSubType'] ?? null;
		}

		/**
		 * Gets the value of the User-Agent field from the feedback report
		 * @return string|null The value of the User-Agent field from the feedback report
		 */
		public function getComplaintUserAgent(): ?string {
			return $this->notificationData['userAgent'] ?? null;
		}

		/**
		 * Gets the value of the Feedback-Type field from the feedback report received from the ISP
		 * @return string|null The value of the Feedback-Type field from the feedback report received from the ISP
		 */
		public function getComplaintFeedbackType(): ?string {
			return $this->notificationData['complaintFeedbackType'] ?? null;
		}

		/**
		 * Gets the value of the Arrival-Date or Received-Date field from the feedback report
		 * @return Carbon|null The value of the Arrival-Date or Received-Date field from the feedback report
		 * @throws Exception
		 */
		public function getComplaintArrivalDate(): ?Carbon {
			$timeStr = $this->notificationData['arrivalDate'] ?? null;
			if (!$timeStr)
				return null;

			return new Carbon($timeStr);
		}

		/**
		 * @inheritDoc
		 */
		public function toArray(): array {
			return [
				'notificationType' => 'Complaint',
				'mail'             => $this->mailData,
				'complaint'        => $this->notificationData,
			];
		}
	}