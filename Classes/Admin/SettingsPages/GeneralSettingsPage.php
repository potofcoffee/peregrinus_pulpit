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

namespace Peregrinus\Pulpit\Admin\SettingsPages;

use Peregrinus\Pulpit\Admin\Setup\SetupSettingsSection;
use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Fields\FileRelationField;
use Peregrinus\Pulpit\Fields\HandoutFormatField;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Fields\TextAreaField;
use Peregrinus\Pulpit\Installer\ComposerWrapper;
use Peregrinus\Pulpit\PostTypes\PostTypeFactory;
use Peregrinus\Pulpit\Settings\Setting;
use Peregrinus\Pulpit\Settings\SettingsSection;
use Peregrinus\Pulpit\Settings\SettingsTab;
use Peregrinus\Pulpit\Taxonomies\TaxonomyFactory;

class GeneralSettingsPage extends AbstractSettingsPage
{

    public function __construct()
    {
        parent::__construct();
        $this->setPageTitle(__('Sermons', 'pulpit'));
        $this->setMenuTitle(__('Sermons', 'pulpit'));


        $permalinksTab = new SettingsTab(
            $this,
            'permalinks',
            __('Permalinks', 'pulpit'),
            []
        );

        $rewriteSettings = [];
        foreach (PostTypeFactory::getAll() as $postType) {
            $rewriteSettings[] = new Setting(
                'slug_' . $postType->getKey(),
                sprintf(__('Permalink for %s', 'pulpit'), __(ucfirst($postType->getKey()), 'pulpit')),
                new InputField(PEREGRINUS_PULPIT.'_slug_' . $postType->getKey(), '')
            );
        }
        foreach (TaxonomyFactory::getAll() as $taxonomy) {
            $rewriteSettings[] = new Setting(
                'slug_' . $taxonomy->getKey(),
                sprintf(__('Permalink for %s', 'pulpit'), __(ucfirst($taxonomy->getKey()), 'pulpit')),
                new InputField(PEREGRINUS_PULPIT.'_slug_' . $taxonomy->getKey(), '')
            );
        }


        $permalinksTab->setSections(
            [
                new SettingsSection(
                    'rewrite',
                    __('URL rewriting', 'pulpit'),
                    __('Here you can define your own texts for the permalinks created by PULPIT:', 'pulpit'),
                    $rewriteSettings
                ),
            ]
        );

        $this->addTab($permalinksTab);

        $this->addTab(new SettingsTab(
                $this,
                'podcast',
                __('Podcast', 'pulpit'),
                [
                    new SettingsSection(
                        'podcast',
                        __('Podcast settings', 'pulpit'),
                        __('Here you can set up the required information for your podcast'),
                        [
                            new Setting(
                                'podcast_title',
                                __('Podcast title', 'pulpit'),
                                new InputField('pulpit_podcast_title', '')
                            ),
                            new Setting(
                                'podcast_description',
                                __('Podcast description', 'pulpit'),
                                new TextAreaField('pulpit_podcast_description', '', 5)
                            ),
                            new Setting(
                                'podcast_image',
                                __('Podcast title image', 'pulpit'),
                                new FileRelationField(
                                    'pulpit_podcast_image',
                                    '',
                                    'image',
                                    __('Select image', 'pulpit'),
                                    __('Select image', 'pulpit')
                                )
                            ),
                            new Setting(
                                'podcast_language',
                                __('Podcast language', 'pulpit'),
                                new InputField('pulpit_podcast_language', '')
                            ),
                            new Setting(
                                'podcast_copyright',
                                __('Podcast copyright', 'pulpit'),
                                new InputField('pulpit_podcast_copyright', '')
                            ),
                            new Setting(
                                'podcast_author_name',
                                __('Name of the podcast author', 'pulpit'),
                                new InputField('pulpit_podcast_author_name', '')
                            ),
                            new Setting(
                                'podcast_author_email',
                                __('Email address of the podcast author', 'pulpit'),
                                new InputField('pulpit_podcast_author_email', '')
                            ),
                            new Setting(
                                'podcast_category',
                                __('Content category', 'pulpit'),
                                new InputField('pulpit_podcast_category', '')
                            ),
                        ]
                    )

                ]
            )
        );
        $this->addTab(new SettingsTab(
                $this,
                'liturgy',
                __('Liturgy', 'pulpit'),
                [
                    new SettingsSection(
                        'liturgy',
                        __('Liturgy planning', 'pulpit'),
                        __('Here you can set up some features for liturgy planning.'),
                        [
                            new Setting(
                                'agenda_instructions',
                                __('Provide agenda instructions for', 'pulpit'),
                                new InputField('pulpit_agenda_instructions', '')
                            ),
                            new Setting(
                                'default_handout_layout',
                                __('Default handout layout', 'pulpit'),
                                new HandoutFormatField('default_handout_layout', '')
                            )
                        ]
                    ),
                ]
            )
        );
        $this->addTab(new SettingsTab(
                $this,
                'setup',
                __('Setup', 'pulpit'),
                [
                    new SetupSettingsSection('setup')
                ]
            )
        );

    }

    public function headerFunctions()
    {
        parent::headerFunctions();

        $composer = new ComposerWrapper();
        if ($_REQUEST['composerUpdate']) {
            // composer update requested
            $this->notice(
                'info',
                '<div class="spinner"></div> ' . __('Updating external components...', 'pulpit'),
                false,
                'composer-update-notice'
            );
            echo '<script type="text/javascript" src="'
                . PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Updater.js"></script>';
        } else {
            $updateUrl = $this->getUrl(['composerUpdate' => 1]);
            if ($composer->isOutdated()) {
                $this->notice('warning',
                    sprintf(
                        __("It looks like some of the external components PULPIT uses need to be updated. <a href=\"%s\">Click here</a> to load all necessary updates.",
                            'pulpit'),
                        $updateUrl
                    ),
                    false
                );
            } else {
                $this->notice('info', __('Good news: All external components for PULPIT are up to date.', 'pulpit'));
            }
        }
    }
}
