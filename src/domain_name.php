<?php
/**
 * Copyright (c) 2016, Fifi Lyu. All rights reserved.
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace DomainName;

// 2.3.4. Size limits https://tools.ietf.org/html/rfc1035
/** @var integer 域名最大长度 */
define("kDomainNameMaxSize", 253);

/** @var integer 字段最大长度 */
define("kFeildMaxSize", 63);

/** @var integer 顶级域最小长度(点+两个字母) */
define("kTLDMinSize", 3);

/**
 * 无效域名异常类
 */
class DomainNameException extends \Exception
{
}

/**
 * 域名数据类
 *
 * 本类包含域名及其所有字段数据
 *
 */
class DomainName
{
    /** @var string 域名字符串 */
    private $name = '';
    /** @var array 主机字段数组 */
    private $feild_hosts = array();
    /** @var string 域名字段 */
    private $feild_domain_name = '';
    /** @var array 顶级域字段数组 */
    private $feild_top_level_domains = array();

    /**
     * DomainName构造函数
     *
     * @param string 域名字符串
     */
    function __construct($domain_name)
    {
        $this->name = $domain_name;
    }

    /**
     * 获取整个域名字符串
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取主机字段值
     *
     * @return array
     */
    public function getFeildHosts()
    {
        return $this->feild_hosts;
    }

    /**
     *  增加主机字段值
     *
     * @param string $value
     */
    public function addFeildHost($value)
    {
        $this->feild_hosts[] = $value;
    }
    /**
     * 获取域名字段值
     *
     * @return string
     */
    public function getFeildDomainName()
    {
        return $this->feild_domain_name;
    }

    /**
     * 设置域名字段值
     *
     * @param string $value
     */
    public function setFeildDomainName($value)
    {
        $this->feild_domain_name = $value;
    }
    /**
     * 增加顶级域字段值
     *
     * @return array
     */
    public function getFeildTopLevelDomains()
    {
        return $this->feild_top_level_domains;
    }

    /**
     * 增加顶级域字段值
     *
     * @param string $value
     */
    public function addFeildTopLevelDomain($value)
    {
        $this->feild_top_level_domains[] = $value;
    }
}

/**
 * 载入顶级域数据
 *
 * 从指定顶级域数据文件载入数据
 *
 * @return array
 *
 * @throws DomainNameException
 */
function load_tlds_db()
{
    $tlds_file = __DIR__ . '/../tlds.txt';

    if (!file_exists($tlds_file))
        throw new DomainNameException('TLDs data file not found：' . $tlds_file);

    $fh = fopen($tlds_file, 'r');

    if (!$fh)
        throw new DomainNameException('Cannot open TLDs data file：' . $tlds_file);

    $array = array();

    while ($line = fgets($fh))
        $array[trim($line, "\r\n")] = 0;

    fclose($fh);

    return $array;
}

/**
 * 验证主机字段
 *
 * 主机字段由字母、数字以及中横线（“-”）组成，但不能为空或中横线（“-”），不能以中横线（“-”）开头或结尾
 *
 * @param string $feild 字段值
 *
 * @return boolean
 *
 */
function _validate_host($feild)
{
    $len = strlen($feild);

    if ($len == 0 || $len > kFeildMaxSize)
        return false;

    if ($feild == '-')
        return false;

    if ($len >= 2 && ($feild[0] == '-' || $feild[$len - 1] == '-'))
        return false;

    // 只对标准代码做正则匹配，忽略类似中文编码
    if (mb_check_encoding($feild, 'ASCII'))
        return preg_match('/^[a-z0-9-]+$/i', $feild);

    return true;
}

/**
 * 验证域名字段
 *
 * 域名字段由字母、数字以及中横线（“-”）组成，但长度不能为1，不能以中横线（“-”）开头或结尾
 *
 * @param string $feild 字段值
 *
 * @return boolean
 *
 */
function _validate_dn($feild)
{
    $len = strlen($feild);

    if ($len <= 1 || $len > kFeildMaxSize)
        return false;

    if ($feild[0] == '-' || $feild[$len - 1] == '-')
        return false;

    // 只对标准代码做正则匹配，忽略类似中文编码
    if (mb_check_encoding($feild, 'ASCII'))
        return preg_match('/^[a-z0-9-]+$/i', $feild);

    return true;
}

/** 载入顶级域数据到数组中 */
$tlds = load_tlds_db();

/**
 * 验证顶级域字段
 *
 * 在载入的顶级域数据数组中，查找存在的顶级域
 *
 * @param string $feild 字段值
 *
 * @return boolean
 *
 */
function _validate_tld($feild)
{
    global $tlds;
    $key = '.' . $feild;
    $len = strlen($key);

    if ($len < kTLDMinSize || $len > kFeildMaxSize)
        return false;

    // 效率无限接近 O(1)
    return isset($tlds[$key]) || array_key_exists($key, $tlds);
}

/**
 * 检测域名
 *
 * 验证域名字符串，通过验证后，将字段值保存到DomainName类对象
 *
 * @param string 域名字符串
 *
 * @return DomainName
 *
 * @throws DomainNameException
 */
function detect($domain_name)
{
    if (!$domain_name || strlen($domain_name) > kDomainNameMaxSize || $domain_name[0] == '.')
        throw new DomainNameException('Invalid domain name.');

    $dn_obj = new DomainName($domain_name);

    $feilds = explode('.', $domain_name);

    $len = count($feilds);

    // 至少两个字段
    if ($len < 2)
        throw new DomainNameException('Invalid domain name.');

    // foobar.com net.cn www.foobar
    if ($len == 2) {
        if (_validate_dn($feilds[0]) && _validate_tld($feilds[1])) {
            // foobar.com
            $dn_obj->setFeildDomainName($feilds[0]);
            $dn_obj->addFeildTopLevelDomain('.' . $feilds[1]);

            return $dn_obj;
        } else {
            // net.cn www.foobar
            throw new DomainNameException('Invalid domain name.');
        }
    }

    // www.foobar.com foobar.com.cn
    if (!_validate_tld($feilds[$len - 1]))
        throw new DomainNameException('Invalid domain name.');

    if (_validate_tld($feilds[$len - 2])) {
        // foobar.com.cn

        // .com
        $dn_obj->addFeildTopLevelDomain('.' . $feilds[$len - 2]);
        $host_index = 3;
    } else {
        // www.foobar.com
        $host_index = 2;
    }

    // .cn
    $dn_obj->addFeildTopLevelDomain('.' . $feilds[$len - 1]);

    // 检查域名字段
    // foobar
    if (_validate_dn($feilds[$len - $host_index])) {
        $dn_obj->setFeildDomainName($feilds[$len - $host_index]);
        $host_len = $len - $host_index;
    } else {
        throw new DomainNameException('Invalid domain name.');
    }

    // www.foobar.com
    // 检查主机字段
    for ($i = 0; $i < $host_len; ++$i) {
        if (_validate_host($feilds[$i]))
            $dn_obj->addFeildHost($feilds[$i]);
        else
            throw new DomainNameException('Invalid domain name.');
    }

    return $dn_obj;
}
