<?php

if( ! function_exists('dump')){

    /**
     * @param $var
     * @param string $label
     * @param bool $echo
     * @return string
     */
    function dump($var, $label = 'Dump', $echo = TRUE)
    {
        //start output buffer
        ob_start();
        //run var_dump on $var
        var_dump($var);
        //clean buffer so nothing is displayed YET
        $output = ob_get_clean();

        //add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #111111; color:#ffffff; border: 1px dotted #f00; padding: 10px; margin: 10px; text-align: left; ">' . $label . " => " . $output . '</pre>';

        //output
        if($echo == TRUE)
        {
            echo $output;
            return '';
        }else{
            return $output;
        }
    }

}

if( ! function_exists('dump_exit')){

    /**
     * Calls dump and exits after
     * @param $var
     * @param string $label
     * @param bool $echo
     * @param string $exit_message
     */
    function dump_exit($var, $label = 'Dump', $echo = TRUE, $exit_message = '')
    {
        dump($var, $label, $echo);
        exit($exit_message);
    }

}

/* End of file dump_helper.php */
/* Location ./application/helpers/dump_helper.php */