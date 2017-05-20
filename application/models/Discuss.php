<?php
class DiscussModel extends Db_Mysql{

    public $pk = 'id';

    public $tablename = 'share';


    /**
     * 用户发表文章数据
    */
    public function share_data(){

        $share_data = array(
            'caption' => $_POST['caption'],
            'content' => $_POST['content'],
            'volunteer_id' => $_POST['volunteerid']
        );

        if(!$this->insert($share_data)) {
            return false;
        }
        return true;
    }

}