<?php
/**
 * Copyright Enalean (c) 2014. All rights reserved.
 *
 * Tuleap and Enalean names and logos are registrated trademarks owned by
 * Enalean SAS. All other trademarks or names are properties of their respective
 * owners.
 *
 * This file is a part of Tuleap.
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
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class AgileDashboard_Milestone_Pane_Planning_PlanningV2Presenter {

    /** @var int */
    public $project_id;

    /** @var int */
    public $milestone_id;

    /** @var string */
    public $lang;

    public function __construct(PFUser $current_user, Project $project, $milestone_id) {
        $this->lang                  = $this->getLanguageAbbreviation($current_user);
        $this->project_id            = $project->getId();
        $this->milestone_id          = $milestone_id;
        $this->use_angular_new_modal = (ForgeConfig::get('use_angular_new_modal')) ? 'true' : 'false';
    }

    private function getLanguageAbbreviation($current_user) {
        list($lang, $country) = explode('_', $current_user->getLocale());

        return $lang;
    }
}
