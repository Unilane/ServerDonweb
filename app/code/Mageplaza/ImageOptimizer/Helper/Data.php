<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ImageOptimizer
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ImageOptimizer\Helper;

use CURLFile;
use Exception;
use ImagickException;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssertImageFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Image;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\ConfigInterface as ViewConfig;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\Config\Customization as ThemeCustomizationConfig;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use Magento\Theme\Model\Theme;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\ImageOptimizer\Model\Config\Source\Quality;
use Mageplaza\ImageOptimizer\Model\Config\Source\Status;
use Mageplaza\ImageOptimizer\Model\ResourceModel\Image\Collection as ImageOptimizerCollection;
use Mageplaza\ImageOptimizer\Model\ResourceModel\Image\CollectionFactory;

/**
 * Class Data
 * @package Mageplaza\ImageOptimizer\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpimageoptimizer';

    /**
     * @var DriverFile
     */
    protected $driverFile;

    /**
     * @var IoFile
     */
    protected $ioFile;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var MediaConfig
     */
    protected $imageConfig;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var mixed
     */
    protected $fileStorageDatabase;

    /**
     * @var ViewConfig
     */
    protected $viewConfig;

    /**
     * @var ParamsBuilder
     */
    protected $paramsBuilder;

    /**
     * @var Collection
     */
    protected $themeCollection;

    /**
     * @var ThemeCustomizationConfig
     */
    protected $themeCustomizationConfig;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * @var AssertImageFactory
     */
    protected $assertImageFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param DriverFile $driverFile
     * @param IoFile $ioFile
     * @param Filesystem $filesystem
     * @param CurlFactory $curlFactory
     * @param CollectionFactory $collectionFactory
     * @param MediaConfig $imageConfig
     * @param ViewConfig $viewConfig
     * @param ParamsBuilder $paramsBuilder
     * @param Collection $themeCollection
     * @param ThemeCustomizationConfig $themeCustomizationConfig
     * @param ImageFactory $imageFactory
     * @param AssertImageFactory $assertImageFactory
     * @param Database|null $fileStorageDatabase
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        DriverFile $driverFile,
        IoFile $ioFile,
        Filesystem $filesystem,
        CurlFactory $curlFactory,
        CollectionFactory $collectionFactory,
        MediaConfig $imageConfig,
        ViewConfig $viewConfig,
        ParamsBuilder $paramsBuilder,
        Collection $themeCollection,
        ThemeCustomizationConfig $themeCustomizationConfig,
        ImageFactory $imageFactory,
        AssertImageFactory $assertImageFactory,
        Database $fileStorageDatabase = null
    ) {
        $this->driverFile               = $driverFile;
        $this->ioFile                   = $ioFile;
        $this->filesystem               = $filesystem;
        $this->curlFactory              = $curlFactory;
        $this->collectionFactory        = $collectionFactory;
        $this->imageConfig              = $imageConfig;
        $this->mediaDirectory           = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileStorageDatabase      = $fileStorageDatabase ?:
            ObjectManager::getInstance()->get(Database::class);
        $this->viewConfig               = $viewConfig;
        $this->paramsBuilder            = $paramsBuilder;
        $this->themeCollection          = $themeCollection;
        $this->themeCustomizationConfig = $themeCustomizationConfig;
        $this->imageFactory             = $imageFactory;
        $this->assertImageFactory       = $assertImageFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCronJobConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('cron_job' . $code, $storeId);
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    public function scanFiles()
    {
        $scanAll            = false;
        $images             = [];
        $includePatterns    = ['jpg', 'png', 'gif', 'tif', 'bmp', 'tiff', 'jpeg'];
        $includeDirectories = $this->getIncludeDirectories();
        if (empty($includeDirectories)) {
            $scanAll            = true;
            $includeDirectories = [$this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath()];
        } else {
            $includeDirectories = array_map(function ($directory) {
                return ltrim($directory, '/');
            }, $includeDirectories);
        }
        /** @var ImageOptimizerCollection $collection */
        $collection = $this->collectionFactory->create();
        $pathValues = $collection->getColumnValues('path');

        foreach ($includeDirectories as $directory) {
            if (!$scanAll) {
                $directory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath() . $directory;
            }
            if (!$this->checkDirectoryReadable($directory)) {
                continue;
            }
            $files = $this->driverFile->readDirectoryRecursively($directory);
            foreach ($files as $file) {
                if (!$this->checkExcludeDirectory($file)) {
                    continue;
                }
                $pathInfo      = $this->getPathInfo(strtolower($file));
                $extensionPath = isset($pathInfo['extension']) ? $pathInfo['extension'] : false;
                if (!array_key_exists($file, $images)
                    && !in_array($file, $pathValues, true)
                    && ($extensionPath && in_array($extensionPath, $includePatterns, true))
                ) {
                    $fileSize = $this->driverFile->stat($file)['size'];
                    if ($fileSize === 0) {
                        continue;
                    }

                    if ($this->isTransparentImage($file, $extensionPath)) {
                        $status  = Status::SKIPPED;
                        $message = __('Skipped because it is a transparent image.');
                    } elseif ($fileSize > 5000000) {
                        $status  = Status::SKIPPED;
                        $message = __('Uploaded file must be below 5MB.');
                    } else {
                        $status  = Status::PENDING;
                        $message = '';
                    }
                    $images[$file] = [
                        'path'        => $file,
                        'status'      => $status,
                        'origin_size' => $fileSize,
                        'message'     => $message
                    ];
                }
            }
        }
        $images = array_values($images);

        return $images;
    }

    /**
     * @param string $file
     *
     * @return bool
     * @throws FileSystemException
     */
    protected function checkExcludeDirectory($file)
    {
        if (!$this->driverFile->isFile($file)) {
            return false;
        }

        $excludeDirectories = $this->getExcludeDirectories();
        foreach ($excludeDirectories as $excludeDirectory) {
            if (strpos($file, $excludeDirectory) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getExcludeDirectories($storeId = null)
    {
        try {
            $directories = $this->unserialize($this->getModuleConfig('image_directory/exclude_directories', $storeId));
        } catch (Exception $e) {
            $directories = [];
        }

        $result = [];
        foreach ($directories as $key => $directory) {
            $result[$key] = $directory['path'];
        }

        return $result;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getIncludeDirectories($storeId = null)
    {
        try {
            $directories = $this->unserialize($this->getModuleConfig('image_directory/include_directories', $storeId));
        } catch (Exception $e) {
            $directories = [];
        }

        $result = [];
        foreach ($directories as $key => $directory) {
            $result[$key] = $directory['path'];
        }

        return $result;
    }

    /**
     * @param string $directory
     *
     * @return bool
     * @throws FileSystemException
     */
    protected function checkDirectoryReadable($directory)
    {
        return $this->driverFile->isExists($directory) && $this->driverFile->isReadable($directory);
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getPathInfo($path)
    {
        return $this->ioFile->getPathInfo($path);
    }

    /**
     * @param string $file
     * @param string $extensionPath
     *
     * @return bool
     */
    protected function isTransparentImage($file, $extensionPath)
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        $isTransparentImage = false;
        if ($extensionPath === 'png' && $this->skipTransparentImage()) {
            try {
                $imageColorTransparent = imagecolortransparent(imagecreatefrompng($file));
                $isTransparentImage    = $imageColorTransparent >= 0 || $imageColorTransparent !== null;
            } catch (Exception $e) {
                $isTransparentImage = false;
            }
        }

        return $isTransparentImage;
    }

    /**
     * @return mixed
     */
    public function skipTransparentImage()
    {
        return $this->getOptimizeOptions('skip_transparent_img');
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOptimizeOptions($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('optimize_options' . $code, $storeId);
    }

    /**
     * @param string $path
     *
     * @return array|mixed
     */
    public function optimizeImage($path)
    {
        $result = [];
        if (!$this->fileExists($path)) {
            $result = [
                'error'      => true,
                'error_long' => __('file %1 does not exist', $path)
            ];

            return $result;
        }

        $curl = $this->curlFactory->create();
        //End point
        $url    = $this->buildEndpointUrl();
        $params = $this->getParams($path);
        try {
            if ($this->getOptimizeOptions('create_webp_image')) {
                $this->optimizeImageToWebp($path);
            }
            $curl->write("POST", $url, '1.1', [], $params);
            $resultCurl = $curl->read();
            if (!empty($resultCurl)) {
                $responseBody = $this->extractBody($resultCurl);
                $result       += self::jsonDecode($responseBody);
            }
        } catch (Exception $e) {
            $result['error']      = true;
            $result['error_long'] = $e->getMessage();
        }
        $curl->close();

        if (isset($result['dest'], $result['percent'])) {
            if ($result['percent'] > 0) {
                try {
                    if ($this->saveImage($result['dest'], $path) === false) {
                        $result['error']      = true;
                        $result['error_long'] = __('The file %1 is not writable', $path);
                    }
                } catch (Exception $e) {
                    $result['error']      = true;
                    $result['error_long'] = $e->getMessage();
                }
            } else {
                $result['error_long'] = __('The image cannot be compressed more. Please reduce the image quality to continue optimizing.');
            }
        }

        return $result;
    }

    /**
     * Extract the body from a response string
     *
     * @param string $response_str
     * @return string
     */
    public static function extractBody($response_str)
    {
        $parts = preg_split('|(?:\r\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return $parts[1];
        }
        return '';
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path)
    {
        try {
            $isExists = $this->driverFile->isExists($path);
        } catch (FileSystemException $e) {
            $isExists = false;
            $this->_logger->critical($e->getMessage());
        }

        return $isExists;
    }

    /**
     * Build end point api
     *
     * @return string
     */
    public function buildEndpointUrl()
    {
        $endpoint = 'http://api.resmush.it/';

        return $endpoint . '/?qlty=' . $this->getQuality();
    }

    /**
     * @return int|mixed
     */
    public function getQuality()
    {
        $quality = 100;

        if ($this->getOptimizeOptions('image_quality') === Quality::CUSTOM) {
            $quality = $this->getOptimizeOptions('quality_percent');
        }

        return $quality;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getParams($path)
    {
        $mime   = mime_content_type($path);
        $info   = $this->getPathInfo($path);
        $name   = $info['basename'];
        $output = new CURLFile($path, $mime, $name);

        return [
            'files' => $output
        ];
    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return bool|int
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function saveImage($url, $path)
    {
        if (!$this->driverFile->isWritable($path)) {
            return false;
        }

        if ($this->getConfigGeneral('backup_image')) {
            $this->backupImage($path);
        }

        $content = $this->driverFile->fileGetContents($url);
        $this->driverFile->deleteFile($path);
        $result  = $this->driverFile->filePutContents($path, $content);

        if ($this->getOptimizeOptions('force_permission')) {
            $this->driverFile->changePermissions($path, octdec($this->getOptimizeOptions('select_permission')));
        }

        return $result;
    }

    /**
     * Handle image backup process
     *
     * @param string $path
     */
    public function backupImage($path)
    {
        $pathInfo = $this->getPathInfo($path);
        $folder   = BP . '/var/backup_image' . $pathInfo['dirname'];

        try {
            $this->ioFile->checkAndCreateFolder($folder, 0775);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        if (!$this->fileExists(BP . '/var/backup_image' . $path)) {
            $this->ioFile->write(BP . '/var/backup_image' . $path, $path, 0664);
        }
    }

    /**
     * Handle image rollback process
     *
     * @param string $path
     *
     * @return bool|int
     * @throws LocalizedException
     */
    public function restoreImage($path)
    {
        if (!$this->fileExists('var/backup_image/' . $path)) {
            throw new LocalizedException(__('Image %1 has not been backed up.', $path));
        }

        return $this->ioFile->write($path, 'var/backup_image/' . $path);
    }

    /**
     * @throws Exception
     */
    public function createHtaccessFile()
    {
        $this->ioFile->checkAndCreateFolder('var/backup_image', 0664);
        $this->ioFile->cp('pub/media/.htaccess', 'var/backup_image/.htaccess');
    }

    /**
     * @param string $path
     *
     * @throws FileSystemException
     * @throws ImagickException
     * @throws NotFoundException
     */
    public function optimizeImageToWebp($path)
    {
        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product');
        $newPath   = str_replace($mediaPath, '', $path);

        if (strpos($path, 'catalog/product') !== false) {
            $this->resizeFromImageName($newPath);
        } else {
            $this->convertImageToWebp($path);
        }
    }

    /**
     * @param string $originalImageName
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function resizeFromImageName($originalImageName)
    {
        $mediaStorageFileName = $this->imageConfig->getMediaPath($originalImageName);
        $originalImagePath    = $this->mediaDirectory->getAbsolutePath($mediaStorageFileName);

        if ($this->fileStorageDatabase->checkDbUsage() &&
            !$this->mediaDirectory->isFile($mediaStorageFileName)
        ) {
            $this->fileStorageDatabase->saveFileToFilesystem($mediaStorageFileName);
        }

        if (!$this->mediaDirectory->isFile($originalImagePath)) {
            throw new NotFoundException(__('Cannot resize image "%1" - original image not found', $originalImagePath));
        }

        foreach ($this->getViewImages($this->getThemesInUse()) as $viewImage) {
            $this->resize($viewImage, $originalImagePath, $originalImageName);
        }
    }

    /**
     * Get view images data from themes.
     *
     * @param array $themes
     *
     * @return array
     */
    private function getViewImages($themes)
    {
        $viewImages = [];
        $stores     = $this->storeManager->getStores(true);
        /** @var Theme $theme */
        foreach ($themes as $theme) {
            $config = $this->viewConfig->getViewConfig(
                [
                    'area'       => Area::AREA_FRONTEND,
                    'themeModel' => $theme,
                ]
            );
            $images = $config->getMediaEntities('Magento_Catalog', ImageHelper::MEDIA_TYPE_CONFIG_NODE);
            foreach ($images as $imageId => $imageData) {
                foreach ($stores as $store) {
                    $data                   = $this->paramsBuilder->build($imageData, (int) $store->getId());
                    $uniqIndex              = $this->getUniqueImageIndex($data);
                    $data['id']             = $imageId;
                    $viewImages[$uniqIndex] = $data;
                }
            }
        }

        return $viewImages;
    }

    /**
     * Search the current theme.
     *
     * @return array
     */
    private function getThemesInUse()
    {
        $themesInUse      = [];
        $registeredThemes = $this->themeCollection->loadRegisteredThemes();
        $storesByThemes   = $this->themeCustomizationConfig->getStoresByThemes();
        $keyType          = is_integer(key($storesByThemes)) ? 'getId' : 'getCode';
        foreach ($registeredThemes as $registeredTheme) {
            if (array_key_exists($registeredTheme->$keyType(), $storesByThemes)) {
                $themesInUse[] = $registeredTheme;
            }
        }

        return $themesInUse;
    }

    /**
     * @param array $imageData
     *
     * @return string
     */
    protected function getUniqueImageIndex($imageData)
    {
        ksort($imageData);
        unset($imageData['type']);

        // phpcs:disable Magento2.Security.InsecureFunction
        return md5(json_encode($imageData));
    }

    /**
     * @param array $imageParams
     * @param string $originalImagePath
     * @param string $originalImageName
     *
     * @throws FileSystemException
     * @throws ImagickException
     */
    private function resize($imageParams, $originalImagePath, $originalImageName)
    {
        unset($imageParams['id']);
        $image      = $this->makeImage($originalImagePath, $imageParams);
        $imageAsset = $this->assertImageFactory->create(
            [
                'miscParams' => $imageParams,
                'filePath'   => $originalImageName,
            ]
        );

        if ($imageParams['image_width'] !== null && $imageParams['image_height'] !== null) {
            $image->resize($imageParams['image_width'], $imageParams['image_height']);
        }

        if (isset($imageParams['watermark_file'])) {
            if ($imageParams['watermark_height'] !== null) {
                $image->setWatermarkHeight($imageParams['watermark_height']);
            }

            if ($imageParams['watermark_width'] !== null) {
                $image->setWatermarkWidth($imageParams['watermark_width']);
            }

            if ($imageParams['watermark_position'] !== null) {
                $image->setWatermarkPosition($imageParams['watermark_position']);
            }

            if ($imageParams['watermark_image_opacity'] !== null) {
                $image->setWatermarkImageOpacity($imageParams['watermark_image_opacity']);
            }

            $image->watermark($this->getWatermarkFilePath($imageParams['watermark_file']));
        }

        $imageAssetPath = str_replace('/cache/', '/mpiowebpcache/', $imageAsset->getPath());

        $image->save($imageAssetPath);
        $this->convertImageToWebp($imageAssetPath, true);
    }

    /**
     * @param string $imageAssetPath
     * @param bool $isDelete
     *
     * @throws FileSystemException
     * @throws ImagickException
     */
    public function convertImageToWebp($imageAssetPath, $isDelete = false)
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        $image        = '';
        $imageConvert = true;
        $info         = $this->getPathInfo($imageAssetPath);
        $outputFile   = $info['dirname'] . '/' . $info['filename'] . '.webp';

        if ($this->fileExists($outputFile)) {
            $this->driverFile->deleteFile($outputFile);
        }

        if (function_exists('imagewebp')) {
            switch (mime_content_type($imageAssetPath)) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($imageAssetPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($imageAssetPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($imageAssetPath);
                    imagepalettetotruecolor($image);
                    break;
                case 'image/bmp':
                    $image = imagecreatefrombmp($imageAssetPath);
                    break;
                default:
                    if (class_exists('Imagick')) {
                        $imageConvert = false;
                        $newImage     = new \Imagick();
                        $newImage->readImage($imageAssetPath);
                        $newImage->writeImage($outputFile);
                    }

                    break;
            }

            if ($image && $imageConvert) {
                imagewebp($image, $outputFile, $this->getQuality());
                imagedestroy($image);
            }

            if ($this->getOptimizeOptions('force_permission')) {
                $this->driverFile->changePermissions(
                    $outputFile,
                    octdec($this->getOptimizeOptions('select_permission'))
                );
            }
        }

        if ($isDelete) {
            $this->driverFile->deleteFile($imageAssetPath);
        }
    }

    /**
     * Returns watermark file absolute path
     *
     * @param string $file
     *
     * @return string
     */
    private function getWatermarkFilePath($file)
    {
        $path = $this->imageConfig->getMediaPath('/watermark/' . $file);

        return $this->mediaDirectory->getAbsolutePath($path);
    }

    /**
     * @param string $originalImagePath
     * @param array $imageParams
     *
     * @return Image
     */
    private function makeImage($originalImagePath, $imageParams)
    {
        $image = $this->imageFactory->create($originalImagePath);
        $image->keepAspectRatio($imageParams['keep_aspect_ratio']);
        $image->keepFrame($imageParams['keep_frame']);
        $image->keepTransparency($imageParams['keep_transparency']);
        $image->constrainOnly($imageParams['constrain_only']);
        $image->backgroundColor($imageParams['background']);
        $image->quality($imageParams['quality']);

        return $image;
    }

    /**
     * @return bool
     */
    public function isReplaceWebpImage()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }

        return $this->getOptimizeOptions('create_webp_image', $storeId)
            && $this->getOptimizeOptions('replace_webp_image', $storeId);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function checkImageExists($url)
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        $connection = curl_init($url);
        curl_setopt($connection, CURLOPT_HEADER, true);
        curl_setopt($connection, CURLOPT_NOBODY, true);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($connection);
        $responseStatus = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        return $responseStatus === 200;
    }
}
