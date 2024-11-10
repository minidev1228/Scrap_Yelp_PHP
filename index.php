<?php
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "GET"){
        require("ui.php");
    }
    else{
        include_once('simple_html_dom.php');
        function getOnlyBody($str) {
            $ind = strripos($str, "<body");
            $str = substr($str, $ind);
            while(1){
                $startInd = strripos($str,"<script");
                if($startInd==false) break;
                $str1 = substr($str, 0, $startInd);
                $endInd = strripos($str, "</script>");
                $endInd += 9;
                $str2 = substr($str, $endInd, strlen($str));
                $str = $str1.$str2;
            }
            return $str;
        }
        $q = $_REQUEST["q"];
        // // $adre = $html->find("'div.y-css-31u4uo div.y-css-cxcdjj p.y-css-dg8xxd'", 0);
        $final = array();
        for($i = 0;$i<1000;$i+=10){
            $shURL = "https://www.yelp.com/search?find_desc=Grocery&find_loc=".$q."%2C&start=".$i;
            $ch = curl_init();
            $apiKey = "bb28cb2cef97b6257074ede19411df4fbed1010d";
            curl_setopt($ch, CURLOPT_URL, $shURL);
            curl_setopt($ch, CURLOPT_PROXY, 'http://'.$apiKey.':premium_proxy=true&proxy_country=us@proxy.zenrows.com:8001');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $htmlContent = curl_exec($ch);
            curl_close($ch);
            $htmlContent = getOnlyBody($htmlContent);
            if(strripos($htmlContent, "We're sorry")) break;
            // var_dump($shURL);

            // var_dump($htmlContent);
            $html = str_get_html($htmlContent);
            // $aTags = $html->find('li.y-css-1iy1dw');
            if($html==null){
                continue;
            }
            $mainContent = $html->find('main#main-content',0);
            if($mainContent==null){
                $i = $i - 10;
                $html->clear();
                continue;
            }
            $ul = $mainContent->find('ul.list__09f24__ynIEd', 0);
            if($ul==null){
                $html->clear();
                continue;
            }
            $lists =$ul->find('li.y-css-1iy1dwt');
            if($lists == null){
                $html->clear();
                continue;
            }
            
            for ($id = 0;$id<sizeof($lists);$id++) {
                // if($id>=26) break;
                // if($id<6) continue;
                $list = $lists[$id];
                $a = $list->find('a.y-css-1t5uorm',0);
                if($a == null) continue;
                $url = "https://www.yelp.com".$a->href;
                if(strripos($url, "&amp;")) continue;
                // var_dump($url);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_PROXY, 'http://'.$apiKey.':premium_proxy=true&proxy_country=us@proxy.zenrows.com:8001');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $htmlContent = curl_exec($ch);
                curl_close($ch);
                $htmlContent = getOnlyBody($htmlContent);
                // var_dump($htmlContent);
                // break;
                $html = str_get_html($htmlContent);
                if($html == null){
                    // var_dump("1");
                    continue;
                }
                $aside = $html->find('aside', 0);
                if($aside == null){
                    // var_dump($htmlContent);
                    // var_dump("2");
                    $id = $id - 1;
                    $html->clear();
                    continue;
                }
                $last = $aside->find('div.y-css-cxcdjj');
                if(!isset($last) || !isset($last[0]) || !isset($last[1])){
                    // var_dump("3");
                    $html->clear();
                    continue;
                }
                if($last[0]==null || $last[1]==null){
                    // var_dump("4");
                    $html->clear();
                    continue;
                }
                $h = $html->find('h1',0);
                $ad = $aside->find('p.y-css-dg8xxd', 0);
                $wb = $last[0]->find('a', 0);
                $pp = $last[0]->find('p')[1];
                $pn = $last[1]->find('p')[1];
                $adre = "*";
                if($ad!=null) $adre = $ad->plaintext;
                $nameOfBusiness = "*";
                if($h!=null) $nameOfBusiness = $h->plaintext;
                $website = "*";
                if($wb != null) $website = $wb->plaintext;
                $phoneNumber = "*";
                if($pn != null) $phoneNumber = $pn->plaintext;
                if(!strripos($phoneNumber, "-")) $phoneNumber="*";
                if($website == "*" && $pp!=null){
                    $phoneNumber = $pp->plaintext;
                    if(!strripos($phoneNumber, "-")) $phoneNumber="*";
                }
                $rlt = array($nameOfBusiness, $website, $phoneNumber, $adre );
                array_push($GLOBALS['final'], $rlt);
                $html->clear();
                // break;
            }
            $html->clear();
        }
        $comp="";
        foreach ($final as $key => $value) {
            if($GLOBALS["comp"]==$value[0]){
                array_splice($GLOBALS['final'], $key, 1);
                continue;
            }
            $GLOBALS["comp"] = $value[0];
        }
        echo json_encode($final);
    }
?>