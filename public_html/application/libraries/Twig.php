<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/Twig/Autoloader.php';
require_once APPPATH . 'libraries/Twig/ExtensionInterface.php';
require_once APPPATH . 'libraries/Twig/Extension.php';

class Twig
{
    protected $twig;
    private $ci;

    public function __construct()
    {

        Twig_Autoloader::register();
        $this->ci = & get_instance();

        $config['auto_reload']      = TRUE;
        $config['strict_variables'] = TRUE;

        $loader = new Twig_Loader_Filesystem(APPPATH.'views');
        $this->twig = new Twig_Environment($loader, $config);
        $this->twig->addExtension(new WegoTwig());
    }

    public function __call($method, $args)
    {
        if ( ! method_exists($this->twig, $method)) {
            throw new Exception("Undefined method $method attempt in the Twig class.");
        }

        $this->ci->output->append_output( call_user_func_array(array($this->twig, $method), $args) );
    }
}

class WegoTwig extends Twig_Extension
{
    private $ci;

    function __construct()
    {
        $this->ci =& get_instance();
    }

    public function getName()
    {
        return 'wegodb';
    }

    function getGlobals()
    {
        $this->ci->load->helper('url');

        return array(
            'base_url'=>base_url(),
            'assets_url'=>base_url() . 'assets/',
            'js_url'=>base_url() . 'assets/js/',
            'csrf_token_name'=>$this->ci->security->get_csrf_token_name(),
            'csrf_hash'=>$this->ci->security->get_csrf_hash()
        );
    }

    function getFilters()
    {
        return array(
            'to_array'=>new Twig_SimpleFilter('to_array', function($object)
            {
            $array = array();
            foreach($object as $key => $value)
            {
                $array[$key] = $value;
            }
            return $array;
            })
        );
    }

    function getFunctions()
    {
        $this->ci->load->helper('form');
        $this->ci->load->library('form_validation');

        $html_safe = array('is_safe'=>array('html'));

        return array(
            'form_delete'=>new Twig_SimpleFunction('form_delete', function($action, $resource, $id)
            {
                return $this->ci->form->delete($action, $resource, $id);
            }, $html_safe),

            'form_edit'=>new Twig_SimpleFunction('form_edit', function($action, $method, $field_data, $data, $multi = FALSE)
            {
                return $this->ci->form->edit($action, $method, $field_data, $data, $multi);
            }, $html_safe),

            'link_to'=>new Twig_SimpleFunction('link_to', function($href, $text, $attributes = array())
            {
                $attr = '';
                foreach($attributes as $key => $value)
                {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
                return '<a href="' . $href . '"' . $attr . '>' . $text . '</a>';
            }, $html_safe),

            'link_new'=>new Twig_SimpleFunction('link_new', function($href, $text, $attributes = array())
            {
                $attr = '';
                foreach($attributes as $key => $value)
                {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
                return '<a href="' . $href . '?new=1"' . $attr . '>' . $text . '</a>';
            }, $html_safe),

            'link_ajax'=>new Twig_SimpleFunction('link_ajax', function($href, $text, $method, $attributes = array())
            {
                $attr = '';
                if( ! isset($attributes['class']))
                {
                    $attributes['class'] = $method;
                }else{
                    $attributes['class'] .= ' ' . $method;
                }
                foreach($attributes as $key => $value)
                {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
                return '<a href="' . $href . '"' . $attr . '>' . $text . '</a>';
            }, $html_safe),

            'profiler'=>new Twig_SimpleFunction('profiler', function()
            {
                $this->ci->output->enable_profiler(TRUE);
            }),

            'dump'=>new Twig_SimpleFunction('dump', function($var, $label = 'Dump', $echo = TRUE)
            {
                return dump($var, $label, $echo);
            }, $html_safe),

            'validation_errors'=>new Twig_SimpleFunction('validation_errors', function()
            {
                return $this->ci->form_validation->error_string();
            }, $html_safe)
        );
    }
}