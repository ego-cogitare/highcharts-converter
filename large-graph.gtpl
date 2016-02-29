{
    "infile":"{
        \"series\": %SERIES_DATA%,
        \"chart\": {
            \"type\": \"spline\",
            \"width\": 800,
            \"height\": 300
        },
        \"credits\": {
            \"text\": \"\"
        },
        \"title\": {
            \"text\": \"\"
        },
        \"xAxis\": {
            \"lineWidth\": 1,
            \"type\": \"category\",
            \"gridLineWidth\": 1,
            \"tickLength\": 1,
            \"tickWidth\": 1,
            \"tickPosition\": \"outside\"
        }, 
        \"yAxis\": {
            \"min\": 0,
            \"max\": %Y_AXIS_MAX%,
            \"title\": {
                \"text\": \"\"
            }
        },
        \"plotOptions\": {
            \"series\": {
                \"marker\": {
                    \"enabled\": false
                }
            },
            \"spline\": {
                \"marker\": {
                    \"enabled\": true
                }
            }
        },
        \"legend\": {
            \"enabled\": true
        }           
    }"
}