<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_m extends MY_Model
{
    public $table = 'apps';
    public $post_filter = array(
        'name'=>'name',
        'token'=>'token'
    );

    public function prep()
    {
        $data = parent::prep();
        $data['token'] = $this->auth->hash($data['name'] . $this->auth->salt);
        return $data;
    }

    public function field_data()
    {
        $field_data = parent::field_data();
        $filtered = array();
        foreach($field_data as $field)
        {
            if($field->name != 'token')
            {
                $filtered[] = $field;
            }
        }
        return $filtered;
    }
}