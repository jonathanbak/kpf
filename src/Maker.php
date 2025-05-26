<?php

namespace Kpf;

use Kpf\Helper\File;

class Maker extends Singleton
{
    protected function make(string $type, string $rawName, bool $force = false): void
    {
        $type = strtolower($type);
        $namespace = ucfirst(Application::getNamespace());

        switch ($type) {
            case 'controller':
                $this->makeController($rawName, $namespace, $force);
                break;
            case 'model':
                $this->makeModel($rawName, $namespace, $force);
                break;
            case 'view':
                $this->makeView($rawName, $force);
                break;
            case 'page':
                $this->makeController($rawName, $namespace, $force);
                $this->makeView($rawName, $force);
                echo "\n[Page URL] /" . str_replace(Directory::DIRECTORY_SEPARATOR, '/', strtolower($rawName)) . "\n";
                break;
            default:
                throw new \InvalidArgumentException("Unsupported type: {$type}");
        }
    }

    protected function parseName(string $raw): array
    {
        $raw = str_replace(['\\', '@'], '/', $raw);
        $segments = explode('/', $raw);
        $name = ucfirst(array_pop($segments));
        $subdir = implode('/', array_map('ucfirst', $segments));
        return [$subdir, $name];
    }

    protected function makeController(string $rawName, string $namespace, bool $force = false): void
    {
        [$subdir, $name] = $this->parseName($rawName);

        $template = $this->resolveTemplatePath('controllers/Blank.php.txt');
        $targetDir = $this->getDestDirectory('controller') . ($subdir ? "{$subdir}/" : "");
        File::makeDir($targetDir);

        $targetFile = $targetDir . "{$name}.php";
        $classNamespace = $this->buildNamespace("{$namespace}\\Controller", $subdir);
        $viewPath = strtolower($subdir ? "{$subdir}/{$name}" : $name);

        if (file_exists($targetFile) && !$force) {
            echo "Skipped (already exists): {$targetFile}\n";
            return;
        }

        $content = File::load($template);
        $content = str_replace(
            ['<<classNamespace>>', '<<subdir>>', '<<name>>', '<<view_path>>'],
            [$classNamespace, $subdir, $name, $viewPath],
            $content
        );

        File::write($targetFile, $content);
        echo "Created controller: {$targetFile}\n";
    }

    protected function makeModel(string $rawName, string $namespace, bool $force = false): void
    {
        [$subdir, $name] = $this->parseName($rawName);

        $template = $this->resolveTemplatePath('models/Blank.php.txt');
        $targetDir = $this->getDestDirectory('model') . ($subdir ? "{$subdir}/" : "");
        File::makeDir($targetDir);

        $targetFile = $targetDir . "{$name}.php";
        $classNamespace = $this->buildNamespace("{$namespace}\\Model", $subdir);

        if (file_exists($targetFile) && !$force) {
            echo "Skipped (already exists): {$targetFile}\n";
            return;
        }

        $content = File::load($template);
        $content = str_replace(
            ['<<classNamespace>>', '<<subdir>>', '<<name>>'],
            [$classNamespace, $subdir, $name],
            $content
        );

        File::write($targetFile, $content);
        echo "Created model: {$targetFile}\n";
    }

    protected function makeView(string $rawName, bool $force = false): void
    {
        [$subdir, $name] = $this->parseName($rawName);
        if (!empty($subdir)) $subdir = strtolower($subdir);

        $template = $this->resolveTemplatePath('views/blank.twig.txt');
        $targetDir = $this->getDestDirectory('view') . ($subdir ? "{$subdir}/" : "");
        File::makeDir($targetDir);

        $targetFile = $targetDir . strtolower($name) . '.twig';

        if (file_exists($targetFile) && !$force) {
            echo "Skipped (already exists): {$targetFile}\n";
            return;
        }

        $content = File::load($template);
        $content = str_replace(['<<name>>'], [$name], $content);

        File::write($targetFile, $content);
        echo "Created view: {$targetFile}\n";
    }

    protected function buildNamespace(string $base, string $subpath): string
    {
        $base = rtrim($base, '\\');
        $sub = trim(str_replace('/', '\\', $subpath), '\\');
        return $sub ? "{$base}\\{$sub}" : $base;
    }

    protected function initResource(): void
    {
        $projectRoot = $this->getProjectRoot();
        $configFile = $projectRoot . '/.kpfconfig.json';

        if (file_exists($configFile)) {
            echo "Skipped: .kpfconfig.json already exists\n";
            return;
        }

        $config = [
            "resourcePath" => "kpf-resource",
            "destDirs" => [
                "controller" => "controllers",
                "model" => "models",
                "view" => "views"
            ]
        ];
        file_put_contents($projectRoot . '/.kpfconfig.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Created: .kpfconfig.json\n";

        // 필요한 리소스만 복사
        $sourceBase = $this->getResourceBasePath();
        $targetBase = $projectRoot . '/kpf-resource';

        $requiredFiles = [
            'controllers/Blank.php.txt',
            'models/Blank.php.txt',
            'views/blank.twig.txt',
        ];

        foreach ($requiredFiles as $file) {
            $source = $sourceBase . '/' . $file;
            $dest = $targetBase . '/' . $file;

            File::makeDir(dirname($dest));

            if (!file_exists($dest)) {
                copy($source, $dest);
                echo "Copied: {$file}\n";
            } else {
                echo "Skipped (already exists): {$file}\n";
            }
        }

        echo "kpf-resource created.\n";
    }

    protected function resolveTemplatePath(string $fileName): string
    {
        $projectRoot = $this->getProjectRoot();
        $localConfig = $projectRoot . '/.kpfconfig.json';

        if (file_exists($localConfig)) {
            $config = json_decode(file_get_contents($localConfig), true);
            if (!empty($config['resourcePath'])) {
                $customPath = $projectRoot . '/' . trim($config['resourcePath'], '/') . '/' . $fileName;
                if (file_exists($customPath)) {
                    return $customPath;
                }
            }
        }

        return $this->getResourceBasePath() . '/' . $fileName;
    }

    protected function getProjectRoot(): string
    {
        // 테스트 환경 변수 우선
        if (!empty($GLOBALS['EXAMPLE_ROOT'])) {
            return realpath($GLOBALS['EXAMPLE_ROOT']);
        } elseif (!empty($_SERVER['EXAMPLE_ROOT'])) {
            return realpath($_SERVER['EXAMPLE_ROOT']);
        }

        // configure.json 이 존재하는 경로 기준으로 프로젝트 루트 결정
        $candidates = [
            dirname(__DIR__, 4), // vendor 실행 환경
            dirname(__DIR__, 1), // 개발소스 환경
        ];

        foreach ($candidates as $path) {
            if (file_exists($path . '/configure.json')) {
                return realpath($path);
            }
        }

        throw new \RuntimeException("Project root with configure.json not found.");
    }

    protected function getResourceBasePath(): string
    {
        $path = dirname(__DIR__, 1) . '/resource'; // 개발소스 환경

        if (is_dir($path)) {
//            echo realpath($path)."\n";
            return realpath($path);
        }

        throw new \RuntimeException("Resource folder not found.");
    }

    protected function getDestDirectory(string $type): string
    {
        $projectRoot = $this->getProjectRoot();
        $default = Application::getDirectory()->get(
            constant('Kpf\\Constant::DIR_' . strtoupper($type))
        );

        $configPath = $projectRoot . '/.kpfconfig.json';
        if (!is_file($configPath)) return $default;

        $config = json_decode(file_get_contents($configPath), true);
        if (!isset($config['destDirs'][$type])) return $default;

        return rtrim($projectRoot . '/' . trim($config['destDirs'][$type], '/'), '/') . '/';
    }
}