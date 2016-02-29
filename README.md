###Requirements:
  1. NodeJS
  2. PhantomJS

###Start highcharts server:
```shell 
phantomjs highcharts-convert.js -host 0.0.0.0 -port 3003
  ```

###Start highmaps server:
```shell
phantomjs highmaps-convert.js -host 0.0.0.0 -port 3005
```

###Render graph (base64 string will be returned):
  
#####Render world graph example:
```php
$this->HCConverter->seriePush([
    'data' => $graphData[$tabKey],
    'joinBy' => 'hc-key',
    'name' => '',
    'type' => 'map',
    'mapData' => '!Highcharts.maps[\'custom/world\']!',
    'dataLabels' => ['enabled' => false]
]);
$HCConverter->getGraphImage(HCConverter::$GRAPH_TEMPLATE_SMALL), null, null, 35, 12, 'PNG');
```  
#####Render highchart graph example (we can pass callback function to modify input graph data array):
```php
$this->HCConverter->seriePush(
    [
        'data'      => $data['graphData'],
        'color'     => '#EF4E22',
        'lineWidth' => 3,
        'type'      => 'area'
    ], 
    function(&$el) {
        $el = [
            !empty($el[0]) ? (int)$el[0] : 0, 
            !empty($el[1]) ? (int)$el[1] : 0
        ];
    }
);
  ```

