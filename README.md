CUL Course Visibility local plugin
==================================

This plugin makes hidden courses visible when the course start date it reached. It also makes visible courses hidden when the course end date is reached. Both options can be enabled/disabled in theplugin settings. It runs daily as a scheduled task. 

It will only make visible courses with a start date matching the current day.

Courses which have start dates older than the current date will not be updated.

It will only hide courses with a end date matching the current day.

Courses which have end dates older than the current date will not be updated.


Maintainer
----------

The local plugin was originaly written as a cron job by Tim Gagen and has been refactored to run as a scheduled task by 
Amanda Doughty. It is currently maintained by Amanda Doughty.

