<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_users extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field(array(
            'id'=>array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE,
                'auto_increment'=>TRUE
            ),
            'email'=>array(
                'type'=>'VARCHAR',
                'constraint'=>'255',
            ),
            'username'=>array(
                'type'=>'VARCHAR',
                'constraint'=>'255',
            ),
            'password'=>array(
                'type'=>'VARCHAR',
                'constraint'=>'128',
            ),
            'active' => array(
                'type'=>'TINYINT'
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}