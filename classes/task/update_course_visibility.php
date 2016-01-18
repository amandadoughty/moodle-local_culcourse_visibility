<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A scheduled task for updating the course visibility based on the start date.
 *
 * @package   local_culcourse_visibility
 * @category  task
 * @copyright 2016 Tim Gagen and Amanda Doughty
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_culcourse_visibility\task;

class update_course_visibility extends \core\task\scheduled_task {

    public function get_name() {
        // Shown in admin screens.
        return get_string('updatecoursevisibility', 'local_culcourse_visibility');
    }

    /**
     * Update course visibility if the startdate has become due.
     * @return void
     */
    public function execute() {
        global $CFG, $DB;

        $start = time();
        // Get list of courses to update.
        mtrace("\n  Searching for courses to make visible ...");
        // Use the configured timezone.
        date_default_timezone_set($CFG->timezone);
        // Course start dates are always set to midnight but we will check the whole day in case the value has been
        // manually updated.
        $beginofday = strtotime('midnight', time());
        $endofday   = strtotime('tomorrow', $beginofday) - 1;
        // If startdate is today and visibility = 0 then set visibility = 1.
        $select = "visible = 0 AND startdate BETWEEN {$beginofday} AND {$endofday}";

        if ($courses = $DB->get_records_select('course', $select)) {
            foreach ($courses as $course) {
                if (!$DB->set_field('course', 'visible', 1 , array('id' => $course->id))) {
                    mtrace("    {$course->id}: {$course->shortname} could not be updated for some reason.");
                } else {
                    mtrace("    {$course->id}: {$course->shortname} is now visible");
                }
            }
        } else {
            mtrace("  Nothing to do, except ponder the boundless wonders of the Universe, perhaps. ;-)\n");
        }

        $end = time();
        mtrace(($end - $start) / 60 . ' mins');
    }
}
