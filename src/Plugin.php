<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Package\Locker;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var JsonManipulator
     */
    private $manipulator;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $composerFile = Factory::getComposerFile();
        $this->manipulator = new JsonManipulator(file_get_contents($composerFile));
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        $this->manipulator->removeSubNode('extra', 'hooks');

        $this->writeComposerJson();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'configureProject',
        ];
    }

    public function configureProject()
    {
        $this->manipulator->addMainKey('extra', ['hooks' => ['pre-commit' => ['echo codestyle check']]]);

        $this->writeComposerJson();

        $this->updateComposerLock();
    }

    private function writeComposerJson()
    {
        file_put_contents(Factory::getComposerFile(), $this->manipulator->getContents());
    }

    private function updateComposerLock()
    {
        $lock = substr(Factory::getComposerFile(), 0, -4).'lock';
        $composerJson = file_get_contents(Factory::getComposerFile());
        $lockFile = new JsonFile($lock, null, $this->io);
        $locker = new Locker($this->io, $lockFile, $this->composer->getInstallationManager(), $composerJson);
        $lockData = $locker->getLockData();
        $lockData['content-hash'] = Locker::getContentHash($composerJson);
        $lockFile->write($lockData);
    }
}