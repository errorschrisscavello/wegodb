<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_trash extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE,
                'auto_increment'=>TRUE
            ),
            'source'=>array(
                'type'=>'VARCHAR',
                'constraint'=>'255'
            ),
            'data'=>array(
                'type'=>'TEXT'
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('trash');
    }

    public function down()
    {
        $this->dbforge->drop_table('trash');
    }
}