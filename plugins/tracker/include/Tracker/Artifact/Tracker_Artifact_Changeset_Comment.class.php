<?php
/**
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi. If not, see <http://www.gnu.org/licenses/>.
 */

require_once('common/date/DateHelper.class.php');

class Tracker_Artifact_Changeset_Comment {

    /**
     * @const Changeset comment format is text.
     */
    const TEXT_COMMENT = 'text';

    /**
     * @const Changeset comment format is HTML
     */
    const HTML_COMMENT = 'html';

    /**
    * @const Changeset available comment formats
    */
    private static $available_comment_formats = array(
        self::TEXT_COMMENT,
        self::HTML_COMMENT,
    );

    public $id;
    /**
     *
     * @var Tracker_Artifact_Changeset
     */
    public $changeset;
    public $comment_type_id;
    public $canned_response_id;
    public $submitted_by;
    public $submitted_on;
    public $body;
    public $bodyFormat;
    public $parent_id;

    /**
     * @var array of purifier levels to be used when the comment is displayed in text/plain context
     */
    public static $PURIFIER_LEVEL_IN_TEXT = array(
        'html' => CODENDI_PURIFIER_STRIP_HTML,
        'text' => CODENDI_PURIFIER_DISABLED,
    );

    /**
     * @var array of purifier levels to be used when the comment is displayed in text/html context
     */
    public static $PURIFIER_LEVEL_IN_HTML = array(
        'html' => CODENDI_PURIFIER_FULL,
        'text' => CODENDI_PURIFIER_BASIC,
    );

    /**
     * Constructor
     *
     * @param int                        $id                 Changeset comment Id
     * @param Tracker_Artifact_Changeset $changeset          The associated changeset
     * @param int                        $comment_type_id    The comment type Id
     * @param int                        $canned_response_id The canned response Id
     * @param int                        $submitted_by       The Id of the user that made the comment
     * @param int                        $submitted_on       The date the comment has been done
     * @param string                     $body               The comment (aka follow-up comment)
     * @param string                     $bodyFormat         The comment type (text or html follow-up comment)
     * @param int                        $parent_id          The id of the parent (if comment has been modified)
     */
    public function __construct($id,
                                $changeset,
                                $comment_type_id,
                                $canned_response_id,
                                $submitted_by,
                                $submitted_on,
                                $body,
                                $bodyFormat,
                                $parent_id) {
        $this->id                 = $id;
        $this->changeset          = $changeset;
        $this->comment_type_id    = $comment_type_id;
        $this->canned_response_id = $canned_response_id;
        $this->submitted_by       = $submitted_by;
        $this->submitted_on       = $submitted_on;
        $this->body               = $body;
        $this->bodyFormat         = $bodyFormat;
        $this->parent_id          = $parent_id;
    }

    /**
     * @return string the cleaned body to be included in a text/plain context
     */
    public function getPurifiedBodyForText() {
        $level = self::$PURIFIER_LEVEL_IN_TEXT[$this->bodyFormat];
        return $this->purifyBody($level);
    }

    /**
     * @return string the cleaned body to be included in a text/html context
     */
    public function getPurifiedBodyForHTML() {
        $level = self::$PURIFIER_LEVEL_IN_HTML[$this->bodyFormat];
        return $this->purifyBody($level);
    }

    private function purifyBody($level) {
        $hp = Codendi_HTMLPurifier::instance();
        return $hp->purify($this->body, $level, $this->changeset->artifact->getTracker()->group_id);
    }

    /**
     * Returns the HTML code of this comment
     *
     * @param String  $format          Format of the output
     * @param Boolean $forMail         If the output is intended for mail notification then value should be true
     * @param Boolean $ignoreEmptyBody If true then display the user and the time even if the body is empty
     *
     * @return string the HTML code of this comment
     */
    public function fetchFollowUp($format='html', $forMail = false, $ignoreEmptyBody = false) {
        if ($ignoreEmptyBody || !empty($this->body)) {
            $uh = UserHelper::instance();
            $hp = Codendi_HTMLPurifier::instance();
            switch ($format) {
                case 'html':
                    $html = '';
                    if ($forMail) {
                        $html .= '<div class="tracker_artifact_followup_title">';
                        $html .= '<span class="tracker_artifact_followup_title_user">';
                        $user = UserManager::instance()->getUserById($this->submitted_by);
                        if ($user && !$user->isAnonymous()) {
                            $html .= '<a href="mailto:'.$hp->purify($user->getEmail()).'">'.$hp->purify($user->getRealName()).' ('.$hp->purify($user->getUserName()) .')</a>';
                        } else {
                            $user = UserManager::instance()->getUserAnonymous();
                            $user->setEmail($this->changeset->getEmail());
                            $html .= $GLOBALS['Language']->getText('tracker_include_artifact','anon_user');
                        }
                        $html .= '</span></div>';
                        $timezone = '';
                        if ($user->getId() != 0) {
                            $timezone = ' ('.$user->getTimezone().')';
                        }
                        $html .= '<div class="tracker_artifact_followup_date">'. format_date($GLOBALS['Language']->getText('system', 'datefmt'), $this->submitted_on).$timezone.'</div>';
                        $html .= '</div>';
                        if (Config::get('sys_enable_avatars')) {
                            $html .= '<div class="tracker_artifact_followup_avatar">';
                            $html .= $user->fetchHtmlAvatar();
                            $html .= '</div>';
                        }
                        $html .= '<div class="tracker_artifact_followup_content">';
                        $html .= '<div class="tracker_artifact_followup_comment">';
                    } else {
                        $html .= '<div class="tracker_artifact_followup_comment_edited_by">';
                        if ($this->parent_id) {
                            $html .= $GLOBALS['Language']->getText('plugin_tracker_include_artifact', 'last_edited');
                            $html .= ' '. $uh->getLinkOnUserFromUserId($this->submitted_by) .' ';
                            $html .= DateHelper::timeAgoInWords($this->submitted_on, false, true);
                        }
                        $html .= '</div>';
                    }
                    if (!$forMail || !empty($this->body)) {
                        $html .= '<input type="hidden" id="tracker_artifact_followup_comment_body_format_'.$this->changeset->getId().'" name="tracker_artifact_followup_comment_body_format_'.$this->changeset->getId().'" value="'.$this->bodyFormat.'" >';
                        $html .= '<div class="tracker_artifact_followup_comment_body">';
                        if ($this->parent_id && !trim($this->body)) {
                            $html .= '<em>'. $GLOBALS['Language']->getText('plugin_tracker_include_artifact', 'comment_cleared') .'</em>';
                        } else {
                            $html .= $this->getPurifiedBodyForHTML();
                        }
                        $html .= '</div>';
                    }
                    if ($forMail) {
                        $html .= '</div>';
                    }
                    return $html;
                    break;
                default:
                    $output = '';
                    //if ($this->parent_id) {
                    //$output .= $GLOBALS['Language']->getText('plugin_tracker_include_artifact', 'last_edited');
                    //$output .= ' '.$uh->getDisplayNameFromUserId($this->submitted_by);
                    //$output .= ' '.DateHelper::timeAgoInWords($this->submitted_on).PHP_EOL;
                    //}
                    if ( !empty($this->body) ) {
                        $body    = $this->getPurifiedBodyForText();
                        $output .= PHP_EOL.PHP_EOL.$body.PHP_EOL.PHP_EOL;
                    }
                    return $output;
                    break;
            }
        } else {
            return null;
        }
    }

    /**
     *
     * @return bool
     */
    public function hasEmptyBody() {
        return empty($this->body);
    }

    /**
     * Returns the HTML code of this comment
     *
     * @param String  $format Format of the output
     * @return string the HTML code of this comment
     */
    public function fetchMailFollowUp($format = 'html') {
        if ($format != 'html') {
            if ($this->hasEmptyBody()) {
                return '';
            }

            $body = $this->getPurifiedBodyForText();
            return PHP_EOL.PHP_EOL.$body.PHP_EOL.PHP_EOL;
        }
        
        $user     = UserManager::instance()->getUserById($this->submitted_by);
        $avatar   = (Config::get('sys_enable_avatars')) ? $user->fetchHtmlAvatar() : '';
        $timezone = ($user->getId() != 0) ? ' ('.$user->getTimezone().')' : '';

        $html =
            '<tr>
                <td align="left">'.
                    $avatar.'
                </td>
                <td align="left" valign="top" colspan="2">
                    <div style="
                        padding:15px;
                        margin:10px;
                        min-height:50px;
                        border: 1px solid #f6f6f6;
                        border-top: none;
                        -webkit-border-radius:20px;
                        border-radius:20px;
                        -moz-border-radius:20px;
                        background-color:#F6F6F6;"
                    >
                        <table style="width:100%; background-color:#F6F6F6;">
                            <tr>
                                <td>
                                    <span> '.
                                        $this->fetchFormattedMailUserInfo($user).'
                                    </span>
                                </td>
                                <td align="right" valign="top">
                                    <div style="text-align:right;font-size:0.95em;color:#666;">'.
                                        format_date($GLOBALS['Language']->getText('system', 'datefmt'), $this->submitted_on).
                                        $timezone.'
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" >'.
                                    $this->fetchFormattedMailComment() . ' ' .'
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>';
        
        return $html;
    }

    /**
     * Check the comment format, to ensure it is in
     * a known one.
     *
     * @param string $comment_format the format of the comment
     *
     * @return string $comment_format
     */
    public static function checkCommentFormat($comment_format) {
        if (! in_array($comment_format, self::$available_comment_formats)) {
            $comment_format = Tracker_Artifact_Changeset_Comment::TEXT_COMMENT;
        }

        return $comment_format;
    }

    public function exportToSOAP() {
        if (! $this->body) {
            return null;
        }

        return array(
            'submitted_by' => $this->changeset->getSubmittedBy(),
            'email'        => $this->getEmailForUndefinedSubmitter(),
            'submitted_on' => $this->submitted_on,
            'body'         => $this->body,
        );
    }

    private function getEmailForUndefinedSubmitter() {
        if (! $this->changeset->getSubmittedBy()) {
            return $this->changeset->getEmail();
        }
    }

    private function fetchFormattedMailComment() {
        $formatted_comment = '';
        if (!empty($this->body)) {
           if ($this->parent_id && !trim($this->body)) {
               $comment =
                '<em>'.
                    $GLOBALS['Language']->getText('plugin_tracker_include_artifact', 'comment_cleared') .'
                </em>';
           } else {
               $comment = $this->getPurifiedBodyForHTML();
           }

           $formatted_comment =
            '<div>
                 <input type="hidden"
                     id="tracker_artifact_followup_comment_body_format_'.$this->changeset->getId().'"
                     name="tracker_artifact_followup_comment_body_format_'.$this->changeset->getId().'"
                     value="'.$this->bodyFormat.'"
                 />
                 <div style="border-color: #e8ebb5;
                     -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
                     -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
                     box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
                     margin: 1em 0;
                     padding: 0.5em 1em;"
                 >'.
                     $comment.'
                 </div>
             </div>';
        }

        return $formatted_comment;
    }

    private function fetchFormattedMailUserInfo(User $user) {
        $hp = Codendi_HTMLPurifier::instance();

        if ($user && !$user->isAnonymous()) {
            $user_info =
                '<a href="mailto:'.$hp->purify($user->getEmail()).'">'.
                    $hp->purify($user->getRealName()).' ('.$hp->purify($user->getUserName()) .')
                </a>';
        } else {
            $user = UserManager::instance()->getUserAnonymous();
            $user->setEmail($this->changeset->getEmail());
            $user_info = $GLOBALS['Language']->getText('tracker_include_artifact','anon_user');
        }

        return $user_info;
    }
}
?>
