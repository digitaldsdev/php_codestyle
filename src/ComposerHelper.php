<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Package\Locker;
use Seld\JsonLint\ParsingException;

class ComposerHelper
{
    private Composer $composer;

    private IOInterface $io;

    private JsonManipulator $manipulator;

    public function __construct(Composer $composer, IOInterface $io, JsonManipulator $manipulator)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->manipulator = $manipulator;
    }

    public function getManipulator(): JsonManipulator
    {
        return $this->manipulator;
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws ParsingException
     */
    public function getKey(string $key)
    {
        $json = JsonFile::parseJson($this->getComposerJsonContent());

        return $json[$key];
    }

    public function writeComposerJson(): void
    {
        file_put_contents(Factory::getComposerFile(), $this->manipulator->getContents());
    }

    public function updateComposerLock(): void
    {
        $composerFile = Factory::getComposerFile();
        $composerJson = $this->getComposerJsonContent();
        $lockFile = new JsonFile(Factory::getLockFile($composerFile), null, $this->io);
        $locker = new Locker($this->io, $lockFile, $this->composer->getInstallationManager(), $composerJson);
        $lockData = $locker->getLockData();
        $lockData['content-hash'] = Locker::getContentHash($composerJson);
        $lockFile->write($lockData);
    }

    private function getComposerJsonContent(): string
    {
        return file_get_contents(Factory::getComposerFile());
    }
}
