<?php

namespace App\Console\Commands\Src;

use File;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ViewMaker
{
    /**
     * The path where views exists
     */
    CONST VIEWS_PATH = "resources/views/";

    /**
     * The blade file extension
     */
    CONST BLADE_EXT = ".blade.php";

    /**
     * @var array
     */
    protected $resourceViews = ['index', 'create', 'edit', 'show'];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $resource = false;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @return bool
     */
    public function isResource ()
    {
        return $this->resource;
    }

    /**
     * @param $resource
     * @return $this
     */
    public function setResource ($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return array
     */
    protected function getViews ()
    {
        if (! $this->isResource()) {
            return [$this->name];
        }

        return array_map(function ($view){
            return Str::plural($this->name) . '.' . $view;
        }, $this->resourceViews);
    }

    /**
     * @param array $views
     * @return array
     */
    protected function getViewNames (array $views)
    {
        return array_map(function ($view) {
            return self::VIEWS_PATH . str_replace(".", '/', $view) . self::BLADE_EXT;
        }, $views);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setViewName ($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param array $paths
     * @return array
     */
    protected function normalizeDirectoriesPaths (array $paths)
    {
        return array_map(function ($path) {
            return str_replace(strrchr($path, '/'), '', $path);
        }, $paths);
    }

    /**
     * @param array $views
     */
    protected function mkDir (array $views)
    {
        $paths = $this->normalizeDirectoriesPaths($views);
        array_map(function ($path) {
            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }, $paths);
    }

    /**
     * @param array $views
     */
    protected function makeViews (array $views)
    {
        foreach ($views as $view) {
            if (! File::exists($view)) {
                file_put_contents($view, '');
            } else {
                $this->setErrors("This view already exists: " . $view);
            }
        }
    }

    /**
     * @param $error
     */
    protected function setErrors ($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    protected function getErrors ()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    protected function hasErrors ()
    {
        return ! empty($this->getErrors());
    }

    /**
     * @param Command $command
     */
    public function exit (Command $command)
    {
        (!$this->hasErrors()) ?
            $command->info("View created successfully") :
            $command->error(implode("\n", $this->getErrors()));
    }

    /**
     * @return $this
     */
    public function generate ()
    {
        $views = $this->getViewNames($this->getViews());
        $this->mkDir($views);
        $this->makeViews($views);
        return $this;
    }
}
