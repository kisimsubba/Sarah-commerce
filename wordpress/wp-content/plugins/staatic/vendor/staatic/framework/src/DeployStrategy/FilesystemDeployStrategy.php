<?php

namespace Staatic\Framework\DeployStrategy;

use FilesystemIterator;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Staatic\Vendor\Symfony\Component\Filesystem\Exception\IOException;
use Staatic\Vendor\Symfony\Component\Filesystem\Filesystem;
use Staatic\Framework\Deployment;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\Util\PathHelper;
final class FilesystemDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var string
     */
    private $targetDirectory;
    /**
     * @var string|null
     */
    private $stagingDirectory;
    /**
     * @var string
     */
    private $workingDirectory;
    /**
     * @var string
     */
    private $basePath = '';
    /**
     * @var mixed[]
     */
    private $retainPaths = [];
    /**
     * @var mixed[]
     */
    private $excludePaths = [];
    /**
     * @var mixed[]
     */
    private $symlinks = [];
    /**
     * @var bool
     */
    private $copyOnWindows = \false;
    /**
     * @var mixed[]
     */
    private $loggerContext = [];
    public function __construct(ResourceRepositoryInterface $resourceRepository, array $options = [])
    {
        $this->logger = new NullLogger();
        $this->filesystem = new Filesystem();
        $this->resourceRepository = $resourceRepository;
        if (empty($options['targetDirectory'])) {
            throw new InvalidArgumentException('Missing required option "targetDirectory"');
        }
        $this->setTargetDirectory($options['targetDirectory']);
        if (!empty($options['stagingDirectory'])) {
            $this->setStagingDirectory($options['stagingDirectory']);
        }
        $this->workingDirectory = $this->stagingDirectory ?: $this->targetDirectory;
        if (!empty($options['basePath'])) {
            $this->basePath = \rtrim($options['basePath'], '/');
        }
        if (!empty($options['retainPaths'])) {
            $this->retainPaths = $this->normalizePaths($options['retainPaths']);
        }
        if (!empty($options['excludePaths'])) {
            $this->excludePaths = $this->normalizePaths($options['excludePaths']);
        }
        if (!empty($options['symlinks'])) {
            $this->setSymlinks($options['symlinks']);
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->copyOnWindows = $options['copyOnWindows'] ?? \false;
        }
    }
    /**
     * @return void
     */
    private function setTargetDirectory(string $targetDirectory)
    {
        if (!\is_dir($targetDirectory)) {
            throw new InvalidArgumentException("Target directory does not exist in {$targetDirectory}");
        }
        if (!\is_writable($targetDirectory)) {
            throw new InvalidArgumentException("Target directory is not writable in {$targetDirectory}");
        }
        $this->targetDirectory = $this->normalizePath($targetDirectory);
    }
    /**
     * @return void
     */
    private function setStagingDirectory(string $stagingDirectory)
    {
        if (!\is_dir($stagingDirectory)) {
            throw new InvalidArgumentException("Staging directory does not exist in {$stagingDirectory}");
        }
        if (!\is_writable($stagingDirectory)) {
            throw new InvalidArgumentException("Staging directory is not writable in {$stagingDirectory}");
        }
        if (\realpath($stagingDirectory) === \realpath($this->targetDirectory)) {
            throw new InvalidArgumentException("Staging directory ({$stagingDirectory}) cannot be the same as target directory ({$this->targetDirectory})");
        }
        $this->stagingDirectory = $this->normalizePath($stagingDirectory);
    }
    /**
     * @return void
     */
    private function setSymlinks(array $symlinks)
    {
        $normalizedSymlinks = [];
        foreach ($symlinks as $source => $target) {
            $normalizedSymlinks[$this->normalizePath($source)] = $this->normalizePath($target);
        }
        $this->symlinks = $normalizedSymlinks;
    }
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment) : array
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Initiating deployment', $this->loggerContext);
        $this->clearWorkingDirectory($this->workingDirectory);
        $this->createSymlinks();
        return [];
    }
    /**
     * @param Deployment $deployment
     * @param mixed[] $results
     * @return void
     */
    public function processResults($deployment, $results)
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Deploying results', $this->loggerContext);
        $numResults = 0;
        foreach ($results as $result) {
            $filePath = $this->determineFilePath($result->url()->getPath());
            \assert(strncmp($filePath, '/', strlen('/')) === 0);
            $targetPath = $this->workingDirectory . $filePath;
            if ($this->isCoveredBySymlink($filePath)) {
                $this->logger->debug("Skipping '{$targetPath}' as it is covered by a symlink", \array_merge($this->loggerContext, ['resultId' => $result->id()]));
                $numResults++;
                continue;
            }
            $resource = $this->resourceRepository->find($result->sha1());
            if ($resource === null) {
                throw new RuntimeException("Unable to find resource with sha1 {$result->sha1()}");
            }
            $resource->content()->rewind();
            $this->filesystem->dumpFile($targetPath, $resource->content());
            $this->logger->debug("Wrote {$result->size()} bytes to '{$targetPath}'", \array_merge($this->loggerContext, ['resultId' => $result->id()]));
            $numResults++;
        }
        $this->logger->info("Deployed {$numResults} files to {$this->workingDirectory}", $this->loggerContext);
    }
    private function isCoveredBySymlink(string $filePath) : string
    {
        foreach ($this->symlinks as $linkTarget) {
            if (strncmp($filePath, \rtrim($linkTarget, '/\\') . '/', strlen(\rtrim($linkTarget, '/\\') . '/')) === 0) {
                return \true;
            }
        }
        return \false;
    }
    private function determineFilePath(string $path) : string
    {
        if ($this->basePath && strncmp($path, $this->basePath, strlen($this->basePath)) === 0) {
            $path = \mb_substr($path, \mb_strlen($this->basePath));
        }
        return PathHelper::determineFilePath($path);
    }
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment) : bool
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Finishing deployment', $this->loggerContext);
        if ($this->stagingDirectory) {
            $this->mirrorStagingDirectory();
        }
        return \true;
    }
    /**
     * @return void
     */
    private function clearWorkingDirectory()
    {
        $this->logger->debug("Clearing working directory in {$this->workingDirectory}", $this->loggerContext);
        $deleteIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->workingDirectory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($deleteIterator as $file) {
            $this->filesystem->remove($file);
        }
    }
    /**
     * @return void
     */
    private function createSymlinks()
    {
        $this->logger->debug('Creating symlinks', $this->loggerContext);
        foreach ($this->symlinks as $originDir => $targetDir) {
            $targetDir = $this->workingDirectory . $targetDir;
            if ($this->copyOnWindows) {
                $this->logger->info("Copying '{$originDir}' to '{$targetDir}'", $this->loggerContext);
                $this->mirror($originDir, $targetDir, [], $this->excludePaths);
                continue;
            }
            $this->logger->info("Symlinking '{$originDir}' to '{$targetDir}'", $this->loggerContext);
            $this->filesystem->symlink($originDir, $targetDir);
        }
    }
    /**
     * @return void
     */
    private function mirrorStagingDirectory()
    {
        $this->logger->debug(\sprintf('Mirroring staging directory (%s) to target directory (%s)', $this->stagingDirectory, $this->targetDirectory), $this->loggerContext);
        $this->mirror($this->stagingDirectory, $this->targetDirectory, $this->retainPaths);
    }
    /**
     * @return void
     */
    private function mirror(string $originDir, string $targetDir, array $retainPaths = [], array $excludePaths = [])
    {
        if ($this->filesystem->exists($targetDir)) {
            $this->cleanupTargetDirectory($originDir, $targetDir, $retainPaths);
        }
        $originDirLen = \strlen($originDir);
        $flags = $this->copyOnWindows ? FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS : FilesystemIterator::SKIP_DOTS;
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($originDir, $flags), function ($fileInfo, $path, $iterator) use ($excludePaths) {
            return !\in_array($this->normalizePath($path), $excludePaths);
        }), RecursiveIteratorIterator::SELF_FIRST);
        $this->filesystem->mkdir($targetDir);
        $filesCreatedWhileMirroring = [];
        foreach ($iterator as $file) {
            if ($file->getPathname() === $targetDir || $file->getRealPath() === $targetDir || isset($filesCreatedWhileMirroring[$file->getRealPath()])) {
                continue;
            }
            $sourcePath = $this->normalizePath($file);
            $targetPath = $this->normalizePath($targetDir . \substr($file->getPathname(), $originDirLen));
            $filesCreatedWhileMirroring[$targetPath] = \true;
            if ($file->isLink()) {
                $this->logger->debug("Symlinking {$file->getLinkTarget()} to {$targetPath}");
                $this->filesystem->symlink($file->getLinkTarget(), $targetPath, $this->copyOnWindows);
            } elseif ($file->isDir()) {
                $this->logger->debug("Creating directory {$targetPath}");
                $this->filesystem->mkdir($targetPath);
            } elseif ($file->isFile()) {
                $this->logger->debug("Copying file {$sourcePath} to {$targetPath}");
                $this->filesystem->copy($sourcePath, $targetPath, \true);
            } else {
                throw new IOException("Unable to determine file type of {$sourcePath}", 0, null, $file);
            }
        }
    }
    /**
     * @return void
     */
    private function cleanupTargetDirectory(string $originDir, string $targetDir, array $retainPaths = [])
    {
        $retainMap = [];
        $directoryIterator = new RecursiveDirectoryIterator($targetDir, FilesystemIterator::SKIP_DOTS);
        if (!empty($retainPaths)) {
            $retainMap = $this->retainPathsToMap($retainPaths, $targetDir);
            $directoryIterator = new RecursiveCallbackFilterIterator($directoryIterator, function ($fileInfo, $path) use ($retainPaths) {
                return !\in_array($this->normalizePath($path), $retainPaths);
            });
        }
        $deleteIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        $targetDirLen = \strlen($targetDir);
        foreach ($deleteIterator as $file) {
            $normalizedPath = $this->normalizePath($file->getPathname());
            $origin = new SplFileInfo($originDir . \substr($file->getPathname(), $targetDirLen));
            if (isset($retainMap[$normalizedPath])) {
                continue;
            }
            if (!$this->filesystem->exists($origin)) {
                $this->logger->debug("Removing obsolete entry {$normalizedPath}");
                $this->filesystem->remove($file);
            } elseif (!$this->isSameFileType($file, $origin)) {
                $this->logger->debug("Removing incorrectly typed entry {$normalizedPath}");
                $this->filesystem->remove($file);
            }
        }
    }
    private function retainPathsToMap(array $paths, string $targetDir) : array
    {
        $targetDirLen = \strlen($targetDir);
        $map = [];
        foreach ($paths as $path) {
            $path = \substr($path, $targetDirLen);
            $map[$targetDir . $path] = \true;
            while ($pos = \strrpos($path, '/')) {
                $path = \substr($path, 0, $pos);
                $map[$targetDir . $path] = \true;
            }
        }
        return $map;
    }
    private function isSameFileType(SplFileInfo $file, SplFileInfo $fileCompare) : bool
    {
        if ($file->isLink() !== $fileCompare->isLink()) {
            return \false;
        }
        if ($file->isDir() !== $fileCompare->isDir()) {
            return \false;
        }
        if ($file->isFile() !== $fileCompare->isFile()) {
            return \false;
        }
        return \true;
    }
    private function normalizePath(string $path) : string
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            $path = \str_replace('\\', '/', $path);
        }
        if (\substr($path, 1, 1) === ':') {
            $path = \ucfirst($path);
        }
        return \rtrim($path, '/\\');
    }
    private function normalizePaths(array $paths) : array
    {
        return \array_map(function (string $path) {
            return $this->normalizePath($path);
        }, $paths);
    }
}
