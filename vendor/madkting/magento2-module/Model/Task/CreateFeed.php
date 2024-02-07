<?php
/**
 * Madkting Software (http://www.madkting.com)
 *
 *                                      ..-+::moossso`:.
 *                                    -``         ``ynnh+.
 *                                 .d                 -mmn.
 *     .od/hs..sd/hm.   .:mdhn:.   yo                 `hmn. on     mo omosnomsso oo  .:ndhm:.   .:odhs:.
 *    :hs.h.shhy.d.mh: :do.hd.oh:  /h                `+nm+  dm   ys`  ````mo```` hn :ds.hd.yo: :oh.hd.dh:
 *    ys`   `od`   `h+ sh`    `do  .d`              `snm/`  +s hd`        hd     yy yo`    `sd oh`    ```
 *    hh     sh     +m hs      yy   y-            `+mno`    dkdm          +d     o+ no      ss ys    dosd
 *    y+     ss     oh hdsomsmnmy   ++          .smh/`      om ss.        dh     mn yo      oh sm      hy
 *    sh     ho     ys hs``````yy   .s       .+hh+`         ys   hs.      os     yh os      d+ od+.  ./m/
 *    od     od     od od      od   +y    .+so:`            od     od     od     od od      od  `syssys`
 *                                 .ys .::-`
 *                                o.+`
 *
 * @category Module
 * @package Madkting\Connect
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model\Task;

use Madkting\Connect\Helper\Data as MadktingHelper;
use Madkting\Connect\Helper\Images as MadktingImagesHelper;
use Madkting\Connect\Helper\Product as MadktingProductHelper;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\AttributeFactory;
use Madkting\Connect\Model\AttributeMappingFactory;
use Madkting\Connect\Model\AttributeOptionFactory;
use Madkting\Connect\Model\AttributeOptionMappingFactory;
use Madkting\Connect\Model\CategoriesMappingFactory;
use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\ProcessedFeed;
use Madkting\Connect\Model\ProcessedFeedFactory;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductImageFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Exception\MadktingException;
use Madkting\MadktingClient;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CreateFeed
 * @package Madkting\Connect\Model\Task
 */
class CreateFeed
{
    /**
     * Feeds limit
     */
    const MAX_FEED_TASKS = 100;

    /**
     * @var array
     */
    protected $feeds = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $variationsFields;

    /**
     * @var array
     */
    protected $selectiveSync = [];

    /**
     * Fields with empty value allowed
     *
     * @var array
     */
    protected $emptyValueAllowed;

    /**
     * Fields that have to be cleared
     *
     * @var array
     */
    protected $clearAttributes;

    /**
     * Fields allowed to have HTML
     *
     * @var array
     */
    protected $htmlAllowed;

    /**
     * Fields that do not need to be validated
     *
     * @var array
     */
    protected $noValidation;

    /**
     * @var ProcessedFeedFactory
     */
    protected $feedFactory;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var AttributeOptionFactory
     */
    protected $attributeOptionFactory;

    /**
     * @var AttributeMappingFactory
     */
    protected $attributeMappingFactory;

    /**
     * @var AttributeOptionMappingFactory
     */
    protected $attributeOptionMappingFactory;

    /**
     * @var CategoriesMappingFactory
     */
    protected $categoriesMappingFactory;

    /**
     * @var ProductImageFactory
     */
    protected $imageFactory;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var MadktingHelper
     */
    protected $madktingHelper;

    /**
     * @var MadktingProductHelper
     */
    protected $madktingProductHelper;

    /**
     * @var MadktingImagesHelper
     */
    protected $madktingImagesHelper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ProductFactory
     */
    protected $madktingProductFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * CreateFeed constructor
     *
     * @param ProcessedFeedFactory $feedFactory
     * @param MadktingConfig $madktingConfig
     * @param ProductRepositoryInterface $productRepository
     * @param AttributeFactory $attributeFactory
     * @param AttributeOptionFactory $attributeOptionFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     * @param AttributeOptionMappingFactory $attributeOptionMappingFactory
     * @param CategoriesMappingFactory $categoriesMappingFactory
     * @param ProductImageFactory $imageFactory
     * @param EavConfig $eavConfig
     * @param MadktingHelper $madktingHelper
     * @param MadktingProductHelper $madktingProductHelper
     * @param MadktingImagesHelper $madktingImagesHelper
     * @param DateTime $dateTime
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param ProductFactory $madktingProductFactory
     * @param MadktingLogger $logger
     */
    public function __construct(
        ProcessedFeedFactory $feedFactory,
        MadktingConfig $madktingConfig,
        ProductRepositoryInterface $productRepository,
        AttributeFactory $attributeFactory,
        AttributeOptionFactory $attributeOptionFactory,
        AttributeMappingFactory $attributeMappingFactory,
        AttributeOptionMappingFactory $attributeOptionMappingFactory,
        CategoriesMappingFactory $categoriesMappingFactory,
        ProductImageFactory $imageFactory,
        EavConfig $eavConfig,
        MadktingHelper $madktingHelper,
        MadktingProductHelper $madktingProductHelper,
        MadktingImagesHelper $madktingImagesHelper,
        DateTime $dateTime,
        ProductTaskQueueFactory $productTaskQueueFactory,
        ProductFactory $madktingProductFactory,
        MadktingLogger $logger
    ) {
        $this->feedFactory = $feedFactory;
        $this->madktingConfig = $madktingConfig;
        $this->productRepository = $productRepository;
        $this->attributeFactory = $attributeFactory;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeMappingFactory = $attributeMappingFactory;
        $this->attributeOptionMappingFactory = $attributeOptionMappingFactory;
        $this->categoriesMappingFactory = $categoriesMappingFactory;
        $this->imageFactory = $imageFactory;
        $this->eavConfig = $eavConfig;
        $this->madktingHelper = $madktingHelper;
        $this->madktingProductHelper = $madktingProductHelper;
        $this->madktingImagesHelper = $madktingImagesHelper;
        $this->dateTime = $dateTime;
        $this->madktingProductFactory = $madktingProductFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->logger = $logger;
        $this->variationsFields = $madktingHelper->getVariationsFields();
        $this->emptyValueAllowed = $madktingHelper->getEmptyValueAllowed();
        $this->clearAttributes = $madktingHelper->getClearAttributes();
        $this->htmlAllowed = $madktingHelper->getHtmlAllowed();
        $this->noValidation = $madktingHelper->getNoValidation();
    }

    /**
     * @param array $tasks
     * @param string $action
     * @throws LocalizedException
     */
    public function execute($tasks, $action)
    {
        $productFeedCount = 0;
        foreach ($tasks as $task) {
            try {
                switch ($task['type']) {
                    case ProductTaskQueue::TYPE_PRODUCT:

                        switch ($action) {
                            case ProductTaskQueue::ACTION_CREATE:
                                $productData = $this->getProductData($task['taskId'], $task['product'], $action);

                                if (empty($productData)) {
                                    continue 3;
                                }

                                $this->feeds[$task['shop']][ProductTaskQueue::TYPE_PRODUCT][$productFeedCount][] = $productData;
                                break;
                            case ProductTaskQueue::ACTION_UPDATE:
                                $productData = $this->getProductData($task['taskId'], $task['product'], $action, $task['productPk'], false, true);

                                if (empty($productData)) {
                                    continue 3;
                                }

                                $changes = $this->validateChanges($productData);
                                if (!empty($changes)) {
                                    $this->feeds[$task['shop']][ProductTaskQueue::TYPE_PRODUCT][$productFeedCount][] = $changes;
                                } else {

                                    /* Close Task */
                                    $this->productTaskQueueFactory->create()->load($task['taskId'])->finishTask();

                                    /* Set product status */
                                    $this->madktingProductFactory->create()->load($task['product'], 'magento_product_id')
                                        ->setStatus(Product::STATUS_SYNCHRONIZED)
                                        ->save();

                                    /* Check images changes */
                                    $this->madktingImagesHelper->queueImagesUpdates($task['product']);

                                    continue 3;
                                }
                                break;
                            case ProductTaskQueue::ACTION_DELETE:
                                $this->feeds[$task['shop']][ProductTaskQueue::TYPE_PRODUCT][$productFeedCount][] = [
                                    'taskId' => $task['taskId'],
                                    'productId' => $task['product'],
                                    'pk' => $task['productPk']
                                ];
                                break;
                        }

                        if (count($this->feeds[$task['shop']][ProductTaskQueue::TYPE_PRODUCT][$productFeedCount]) == self::MAX_FEED_TASKS) {
                            ++$productFeedCount;
                        }
                        break;
                    case ProductTaskQueue::TYPE_VARIATION:

                        switch ($action) {
                            case ProductTaskQueue::ACTION_CREATE:
                                $variationData = $this->getProductData($task['taskId'], $task['product'], $action, null, true, true);

                                if (empty($variationData)) {
                                    continue 3;
                                }

                                $this->feeds[$task['shop']][ProductTaskQueue::TYPE_VARIATION][$variationData['parentPk']][] = $variationData;
                                break;
                            case ProductTaskQueue::ACTION_UPDATE:
                                $variationData = $this->getProductData($task['taskId'], $task['product'], $action, $task['productPk'], true, true);

                                if (empty($variationData)) {
                                    continue 3;
                                }

                                $changes = $this->validateChanges($variationData);
                                if (!empty($changes)) {
                                    $this->feeds[$task['shop']][ProductTaskQueue::TYPE_VARIATION][$variationData['parentPk']][] = $changes;
                                } else {

                                    /* Close Task */
                                    $this->productTaskQueueFactory->create()->load($task['taskId'])->finishTask();

                                    /* Set product status */
                                    $this->madktingProductFactory->create()->load($task['product'], 'magento_product_id')
                                        ->setStatus(Product::STATUS_SYNCHRONIZED)
                                        ->save();

                                    /* Check images changes */
                                    $this->madktingImagesHelper->queueImagesUpdates($task['product'], true);

                                    continue 3;
                                }
                                break;
                            case ProductTaskQueue::ACTION_DELETE:
                                $this->feeds[$task['shop']][ProductTaskQueue::TYPE_VARIATION][$task['parentPk']][] = [
                                    'taskId' => $task['taskId'],
                                    'productId' => $task['product'],
                                    'pk' => $task['productPk']
                                ];
                                break;
                        }
                        break;
                }
            } catch (\Exception $e) {
                $logCase = $this->logger->exception($e, $e->getMessage(), array(), true);
                $message = __('There has been an error while processing product, check log registry #%1 for more details', $logCase);
                $this->addError($task['taskId'], $task['product'], $message, Product::STATUS_SYSTEM_ERROR);
            } catch (\Throwable $t) {
                $logCase = $this->logger->exception($t, $t->getMessage(), array(), true);
                $message = __('There has been an error while processing product, check log registry #%1 for more details', $logCase);
                $this->addError($task['taskId'], $task['product'], $message, Product::STATUS_SYSTEM_ERROR);
            }
        }

        /* Process feeds in Madkting */
        $this->processFeeds($action);

        /* Process errors */
        $this->processErrors();
    }

    /**
     * @param int $taskId
     * @param int $productId
     * @param int|null $action
     * @param int|null $productPk
     * @param bool $isVariation
     * @param bool $imagesByPk
     * @return array|bool
     */
    protected function getProductData($taskId, $productId, $action, $productPk = null, $isVariation = false, $imagesByPk = false, $singleVariation = true)
    {
        $productData = [
            'taskId' => $taskId,
            'productId' => $productId
        ];

        if (!empty($productPk)) {
            $productData['pk'] = $productPk;
        }

        /**
         * Get product model
         *
         * @var \Magento\Catalog\Model\Product $product
         */
        $storeId = $this->madktingConfig->getSelectedStore();
        $product = $this->productRepository->getById($productId, false, $storeId);

        /* Variation / No Variation special attributes */
        if ($isVariation) {
            $parentId = $this->madktingProductHelper->getParentId($productId);

            /* Get configurable attributes */
            $parentModel = $this->productRepository->getById($parentId, false, $storeId);
            $configurableAttributesArray = $parentModel->getTypeInstance()->getConfigurableAttributesAsArray($parentModel);

            /* Single variation creation */
            if ($singleVariation) {

                /* Get parent PK */
                $parentPk = $this->madktingProductFactory->create()->load($parentId, 'magento_product_id')->getMadktingProductId();
                if (!$parentPk) {

                    /* Add error */
                    $this->addError($taskId, $productId, __('Parent product has not been created in Yuju yet'));
                    return false;
                } else {
                    $productData['parentPk'] = $parentPk;
                }
            }
        } else {
            /* If category_pk can be synchronized */
            if ($this->canSynchronize($taskId, 'category_pk', $action)) {

                /* Get category */
                $productCategories = $product->getCategoryIds();
                if (!empty($productCategories)) {
                    foreach ($productCategories as $category) {
                        $madktingCategory = $this->categoriesMappingFactory->create()->load($category, 'magento_category_id')->getMadktingCategoryId();
                        if (!empty($madktingCategory)) {
                            $productData['category_pk'] = $madktingCategory;
                        }
                    }
                    if (empty($productData['category_pk'])) {
                        $this->addError($taskId, $productId, __('Missing category match'));
                        return false;
                    }
                } else {
                    $this->addError($taskId, $productId, __('Missing category in Magento'));
                    return false;
                }
            }

            /* Get configurable attributes */
            if ($product->getTypeId() === Configurable::TYPE_CODE) {
                $configurableAttributesArray = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
            }
        }

        /* Get attribute mapping */
        $attributeSetId = $product->getAttributeSetId();
        $attributeMappingModel = $this->attributeMappingFactory->create();
        $attributeMapping = $attributeMappingModel->getCollection()
            ->addFieldToFilter('attribute_set_id', $attributeSetId)->getData();

        /* If is empty attribute mapping get by default attribute set */
        if (empty($attributeMapping)) {
            $attributeSetId = $product->getDefaultAttributeSetId();
            $attributeMapping = $attributeMappingModel->getCollection()
                ->addFieldToFilter('attribute_set_id', $attributeSetId)->getData();
        }

        /* If it is not empty attribute mapping get product data */
        if (!empty($attributeMapping)) {
            /* Validate configurable attributes */
            if (!empty($configurableAttributesArray)) {
                $customVariationAttributes = [];
                $matchedAttributesIds = array_column($attributeMapping, 'magento_attribute_id');
                foreach ($configurableAttributesArray as $configurableAttribute) {
                    if (!in_array($configurableAttribute['attribute_id'], $matchedAttributesIds)) {
                        $customVariationAttributes[$configurableAttribute['attribute_id']] = $configurableAttribute['attribute_code'];
                    }
                }
                $customVariationAttributesCount = count($customVariationAttributes);
                if ($customVariationAttributesCount > 1) {
                    $message = __('You can add just one custom configurable attribute, please match at least %1 of this: %2',
                        --$customVariationAttributesCount, implode(', ', $customVariationAttributes));
                    $this->addError($taskId, $productId, $message);
                    return false;
                } elseif ($customVariationAttributesCount) {
                    if ($isVariation) {
                        $attributeMapping[] = [
                            'magento_attribute_id' => key($customVariationAttributes),
                            'madkting_attribute_id' => 240
                        ];
                    } else {
                        $attributeModel = $this->eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, key($customVariationAttributes));
                        $productData['custom_variation_name'] = $attributeModel->getStoreLabel($storeId);
                    }
                }
            }

            /* Color match flag */
            $attributeColorMatch = false;

            /* Get remaining attributes */
            foreach ($attributeMapping as $attribute) {

                /**
                 * Get Yuju's attribute data
                 *
                 * @var \Madkting\Connect\Model\Attribute $attributeModel
                 */
                $attributeModel = $this->attributeFactory->create()->load($attribute['madkting_attribute_id']);
                $madktingCode = $attributeModel->getAttributeCode();

                /* If attribute can be synchronized */
                if (!$this->canSynchronize($taskId, $madktingCode, $action)) {
                    continue;
                }

                /* If is variation only get variations fields */
                if ($isVariation && !in_array($madktingCode, $this->variationsFields)) {
                    continue;
                }

                /* Get attribute value */
                if (!empty($attribute['magento_attribute_id'])) {
                    $magentoAttribute = $this->eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $attribute['magento_attribute_id']);
                    $attributeCode = $magentoAttribute->getAttributeCode();
                    $value = strlen((string)$product->getData($attributeCode)) ? $product->getData($attributeCode) : null;

                    /* If Magento attribute's type is select */
                    $magentoType = $magentoAttribute->getFrontendInput();
                    if ($magentoType == 'select' && !is_null($value)) {

                        /**
                         * If attribute has option mapping
                         *
                         * @var \Madkting\Connect\Model\AttributeOptionMapping $attributeOptionModel
                         */
                        $attributeOptionId = $this->attributeOptionMappingFactory->create()
                            ->load($value, 'magento_attribute_option_id')
                            ->getMadktingAttributeOptionId();
                        if (!empty($attributeOptionId)) {
                            $value = $this->attributeOptionFactory->create()->load($attributeOptionId)->getOptionValue();

                            /* There is color match */
                            if ($madktingCode == 'color') {
                                $attributeColorMatch = true;
                            }
                        } else {
                            $value = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
                        }
                    }

                    /* If value is empty set mapping default option */
                    $value = strlen((string)$value) ? $value : $attribute['default_value'];
                } else {
                    /* Set mapping default option */
                    $value = $attribute['default_value'];
                }

                /* If value is empty set attribute default value */
                $value = strlen((string)$value) ? $value : $attributeModel->getDefaultValue();

                /* Clear data */
                if (in_array($madktingCode, $this->clearAttributes)) {
                    $allowHtml = in_array($madktingCode, $this->htmlAllowed);
                    $value = $this->madktingHelper->cleanMadktingData($value, $allowHtml);
                }

                /* Format value */
                $value = $this->formatAttributeValue($value, $attributeModel->getAttributeFormat());

                /* If value is null skip attribute */
                if (!strlen((string)$value) && !in_array($madktingCode, $this->emptyValueAllowed)) {
                    continue;
                }

                $productData[$madktingCode] = $value;
            }

            /* If description is empty */
            if ($this->canSynchronize($taskId, 'description', $action)) {
                if (!$isVariation || in_array('description', $this->variationsFields)) {
                    if (empty($productData['description'])) {
                        $description = $this->madktingHelper->cleanMadktingData($product->getName());
                        if (!empty($description)) {
                            $productData['description'] = $description;
                        }
                    }
                }
            }

            /* Get link data */
            if ($this->canSynchronize($taskId, 'link', $action)) {
                if (!$isVariation || in_array('link', $this->variationsFields)) {
                    $productData['link'] = $product->getProductUrl();
                }
            }

            /* Get custom category data */
            if ($this->canSynchronize($taskId, 'custom_cat', $action)) {
                if (!$isVariation || in_array('custom_cat', $this->variationsFields)) {
                    $productData['custom_cat'] = $this->madktingProductHelper->getCategoriesPath($product);
                }
            }

            /* Get stock data */
            if ($this->canSynchronize($taskId, 'stock', $action)) {
                if (!$isVariation || in_array('stock', $this->variationsFields)) {
                    $productData['stock'] = $this->madktingProductHelper->getProductStock($product->getSku());
                }
            }

            /* Process color information */
            if ($this->canSynchronize($taskId, 'color', $action) || $this->canSynchronize($taskId, 'color_text', $action)) {
                if (isset($productData['color_text'])) {
                    if ($attributeColorMatch) {
                        $productData['color_text'] = null;
                    } else {
                        $productData['color'] = 'multicolor';
                    }
                }
            }

            /* If has variations */
            $variations = $this->madktingProductHelper->getVariationsId($productId);
            if (!empty($variations)) {
                /* If price is not set */
                $setPrice = false;
                if ($this->canSynchronize($taskId, 'price', $action)) {
                    if (empty($productData['price'])) {
                        $setPrice = true;
                        $productData['price'] = 1;
                    }
                }
                foreach ($variations as $variation) {
                    $variationData = $this->getProductData($taskId, $variation, $action, null, true, false, false);
                    if (!empty($variationData)) {
                        $productData['variations'][] = $variationData;
                    }
                    if ($setPrice && !empty($variationData['price']) && $variationData['price'] > $productData['price']) {
                        $productData['price'] = $variationData['price'];
                    }
                }
            }

            /* Get images */
            if ($this->canSynchronize($taskId, 'images', $action)) {
                $imageParentPk = !empty($productData['parentPk']) ? $productData['parentPk'] : null;
                if (!empty($images = $this->getImages($product, $variations, !$isVariation, $imagesByPk, $imageParentPk))) {
                    $productData['images'] = $images;
                }
            }

            /* Validate images condition */
            $validateImages = true;
            if ($isVariation ||
                $action == ProductTaskQueue::ACTION_UPDATE ||
                ($this->canSynchronize($taskId, 'images', $action))) {
                $validateImages = false;
            }

            /* Validate information */
            $validation = $this->validateData($productData, $isVariation, $validateImages, !empty($variations));
            if ($validation['error']) {
                $this->addError($taskId, $productId, $validation['message']);
                return false;
            }
        } else {
            $this->addError($taskId, $productId, __('No attribute match defined'), Product::STATUS_SYSTEM_ERROR);
            return false;
        }

        return $productData;
    }

    /**
     * @param int $taskId
     * @param string $attributeCode
     * @param int|null $action
     * @return bool
     */
    protected function canSynchronize($taskId, $attributeCode, $action = null)
    {
        /* Check selective synchronization */
        if (!isset($this->selectiveSync[$taskId])) {
            $selectiveSync = $this->productTaskQueueFactory->create()->load($taskId)->getSelectiveSync();
            $this->selectiveSync[$taskId] = !empty($selectiveSync) ? json_decode($selectiveSync, true) : [];
        }
        if (!empty($this->selectiveSync[$taskId]) && !in_array($attributeCode, $this->selectiveSync[$taskId])) {
            return false;
        }

        /* Check attributes disabled for synchronization */
        $disabledAttributesSync = $this->madktingConfig->getAttributesDisabledSynchronization();
        if (in_array($attributeCode, $disabledAttributesSync) && $action !== ProductTaskQueue::ACTION_CREATE) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $variations
     * @param bool $saveImages
     * @param bool $imagesByPk
     * @param int|null $parentPk
     * @return array
     */
    protected function getImages(\Magento\Catalog\Model\Product $product, $variations = [], $saveImages = false, $imagesByPk = false, $parentPk = null)
    {
        $images = [];

        $gallery = $product->getMediaGalleryImages();

        /* Images type return */
        if ($imagesByPk) {
            foreach ($gallery as $image) {

                /** Get images PK */
                $imagesModel = $this->imageFactory->create()->getCollection()->addFieldToFilter('magento_image_url', $image->getUrl());
                if ($parentPk) $imagesModel->addFieldToFilter('madkting_product_id', $parentPk);
                else $imagesModel->addFieldToFilter('magento_product_id', $product->getId());
                $imagesModel->setOrder('position', Collection::SORT_ORDER_ASC);

                if (!empty($imagePk = $imagesModel->setPageSize(1)->getFirstItem()->getMadktingImageId())) {
                    $images[] = ['pk' => $imagePk];
                }
            }
        } else {
            $toSave = [];

            foreach ($gallery as $image) {
                if (array_search($image->getUrl(), array_column($images, 'url')) === false) {
                    $images[] = ['url' => $image->getUrl()];
                    $toSave[] = ['url' => $image->getUrl(), 'id' => $image->getId()];
                }
            }

            if (!empty($variations)) {
                $storeId = $this->madktingConfig->getSelectedStore();

                foreach ($variations as $variation) {
                    /**
                     * Get product model
                     *
                     * @var \Magento\Catalog\Model\Product $variationImages
                     */
                    $variationImages = $this->productRepository->getById($variation, false, $storeId)->getMediaGalleryImages();
                    foreach ($variationImages as $image) {
                        if (array_search($image->getUrl(), array_column($images, 'url')) === false) {
                            $images[] = ['url' => $image->getUrl()];
                            $toSave[] = ['url' => $image->getUrl(), 'id' => $image->getId()];
                        }
                    }
                }
            }

            /* Save images */
            !$saveImages?: $this->saveProductImages($product->getId(), $toSave);
        }

        return $images;
    }

    /**
     * Save images
     *
     * @param int $productId
     * @param array $images
     */
    protected function saveProductImages($productId, $images)
    {
        $position = 0;
        foreach ($images as $image) {
            $productImage = $this->imageFactory->create()->getCollection()
                ->addFieldToFilter('magento_product_id', $productId)
                ->addFieldToFilter('magento_image_url', $image['url'])
                ->setPageSize(1)
                ->getFirstItem();

            if (empty($productImage->getId())) {
                $productImage->setData([
                    'magento_image_url' => $image['url'],
                    'magento_product_id' => $productId,
                    'magento_image_id' => $image['id'],
                    'position' => $position
                ])->save();
            } else {
                $productImage->setPosition($position)->save();
            }
            ++$position;
        }
    }

    /**
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    protected function formatAttributeValue($value, $format)
    {
        switch ($format) {
            case 'number':
                $value = (int)$value;
                break;
            case 'money':
                $value = round($value, 2);
                break;
	        case 'decimal':
		        $value = floatval($value);
                $value = round($value, 2);
                break;
            case 'date':
                $value = date("M d Y H:i:s", $this->dateTime->gmtTimestamp($value));
                break;
            default:
                $value = (string)$value;
                break;
        }

        return $value;
    }

    /**
     * @param array $productData
     * @param bool $isVariation
     * @param bool $validateImages
     * @param bool $isConfigurable
     * @return array
     */
    protected function validateData($productData, $isVariation, $validateImages = false, $isConfigurable = false)
    {
        $response = [
            'error' => false,
            'message' => ''
        ];

        /* Check max length, min num and max num */
        foreach ($productData as $code => &$value) {
            /* If it is a configurable product and the field does not have to be validated, skip it */
            if ($isConfigurable && in_array($code, $this->noValidation)) {
                continue;
            }

            /* If attribute can be synchronized */
            if ($this->canSynchronize($productData['taskId'], $code)) {
                $attributeModel = $this->attributeFactory->create()->load($code, 'attribute_code');

                if (!empty($maxLength = $attributeModel->getMaxLength())) {
                    if (strlen((string)$value) > $maxLength) {
                        $response['error'] = true;
                        $response['message'] .= __('%1 max length is %2', $attributeModel->getAttributeLabel(), $maxLength) . ' | ';
                    }
                }

                if (!empty($minNum = $attributeModel->getMinNum())) {
                    if ($value < $minNum) {
                        $response['error'] = true;
                        $response['message'] .= __('%1 min value is %2', $attributeModel->getAttributeLabel(), $minNum) . ' | ';
                    }
                }

                if (!empty($maxNum = $attributeModel->getMaxNum())) {
                    if ($value > $maxNum) {
                        $response['error'] = true;
                        $response['message'] .= __('%1 max value is %2', $attributeModel->getAttributeLabel(), $maxNum) . ' | ';
                    }
                }
            }
        }

        /* Check for missing required attributes */
        $attributeModel = $this->attributeFactory->create()->getCollection()->addFieldToFilter('requirement', 'Required');

        /** @var \Madkting\Connect\Model\Attribute $attribute */
        foreach ($attributeModel as $attribute) {
            $code = $attribute->getAttributeCode();

            /* If it is a configurable product and the field does not have to be validated, skip it */
            if ($isConfigurable && in_array($code, $this->noValidation)) {
                continue;
            }

            /* If attribute can be synchronized */
            if ($this->canSynchronize($productData['taskId'], $code)) {
                /* If it is a variation and the field is not for variations, skip it */
                if ($isVariation && !in_array($code, $this->variationsFields)) {
                    continue;
                }

                /* If attribute is images and it does not have to be validated, skip it */
                if ($code == 'images' && !$validateImages) {
                    continue;
                }

                if (!array_key_exists($code, $productData) ||
                    (!is_array($productData[$code]) && !strlen((string)$productData[$code]) && !in_array($code, $this->emptyValueAllowed))) {
                    $response['error'] = true;
                    $response['message'] .= __('%1 is required', $attribute->getAttributeLabel()) . ' | ';
                }
            }
        }

        return $response;
    }

    /**
     * Process feeds in Madkting
     *
     * @param int $action
     * @param array|null $products
     * @throws LocalizedException
     */
    protected function processFeeds($action, $products = null)
    {
        if (is_null($products)) {
            $products = $this->feeds;
        }

        if (!empty($products)) {
            /* Get Madkting token */
            $token = $this->madktingConfig->getMadktingToken();
            if (!$token) {
                throw new LocalizedException(__('There is no Yuju token information'));
            }
            $client = new MadktingClient(['token' => $token]);

            /* Process feeds in Madkting */
            foreach ($products as $feedShop => $feeds) {
                foreach ($feeds as $feedType => $feedsData) {

                    switch ($feedType) {
                        case ProductTaskQueue::TYPE_PRODUCT:
                            $service = $client->serviceProduct();

                            switch ($action) {
                                case ProductTaskQueue::ACTION_CREATE:
                                    foreach ($feedsData as $products) {

                                        /* Format products to send */
                                        $productsToSend = $products;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($productsToSend as &$product) {
                                            $positions[] = [
                                                'taskId' => $product['taskId'],
                                                'productId' => $product['productId']
                                            ];

                                            unset($product['taskId']);
                                            unset($product['productId']);
                                        }

                                        try {
                                            $location = $service->post([
                                                'shop_pk' => $feedShop,
                                                'products' => $productsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId, $productsToSend);
                                        } catch (MadktingException $e) {
                                            $products = $this->processMadktingException($e->getResponse()->body, $positions, $products);

                                            /* Send correct products */
                                            if (!empty($products) && count($products) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$products]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                                case ProductTaskQueue::ACTION_UPDATE:
                                    foreach ($feedsData as $products) {

                                        /* Format products to send */
                                        $productsToSend = $products;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($productsToSend as &$product) {
                                            $positions[] = [
                                                'taskId' => $product['taskId'],
                                                'productId' => $product['productId']
                                            ];

                                            unset($product['taskId']);
                                            unset($product['productId']);
                                        }

                                        try {
                                            $location = $service->put([
                                                'shop_pk' => $feedShop,
                                                'products' => $productsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId, $productsToSend);
                                        } catch (MadktingException $e) {
                                            $products = $this->processMadktingException($e->getResponse()->body, $positions, $products);

                                            /* Send correct products */
                                            if (!empty($products) && count($products) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$products]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                                case ProductTaskQueue::ACTION_DELETE:
                                    foreach ($feedsData as $products) {

                                        /* Format products to send */
                                        $productsToSend = $products;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($productsToSend as &$product) {
                                            $positions[] = [
                                                'taskId' => $product['taskId'],
                                                'productId' => $product['productId']
                                            ];

                                            unset($product['taskId']);
                                            unset($product['productId']);
                                        }

                                        try {
                                            $location = $service->delete([
                                                'shop_pk' => $feedShop,
                                                'products' => $productsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId);
                                        } catch (MadktingException $e) {
                                            $products = $this->processMadktingException($e->getResponse()->body, $positions, $products);

                                            /* Send correct products */
                                            if (!empty($products) && count($products) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$products]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                            }
                            break;
                        case ProductTaskQueue::TYPE_VARIATION:
                            $service = $client->serviceProductVariation();

                            switch ($action) {
                                case ProductTaskQueue::ACTION_CREATE:
                                    foreach ($feedsData as $productPk => $variations) {

                                        /* Format products to send */
                                        $variationsToSend = $variations;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($variationsToSend as &$variation) {
                                            $positions[] = [
                                                'taskId' => $variation['taskId'],
                                                'productId' => $variation['productId']
                                            ];

                                            /* Set parent PK */
                                            $this->madktingProductFactory->create()->load($variation['productId'], 'magento_product_id')
                                                ->setMadktingParentId($variation['parentPk'])
                                                ->save();

                                            unset($variation['taskId']);
                                            unset($variation['productId']);
                                            unset($variation['parentPk']);
                                        }

                                        try {
                                            $location = $service->post([
                                                'shop_pk' => $feedShop,
                                                'product_pk' => $productPk,
                                                'variations' => $variationsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId, $variationsToSend);
                                        } catch (MadktingException $e) {
                                            $variations = $this->processMadktingException($e->getResponse()->body, $positions, $variations);

                                            /* Send correct products */
                                            if (!empty($variations) && count($variations) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$variations]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                                case ProductTaskQueue::ACTION_UPDATE:
                                    foreach ($feedsData as $productPk => $variations) {

                                        /* Format products to send */
                                        $variationsToSend = $variations;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($variationsToSend as &$variation) {
                                            $positions[] = [
                                                'taskId' => $variation['taskId'],
                                                'productId' => $variation['productId']
                                            ];

                                            unset($variation['taskId']);
                                            unset($variation['productId']);
                                            unset($variation['parentPk']);
                                        }

                                        try {
                                            $location = $service->put([
                                                'shop_pk' => $feedShop,
                                                'product_pk' => $productPk,
                                                'variations' => $variationsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId, $variationsToSend);
                                        } catch (MadktingException $e) {
                                            $variations = $this->processMadktingException($e->getResponse()->body, $positions, $variations);

                                            /* Send correct products */
                                            if (!empty($variations) && count($variations) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$variations]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                                case ProductTaskQueue::ACTION_DELETE:
                                    foreach ($feedsData as $productPk => $variations) {

                                        /* Format products to send */
                                        $variationsToSend = $variations;

                                        /* Get positions */
                                        $positions = [];
                                        foreach ($variationsToSend as &$variation) {
                                            $positions[] = [
                                                'taskId' => $variation['taskId'],
                                                'productId' => $variation['productId']
                                            ];

                                            unset($variation['taskId']);
                                            unset($variation['productId']);
                                        }

                                        try {
                                            $location = $service->delete([
                                                'shop_pk' => $feedShop,
                                                'product_pk' => $productPk,
                                                'variations' => $variationsToSend
                                            ]);

                                            $feedId = $this->setFeedInformation($location);

                                            $this->setTaskFeedData($positions, $feedId);
                                        } catch (MadktingException $e) {
                                            $variations = $this->processMadktingException($e->getResponse()->body, $positions, $variations);

                                            /* Send correct products */
                                            if (!empty($variations) && count($variations) < count($positions)) {
                                                $this->processFeeds($action, [$feedShop=>[$feedType=>[$variations]]]);
                                            }
                                        } catch (\Exception $e) {
                                            $this->logger->debug($e->getMessage());
                                        }
                                    }
                                    break;
                            }
                            break;
                    }
                }
            }
        }
    }

    /**
     * Add error data
     *
     * @param int $taskId
     * @param int $productId
     * @param string $message
     * @param int $type
     */
    protected function addError($taskId, $productId, $message, $type = Product::STATUS_ERROR)
    {
        if (empty($this->errors[$taskId])) {
            $this->errors[$taskId] = [
                'productId' => $productId,
                'message' => $message,
                'type' => $type
            ];
        } else {
            $this->errors[$taskId]['message'] .= $message;
        }
    }

    /**
     * Process errors if exists
     */
    protected function processErrors()
    {
        if (!empty($this->errors)) {
            foreach ($this->errors as $taskId => $error) {

                /* Close Task */
                $this->productTaskQueueFactory->create()->load($taskId)->finishTask();

                /* Add feed info to product */
                $product = $this->madktingProductFactory->create()->load($error['productId'], 'magento_product_id')
                    ->setStatus($error['type'])
                    ->setStatusMessage(trim($error['message'], ' | '))
                    ->save();

                if ($product->getHasVariations()) {
                    if (!empty($parentId = $product->getMadktingProductId())) {
                        $variations = $this->madktingProductFactory->create()->getCollection()
                            ->addFieldToFilter('madkting_parent_id', $parentId);

                        foreach ($variations as $variation) {
                            $variation->setStatus(Product::STATUS_PARENT_ERROR)
                                ->setStatusMessage(trim($error['message'], ' | '))
                                ->save();

                            $variationTask = $this->productTaskQueueFactory->create()->getCollection()
                                ->addFieldToFilter('product_id', $variation->getMagentoProductId())
                                ->addFieldToFilter('status', ProductTaskQueue::STATUS_PROCESSING);
                            foreach ($variationTask as $task) {
                                $task->finishTask();
                            }
                        }
                    } else {
                        $variationsIds = $this->madktingProductHelper->getVariationsId($error['productId']);

                        foreach ($variationsIds as $variationId) {
                            $variation = $this->madktingProductFactory->create()->load($variationId, 'magento_product_id');

                            if (!empty($variation->getId())) {
                                $variation->setStatus(Product::STATUS_PARENT_ERROR)
                                    ->setStatusMessage(trim($error['message'], ' | '))
                                    ->save();
                            }

                            $variationTask = $this->productTaskQueueFactory->create()->getCollection()
                                ->addFieldToFilter('product_id', $variationId)
                                ->addFieldToFilter('status', ProductTaskQueue::STATUS_PROCESSING);
                            foreach ($variationTask as $task) {
                                $task->finishTask();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Set feed info
     * @param string $location
     * @return string
     */
    protected function setFeedInformation($location)
    {
        /* Get feed ID */
        preg_match('/feeds\/([\w\-]+)\/?/', $location, $match);
        $feedId = empty($match[1])?:$match[1];

        /* Save feed information */
        $feed = $this->feedFactory->create()->setData([
            'feed_id' => $feedId,
            'event' => ProcessedFeed::EVENT_PRODUCT,
            'location' => $location
        ])->save();

        return $feed->getId();
    }

    /**
     * Set task feed's data
     * @param array $positions
     * @param string $feedId
     * @param array|null $products
     */
    protected function setTaskFeedData($positions, $feedId, $products = null)
    {
        foreach ($positions as $position => $task) {

            /**
             * Add feed info to product
             *
             * @var $taskModel ProductTaskQueue
             */
            $taskModel = $this->productTaskQueueFactory->create()->load($task['taskId'])
                ->setFeedId($feedId)
                ->setFeedPosition($position)
                ->save();

            /* Set sent Madkting attributes */
            if (!empty($products)) {
                if (!empty($products[$position])) {
                    unset($products[$position]['pk']);
                    unset($products[$position]['images']);
                    unset($products[$position]['variations']);
                    $attributes = json_encode($products[$position]);
                    $taskModel->setMadktingAttributes($attributes)->save();
                }
            }
        }
    }

    /**
     * Madkting's exception
     * @param mixed $responseBody
     * @param array $positions
     * @param array $products
     * @return array
     */
    protected function processMadktingException($responseBody, $positions, $products)
    {
        foreach ($responseBody as $key => $error) {
            $errorMessage = '';
            foreach ($error as $field => $value) {
                foreach ($value as $message) {
                    if (is_object($message)) {
                        foreach ($message as $field2 => $value2) {
                            foreach ($value2 as $message2) {
                                $errorMessage .= $field2 . ' ' . $message2 . ' | ';
                            }
                        }
                    } else {
                        $errorMessage .= $field . ' ' . $message . ' | ';
                    }
                }
            }
            if (!empty($errorMessage)) {
                $this->addError($positions[$key]['taskId'], $positions[$key]['productId'], $errorMessage);
                unset($products[$key]);
            }
        }

        /* Sort products */
        usort($products, function($a,$b){
            if ($a['taskId'] == $b['taskId']) {
                return 0;
            }

            return $a['taskId'] < $b['taskId'] ? -1 : 1;
        });

        return $products;
    }

    /**
     * Validate if attributes have changes
     *
     * @param array $productData
     * @return array|bool
     */
    protected function validateChanges($productData)
    {
        /* Clean unnecessary data */
        $data = $productData;
        unset($data['productId']);
        unset($data['taskId']);
        unset($data['parentPk']);
        unset($data['pk']);
        unset($data['images']);
        unset($data['variations']);

        /* Get last attributes */
        $lastAttributes = $this->madktingProductFactory->create()->load($productData['productId'], 'magento_product_id')->getMadktingAttributes();
        $lastAttributes = json_decode($lastAttributes, true);
        $lastAttributes = is_array($lastAttributes) ? $lastAttributes : [];

        $disabledAttributesSync = $this->madktingConfig->getAttributesDisabledSynchronization();
        foreach ($lastAttributes as $code => $lastAttribute) {
            if (array_key_exists($code, $data)) {
                if ($lastAttribute == $data[$code]) {
                    unset($data[$code]);
                }
            } else {
                /* Clean attributes/values deleted */
                if (empty($this->selectiveSync)) {
                    if (!in_array($code, $disabledAttributesSync)) {
                        $data[$code] = null;
                    }
                }
            }
        }

        if (!empty($data)) {
            if (!empty($productData['productId'])) $data['productId'] = $productData['productId'];
            if (!empty($productData['taskId'])) $data['taskId'] = $productData['taskId'];
            if (!empty($productData['parentPk'])) $data['parentPk'] = $productData['parentPk'];
            if (!empty($productData['pk'])) $data['pk'] = $productData['pk'];

            return $data;
        } else {
            return false;
        }
    }
}
