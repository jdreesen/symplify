<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\HttpKernel;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use Symplify\SymplifyKernel\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;

/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    private Container|null $container = null;

    /**
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());

        $compilerPasses[] = new AutowireArrayParameterCompilerPass();

        $configFiles[] = SymplifyKernelConfig::FILE_PATH;

        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();

        $this->container = $containerBuilder;

        return $containerBuilder;
    }

    public function getContainer(): ContainerInterface
    {
        if (! $this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }

        return $this->container;
    }
}
