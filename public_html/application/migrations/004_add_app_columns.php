<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_app_columns extends CI_Migration
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
            'name'=>array(
                'type'=>'VARCHAR',
                'constraint'=>'255'
            ),
            'app_table_id'=>array(
                'type'=>'INT',
                'constraint'=>11
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('app_columns');
    }

    public function down()
    {
        $this->dbforge->drop_table('app_columns');
    }
}