<?php

    class coreDebugLibrary {

        public static function getTraceSummary() {
            $trace = debug_backtrace();
            $app_path = Application::getSitePath();

            $out = "<table>";
            $out .= "<tr><th>Function</th><th>File</th><th>Line</th></tr>";
            foreach ($trace as $entry) {
                $function = isset($entry['class']) ? $entry['class'] . '::' . $entry['function'] : $entry['function'];
                $file = isset($entry['file']) ? str_replace($app_path, '', $entry['file']) : '';
                $line = isset($entry['line']) ? $entry['line'] : '';
                $out .= "<tr><td>$function</td><td>$file</td><td>$line</td></tr>";
            }

            $out .= "</table>";

            return $out;
        }

    }
