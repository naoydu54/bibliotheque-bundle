<?php

namespace Ip\BibliothequeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ip_bibliotheque');
        $rootNode
            ->children()
                ->scalarNode('assets_path')->defaultValue('/assets/ipbibliotheque')->end()
                ->scalarNode('root_folder')->end()
                ->booleanNode('include_assets')->end()
                ->booleanNode('include_jQuery')->end()
                ->booleanNode('include_bootstrap')->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->scalarNode('default_image')->defaultValue('/assets/ipbibliotheque/plugins/bibliotheque/assets/no-image.jpg')->end()
            ->end()
        ;
        $this->addFileSection($rootNode);
        $this->addFolderSection($rootNode);
        return $treeBuilder;
    }

    public function addFileSection(ArrayNodeDefinition $node){
        $node
            ->children()
                ->arrayNode('file')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('file_manager')->defaultValue('ip_bibliotheque.file_manager.default')->end()
                        ->scalarNode('file_class')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function addFolderSection(ArrayNodeDefinition $node){
        $node
            ->children()
                ->arrayNode('folder')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('folder_manager')->defaultValue('ip_bibliotheque.folder_manager.default')->end()
                        ->scalarNode('folder_class')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
