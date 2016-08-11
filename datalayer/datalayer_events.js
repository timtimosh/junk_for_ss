 google_ecomerce = {
                add_product: function(prod_id,qty){
                    this.get_product_info(prod_id,qty,this.send_add_product_query_to_ecomerce);
                },
                remove_product: function(prod_id,qty){
                    this.get_product_info(prod_id,qty,this.send_remove_product_query_to_ecomerce);
                },
                
                get_product_info: function(prod_id,qty,callback){
                    $.ajax({
                        type: "GET",
                        url: '/product_to_ajax/'+prod_id,
                        dataType: 'json',
                        success: function(ObjectInfo){
                            ObjectInfo['quantity']=qty;
                            callback(ObjectInfo);
                        },
                        error: function(){
                            console.log("cant get product with id "+prod_id);
                        }
                     });  
                },
                send_remove_product_query_to_ecomerce: function(ObjectInfo){
                    // Measure the removal of a product from a shopping cart.
                    dataLayer.push({
                      'event': 'removeFromCart',
                      'ecommerce': {
                        'remove': {                               // 'remove' actionFieldObject measures.
                          'products': [{                          //  removing a product to a shopping cart.
                              'name': ObjectInfo['full_name'],
                              'id': ObjectInfo['id'],
                              'price': ObjectInfo['price'],
                              'brand': ObjectInfo['brand'],
                              'category': ObjectInfo['category'],
                              'quantity': ObjectInfo['quantity']
                          }]
                        }
                      }
                    });

                },
                send_add_product_query_to_ecomerce: function(ObjectInfo){
                    // Measure adding a product to a shopping cart by using an 'add' actionFieldObject
                    // and a list of productFieldObjects.
                    dataLayer.push({
                      'event': 'addToCart',
                      'ecommerce': {
                        'currencyCode': 'UAH',
                        'add': {                                // 'add' actionFieldObject measures.
                          'products': [{                        //  adding a product to a shopping cart.
                              'name': ObjectInfo['full_name'],
                              'id': ObjectInfo['id'],
                              'price': ObjectInfo['price'],
                              'brand': ObjectInfo['brand'],
                              'category': ObjectInfo['category'],
                              'quantity': ObjectInfo['quantity']
                           }]
                        }
                      }
                    });
                }, 
                
                save_old_qty: function(){
                    //for google ecomerce only 
                        //first values on init
                        $( "#cart_form .input-qty-box input" ).each(function( index ) {
                              $(this).data('val', $(this).val());
                        })
                }
        }
