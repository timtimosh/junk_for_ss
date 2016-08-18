<?php
/*
 * @author mrtimosh@gmail.com
 */

header('Content-Type: text/html; charset=utf-8');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$url = 'http://weightless.com.ua';

$time_start = microtime(true);

$parser = new UrlParser($url);



$parsed_urls = $parser->start();

echo '<pre>';
print_r($parser->links);
print_r($parser->errors);
///var_dump($parser);
echo '</pre>';

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Парсил $time секунд " . count($parser->links) . " элементов \n";

class UrlParser {

    private $_iterator = 0;
    public $links = array();
    // private $_doc = [];
    private $_domain_to_skan;
    private $_site_categories = ["blog","recepies","news","blog"]; //система  сама должна определять категория это или нет. но иногда юрл-ы вида site.ru/product-name, а не site.ru/product-category/product-name
    private $_bad_links = [".jpg", ".gif", ".png", ".pdf", ".doc", ".xls",".docx",".jpeg"]; // preventing links like that http://site.ua/script/jquery.js was in sitemap, 
   // private $_site_categories = ["blog/", "category/news/", "category/recepies/"]; //categories for date_update parameter in sitemap and for priority
    public  $errors; //parsed url with errors
    private $_except;
    private $_robots;

    const PARSER = 'curl'; //or file_get_contents

    public function __construct($domain_to_skan = '') {

        if (filter_var($domain_to_skan, FILTER_VALIDATE_URL) === false) {
            throw new Exception("bad domain to scan: " . $domain_to_skan);
        }


        $this->_domain_to_skan = $domain_to_skan;

        $parsed = parse_url($domain_to_skan);
        $this->_robots = file("http://{$parsed['host']}/robots.txt");
        $this->set_sitemap_meta();
    }

    /*
     * @var string add url that not be scanned
     */

    public function add_except($url) {
        $this->_except[$url] = 1;
    }

    /**
     * run parsing
     * @return array successed parsed links
     */
    public function start() {
        $this->recursive_fetch_url($this->_domain_to_skan);
        $this->find_link_category_and_save();
        $this->set_sitemap_meta(); //set date and priority
        
        return $this;
    }

    private function recursive_fetch_url($url,$parent_url=false) {
        $this->_iterator++;
        if ($this->_iterator > 5) { return; }

        $html = $this->get_url_body($url);

        //if html not valid
        if ($html === false || !$this->is_html_valid($url, $html)) {
            return;
        }
        
  
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_clear_errors();
        
        $url_data = array();
        $url_data['link'] = $url;
        $url_data['parent_url'] = $parent_url;
        
        $this->links[$url] = $url_data;
        
        
        foreach ($doc->getElementsByTagName('a') as $element) {
            
            if (!$element->hasAttribute('href')) { continue; }
            
            $child_href = $this->url_normalize($element->getAttribute('href'),$url);
            
          //  echo $child_href.'<=become was=> '.$element->getAttribute('href').'<br>';
             
            //if element was already parsed not parse it or it is bad url 
            if (isset($this->links[$child_href]) || !$this->is_url_valid($child_href)) {continue;}
            
           
            $this->recursive_fetch_url($child_href,$url);
            
        }

        unset($doc);
    }

    private function url_normalize($url,$parent) {
        $url = strtok($url, '#');
        
        $path=str_replace("/", "", $url);
//it was link on main page
        if($path=='') { return $this->_domain_to_skan; }
        
        if (strpos($url, 'http') === false) {
            if ($url{0} != '/'){
                if(!empty($parent) and $parent{strlen($parent)}!='/'){  $url = $parent. '/' . $url; }
                else {  $url = $parent. '' . $url; }
            }
            else
                $url = $this->_domain_to_skan . '' . $url;
        }

        

        return $url;
    }

    /*
     * dissalowing to parse url contains bad characters, images for example
     * @var string $child_href
     */

    private function is_url_valid($child_href) {

        if (strpos($child_href, 'http') !== false && strpos($child_href, $this->_domain_to_skan) === false) {
            $this->errors_repository($child_href, "bad url or other domain");
        } elseif (strpos($child_href, '@') !== false) {
            $this->errors_repository($child_href, "bad url @ in url");
        } elseif (self::str_contains_one_of_array_els($child_href, $this->_bad_links)) {
            $this->errors_repository($child_href, "looks like its a file ");
        } elseif (!$this->robots_allowed($child_href, $useragent = false)) {
            $this->errors_repository($child_href, "robots not allowed this page");
        } elseif (filter_var($child_href, FILTER_VALIDATE_URL) === false) {
            $this->errors_repository($child_href, " this url is not valid");
        }


        if (!isset($this->errors[$child_href])) {
            return true;
        }

        return false;
    }
    
    static function str_contains_one_of_array_els($str, array $arr)
    {
        foreach($arr as $a) {
            if (stripos($str,$a) !== false) return true;
        }
        return false;
    }
    /*
     * @var $url str
     * @var $html str html of the page
     * @return boolean 
     */

    private function is_html_valid($url, $html) {
        $body = strtolower($html);
        if (strpos($body, 'content="noindex,') !== false) {
            $this->errors_repository($url, " noindex this url meta tags ");
        }

        if (isset($this->errors[$url])) {
            return false;
        }
        return true;
    }

    private function errors_repository($url, $message) {
        $this->errors[$url] = $message;
    }

    private function get_url_body($url) {
        if (self::PARSER == 'curl') {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_ENCODING, ""); //gzip
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
            
            $output = curl_exec($ch);
            $info = curl_getinfo($ch);

            // Check for errors and display the error message
            if ($errno = curl_errno($ch)) {
                $error_message = curl_error($ch);
                $this->errors_repository($url, 'bad curl exec: ' . $error_message);
            } elseif ($info["http_code"] != "200") {
                $this->errors_repository($url, 'bad code: ' . $info["http_code"]);
            }

            
            
            curl_close($ch);

            if (isset($this->errors[$url])) {
                return false;
            }

            return $output;
        } else {
            return file_get_contents($url);
        }
    }

    // Original PHP robots_allowed function code by www.chirp.com.au
    /*
     * checks if robots.txt allowed this url
     * @var string $url your url
     * @var string $useragent
     * @return bolean
     */
    private function robots_allowed($url, $useragent = false) {
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

        foreach ($rules as $rule) {
            // check if page is disallowed to us
            if (preg_match("/^$rule/", $parsed['path']))
                return false;
        }

        // page is not disallowed
        return true;
    }

    public function set_sitemap_meta() {
        foreach ($this->links as $key => $sitemap_link) {
            if($key==$this->_domain_to_skan){
               // $this->links['lastmod']=$date = date('Y-m-d', strtotime("-1 week")); //1 week ago
                $this->links[$key]['priority']='1';
                $this->links[$key]['changefreq']='weekly';
            }
            elseif($this->is_category($key)){
               // $this->links['lastmod']=$date = date('Y-m-d', strtotime("-1 week")); //1 week ago
                $this->links[$key]['priority']='0.8';
                $this->links[$key]['changefreq']='weekly';
            }
           
            else{
                $this->links[$key]['priority']='0.5';
                $this->links[$key]['changefreq']='monthly';
                //$this->links['lastmod']=$date = date('Y-m-d', strtotime("-1 month")); //1 week ago
            }
        }
    }

    /*
     * rules for date_update sitemap
     * @var $url
     */

    private function date_updated($url) {


        if ($url == $this->_domain_to_skan) {
            $date = date('Y-m-d', strtotime("-1 week")); //1 week ago
        } elseif ($this->is_category($url)) {
            $date = date('Y-m-d', strtotime("-1 week")); //1 week ago
        } else {
            $date = date('Y-m-d', strtotime("-1 month")); //1 m ago
        }

        return $date;
    }
    
    public function find_link_category_and_save(){
        foreach ($this->links as $url => $url_arr) {
            
            //if there is parent cat in links
            $parsed = parse_url($url);
            if(!isset($parsed['path']) or $parsed['path']=='/') continue; //maybe it the main page
            $parent=dirname($parsed['path']);
           
            if(isset($this->links[$parent])){
                $this->set_category_for_url($parent,$url);
            }
            elseif(isset($this->links[$parent.'/'])){
                $this->set_category_for_url($parent.'/',$url);
            }
            elseif($this->check_in_site_categories($parsed['path'])){
                 $this->set_category_for_url($url);
            }
        }
    }
    
    private function set_category_for_url($url,$child=false){
        $this->links[$url]['is_category']=1;
        if($child) { $this->links[$url]['childs'][]=$child; }
    }
    
    private function check_in_site_categories($path){
        $last_path_array=explode("/",$path);
        foreach ($last_path_array as $key => $value) {
            if($value!=''){
                $path=$value;
            }
        }
       
        $path=str_replace("/", "", $path);
          
        if (in_array($path, $this->_site_categories)) {
            return true;
        }
       
        return false;
    }
    
    private function is_category($path) {
        
       
    }

    /*
     * rules for priority sitemap
     * @var $url
     */

    private function priority($url) {
        
    }

}

function sitemap_generator($links_array) {

    $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo $output;
    ?>
    <?php foreach ($links_array as $i => $post) { ?>
        <url>
            <loc><?php print $post['url'] ?></loc>
            <lastmod><?php print $post['date_updated'] ?></lastmod>
        </url>
    <?php } ?>
    </urlset>
    <?php
}

