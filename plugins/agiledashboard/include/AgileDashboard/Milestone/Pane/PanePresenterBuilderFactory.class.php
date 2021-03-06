<?php
/**
 * Copyright Enalean (c) 2013. All rights reserved.
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

/**
 * Like RepRap, I build builders
 */
class AgileDashboard_Milestone_Pane_PanePresenterBuilderFactory {

    /** @var AgileDashboard_Milestone_Backlog_BacklogStrategyFactory */
    private $strategy_factory;

    /** @var AgileDashboard_Milestone_Backlog_BacklogItemCollectionFactory */
    private $row_collection_factory;

    public function __construct(
        AgileDashboard_Milestone_Backlog_BacklogStrategyFactory $strategy_factory,
        AgileDashboard_Milestone_Backlog_BacklogItemCollectionFactory $row_collection_factory
    ) {
        $this->strategy_factory       = $strategy_factory;
        $this->row_collection_factory = $row_collection_factory;
    }

    /**
     * @return AgileDashboard_Milestone_Pane_Content_ContentPresenterBuilder
     */
    public function getContentPresenterBuilder() {
        return new AgileDashboard_Milestone_Pane_Content_ContentPresenterBuilder(
            $this->strategy_factory,
            $this->row_collection_factory
        );
    }
}
