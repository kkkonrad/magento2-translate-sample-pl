<?php
namespace Kkkonrad\Console29\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Statickidz\GoogleTranslate;

/**
 * An Abstract class for Indexer related commands.
 */
class SendPhrasesCommand extends Command
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Kkkonrad\Console29\Helper\Translation $resolver,
        \Kkkonrad\Translate29\Model\PhraseFactory $phraseFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->_resource = $resource;
        $this->_resolver = $resolver;
        $this->_state = $state;
        $this->_phraseFactory = $phraseFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('kkkonrad:send_phrases')->setDescription('Send phrases do translae29 module.');
    }

    protected function sendPhrases($table,$idfiled,$valuefield,$where = "1"){
        if(is_string($valuefield))$valuefield = array($valuefield);
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tablename = $connection->getTableName($table);
        $query = "select $idfiled,".join(',',$valuefield)." from $tablename where $where";
        $result = $connection->fetchAll($query);
        foreach($result as $att){
            foreach($valuefield as $v){
                if($att[$v] != NULL && is_string($att[$v])){
                    if($this->_phraseFactory->create()->getCollection()->addFieldToFilter('en_US',$att[$v])->count()==0){
                        $p = $this->_phraseFactory->create();
                        $p->setData("en_US",$att[$v]);
                        $p->setData("type",'module');
                        $p->setData("module",'Magento_Sample');
                        $p->save();
                    }
                }
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sendPhrases('eav_attribute','attribute_id','frontend_label');
        $this->sendPhrases('eav_attribute_group','attribute_group_id','attribute_group_name');
        $this->sendPhrases('eav_attribute_option_value','value_id','value');
        $this->sendPhrases('eav_attribute_set','attribute_set_id',"attribute_set_name");
        $this->sendPhrases('rating','rating_id',"rating_code");
        $this->sendPhrases('catalog_category_entity_varchar','value_id',"value","attribute_id=45");
        $this->sendPhrases('catalog_product_bundle_option_value','value_id','title');
        $this->sendPhrases('catalog_product_entity_text','value_id',"value","attribute_id=75");
        $this->sendPhrases('catalog_product_entity_varchar','value_id',"value","attribute_id=73");
        $this->sendPhrases('catalog_product_super_attribute_label','value_id',"value");
        $this->sendPhrases('cms_block',"block_id",array('title','content'));
        $this->sendPhrases('cms_page',"page_id",array('title','content_heading','content'));
        $this->sendPhrases('downloadable_link_title','title_id','title');
        $this->sendPhrases('downloadable_sample_title','title_id','title');
        $this->sendPhrases('review_detail','detail_id',array('title','detail'));
        $this->sendPhrases('sales_order_status','status','label');
        $this->sendPhrases('store','store_id','name');
        $this->sendPhrases('store_group',"group_id",'name');
        $this->sendPhrases('store_website','website_id','name');
        $this->sendPhrases('tax_class','class_id',"class_name");
        $this->sendPhrases('widget_instance','instance_id','title');
        $output->writeln('Done!');
    }

}
