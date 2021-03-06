<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     Varien_Db
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for an PDO MySQL adapter
 */
class Varien_Db_Adapter_Pdo_MysqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Database adapter instance
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_dbAdapter = null;

    protected function tearDown()
    {
        $this->_dbAdapter = null;
    }

    /**
     * Test lost connection re-initializing
     *
     * @throws Exception
     */
    public function testWaitTimeout()
    {
        if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestSkipped('Test is designed to run on MySQL only.');
        }
        if (!($this->_getDbAdapter() instanceof Varien_Db_Adapter_Pdo_Mysql)) {
            $this->markTestSkipped('This test is for Varien_Db_Adapter_Pdo_Mysql');
        }
        try {
            $defaultWaitTimeout = $this->_getWaitTimeout();
            $minWaitTimeout = 1;
            $this->_setWaitTimeout($minWaitTimeout);
            $this->assertEquals($minWaitTimeout, $this->_getWaitTimeout(), 'Wait timeout was not changed');

            // Sleep for time greater than wait_timeout and try to perform query
            sleep($minWaitTimeout + 1);
            $result = $this->_executeQuery('SELECT 1');
            $this->assertInstanceOf('Varien_Db_Statement_Pdo_Mysql', $result);
            // Restore wait_timeout
            $this->_setWaitTimeout($defaultWaitTimeout);
            $this->assertEquals($defaultWaitTimeout, $this->_getWaitTimeout(), 'Default wait timeout was not restored');
        } catch (Exception $e) {
            // Reset connection on failure to restore global variables
            $this->_getDbAdapter()->closeConnection();
            throw $e;
        }
    }

    /**
     * Get session wait_timeout
     *
     * @return int
     */
    protected function _getWaitTimeout()
    {
        $result = $this->_executeQuery('SELECT @@session.wait_timeout');
        return (int)$result->fetchColumn();
    }

    /**
     * Set session wait_timeout
     *
     * @param int $waitTimeout
     */
    protected function _setWaitTimeout($waitTimeout)
    {
        $this->_executeQuery("SET @@session.wait_timeout = {$waitTimeout}");
    }

    /**
     * Execute SQL query and return result statement instance
     *
     * @param string $sql
     * @return Zend_Db_Statement_Interface
     * @throws Exception
     */
    protected function _executeQuery($sql)
    {
        /**
         * Suppress PDO warnings to work around the bug
         * @link https://bugs.php.net/bug.php?id=63812
         */
        $phpErrorReporting = error_reporting();
        /** @var $pdoConnection PDO */
        $pdoConnection = $this->_getDbAdapter()->getConnection();
        $pdoWarningsEnabled = $pdoConnection->getAttribute(PDO::ATTR_ERRMODE) & PDO::ERRMODE_WARNING;
        if (!$pdoWarningsEnabled) {
            error_reporting($phpErrorReporting & ~E_WARNING);
        }
        try {
            $result = $this->_getDbAdapter()->query($sql);
            error_reporting($phpErrorReporting);
        } catch (Exception $e) {
            error_reporting($phpErrorReporting);
            throw $e;
        }
        return $result;
    }

    /**
     * Retrieve database adapter instance
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getDbAdapter()
    {
        if (is_null($this->_dbAdapter)) {
            /** @var $coreResource Mage_Core_Model_Resource */
            $coreResource = Mage::getSingleton('Mage_Core_Model_Resource');
            $this->_dbAdapter = $coreResource->getConnection(Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);
        }
        return $this->_dbAdapter;
    }
}
