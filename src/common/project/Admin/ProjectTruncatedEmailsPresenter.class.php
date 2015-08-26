<?php
/**
 * Copyright (c) Enalean, 2015. All Rights Reserved.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class ProjectTruncatedEmailsPresenter {

    /** @var Project */
    private $project;

    /** @var array */
    private $impacted_services_list;

    public function __construct(Project $project, array $impacted_services_list) {
        $this->project                = $project;
        $this->impacted_services_list = $impacted_services_list;
    }
    public function truncated_emails_title() {
        return $GLOBALS['Language']->getText('project_admin_editgroupinfo', 'truncated_emails_title');
    }

    public function use_truncated_emails() {
        return $GLOBALS['Language']->getText('project_admin_editgroupinfo', 'use_truncated_emails');
    }

    public function project_uses_truncated_emails() {
        return (bool) $this->project->getTruncatedEmailsUsage();
    }

    public function impacted_services() {
        return $this->impacted_services_list;
    }

    public function truncated_emails_impacted_services_introduction() {
        return $GLOBALS['Language']->getText('project_admin_editgroupinfo','truncated_emails_impacted_services_introduction');
    }
}