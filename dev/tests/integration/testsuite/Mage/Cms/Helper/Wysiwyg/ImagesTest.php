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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Cms_Helper_Wysiwyg_ImagesTest extends PHPUnit_Framework_TestCase
{
    public function testGetStorageRoot()
    {
        /** @var $dir Mage_Core_Model_Dir */
        $dir = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $helper = new Mage_Cms_Helper_Wysiwyg_Images($filesystem);
        $this->assertStringStartsWith($dir->getDir(Mage_Core_Model_Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $helper = new Mage_Cms_Helper_Wysiwyg_Images($filesystem);
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
