<?php

class Xxx_Catalog_UploadController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function postAction()
    {
        if (!empty($_FILES)) {
            $type = 'file';
            if (isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
                try {
                    $uploadsDir = Mage::getBaseDir('upload');

                    $uploader = new Varien_File_Uploader($type);
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS . 'upload';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $uploader->save($path, $_FILES[$type]['name']);
                    $filename = $uploader->getUploadedFileName();

                    $md5 = md5($filename);
                    $owner = Mage::getSingleton('customer/session')->getCustomerId() ? Mage::getSingleton('customer/session')->getCustomerId() : Mage::getSingleton('customer/session')->getSessionId();
                    Mage::getModel('xxx_catalog/upload')
                        ->setMd5($md5)
                        ->setFilename($filename)
                        ->setOwner($owner)
                        ->save();

                    $varienImage = new Varien_Image($uploadsDir . $filename);
                    $width = $varienImage->getOriginalWidth();
                    $height = $varienImage->getOriginalHeight();

                    $data = [
                        'id' => $md5,
                        'width' => $width,
                        'height' => $height,
                    ];

                    echo json_encode($data);
                    die();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, $this->_logFile);
                    echo json_encode(['error' => $this->__($e->getMessage())]);
                }
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
    }
}
