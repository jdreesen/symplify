<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/packages.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INDENTATION, Option::INDENTATION_SPACES);
    $parameters->set(Option::LINE_ENDING, PHP_EOL);

    $cacheDirectory = sys_get_temp_dir() . '/changed_files_detector%env(TEST_SUFFIX)%';
    if (StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
        $cacheDirectory .= '_' . StaticVersionResolver::PACKAGE_VERSION;
    }

    $parameters->set(Option::CACHE_DIRECTORY, $cacheDirectory);

    $cacheNamespace = str_replace(DIRECTORY_SEPARATOR, '_', getcwd());
    $parameters->set(Option::CACHE_NAMESPACE, $cacheNamespace);

    // parallel
    $parameters->set(Option::PARALLEL, true);

    // how many files are processed in single process
    $parameters->set(Option::PARALLEL_JOB_SIZE, 60);
    $parameters->set(Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, 16);

    $parameters->set(Option::PATHS, []);
    $parameters->set(Option::FILE_EXTENSIONS, ['php']);

    $parameters->set('env(TEST_SUFFIX)', '');
};
