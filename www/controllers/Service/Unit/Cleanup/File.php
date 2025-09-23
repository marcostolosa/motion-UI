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
        parent::log('Cleaning files...');
        /**
         *  Clean service units logs older than 15 days
         */
        if (is_dir(SERVICE_LOGS_DIR)) {
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
         *  Clean go2rtc logs older than 7 days
         */
        if (is_dir(GO2RTC_DIR . '/logs')) {
            $files = glob(GO2RTC_DIR . '/logs/*.log');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-7 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }
                    }
                }
            }
        }

        parent::log('Files cleaning finished');

        unset($dirs, $dir, $files, $file);
    }
}
