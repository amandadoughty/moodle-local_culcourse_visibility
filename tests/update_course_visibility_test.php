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
 * This file contains the unittests for scheduled tasks.
 *
 * @package   local_culcourse_visibility
 * @copyright 2017 Amanda Doughty
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// ... vendor/bin/phpunit local_culcourse_visibility_update_course_visibility_testcase
// local/culcourse_visibility/tests/update_course_visibility_test.php.

/**
 * Test class for update course visibility task.
 *
 * @package local_culcourse_visibility
 * @copyright 2017 Amanda Doughty
 * @group local_culcourse_visibility
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_culcourse_visibility_update_course_visibility_testcase extends advanced_testcase {

    /** @var array of stdClass $course New courses created to test task */
    protected $courses = [];

    /**
     * Setup function - we will create courses with differing start and end dates.
     */
    protected function setUp(): void {
        global $DB;

        $this->resetAfterTest(true);

        $today = time();
        // Course 2 should be hidden.
        $yesterday = time() - (24 * 60 * 60);
        $tomorrow = time() + (24 * 60 * 60);

        $properties = [
            'startdate' => $today,
            'enddate' => $tomorrow,
            'visible' => 0
        ];

        $this->courses[1] = $this->getDataGenerator()->create_course($properties);

        $properties = [
            'startdate' => $tomorrow,
            'enddate' => $tomorrow,
            'visible' => 0
        ];

        $this->courses[2] = $this->getDataGenerator()->create_course($properties);

        $properties = [
            'startdate' => $yesterday,
            'enddate' => $today,
            'visible' => 1
        ];

        $this->courses[3] = $this->getDataGenerator()->create_course($properties);

        $properties = [
            'startdate' => $yesterday,
            'enddate' => $yesterday,
            'visible' => 1
        ];

        $this->courses[4] = $this->getDataGenerator()->create_course($properties);

        $properties = [
            'startdate' => $today,
            'enddate' => $today,
            'visible' => 0
        ];

        $this->courses[5] = $this->getDataGenerator()->create_course($properties);
    }


    /**
     * Test that the update_course_visibility_task hides/shows courses
     * as expected.
     */
    public function test_update_course_visibility_none() {
        global $CFG;

        $this->resetAfterTest(true);
        ob_start();
        $task = \core\task\manager::get_scheduled_task('\\local_culcourse_visibility\\task\\update_course_visibility');
        $this->assertInstanceOf('\local_culcourse_visibility\task\update_course_visibility', $task);
        // Change task settings.
        set_config('showcourses', 0, 'local_culcourse_visibility');
        set_config('hidecourses', 0, 'local_culcourse_visibility');
        $task->execute();
        ob_get_clean();
        $this->reload_courses();

        // Course 1 should be hidden.
        // Course 2 should be hidden.
        // Course 3 should be visible.
        // Course 4 should be visible.
        // Course 5 should be hidden.
        $this->assertEquals(0, $this->courses[1]->visible);
        $this->assertEquals(0, $this->courses[2]->visible);
        $this->assertEquals(1, $this->courses[3]->visible);
        $this->assertEquals(1, $this->courses[4]->visible);
        $this->assertEquals(0, $this->courses[5]->visible);
    }

    /**
     * Test that the update_course_visibility_task hides/shows courses
     * as expected.
     */
    public function test_update_course_visibility_start() {
        global $CFG;

        $this->resetAfterTest(true);
        ob_start();
        $task = \core\task\manager::get_scheduled_task('\\local_culcourse_visibility\\task\\update_course_visibility');
        $this->assertInstanceOf('\local_culcourse_visibility\task\update_course_visibility', $task);
        // Change task settings.
        set_config('showcourses', 1, 'local_culcourse_visibility');
        set_config('hidecourses', 0, 'local_culcourse_visibility');
        $task->execute();
        ob_get_clean();
        $this->reload_courses();

        // Course 1 should be visible.
        // Course 2 should be hidden.
        // Course 3 should be visible.
        // Course 4 should be visible.
        // Course 5 should be visible.
        $this->assertEquals(1, $this->courses[1]->visible);
        $this->assertEquals(0, $this->courses[2]->visible);
        $this->assertEquals(1, $this->courses[3]->visible);
        $this->assertEquals(1, $this->courses[4]->visible);
        $this->assertEquals(1, $this->courses[5]->visible);
    }

    /**
     * Test that the update_course_visibility_task hides/shows courses
     * as expected.
     */
    public function test_update_course_visibility_end() {
        global $CFG;

        $this->resetAfterTest(true);
        ob_start();
        $task = \core\task\manager::get_scheduled_task('\\local_culcourse_visibility\\task\\update_course_visibility');
        $this->assertInstanceOf('\local_culcourse_visibility\task\update_course_visibility', $task);

        // Change task settings.
        set_config('showcourses', 0, 'local_culcourse_visibility');
        set_config('hidecourses', 1, 'local_culcourse_visibility');
        $task->execute();
        ob_get_clean();
        $this->reload_courses();

        // Course 1 should be hidden.
        // Course 2 should be hidden.
        // Course 3 should be hidden.
        // Course 4 should be visible.
        // Course 5 should be hidden.
        $this->assertEquals(0, $this->courses[1]->visible);
        $this->assertEquals(0, $this->courses[2]->visible);
        $this->assertEquals(0, $this->courses[3]->visible);
        $this->assertEquals(1, $this->courses[4]->visible);
        $this->assertEquals(0, $this->courses[5]->visible);
    }

    /**
     * Test that the update_course_visibility_task hides/shows courses
     * as expected.
     */
    public function test_update_course_visibility_both() {
        global $CFG;

        $this->resetAfterTest(true);
        ob_start();
        $task = \core\task\manager::get_scheduled_task('\\local_culcourse_visibility\\task\\update_course_visibility');
        $this->assertInstanceOf('\local_culcourse_visibility\task\update_course_visibility', $task);

        // Change task settings.
        set_config('showcourses', 1, 'local_culcourse_visibility');
        set_config('hidecourses', 1, 'local_culcourse_visibility');
        $task->execute();
        ob_get_clean();
        $this->reload_courses();

        // Course 1 should be visible.
        // Course 2 should be hidden.
        // Course 3 should be hidden.
        // Course 4 should be visible.
        // Course 5 should be hidden.
        $this->assertEquals(1, $this->courses[1]->visible);
        $this->assertEquals(0, $this->courses[2]->visible);
        $this->assertEquals(0, $this->courses[3]->visible);
        $this->assertEquals(1, $this->courses[4]->visible);
        $this->assertEquals(0, $this->courses[5]->visible);
    }

    /**
     * Reloads the courses array from the DB.
     *
     * @return void.
     */
    private function reload_courses() {
        global $DB;

        $courses = $DB->get_records('course');
        // Reset the array keys. NB The front page will be $this->courses[0].
        $this->courses = array_values($courses);
    }
}

