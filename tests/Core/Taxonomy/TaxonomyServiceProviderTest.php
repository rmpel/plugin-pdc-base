<?php

namespace OWC_PDC_Base\Core\Taxonomy;

use Mockery as m;
use OWC_PDC_Base\Core\Config;
use OWC_PDC_Base\Core\Plugin\BasePlugin;
use OWC_PDC_Base\Core\Plugin\Loader;
use OWC_PDC_Base\Core\Tests\TestCase;

class TaxonomyServiceProviderTest extends TestCase
{

	public function setUp()
	{
		\WP_Mock::setUp();
	}

	public function tearDown()
	{
		\WP_Mock::tearDown();
	}

	/** @test */
	public function check_registration_of_taxonomies()
	{
		$config = m::mock(Config::class);
		$plugin = m::mock(BasePlugin::class);

		$plugin->config = $config;
		$plugin->loader = m::mock(Loader::class);

		$service = new TaxonomyServiceProvider($plugin);

		$plugin->loader->shouldReceive('addAction')->withArgs([
			'init',
			$service,
			'registerTaxonomies'
		])->once();

		/**
		 * Examples of registering taxonomies: http://johnbillion.com/extended-cpts/
		 */
		$configTaxonomies = [
			'posttype' => [
				'object_types' => ['post'],
				'args'         => [
				],
				'names'        => [
				]
			]
		];

		$config->shouldReceive('get')->with('taxonomies')->once()->andReturn($configTaxonomies);

		//test for filter being called
		\WP_Mock::expectFilter('owc/pdc_base/config/taxonomies', $configTaxonomies);

		$service->register();

		$this->assertEquals($configTaxonomies, $service->getConfigTaxonomies());
	}
}
