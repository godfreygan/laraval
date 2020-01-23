<?php
/**
 * Util for process control
 */

namespace ServerBench\Process;

class Util
{
    public static function daemon($nochdir = true, $noclose = true)
    {
        umask(0);

        $pid = pcntl_fork();

        if ($pid > 0) {
            exit();
        } elseif ($pid < 0) {
            return false;
        } else {
            // nothing to do ...
        }

        $pid = pcntl_fork();

        if ($pid > 0) {
            exit();
        } elseif ($pid < 0) {
            return false;
        } else {
            // nothing to do ...
        }

        $sid = posix_setsid();

        if ($sid < 0) {
            return false;
        }

        if (!$nochdir) {
            chdir('/');
        }

        umask(0);

        if (!$noclose) {
            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);
        }

        return true;
    }
}
