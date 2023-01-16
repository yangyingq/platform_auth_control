<?php
    if(!function_exists('request')){
        function request($key = null, $default = null)
        {
            if (is_null($key)) {
                return app('request');
            }

            if (is_array($key)) {
                return app('request')->only($key);
            }

            $value = app('request')->get($key);

            return is_null($value) ? value($default) : $value;
        }
    }
?>
