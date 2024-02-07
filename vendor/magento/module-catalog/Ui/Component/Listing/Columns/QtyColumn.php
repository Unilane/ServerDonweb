 
namespace Magento\Catalog\Ui\Component\Listing\Columns;
 
use Magento\Ui\Component\Listing\Columns\Column;
 
class QtyColumn extends Column
{
    const NAME = 'column.qty';
 
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = (int)$item[$fieldName];
                }
            }
        }
        return $dataSource;
    }
}