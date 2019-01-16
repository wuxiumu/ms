# 构建一个属于自己的PHP框架的想法是怎么来的

月初就在找工作面试，工作其实蛮多的，要求也不低。感觉，个需要提升的地方还有很多，而且面试是双向的选择，也不要忘了自己面试的初心啊。

经历了半个月的面试，在挣扎中萌发出做一个自己的心目中好的php框架。极高的拓展性，和实用性。因为这是在面试的时候想到的，所以叫phpms框架。而域名phpms.cn,已经收入囊中，正准备申请备案。

既然走在技术这条道路上，不管以后怎么样我发展的怎么样，现在想把个人的技术栈的经验分享给大家，主要是自我督促，还不知道，有没有人看呢。

怎么分享呢，最好的方式就是个人技术blog，和各大社区的论坛账号.

其实之前也有自己的blog记录一些，大部分都是别人的经验。

而且php几乎是每一年跟新一次，关联的新技术也是层出不穷,各种唱衰php的声音也没有断过。

我觉得作为web开发，php就是时间上最好的语言，是她让我感受到创造的价值，开发的乐趣。

# phpms 框架

自己写了个PHP的框架，访问地址ms.meiyoufan.com

这是框架的源码，网站我会明天更新。


#  框架目录一览

```
ms                              [PHP应用目录]
│
├── app                         [模块目录]
│   │
│   ├── ctrl                    [控制器目录]
│   │  
│   ├── model                   [数据模型目录]
│   │   
│   ├── rsa                     [私钥公钥存放目录]│   
│   │ 
│   └── viws                    [视图目录]
│    
├── core                        [PHPMS核心框架目录]
│    │
│    ├── common                 [公共函数目录]
│    │   │
│    │   └──function.php        [自定义公共函数]
│    │
│    ├── config                 [配置目录]
│    │
│    ├── flight                 [flight 引擎目录]
│    │
│    ├── lib                    [驱动目录]
│    │   
│    └── phpmsframe.php         [框架类]
│
├── readme                      [PHPMS框架开发思路和笔记]
│    
└── public                      [公共资源目录]
.gitignore                      [git忽略文件配置]
.htaccess                       [伪静态文件]
api.php                         [api入口文件]  
favicon.ico                     [ico图标] 
index.php                       [后端入口文件]
LICENSE                         [lincese文件]
composer.json                   [composer配置文件]
composer.lock                   [composer lock文件]
README.md                       [readme文件]
w_start_web.bat                 [win下一键启动项目文件] 
```