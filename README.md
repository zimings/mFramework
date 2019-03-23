# mFramework

> 实践是最好的老师

#####一些说明

## 模板解析引擎
- 在Controller中定义
    -render_once(要渲染的数据, 在模板中的名称defalut=null)
    -render(模板文件相对App/View的路径, 要渲染的数据defalut=null, 在模板中的名称defalut=null)
    - 原始方法
        - assign()方法插入数据
        - show()方法展示
        
## 获取mysql实例
- Model.class.php中有方法getModel()用以获取mysql连接后的实例