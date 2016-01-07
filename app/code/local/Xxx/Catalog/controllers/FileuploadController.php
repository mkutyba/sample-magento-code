<?php

class Xxx_Catalog_FileuploadController extends Mage_Core_Controller_Front_Action
{
    public function postAction()
    {
        $uploadsDir = Mage::getBaseDir('upload');
        $uploadsUrl = Mage::getBaseUrl('media') . 'upload/';

        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];

            $id = time();
            $filename = $id . '_' . $_FILES['file']['name'];
            // Replace all weird characters with dashes
            $filename = preg_replace('/[^\w\-~_\.]+/u', '-', $filename);
            // Only allow one dash separator at a time (and make string lowercase)
            $filename = mb_strtolower(preg_replace('/--+/u', '-', $filename), 'UTF-8');

            $targetFile = $uploadsDir . DS . $filename;

            move_uploaded_file($tempFile, $targetFile);

            list($width, $height) = getimagesize($targetFile);

            $data = [];
            $data['source'] = "user-upload";
            $data['url'] = $uploadsUrl . $filename;
            $data['id'] = $id;
            $data['name'] = $this->__('plik użytkownika');
            $data['image'] = $data['url'];
            $data['image-width'] = $width;
            $data['image-height'] = $height;
            $data['preview-width'] = $width;
            $data['preview-height'] = $height;
            $data['vector-available'] = "0";
            $data['price'] = "0";

            echo json_encode($data);
            die();
        }

        header("HTTP/1.0 404 Not Found");
        die();
    }
}
