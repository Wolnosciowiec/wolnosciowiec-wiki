<?php declare(strict_types=1);

namespace WikiBundle\Service\StorageManager;

use Symfony\Component\Filesystem\Filesystem;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

class StorageManager implements StorageManagerInterface
{
    /** @var string $storagePath */
    private $storagePath = '';

    /** @var string $compiledFilesPath */
    private $compiledFilesPath = '';

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var array $knownRepositories */
    private $knownRepositories = [];

    public function __construct(string $storagePath, string $compiledFilesPath)
    {
        $this->storagePath = $storagePath;
        $this->compiledFilesPath = $compiledFilesPath;
        $this->filesystem = new Filesystem();
    }

    private function escape(string $path): string
    {
        $path = str_replace('..', '', $path);
        $path = str_replace('/', '', $path);
        $path = str_replace("\x0", '', $path);

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function findPathFor(string $repositoryName, bool $freeUp = false): string
    {
        $path = $this->storagePath . '/' . $this->escape($repositoryName);

        if ($freeUp) {
            if (is_dir($path)) {
                $this->filesystem->remove($path);
            }

            $this->filesystem->mkdir($path);
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function findCompiledPathFor(string $repositoryName, string $srcFilePath): string
    {
        $filePath = str_replace(realpath($this->storagePath), '', realpath($srcFilePath));
        $dir = $this->compiledFilesPath . '/' . dirname($filePath);

        if (!is_dir($dir)) {
            $this->filesystem->mkdir($dir, 0775);
        }

        return $this->compiledFilesPath . '/' . $filePath;
    }

    /**
     * @inheritdoc
     */
    public function getRepositoryName(string $url, string $branch)
    {
        return array_search($url . '@' . $branch, $this->knownRepositories);
    }

    /**
     * @param array $knownRepositories
     * @return StorageManagerInterface
     */
    public function setKnownRepositories(array $knownRepositories): StorageManagerInterface
    {
        $this->knownRepositories = $knownRepositories;
        return $this;
    }

    /**
     * @return array
     */
    public function getKnownRepositories(): array
    {
        return $this->knownRepositories;
    }
}