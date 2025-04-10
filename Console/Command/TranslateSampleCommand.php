<?php
namespace Kkkonrad\Console29\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * An Abstract class for Indexer related commands.
 */
class TranslateSampleCommand extends Command
{
    protected $_resource;
    protected $_resolver;
    protected $_state;
    var $_source = 'en';
    var $_target = 'pl';

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Kkkonrad\Console29\Helper\Translation $resolver,
        \Magento\Framework\App\State $state
    ) {
        $this->_resource = $resource;
        $this->_resolver = $resolver;
        $this->_state = $state;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('kkkonrad:translate_sample')->setDescription('Translate magento db to polish language.');
    }

    protected function translateByMagento($table,$idfiled,$valuefield,$where = "1"){
	echo "translating ".$table."\n";
        if(is_string($valuefield))$valuefield = array($valuefield);
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tablename = $connection->getTableName($table);
        $query = "select $idfiled,".join(',',$valuefield)." from $tablename where $where";
        $result = $connection->fetchAll($query);
        foreach($result as $att){
            foreach($valuefield as $v){
                if($att[$v] != NULL && is_string($att[$v])){
                    $translated = $this->_resolver->translateByLangCode($att[$v],'pl_PL');
                    if($att[$v]!=$translated) {
                        $query = "update $tablename set $v = '".
                        addslashes($translated).
                        "' where $idfiled = '".$att[$idfiled]."'";
                        $connection->exec($query);
                    }
                }
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $this->translateByMagento('eav_attribute','attribute_id','frontend_label');
	$this->translateByMagento('eav_attribute_label','attribute_label_id','value');
        $this->translateByMagento('eav_attribute_group','attribute_group_id','attribute_group_name');
        $this->translateByMagento('eav_attribute_option_value','value_id','value');
        $this->translateByMagento('eav_attribute_set','attribute_set_id',"attribute_set_name");
        $this->translateByMagento('rating','rating_id',"rating_code");
        $this->translateByMagento('catalog_category_entity_varchar','value_id',"value","attribute_id=45");
        $this->translateByMagento('catalog_product_bundle_option_value','value_id','title');
        $this->translateByMagento('catalog_product_entity_text','value_id',"value","attribute_id=75");
        $this->translateByMagento('catalog_product_entity_varchar','value_id',"value","attribute_id=73");
        $this->translateByMagento('catalog_product_super_attribute_label','value_id',"value");
        $this->translateByMagento('cms_block',"block_id",array('title','content'));
        $this->translateByMagento('cms_page',"page_id",array('title','content_heading','content'));
        $this->translateByMagento('downloadable_link_title','title_id','title');
        $this->translateByMagento('downloadable_sample_title','title_id','title');
        $this->translateByMagento('review_detail','detail_id',array('title','detail'));
        $this->translateByMagento('sales_order_status','status','label');
        $this->translateByMagento('store','store_id','name');
        $this->translateByMagento('store_group',"group_id",'name');
        $this->translateByMagento('store_website','website_id','name');
        $this->translateByMagento('tax_class','class_id',"class_name");
        $this->translateByMagento('widget_instance','instance_id','title');
        $output->writeln('Done!');
	return 0;
    }

}
