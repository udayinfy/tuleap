<?php
/**
 * Copyright (c) Enalean, 2013. All Rights Reserved.
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

class Tracker_Workflow_Trigger_FieldValue {
    private $field;
    private $value;

    public function __construct(
            Tracker_FormElement_Field_List $field,
            Tracker_FormElement_Field_List_BindValue $value) {
        $this->field = $field;
        $this->value = $value;
     }

    /**
     * @return Tracker_FormElement_Field_List
     */
    public function getField() {
        return $this->field;
    }

    /**
     * @return Tracker_FormElement_Field_List_BindValue
     */
    public function getValue() {
        return $this->value;
    }
}

?>
