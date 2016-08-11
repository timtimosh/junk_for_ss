
<script type="text/javascript"><!--//<![CDATA[
    if (typeof dataLayer !== 'undefined'){
        var ecomerce_isset = 0;
            $.each(dataLayer, function(index, dl) {
                if (typeof dl['ecommerce'] !== 'undefined'){ ecomerce_isset = 1; return;}
            });
            if (ecomerce_isset == 0) {
                //console.log('ecommerce_not isset');
                dataLayer.push({
                    'ecommerce': {
                      'dynx_pagetype': 'other',
                    }
                });
            }
    
    }
//]]>-->
</script>
