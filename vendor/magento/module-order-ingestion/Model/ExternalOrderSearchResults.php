<?php

namespace Magento\OrderIngestion\Model;

use Magento\Framework\Api\SearchResults;
use Magento\OrderIngestion\Api\Data\ExternalOrderSearchResultsInterface;

class ExternalOrderSearchResults extends SearchResults implements ExternalOrderSearchResultsInterface
{
}
