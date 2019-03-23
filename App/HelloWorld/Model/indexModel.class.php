<?php
/**
* 首页
*/
class indexModel extends Model
{
    public function helloWorld()
    {
        $data='Congratulations to you! You have successfully run the framework!';
        return $data;
    }
}
