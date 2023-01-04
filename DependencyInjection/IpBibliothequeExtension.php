<?php

namespace Ip\BibliothequeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class IpBibliothequeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        //$container->setParameter('ip_bibliotheque', $config);

        foreach ($config as $key => $value) {
            $container->setParameter('ip_bibliotheque.'.$key, $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                '' => 'ip_bibliotheque.',
                'root_folder' => 'ip_bibliotheque.root_folder',
                'model_manager_name' => 'ip_bibliotheque.model_manager_name'
            ),
        ));

        $this->loadFiles($config['file'], $container, $loader);
        $this->loadFolders($config['folder'], $container, $loader);

        $this->registerWidget($container);
    }

    public function loadFiles(array $config, ContainerBuilder $container, Loader\YamlFileLoader $loader)
    {
        $loader->load('file.yml');
        $container->setAlias('ip_bibliotheque.file_manager', $config['file_manager']);

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                'file_class' => 'ip_bibliotheque.model.file.class',
            ),
        ));
    }

    public function loadFolders(array $config, ContainerBuilder $container, Loader\YamlFileLoader $loader)
    {
        $loader->load('folder.yml');
        $container->setAlias('ip_bibliotheque.folder_manager', $config['folder_manager']);

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                'folder_class' => 'ip_bibliotheque.model.folder.class',
            ),
        ));
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $namespaces
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function registerWidget(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');
        if (in_array('twig', $templatingEngines)) {
            $formRessource = 'IpBibliothequeBundle:Form:ip_bibliotheque_single_widget.html.twig';
            $container->setParameter('twig.form.resources', array_merge(
                $container->getParameter('twig.form.resources'),
                array($formRessource)
            ));

            $formRessource = 'IpBibliothequeBundle:Form:ip_bibliotheque_multiple_widget.html.twig';
            $container->setParameter('twig.form.resources', array_merge(
                $container->getParameter('twig.form.resources'),
                array($formRessource)
            ));
        }
    }
}
