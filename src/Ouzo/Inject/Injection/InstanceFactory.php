<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AnnotationMetadataProvider;
use Ouzo\Injection\Creator\InstanceCreator;
use Ouzo\Utilities\Arrays;
use ReflectionClass;

class InstanceFactory
{
    /** @var Bindings */
    private $bindings;
    /** @var AnnotationMetadataProvider */
    private $provider;
    /** @var InstanceCreator */
    private $eagerInstanceCreator;
    /** @var InstanceCreator */
    private $lazyInstanceCreator;

    public function __construct(
        Bindings $bindings,
        AnnotationMetadataProvider $provider,
        InstanceCreator $eagerInstanceCreator,
        InstanceCreator $lazyInstanceCreator
    )
    {
        $this->bindings = $bindings;
        $this->provider = $provider;
        $this->eagerInstanceCreator = $eagerInstanceCreator;
        $this->lazyInstanceCreator = $lazyInstanceCreator;
    }

    public function createInstance(InstanceRepository $repository, string $className, bool $eager = true): object
    {
        $instance = $this->constructInstance($repository, $className, $eager);
        if ($eager) {
            $this->injectDependencies($repository, $instance);
        }

        return $instance;
    }

    public function createInstanceThroughFactory(InstanceRepository $repository, string $className, Factory $factory, bool $eager = true): object
    {
        if ($eager || $this->lazyInstanceCreator === $this->eagerInstanceCreator) {
            return $this->eagerInstanceCreator->createThroughFactory($className, null, $repository, $this, $factory);
        }

        return $this->lazyInstanceCreator->createThroughFactory($className, null, $repository, $this, $factory);
    }

    private function injectDependencies(InstanceRepository $repository, object $instance, ReflectionClass $class = null): void
    {
        $parent = true;
        if ($class == null) {
            $class = new ReflectionClass($instance);
            $parent = false;
        }
        $annotations = $this->provider->getMetadata($class, $parent);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $annotation = Arrays::getValue($annotations, $property->getName());
            if ($annotation) {
                $dependencyInstance = $this->getInstance($repository, $annotation);
                $property->setAccessible(true);
                $property->setValue($instance, $dependencyInstance);
            }
        }
        $parentClass = $class->getParentClass();
        if ($parentClass) {
            $this->injectDependencies($repository, $instance, $parentClass);
        }
    }

    private function constructInstance(InstanceRepository $repository, string $className, bool $eager = true): object
    {
        if ($eager || $this->lazyInstanceCreator === $this->eagerInstanceCreator) {
            $arguments = $this->getConstructorArguments($repository, $className);
            return $this->eagerInstanceCreator->create($className, $arguments, $repository, $this);
        }
        return $this->lazyInstanceCreator->create($className, null, $repository, $this);
    }

    private function getConstructorArguments(InstanceRepository $repository, string $className): array
    {
        $annotations = $this->provider->getConstructorMetadata($className);
        return Arrays::map($annotations, function ($annotation) use ($repository) {
            return $this->getInstance($repository, $annotation);
        });
    }

    private function getInstance(InstanceRepository $repository, array $annotation): object
    {
        $binder = $this->bindings->getBinder($annotation['className'], $annotation['name']);
        return $repository->getInstance($this, $binder);
    }
}
