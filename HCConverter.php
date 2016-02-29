<?php

namespace Report\components;

class HCConverter {
    
    // Highcharts convert server url
    private $HCServer = null;
    
    // Highmaps convert server url
    private $HMServer = null;
    
    // Country code to country name mapping array
    private $countryCodesMap = [];
    
    // Highcharts serites array
    private $series = [];
    
    private $MIN_SERIE_LENGTH = 2;
    
    private static $GRAPH_TEMPLATE_PATH  = '';
    public  static $GRAPH_SMALL          = 'small-graph.png';
    public  static $GRAPH_TEMPLATE_SMALL = 'small-graph.gtpl';
    public  static $GRAPH_TEMPLATE_LARGE = 'large-graph.gtpl';
    public  static $GRAPH_TEMPLATE_WORLD = 'world-graph.gtpl';
    
    public function __construct($HCServer, $HMServer) {
        $this->HCServer = $HCServer;
        $this->HMServer = $HMServer;
        self::$GRAPH_TEMPLATE_PATH = rtrim(\Yii::getPathOfAlias('application.data'), '/') . '/';
        require_once self::$GRAPH_TEMPLATE_PATH . 'countries.php';
        $this->countryCodesMap = $aCountries;
    }
    
    private function getCountryCode($country) {
        $country = array_search($country, $this->countryCodesMap);
        
        return !empty($country) ? strtolower($country) : null;
    }
    
    private function getGraphTpl($tpl) {
        $filePath = self::$GRAPH_TEMPLATE_PATH . $tpl;
        
        if (file_exists($filePath) && $tplData = file_get_contents($filePath)) {
            return $tplData;
        }
        return '';
    }
    
    public function seriePush($serieData, $callback = null) {
        if (!is_null($callback) && is_callable($callback)) {
            array_walk($serieData['data'], $callback);
        }
        $this->series[] = $serieData;
    }
    
    public function seriesReset() {
        $this->series = [];
    }
    
    private function serieToJSON($tpl) {
        if ($tpl === self::$GRAPH_TEMPLATE_WORLD) {
            if (empty($this->series[0]['data'])) {
                return '[]';
            }
            $result = [];
            foreach ($this->series[0]['data'] as $statsInfo) {
                $result[] = ['value' => $statsInfo['count'], 'hc-key' => $this->getCountryCode($statsInfo['title'])];
            }
            $this->series[0]['data'] = $result;
            $serieJson = str_replace(["\"!", "!\"", "\""], ["", "", "\\\""], json_encode($this->series));
        } 
        else {
            $serieJson = str_replace("\"", "\\\"", json_encode($this->series));
        }
        return $serieJson;
    }
    
    private function makeRequestBody($tpl) {
        
        $content = str_replace(
            [
                '%SERIES_DATA%',
                '%Y_AXIS_MAX%'
            ], 
            [
                $this->serieToJSON($tpl),
                count($this->series[0]['data']) >= $this->MIN_SERIE_LENGTH ? 'undefined' : 32
            ], 
            $this->getGraphTpl($tpl)
        );
        
        return preg_replace('/[\r\n\t]/', '', $content);
    }
    
    private function getSerieUnique($serieId) {
        
        $values = [];
        if (empty($this->series[$serieId]['data'])) {
            return $values;
        }
        array_walk($this->series[$serieId]['data'], function($val) use (&$values) {
            if (isset($val[1])) {
                $values[] = $val[1];
            }
        });
        return array_unique($values);
    }
    
    public function getGraphImage($tpl) {
        // Empty graph
        if (($tpl === self::$GRAPH_TEMPLATE_SMALL) && (count($this->series[0]['data']) <= $this->MIN_SERIE_LENGTH || count($this->getSerieUnique(0)) < 2)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents(self::$GRAPH_TEMPLATE_PATH . self::$GRAPH_SMALL)); 
        }
        
        // Get endpoint URL depends on required image data
        $url = ($tpl === self::$GRAPH_TEMPLATE_WORLD) ? $this->HMServer : $this->HCServer;
        
        $body = $this->makeRequestBody($tpl);
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json",
                'content' => $body,
                'timeout' => 60
            )
        );
        $context = stream_context_create($opts);
        
        return 'data:image/png;base64,' . file_get_contents($url, false, $context);
    }
}