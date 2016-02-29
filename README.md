Requirements:
  1. NodeJS
  2. PhantomJS

Start highcharts server:
  phantomjs highcharts-convert.js -host 0.0.0.0 -port 3003

Start highmaps server:
  phantomjs highmaps-convert.js -host 0.0.0.0 -port 3005

Render graph (base64 string will be return):
  $HCConverter->getGraphImage(HCConverter::$GRAPH_TEMPLATE_SMALL), null, null, 35, 12, 'PNG');

