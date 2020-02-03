<?php


	namespace MehrItLaraSesExtTest\Unit\Cases;


	use MehrIt\LaraSesExt\Provider\SesExtServiceProvider;

	class TestCase extends \Orchestra\Testbench\TestCase
	{
		protected function getPackageProviders($app) {

			return [SesExtServiceProvider::class];

		}


	}