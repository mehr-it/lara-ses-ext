<?php


	namespace MehrIt\LaraSesExt\Mail;


	use MehrIt\LaraSesExt\Transport\SesExtTransport;
	use Swift_Mime_SimpleMessage;


	trait InternalMailHeaders
	{

		/**
		 * Adds the given internal header to the swift message
		 * @param string $name The header name
		 * @param string $value The header value
		 */
		protected function withInternalHeader(string $name, string $value) {

			$this->withSwiftMessage(function($message) use ($name, $value) {
				/** @var Swift_Mime_SimpleMessage $message */
				$message->getHeaders()->addTextHeader(SesExtTransport::INTERNAL_HEADER_PREFIX . $name, $value);
			});

		}

	}