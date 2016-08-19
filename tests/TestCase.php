<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/domain_name.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    public function testLoadTLDsDb()
    {
        $tlds = DomainName\load_tlds_db();
        $this->assertTrue(is_array($tlds));
        $this->assertNotEquals(0, count($tlds));
    }

    public function testDetect()
    {
        $dn = DomainName\detect('foobar.com');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        $this->assertEquals('foobar.com', $dn->getName());
        $this->assertEquals(0, count($f_host));
        $this->assertEquals('foobar', $dn->getFeildDomainName());
        $this->assertEquals(1, count($f_tlds));
        $this->assertEquals('.com', $f_tlds[0]);

        $dn = DomainName\detect('www.foobar.com.cn');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        $this->assertEquals('www.foobar.com.cn', $dn->getName());
        $this->assertEquals(1, count($f_host));
        $this->assertEquals('www', $f_host[0]);
        $this->assertEquals('foobar', $dn->getFeildDomainName());
        $this->assertEquals(2, count($f_tlds));
        $this->assertEquals('.com', $f_tlds[0]);
        $this->assertEquals('.cn', $f_tlds[1]);

        $dn = DomainName\detect('download.file.foobar.com');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        $this->assertEquals('download.file.foobar.com', $dn->getName());
        $this->assertEquals(2, count($f_host));
        $this->assertEquals('download', $f_host[0]);
        $this->assertEquals('file', $f_host[1]);
        $this->assertEquals('foobar', $dn->getFeildDomainName());
        $this->assertEquals(1, count($f_tlds));
        $this->assertEquals('.com', $f_tlds[0]);

        $dn = DomainName\detect('时尚.中国');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        $this->assertEquals('时尚.中国', $dn->getName());
        $this->assertEquals(0, count($f_host));
        $this->assertEquals('时尚', $dn->getFeildDomainName());
        $this->assertEquals(1, count($f_tlds));
        $this->assertEquals('.中国', $f_tlds[0]);

        $dn = DomainName\detect('xn--9et52u.xn--fiqs8s');
        $f_tlds = $dn->getFeildTopLevelDomains();
        $f_host = $dn->getFeildHosts();
        $this->assertEquals('xn--9et52u.xn--fiqs8s', $dn->getName());
        $this->assertEquals(0, count($f_host));
        $this->assertEquals('xn--9et52u', $dn->getFeildDomainName());
        $this->assertEquals(1, count($f_tlds));
        $this->assertEquals('.xn--fiqs8s', $f_tlds[0]);
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectCase1()
    {
        DomainName\detect('com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectCase2()
    {
        DomainName\detect('foobar.foobar');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectHostCase1()
    {
        DomainName\detect('.foobar.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectHostCase2()
    {
        DomainName\detect('-.foobar.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectHostCase3()
    {
        DomainName\detect('baz-.foobar.foobar');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectHostCase4()
    {
        DomainName\detect('-baz.foobar.foobar');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectHostCase5()
    {
        DomainName\detect('%.foobar.foobar');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectDomainNameCase1()
    {
        DomainName\detect('f.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectDomainNameCase2()
    {
        DomainName\detect('-foobar.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectDomainNameCase3()
    {
        DomainName\detect('foobar-.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectDomainNameCase4()
    {
        DomainName\detect('foobar%.com');
    }

    /**
     * @expectedException DomainName\DomainNameException
     */
    public function testDetectTLDCase()
    {
        DomainName\detect('foobar.baz');
    }
}