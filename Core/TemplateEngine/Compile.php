<?php

/**
 * 编译基类
 */
class Compile
{
    private $template;// 待编译的文件
    private $content;// 需要替换的文件
    private $comfile;// 编译后的文件
    private $T_P = array();// 正则
    private $T_R = array();// 替换内容

    function __construct($template, $compileFile, $config)
    {
        $this->template = $template;
        $this->comfile = $compileFile;
        $this->content = file_get_contents($template);
        if ($config['php_turn'] === true) {
            $this->T_P[] = "#<\?(=|php|)(.+?)\?>#is";
            $this->T_R[] = "&lt;?\1\2?&gt;";
        }
        // 变量匹配
        // \x7f-\xff表示ASCII字符从127到255，其中\为转义，作用是匹配汉字
        $this->T_P[] = "#\{\s*\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}#";
        $this->T_P[] = "#\{\s*\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\[\'([a-zA-Z_\x7f-\xff]+)\'\]\s*\}#";
        $this->T_P[] = "#\{\s*\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\->([a-zA-Z_\x7f-\xff]+)\s*\}#";
        $this->T_R[] = "<?php echo \$this->value['\\1']; ?>";
        $this->T_R[] = "<?php echo \$this->value['\\1']['\\2']; ?>";
        $this->T_R[] = "<?php echo \$this->value['\\1']->\\2; ?>";

        // foreach标签盘匹配
        $this->T_P[] = "#\{\s*(loop|foreach)\s+\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}#i";
        $this->T_P[] = "#\{\s*\/(loop|foreach|if)\s*\}#";
        $this->T_P[] = "#\{\s*([k|v])\s*\}#";
        $this->T_P[] = "#\{\s*([k|v])\[([a-zA-Z_\x7f-\xff\'\"0-9]+)\]+\s*\}#";
        $this->T_R[] = "<?php foreach ((array)\$this->value['\\2'] as \$k => \$v) { ?>";
        $this->T_R[] = "<?php } ?>";
        $this->T_R[] = "<?php echo \$\\1?>";
        $this->T_R[] = "<?php echo \$\\1[\\2]?>";
        // if else标签匹配
        $this->T_P[] = "#\{\s*if (.*?)\s*\}#";
        $this->T_P[] = "#\{\s*(else if|elseif) (.*?)\s*\}#";
        $this->T_P[] = "#\{\s*else\}#";
        $this->T_P[] = "#\{\s*(\#|\*)(.*?)(\#|\*)\s*\}#";
        $this->T_R[] = "<?php if(\\1){ ?>";
        $this->T_R[] = "<?php }elseif(\\2){ ?>";
        $this->T_R[] = "<?php }else{ ?>";
        $this->T_R[] = "";
    }

    public function compile()
    {
        $changeUrls = array('{__CSS__}', '{__JS__}', '{__IMG__}', '{__LIB__}', '{__WEB__}', '{ __CSS__ }', '{ __JS__ }', '{ __IMG__ }', '{ __LIB__ }', '{ __WEB__ }');
        $toChangeUrls = array(__CSS__, __JS__, __IMG__, __LIB__, __WEB__, __CSS__, __JS__, __IMG__, __LIB__, __WEB__);
        $this->content = str_replace($changeUrls, $toChangeUrls, $this->content);
        $this->c_var();
        //$this->c_staticFile();
        file_put_contents($this->comfile, $this->content);
    }

    public function c_var()
    {
        $this->content = preg_replace($this->T_P, $this->T_R, $this->content);
    }

    /* 对引入的静态文件进行解析，应对浏览器缓存 */
    public function c_staticFile()
    {
        $this->content = preg_replace('#\{\!(.*?)\!\}#', '<script src=\1' . '?t=' . time() . '></script>', $this->content);
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
