<?php


	namespace MehrIt\LaraSesExt\Transport;



	class SesExtSimulationTransport extends SesExtTransport
	{
		/**
		 * @inheritDoc
		 */
		protected function sendToSes(string $source, string $rawMessage): ?string {

			// we do not sent the message but generate a random SES like message id
			return implode('-', [
				$this->randomHex(16),
				$this->randomHex(8),
				$this->randomHex(4),
				$this->randomHex(4),
				$this->randomHex(4),
				$this->randomHex(12),
			]) . '-000000';

		}

		/**
		 * Generates a random hex string
		 * @param int $length The string length
		 * @return string The string
		 */
		protected function randomHex(int $length): string {

			$str = '0123456789abcdef';

			$ret = '';

			for ($i = 0; $i < $length; ++$i) {
				$ret .= str_shuffle($str)[0];
			}

			return $ret;

		}


	}