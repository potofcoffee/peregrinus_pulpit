<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2017 Christoph Fischer, http://www.peregrinus.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Peregrinus\Pulpit;

use Peregrinus\Pulpit\Admin\Admin;
use Peregrinus\Pulpit\Admin\AjaxActions\AbstractAjaxAction;
use Peregrinus\Pulpit\Admin\AjaxActions\AjaxActionFactory;
use Peregrinus\Pulpit\Admin\ExternalPlayers\AbstractExternalPlayer;
use Peregrinus\Pulpit\Admin\ExternalPlayers\ExternalPlayerFactory;
use Peregrinus\Pulpit\Admin\Importer;
use Peregrinus\Pulpit\Admin\Installer;
use Peregrinus\Pulpit\Admin\Scheduler;
use Peregrinus\Pulpit\Admin\Setup\Components\ComponentFactory;
use Peregrinus\Pulpit\CustomFormats\AbstractCustomFormat;
use Peregrinus\Pulpit\CustomFormats\CustomFormatFactory;
use Peregrinus\Pulpit\Hooks\AbstractHook;
use Peregrinus\Pulpit\Hooks\HookFactory;
use Peregrinus\Pulpit\PostStatuses\AbstractPostStatus;
use Peregrinus\Pulpit\PostStatuses\PostStatusFactory;
use Peregrinus\Pulpit\PostTypes\AbstractPostType;
use Peregrinus\Pulpit\PostTypes\PostTypeFactory;
use Peregrinus\Pulpit\ShortCodes\ShortCodeFactory;
use Peregrinus\Pulpit\Taxonomies\AbstractTaxonomy;
use Peregrinus\Pulpit\Taxonomies\TaxonomyFactory;

/**
 * Class PulpitPlugin
 * Provides basic plugin registration
 * @package Peregrinus\Pulpit
 */
class PulpitPlugin
{

    private static $instance = null;

    /**
     * Pulpit constructor.
     */
    public function __construct()
    {
        \register_activation_hook(PEREGRINUS_PULPIT_ENTRY_SCRIPT, [Installer::class, 'activate']);
        \register_deactivation_hook(PEREGRINUS_PULPIT_ENTRY_SCRIPT, [Installer::class, 'deactivate']);
        \register_uninstall_hook(PEREGRINUS_PULPIT_ENTRY_SCRIPT, [Installer::class, 'uninstall']);

        \load_plugin_textdomain('pulpit', false,
            PEREGRINUS_PULPIT_DOMAIN_PATH);

        add_action('init', [$this, 'init']);
        add_action('admin_init', [Admin::class, 'init']);

        if (is_admin()) {
            $admin = new Admin();
            $admin->registerFilters();
            $admin->registerSettingsPages();

            add_action('admin_enqueue_scripts', [$this, 'addCSS']);
            add_action('admin_enqueue_scripts', [$this, 'addJS']);
            add_action('admin_menu', [$this, 'adminMenu']);
        }

    }

    /**
     * Creates or returns an instance of this class.
     *
     * @return  \Peregrinus\Pulpit\Pulpit A single instance of this class.
     */
    public
    static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Initialize the plugin's registrations
     */
    public function init()
    {
        /** @var AbstractPostType $postType */
        foreach (PostTypeFactory::getAll() as $postType) {
            $postType->register();

        }

        /** @var AbstractAjaxAction $ajaxAction */
        foreach (AjaxActionFactory::getAll() as $ajaxAction) {
            $ajaxAction->register();
        }

        /** @var AbstractComponent $component */
        foreach (ComponentFactory::getAll() as $component) {
            $component->register();
        }

        /** @var AbstractTaxonomy $taxonomy */
        foreach (TaxonomyFactory::getAll() as $taxonomy) {
            $taxonomy->register();
        }
        \flush_rewrite_rules();

        /** @var AbstractCustomFormat $customFormat */
        foreach (CustomFormatFactory::getAll() as $customFormat) {
            $customFormat->register();
        }

        /** @var AbstractCustomFormat $shortcode */
        foreach (ShortCodeFactory::getAll() as $shortcode) {
            $shortcode->register();
        }


        /** @var AbstractPostStatus $postStatus */
        foreach (PostStatusFactory::getAll() as $postStatus) {
            $postStatus->register();
        }

        /** @var AbstractExternalPlayer $player */
        foreach (ExternalPlayerFactory::getAll() as $player) {
            $player->register();
        }

        // load further hooks
        /** @var AbstractHook $hook */
        foreach (HookFactory::getAll() as $hook) {
            $hook->register();
        }



        $scheduler = new Scheduler();
        $scheduler->register();
    }

    public function addCSS()
    {
        wp_enqueue_style('pulpit-admin-fontawesom', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');
        wp_enqueue_style('pulpit-admin-styles', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Styles/Admin/Admin.css');
        wp_enqueue_style('thickbox'); //Provides the styles needed for this window.
    }

    public function addJS()
    {
    }


    public function adminMenu() {
        add_menu_page(
            __( 'Church services', 'pulpit' ),
            __( 'Church services', 'pulpit' ),
            'edit_posts',
            'edit.php?post_type=pulpit_event',
            '',
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Images/PostTypes/Sermon.svg',
            6
        );

        // https://christoph-fischer.org/wp-admin/edit-tags.php?taxonomy=post_tag
        /** @var AbstractTaxonomy $taxonomy */
        foreach (TaxonomyFactory::getAll() as $taxonomy) {
            add_submenu_page(
                'edit.php?post_type=pulpit_event',
                $taxonomy->labels['name'],
                $taxonomy->labels['name'],
                'edit_posts',
                'edit-tags.php?taxonomy='.$taxonomy->getName(),
                '',
                PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Images/Taxonomies/'.ucfirst($taxonomy->getKey()).'.svg'
            );
        }

        /** @var AbstractPostType $postType */
        foreach (PostTypeFactory::getAll() as $postType) {
            if ($postType->getKey() !== 'event') {
                add_submenu_page(
                    'edit.php?post_type=pulpit_event',
                    $postType->labels['name'],
                    $postType->labels['name'],
                    'edit_posts',
                    'edit.php?post_type='.$postType->getTypeName(),
                    '',
                    PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Images/PostTypes/'.ucfirst($postType->getKey()).'.svg'
                );

            }
        }

        // add import page
        add_submenu_page(
            null,
            __('Import', 'pulpit'),
            __('Import', 'pulpit'),
            'manage_options',
            PEREGRINUS_PULPIT.'_import',
            [Importer::class, 'page']
        );
    }
}
