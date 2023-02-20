<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Cli;

use Staatic\WordPress\Cli\MigrateCommand;
use Staatic\WordPress\Cli\PublishCommand;
use Staatic\WordPress\Module\ModuleInterface;
use WP_CLI;

final class RegisterCommands implements ModuleInterface
{
    /**
     * @var PublishCommand
     */
    private $publishCommand;

    /**
     * @var MigrateCommand
     */
    private $migrateCommand;

    public function __construct(PublishCommand $publishCommand, MigrateCommand $migrateCommand)
    {
        $this->publishCommand = $publishCommand;
        $this->migrateCommand = $migrateCommand;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\defined('WP_CLI') || !\constant('WP_CLI')) {
            return;
        }
        WP_CLI::add_command('staatic', $this->migrateCommand);
        WP_CLI::add_command('staatic', $this->publishCommand);
    }
}
