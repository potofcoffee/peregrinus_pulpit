<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2018 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\Settings;

use Peregrinus\Pulpit\Admin\SettingsPages\AbstractSettingsPage;

class SettingsTab
{

    protected $title = '';
    protected $key = '';
    protected $sections = [];
    /** @var AbstractSettingsPage $page Parent page  */
    protected $page = null;

    public function __construct(AbstractSettingsPage $page, string $key, string $title, array $sections)
    {
        $this->setPage($page);
        $this->setKey($key);
        $this->setTitle($title);
        $this->setSections($sections);
    }

    /**
     * Register all child sections
     */
    public function register() {
        /** @var SettingsSection $section */
        foreach ($this->sections as $section) {
            $section->register($this);
        }
    }

    /**
     * Get the slug for this tab
     * @return string Slug
     */
    public function getSlug() {
        return $this->page->getSlug().'-'.$this->getKey();
    }

    /**
     * Render the tab at the top of the settings page
     * @param bool $active Is this the active tab?
     */
    public function renderTabTitle(bool $active) {
        return '<a href="?page='.$this->page->getSlug().'&tab='.$this->getKey().'" class="nav-tab '
            .($active ? 'nav-tab-active' : '').'">'.$this->getTitle().'</a>';
    }

    /**
     * Render the tab content
     */
    public function render() {
        settings_fields($this->page->getOptionGroupName());
        do_settings_sections($this->getSlug());
        submit_button();
    }



    /**
     * @return AbstractSettingsPage
     */
    public function getPage(): AbstractSettingsPage
    {
        return $this->page;
    }

    /**
     * @param AbstractSettingsPage $page
     */
    public function setPage(AbstractSettingsPage $page): void
    {
        $this->page = $page;
    }



    /**
     * @return string|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string|string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string|string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }



}
