<?php
/**
 * Unified order class
 */

class Unified{

	const UNIFIED = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    protected $values = [];

    public function __construct($values){
    	$this->values = $values;
    	$this->setSign();
        $this->arrToXml();
    }

    protected function arrToXml(){
    	if($this->values && is_array($this->values)){
    		$xml ='<xml>';
    		foreach ($this->values as $key => $value) {
    			if (is_numeric($value)){
	    			$xml.="<".$key.">".$value."</".$key.">";
	    		}else{
	    			$xml.="<".$key."><![CDATA[".$value."]]></".$key.">";
	    		}
    		}
    		$xml .='</xml>';
            file_put_contents('./xml.txt', $xml);
            return $xml;
    	}
        return false;
    }

    protected function setSign(){
    	ksort($this->values);
    	$this->values['sign'] = strtoupper(md5((http_build_query($this->values).'&key='.Config::KEY)));
    	//return $sign;
    } 
}
