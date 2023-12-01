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
use DigitalSector\CodeStyle\Enum\Commands;
use DigitalSector\CodeStyle\Enum\ComposerTemplates;
use Symfony\Component\Filesystem\Filesystem;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;

    private IOInterface $io;

    private Filesystem $filesystem;

    private ComposerHelper $composerHelper;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->filesystem = new Filesystem();
        $this->composerHelper = new ComposerHelper(
            $composer,
            $io,
            new JsonManipulator(file_get_contents(Factory::getComposerFile()))
        );
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        $this->composerHelper->getManipulator()->removeSubNode('scripts', Commands::POST_INSTALL_CMD);
        $this->composerHelper->getManipulator()->removeSubNode('scripts', Commands::POST_UPDATE_CMD);
        $this->composerHelper->getManipulator()->removeSubNode('scripts', Commands::CODE_STYLE_FIX);
        $this->composerHelper->getManipulator()->removeSubNode('scripts', Commands::CODE_STYLE_CHECK);

        $this->composerHelper->getManipulator()->removeSubNode('extra', 'hooks');

        $this->composerHelper->writeComposerJson();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'postInstallCmd',
            ScriptEvents::POST_UPDATE_CMD => 'postUpdateCmd',
        ];
    }

    public function postInstallCmd(): void
    {
        $this->configureProject();
        $this->copyPhpstan();
    }

    public function postUpdateCmd(): void
    {
        $this->configureProject();
        $this->copyPhpstan();
    }

    private function configureProject(): void
    {
        $this->composerHelper->getManipulator()->addMainKey('extra', ComposerTemplates::EXTRA_MAIN);
        $this->composerHelper->getManipulator()->addMainKey('scripts', ComposerTemplates::SCRIPTS);

        $this->composerHelper->writeComposerJson();

        $this->composerHelper->updateComposerLock();
    }

    private function copyPhpstan(): void
    {
        $vendorPath = $this->composer->getConfig()->get('vendor-dir');
        $phpstan = realpath($vendorPath . '/digital-sector/codestyle/phpstan.neon');
        $newPhpstan = realpath($vendorPath . '/../phpstan.neon');

        $this->io->write('[digital-sector/codestyle]: Copy phpstan.neon to root directory');

        $this->filesystem->copy($phpstan, $newPhpstan);
    }
}
