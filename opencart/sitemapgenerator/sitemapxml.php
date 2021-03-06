<?php

class ControllerInformationSitemapxml extends Controller {

    public $sitemap_elements;
    public $domain_to_skan;
    private $_robots;
    private $_exluded_urls = ["http://www.motherworld.com.ua/", ""];
    private $_langprefix='';
    
    public function __construct($registry) {
       

        parent::__construct($registry);
         header('Content-Type: text/html; charset=utf-8');
    }

    public function index() {

        //$this->start($this->config->get('config_url'));

        $this->load->model('localisation/language');

        $data['languages'] = array();

        $results = $this->model_localisation_language->getLanguages();
        $default_language=$this->request->default_lang;   
        foreach ($results as $result) {
           if($result['code']==$default_language){
                $this->_langprefix='';   
           }
           else{            
               $this->_langprefix=$result['code'];
           }
           
           $this->start($this->config->get('config_url').$this->_langprefix);
            
        }
        $this->sitemap_generator($this->sitemap_elements);
          echo '<pre>';
          echo 'элементов '.count($this->sitemap_elements).'<br>';
        print_r($this->sitemap_elements);
        echo '</pre>';
        die("xml generator finish its work!");
    }

    public function start($domain_to_skan) {
        $domain_to_skan = rtrim($domain_to_skan, '/');

        $this->domain_to_skan = $domain_to_skan;
        $parsed = parse_url($domain_to_skan);
        $this->_robots = file("http://{$parsed['host']}/robots.txt");
       
        $this->set_homepage_property();

        $this->find_category_in_db();
        $this->find_products_in_db();
        $this->find_posts_in_db();
        //$this->find_posts_in_db();
      
        //return $this;
    }

    private function set_homepage_property() {
        $url = $this->domain_to_skan;
        $data['priority'] = '1';
        $data['changefreq'] = 'weekly';
        $data['type'] = 'main category';
        $this->set_sitemap_property($url, $data);
    }

    private function find_products_in_db() {
        $this->load->model('catalog/product');
        $filter_data = array(
            'filter_sub_category' => true,
        );

       
        $results = $this->model_catalog_product->getProducts($filter_data);

        foreach ($results as $result) {
            $data = array();
            $url = $this->url->link('product/product', 'product_id=' . $result['product_id']);
            $data['priority'] = '0.5';
            $data['changefreq'] = 'monthly';
            $data['type'] = 'product';
            $this->set_sitemap_property($url, $data);
        }
    }

    private function find_category_in_db() {

        $this->load->model('catalog/category');

        $categories = $this->model_catalog_category->getAllCategories();

        foreach ($categories as $key => $category) {
            $data = array();
            $url = $this->url->link('product/category', 'path=' . $category['category_id']);
            $data['priority'] = '0.8';
            $data['changefreq'] = 'weekly';
            $data['type'] = 'category';
            $this->set_sitemap_property($url, $data);
        }
    }

    private function find_posts_in_db() {

        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            //var_dump($result);
            $url = $this->url->link('information/information', 'information_id=' . $result['information_id']);
            $data['priority'] = '0.5';
            $data['name'] = $result['title'];
            $data['changefreq'] = 'monthly';
            $data['type'] = 'post';
            $this->set_sitemap_property($url, $data);
        }
    }

    private function set_sitemap_property($url, $data) {
        
        if($this->_langprefix!=''){
            $url = str_replace($this->config->get('config_url'), $this->domain_to_skan.'/', $url);
        }
        
      
        if ($this->is_excluded_url($url)) {
            return;
        }
        if (!$this->does_robots_allowed($url)) {
            return;
        }
        
       
        if (isset($this->sitemap_elements[$url]) || isset($this->sitemap_elements[$url . '/'])) {
            return;
        }
      
        $data['url'] = $url;

        $data['priority'] = $data['priority'];
        $data['changefreq'] = $data['changefreq'];
        $data['type'] = $data['type'];
        $this->sitemap_elements[$url] = $data;
    }

    private function is_excluded_url($url) {
        if (in_array($url, $this->_exluded_urls)) {
            return false;
        }
        return false;
    }

    // Original PHP robots_allowed function code by www.chirp.com.au
    /*
     * checks if robots.txt allowed this url
     * @var string $url your url
     * @var string $useragent
     * @return bolean
     */
    private function does_robots_allowed($url, $useragent = false) {
        // parse url to retrieve host and path
        $parsed = parse_url($url);

        $agents = array(preg_quote('*'));
        if ($useragent)
            $agents[] = preg_quote($useragent);
        $agents = implode('|', $agents);

        // location of robots.txt file
        $robotstxt = $this->_robots;

        // if there isn't a robots, then we're allowed in
        if (empty($robotstxt))
            return true;

        $rules = array();
        $ruleApplies = false;
        foreach ($robotstxt as $line) {
            // skip blank lines
            if (!$line = trim($line))
                continue;

            // following rules only apply if User-agent matches $useragent or '*'
            if (preg_match('/^\s*User-agent: (.*)/i', $line, $match)) {
                $ruleApplies = preg_match("/($agents)/i", $match[1]);
            }
            if ($ruleApplies && preg_match('/^\s*Disallow:(.*)/i', $line, $regs)) {
                // an empty rule implies full access - no further tests required
                if (!$regs[1])
                    return true;
                // add rules that apply to array for testing
                $rules[] = preg_quote(trim($regs[1]), '/');
            }
        }
        if (!empty($parsed['path'])) {
            foreach ($rules as $rule) {
                // check if page is disallowed to us
                if (preg_match("/^$rule/", $parsed['path']))
                    return false;
            }
        }


        // page is not disallowed
        return true;
    }

    function sitemap_generator($links_array) {

        ob_start();
        $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        echo $output;
        ?>
        <?php foreach ($links_array as $i => $post) { ?>
            <url>
                <loc><?php print $post['url'] ?></loc>
                <priority><?php print $post['priority'] ?></priority>
                <changefreq><?php print $post['changefreq'] ?></changefreq>
            </url>
        <?php } ?>
        </urlset>
        <?php
        $page = ob_get_contents();
        ob_end_clean();

        $path = DIR_OPENCART;
        $file = fopen($path . "/sitemap.xml", "w");
        if (!$file) {
            throw new Exception('File open failed.');
        }

        fwrite($file, $page);
        fclose($file);
    }

}

