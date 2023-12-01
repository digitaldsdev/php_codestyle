<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonManipulator;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private JsonManipulator $manipulator;

    public function activate(Composer $composer, IOInterface $io)
    {
        $composerFile = Factory::getComposerFile();
        $this->manipulator = new JsonManipulator(file_get_contents($composerFile));
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'configureProject',
            ScriptEvents::POST_UPDATE_CMD => 'configureProject',
        ];
    }

    public function configureProject(): void
    {
        $this->manipulator->addMainKey('extra', ['hooks' => ['pre-commit' => 'echo test']]);
    }

}