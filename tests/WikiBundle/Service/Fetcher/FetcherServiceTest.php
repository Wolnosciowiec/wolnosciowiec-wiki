<?php declare(strict_types=1);

namespace Tests\WikiBundle\Service\Fetcher;

use Tests\TestCase;

/**
 * @see FetcherService
 */
class FetcherServiceTest extends TestCase
{
    /**
     * @group integration
     */
    public function testFetch()
    {
        $targetPath = $this->container->get('wolnosciowiec.wiki.fetcher')
            ->cloneRepository('git', 'https://github.com/Wolnosciowiec/anarchi-faq-pl', 'master');

        $this->assertTrue(is_dir($targetPath));
        $this->assertTrue(is_file($targetPath . '/README.md'));
        $this->assertTrue(is_file($targetPath . '/src/index.md'));
    }

    /**
     * @expectedException \WikiBundle\Exception\Fetcher\RepositoryNotFoundException
     */
    public function testFetchInvalidUrl()
    {
        $this->container->get('wolnosciowiec.wiki.fetcher')
            ->cloneRepository('git', 'https://github.com/asdads', 'master');
    }
}
