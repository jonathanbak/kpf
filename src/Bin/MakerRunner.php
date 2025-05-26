<?php

namespace Kpf\Bin;

use Kpf\Application;
use Kpf\Maker;

class MakerRunner
{
    /**
     * Run the Maker CLI logic from arguments
     *
     * @param array $args Command-line arguments (e.g. $_SERVER['argv'])
     * @return string Output result text
     * @throws \Exception if arguments are missing or generation fails
     */
    public static function run(array $args): string
    {
        $rootDir = Maker::getProjectRoot();

        $type = $args[1] ?? null;
        $name = $args[2] ?? null;
        $force = in_array('--force', $args, true);

        if (!$type) {
            throw new \InvalidArgumentException("Usage: php make.php [type] [name] [--force]");
        }

        ob_start();

        // init 명령은 별도 처리
        if ($type === 'init') {
            Maker::initResource();
        } else {
            Application::init($rootDir);

            if (!$name) {
                throw new \InvalidArgumentException("Missing name for type: {$type}");
            }

            Maker::make($type, $name, $force);
        }

        return ob_get_clean();
    }
}