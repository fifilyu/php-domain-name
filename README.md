# php-domain-name
## 完善的域名验证PHP库

使用php-domain-name可以对域名字符串做完善的验证。从此，不再需要使用简陋的域名验证代码。
验证通过后，域名的各个字段值都保存到了DomainName类对象。

## 特点
* 内含完整的域名数据，来源于[TLDsDb](https://github.com/fifilyu/TLDsDb)
* 严格的域名格式验证。比如，对以下域名做验证，结果都会是**无效**
    * com
    * foobar.foobar
    * .foobar.com
    * -.foobar.com
    * baz-.foobar.com
    * -baz.foobar.com
    * %.foobar.com
    * f.com
    * -foobar.com
    * foobar-.com
    * foobar%.com
    * foobar.baz
    * aekui5phea2Eeyeelaijiex5ahniefaitied5Cohpei1Yoh6chaingohwie9pao123.com
    * aekui5phea2Eeyeelaijiex5ahniefaitied5Cohpei1Yoh6chaingohwie9pao.aekui5phea2Eeyeelaijiex5ahniefaitied5Cohpei1Yoh6chaingohwie9pao.aekui5phea2Eeyeelaijiex5ahniefaitied5Cohpei1Yoh6chaingohwie9pao.aekui5phea2Eeyeelaijiex5ahniefaitied5Cohpei1Yoh6chaingohwie9pao.com
* 支持获取域名字段信息。比如，可以从`www.foobar.com.cn`中，分别得到`www`、`foobar`、`com`、`cn`
* 支持英文域名、中文域名、中文域名转码等等。比如：
    * foobar.com
    * 时尚.中国
    * xn--9et52u.xn--fiqs8s
    * кто.рф
    * foobar.مصر

## 用法

### 示例

    <?php
    require_once __DIR__ . '/src/domain_name.php';
    
    try {
        $dn = DomainName\detect('foobar.com');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        echo '域      名: ' . $dn->getName() . PHP_EOL;
        echo "域名字  段: " . $dn->getFeildDomainName() . PHP_EOL;
        echo "顶级域字段: " . $f_tlds[0] . PHP_EOL . PHP_EOL;
    
    
        $dn = DomainName\detect('www.foobar.com.cn');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        echo '域      名: ' . $dn->getName() . PHP_EOL;
        echo "主机字  段: " . $f_host[0] . PHP_EOL;
        echo "域名字  段: " . $dn->getFeildDomainName() . PHP_EOL;
        echo "顶级域字段: " . $f_tlds[0] . PHP_EOL;
        echo "顶级域字段: " . $f_tlds[1] . PHP_EOL . PHP_EOL;
    
    
        $dn = DomainName\detect('时尚.中国');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        echo '域      名: ' . $dn->getName() . PHP_EOL;
        echo "域名字  段: " . $dn->getFeildDomainName() . PHP_EOL;
        echo "顶级域字段: " . $f_tlds[0] . PHP_EOL . PHP_EOL;
    
    
        $dn = DomainName\detect('xn--9et52u.xn--fiqs8s');
        $f_tlds = $dn->getFeildTopLevelDomains();
        echo '域      名: ' . $dn->getName() . PHP_EOL;
        echo "域名字  段: " . $dn->getFeildDomainName() . PHP_EOL;
        echo "顶级域字段: " . $f_tlds[0] . PHP_EOL . PHP_EOL;
    
        $dn = DomainName\detect('!@#$foobar.com');
    } catch (DomainName\DomainNameException $e) {
        echo '发生错误：' . $e->getMessage() . PHP_EOL;
    }

### 示例输出

    域      名: foobar.com
    域名字  段: foobar
    顶级域字段: .com
    
    域      名: www.foobar.com.cn
    主机字  段: www
    域名字  段: foobar
    顶级域字段: .com
    顶级域字段: .cn
    
    域      名: 时尚.中国
    域名字  段: 时尚
    顶级域字段: .中国
    
    域      名: xn--9et52u.xn--fiqs8s
    域名字  段: xn--9et52u
    顶级域字段: .xn--fiqs8s
    
    发生错误：Invalid domain name.


## 域名规则

* 任何字段不能包含`A-Z`、`a-z`、`0-9`以及`-`以外的字符
* 域名总长度不大于253
* 任何字段长度不大于63
* 域名字段长度大于1
* 至少包含一个域名字段，一个顶级域字段
* 任何字段不能以中横线（“-”）开头或结尾
* 域名不能以点开头
* 顶级域字段值必须是有效的[Top-level domain](https://en.wikipedia.org/wiki/Top-level_domain)


## 域名相关资料
* [rfc1035(DOMAIN NAMES - IMPLEMENTATION AND SPECIFICATION)
](https://www.ietf.org/rfc/rfc1035.txt)
* [Domain Name System](https://en.wikipedia.org/wiki/Domain_Name_System)
* [Domain name](https://en.wikipedia.org/wiki/Domain_name)
* [List of Internet top-level domains](https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains)
* [Top-level domain](https://en.wikipedia.org/wiki/Top-level_domain)
* [Root Zone Database](http://www.iana.org/domains/root/db)