<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Json\JsonManipulator;
use Symfony\Component\Filesystem\Filesystem;

class GitHooksHelper
{
    public const PRE_COMMIT_FILE = '.git-pre-commit.sh';
    public const COMMIT_MSG = '.git-commit-msg.sh';

    public const HOOKS_LIST = [
        self::PRE_COMMIT_FILE,
        self::COMMIT_MSG,
    ];

    private Composer $composer;

    private IOInterface $io;

    private Filesystem $filesystem;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->filesystem = new Filesystem();
    }

    public function installHooks(JsonManipulator $jsonManipulator): void
    {
        $jsonManipulator->addSubNode('extra', 'hooks.pre-commit', ['./' . self::PRE_COMMIT_FILE]);
        $jsonManipulator->addSubNode('extra', 'hooks.commit-msg', ['./' . self::COMMIT_MSG . ' $1']);
        $jsonManipulator->addSubNode('extra', 'config.stop-on-failure', ['pre-commit']);
    }

    public function copy(): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');

        foreach (self::HOOKS_LIST as $hook) {
            $hookVendorPath = realpath($vendorDir . Plugin::PLUGIN_VENDOR_PATH . $hook);
            $newHookPath = $vendorDir . '/../' . $hook;

            if (!$this->filesystem->exists(realpath($newHookPath))) {
                $this->io->write('[digitaldsdev/codestyle]: Copy ' . $hook . ' to project directory');
                $this->filesystem->copy($hookVendorPath, $newHookPath);
            }
        }
    }

    public function delete(): void
    {
        foreach (self::HOOKS_LIST as $hook) {
            if (!$this->filesystem->exists($hook)) {
                $this->io->write('[digitaldsdev/codestyle]: Remove ' . $hook . ' from project directory');
                $this->filesystem->remove($hook);
            }
        }
    }
}
