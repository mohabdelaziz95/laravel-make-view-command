<?php

namespace App\Console\Commands\Src;

use File;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ViewMaker
{

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
     * @var string
     */
    protected $extends;

    /**
     * @var string
     */
    protected $contents = "";

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
            return Path::generate($view);
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
     * @param array $views
     */
    protected function makeViews (array $views)
    {
        foreach ($views as $view) {
            if (! File::exists($view)) {
                if ($this->isExtends()) {
                    $this->extend($this->getLayout());
                }
                if (! $this->hasErrors()) {
                    file_put_contents($view, $this->getContents());
                }

            } else {
                $this->setErrors("This view already exists: " . $view);
            }
        }
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setExtends ($layout)
    {
        $this->extends = $layout;
        return $this;
    }

    /**
     * @return bool
     */
    protected function isExtends ()
    {
        return ! is_null($this->extends);
    }

    /**
     * @return string
     */
    protected function getLayout ()
    {
        return $this->extends;
    }

    /**
     * @param string $layout
     */
    protected function extend ($layout)
    {
        $path = Path::generate($layout);
        (file_exists($path)) ? $this->setContents("@extends(" . "'$layout'" . ")") : $this->setErrors("this layout does not exists: " . $path);
    }

    /**
     * @param string $contents
     */
    protected function setContents ($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    protected function getContents ()
    {
        return $this->contents;
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
        Path::generateIntermediateDirectories($views[0]);
        $this->makeViews($views);
        return $this;
    }
}
