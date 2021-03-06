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
        $this->ci->load->library('auth');

        $html_safe = array('is_safe'=>array('html'));

        //TODO remove unused twig functions

        return array(
            'active'=>new Twig_SimpleFunction('active', function($resource)
            {
                $active = '';
                $uri = $this->ci->uri->segment(1);
                $is_myaccount = ($resource == 'current' && isset($_GET['current']));
                $is_home = ($uri == '' && $resource == '/');
                $is_resource = (! isset($_GET['current']) && $resource == $uri);
                if($is_home || $is_myaccount || $is_resource)
                {
                    $active = ' class="active"';
                }
                return $active;
            }), $html_safe,

            'icon'=>new Twig_SimpleFunction('icon', function($type)
            {
                return icon($type);
            }), $html_safe,

            'form_edit'=>new Twig_SimpleFunction('form_edit', function($action, $method, $field_data, $data, $multi = FALSE)
            {
                return form_edit($action, $method, $field_data, $data, $multi);
            }, $html_safe),

            'form_delete'=>new Twig_SimpleFunction('form_delete', function($action, $resource, $id)
            {
                return form_delete($action, $resource, $id);
            }, $html_safe),

            'user'=>new Twig_SimpleFunction('user', function()
            {
                return $this->ci->auth->current();
            }),

            'link_myaccount'=>new Twig_SimpleFunction('link_myaccount', function()
            {
                $user = $this->ci->auth->current();
                $id = $this->ci->auth->get_user_by_identity($user)->result()[0]->id;
                $href = base_url('user/' . $id);
                $attr = '';
                $icon = icon('cog');
                $text = $icon . ' My Account';
                return '<a href="' . $href . '?current=1"' . $attr . '>' . $text . '</a>';
            }, $html_safe),

            'link_to'=>new Twig_SimpleFunction('link_to', function($href, $text, $attributes = array(), $icon = FALSE)
            {
                $icon = ($icon) ? icon($icon) . ' ' : '';
                $attr = '';
                foreach($attributes as $key => $value)
                {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
                return '<a href="' . $href . '"' . $attr . '>' . $icon . $text . '</a>';
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
                $error_string = $this->ci->form_validation->error_string();
                $error_string = preg_replace('/<p>/', '<div class="alert alert-danger">', $error_string);
                $error_string = preg_replace('/<\/p>/', '</div>', $error_string);
                return $error_string;
            }, $html_safe)
        );
    }
}