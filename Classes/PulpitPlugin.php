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
use Peregrinus\Pulpit\Admin\Installer;
use Peregrinus\Pulpit\Admin\Scheduler;
use Peregrinus\Pulpit\CustomFormats\AbstractCustomFormat;
use Peregrinus\Pulpit\CustomFormats\CustomFormatFactory;
use Peregrinus\Pulpit\PostTypes\AbstractPostType;
use Peregrinus\Pulpit\PostTypes\PostTypeFactory;
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
        }

        add_action('admin_enqueue_scripts', [$this, 'addCSS']);
        add_action('admin_enqueue_scripts', [$this, 'addJS']);
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

        /** @var AbstractTaxonomy $taxonomy */
        foreach (TaxonomyFactory::getAll() as $taxonomy) {
            $taxonomy->register();
        }
        \flush_rewrite_rules();

        /** @var AbstractCustomFormat $customFormat */
        foreach (CustomFormatFactory::getAll() as $customFormat) {
            $customFormat->register();
        }

        $scheduler = new Scheduler();
        $scheduler->register();
    }

    public function addCSS()
    {
        wp_enqueue_style('pulpit-admin-styles', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Styles/Admin/Admin.css');
        wp_enqueue_style('thickbox'); //Provides the styles needed for this window.
    }

    public function addJS()
    {
        wp_enqueue_script('media-upload'); //Provides all the functions needed to upload, validate and give format to files.
        wp_enqueue_script('thickbox'); //Responsible for managing the modal window.
        wp_enqueue_script('pulpit-uploader', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Uploader.js');
        wp_enqueue_script('pulpit-speech', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Speech.js');
        wp_localize_script(
            'pulpit-speech',
            'pulpit_speech',
            [
                'speech_time' => __('Speaking time', 'pulpit')
            ]
        );
    }

}
