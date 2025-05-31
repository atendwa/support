<?php

declare(strict_types=1);

namespace Atendwa\Support\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class FindClassesUsingTrait
{
    /**
     * @param  class-string  $trait
     * @param  string|array<string>  $dirs
     *
     * @return Collection<(int|string), mixed>
     */
    public function execute(string $trait, string|array $dirs): Collection
    {
        $classes = collect();

        $dirs = match (is_string($dirs)) {
            true => [$dirs],
            false => $dirs,
        };

        collect($dirs)->each(function ($dir) use (&$classes, $trait): void {
            if (File::isDirectory($dir) === false) {
                return;
            }

            collect(File::allFiles($dir))->each(function ($file) use ($trait, &$classes): void {
                $filePath = $file->getPathname();
                $namespace = $this->getNamespaceFromFile($filePath);
                $class = $namespace . '\\' . pathinfo($filePath, PATHINFO_FILENAME);

                if (! class_exists($class)) {
                    return;
                }

                $reflectionClass = new ReflectionClass($class);

                when(
                    $reflectionClass->isInstantiable() && in_array($trait, $reflectionClass->getTraitNames()),
                    fn () => $classes->push($class)
                );
            });
        });

        return $classes;
    }

    private function getNamespaceFromFile(string $filePath): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', (string) file_get_contents($filePath), $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
