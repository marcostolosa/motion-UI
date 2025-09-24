<?php

namespace Controllers\Service\Unit\Cleanup;

use Exception;

class File extends \Controllers\Service\Service
{
    public function __construct(string $unit)
    {
        parent::__construct($unit);
    }

    /**
     *  Clean temporary files
     */
    public function run() : void
    {
        /**
         *  Clean service units logs older than 15 days
         */
        if (is_dir(SERVICE_LOGS_DIR)) {
            parent::log('Cleaning service logs...');

            $files = \Controllers\Filesystem\File::findRecursive(SERVICE_LOGS_DIR, ['log']);

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-15 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean autostart logs older than 7 days
         */
        if (is_dir(AUTOSTART_LOGS_DIR)) {
            parent::log('Cleaning autostart logs...');

            $files = glob(AUTOSTART_LOGS_DIR . '/*.log');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-7 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean go2rtc logs older than 7 days
         */
        if (is_dir(GO2RTC_DIR . '/logs')) {
            parent::log('Cleaning go2rtc logs...');

            $files = glob(GO2RTC_DIR . '/logs/*.log');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-7 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean timelapse images older than 30 days
         */
        if (is_dir(CAMERAS_TIMELAPSE_DIR)) {
            parent::log('Cleaning timelapse images...');

            // Calculate the date before which timelapse images should be deleted
            $date = date('Y-m-d', strtotime('-' . TIMELAPSE_RETENTION . ' days'));

            // Get all timelapse directories
            $dirs = glob(CAMERAS_TIMELAPSE_DIR . '/*/*', GLOB_ONLYDIR);

            if (!empty($dirs)) {
                foreach ($dirs as $dir) {
                    $directoryDate = basename($dir);

                    // Skip directory if it is not a date
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $directoryDate)) {
                        continue;
                    }

                    // Skip directory if it is not older than specified date
                    if ($directoryDate >= $date) {
                        continue;
                    }

                    // Delete directory
                    if (!\Controllers\Filesystem\Directory::deleteRecursive($dir)) {
                        throw new Exception('Failed to delete directory ' . $dir);
                    }

                    parent::log('Directory ' . $dir . ' deleted');
                }
            }
        }

        parent::log('Files cleaning finished');

        unset($dirs, $dir, $files, $file);
    }
}
