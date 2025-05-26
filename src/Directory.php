<?php

namespace Kpf;

use Kpf\Exception\DirectoryException;

class Directory
{
    const DIRECTORY_SEPARATOR = '/';
    protected $rootDir = Constant::NONE;
    protected $dirMap = [];

    protected $defaultDirs = [
        // 기본 디렉토리 명칭은 Constant 내 문자열을 그대로 사용
        Constant::DIR_CONFIG     => Constant::DIR_CONFIG,
        Constant::DIR_CONTROLLER => Constant::DIR_CONTROLLER,
        Constant::DIR_MODEL      => Constant::DIR_MODEL,
        Constant::DIR_VIEW       => Constant::DIR_VIEW,
        Constant::DIR_TEMP       => Constant::DIR_TEMP,
        Constant::DIR_LOG        => Constant::DIR_LOG,
        Constant::DIR_HOME       => Constant::DIR_HOME,
        Constant::DIR_COMPILE    => Constant::DIR_TEMP . Directory::DIRECTORY_SEPARATOR . Constant::DIR_COMPILE,
    ];

    public function setRoot(string $rootDir): void
    {
        $this->rootDir = rtrim($rootDir, '/');
    }

    public function root(): string
    {
        if ($this->rootDir === Constant::NONE) {
            throw new DirectoryException(Error::REQUIRE_ROOT_DIR);
        }
        return $this->rootDir;
    }

    public function set(string $key, string $path): void
    {
        $this->dirMap[$key] = trim($path, Directory::DIRECTORY_SEPARATOR);
    }

    public function get(string $key): string
    {
        $base = $this->dirMap[$key] ?? ($this->defaultDirs[$key] ?? '');
        return $this->root() . Directory::DIRECTORY_SEPARATOR . $base . Directory::DIRECTORY_SEPARATOR;
    }

    public function getAll(): array
    {
        $result = [];
        foreach (array_keys($this->defaultDirs) as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
}
