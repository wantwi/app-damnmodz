<?php

class ApiHandler
{
    private $secreteKey;
    private $consumerKey;
    private $store;

    public function __construct()
    {
        $this->secreteKey = WC_SECRETE_KEY;
        $this->consumerKey = WC_CONSUMER_KEY;
        $this->store = WC_STORE;
    }

public function getAllOrders(){
                // WooCommerce API endpoint to get all orders
        $url = $this->store."/wp-json/wc/v3/orders?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err) {
            echo 'Error:' . curl_error($ch);
        }else{
            $orders = json_decode($response, true);
            return $orders;
        }
    }
    
public function allOrders()
{

    // Initialize variables
    $orders = [];
    $page = 1;
    $per_page = 100; // WooCommerce maximum per page

    do {
        // Build the URL for pagination and status filtering
        $requestUrl = $this->store."/wp-json/wc/v3/orders?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}&status=processing&page={$page}&per_page={$per_page}";

        // Initialize cURL request
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);

        // Handle any errors in cURL
        if ($err) {
            echo 'Error:' . curl_error($ch);
            break;
        } else {
            // Decode the response into an array
            $currentOrders = json_decode($response, true);

            // Merge the current page's orders into the overall orders array
            $orders = array_merge($orders, $currentOrders);

            // Increase the page number for the next request
            $page++;

            // Stop looping if there are no more orders returned (end of pagination)
        }
    } while (!empty($currentOrders));

    // Return all the processing orders
    return $orders;
}


public function productCategories()
{
        $allCategories = [];
        $page = 1;
        $perPage = 100; // You can set a higher limit per request (WooCommerce allows up to 100 per request)
    
        do {
            // WooCommerce API endpoint with pagination and per_page parameters
            $url = $this->store . "/wp-json/wc/v3/products/categories?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}&per_page=$perPage&page=$page";
    
            // Initialize cURL request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            // Execute request
            $response = curl_exec($ch);
            $err = curl_errno($ch);
            curl_close($ch);
    
            if ($err) {
                echo 'Error:' . curl_error($ch);
                return [];
            } else {
                $categories = json_decode($response, true);
    
                // Merge categories with the allCategories array
                $allCategories = array_merge($allCategories, $categories);
    
                // Increase the page number for the next request
                $page++;
            }
        } while (count($categories) === $perPage); // Continue if we get exactly $perPage results (indicating there may be more pages)
    
        return $allCategories;
}
        
public function product($id)
{
    
        // WooCommerce API endpoint to get all orders
        $url = $this->store."/wp-json/wc/v3/products/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err) {
            echo 'Error:' . curl_error($ch);
        }else{
            $orders = json_decode($response, true);
            return $orders;
        }
        
}
        
public function updateOrder($id, $newStatus)
{
        // WooCommerce API endpoint for updating an order
        $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
    
        // Data to update the order status
        $data = [
            'status' => $newStatus
        ];
    
        // Initialize cURL
        $ch = curl_init($url);
    
        // Set cURL options for PUT request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Body of the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
    
        // Execute the request and get the response
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
    
        // Handle errors or return response
        if ($err) {
            echo 'Error:' . curl_error($ch);
        } else {
            $updatedOrder = json_decode($response, true);
            return $updatedOrder;
        }
}

public function getOrder($id)
{
        // WooCommerce API endpoint for updating an order
        $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err) {
            echo 'Error:' . curl_error($ch);
        }else{
            $orders = json_decode($response, true);
            return $orders;
        }
}



public function updateOrderTrustPilot($id, $bcc)
{
        // WooCommerce API endpoint for retrieving the order
        $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
        
        // Initialize cURL to fetch the order
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
    
        // Handle errors or decode the response
        if ($err) {
            echo 'Error fetching order: ' . curl_error($ch);
            return;
        } else {
            $orderData = json_decode($response, true);
            
            // If the order exists and has meta_data
            if (isset($orderData['meta_data'])) {
                // Loop through meta_data to find 'bcc_value'
                foreach ($orderData['meta_data'] as &$meta) {
                    if ($meta['key'] === 'bcc_value') {
                        $meta['value'] = $bcc;  // Update the value
                        break;
                    }
                }
            } else {
                echo "Order doesn't contain meta_data.";
                return;
            }
        }
        
        // Now send the updated data back to WooCommerce
        $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
    
        // Prepare the updated order data with modified meta_data
        $data = [
            'meta_data' => $orderData['meta_data']
        ];
        
        // Initialize cURL for PUT request to update the order
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Body of the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        
        // Execute the request and get the response
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
    
        // Handle errors or return response
        if ($err) {
            echo 'Error updating order: ' . curl_error($ch);
        } else {
            $updatedOrder = json_decode($response, true);
            return $updatedOrder;
        }
    }
    
    
public function getSectionId($id, $product_id)
{
        // WooCommerce API endpoint for updating an order
        $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err) {
            echo 'Error:' . curl_error($ch);
        }else{
            $orderData = json_decode($response, true);
            
            // If the order exists and has meta_data
            if (isset($orderData['line_items'])) {
                // Loop through meta_data to find 'bcc_value'
                foreach ($orderData['line_items'] as $product) {
                    if ($product['product_id'] === $product_id) {
                        if (!empty($product['meta_data']) && is_array($product['meta_data'])) {
                            foreach ($product['meta_data'] as $index => $product_meta_data) {
                                // Skip the first element (meta_data[0])
                                if ($index === 0) {
                                    foreach ($product_meta_data['value'] as $index => $value){
                                        // Loop through the fields to find "email" or "text" type
                                        return $value['extra']['section_id'];
                                    }
                                    
                                }
                            }
                        }
                    }
                }
            } else {
                echo "Order doesn't contain meta_data.";
                return;
            }
        }
}
    
    
public function updateMetaData($id, $product_id, $section_id)
{
    // WooCommerce API endpoint for retrieving the order
    $url = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";

    // Initialize cURL to fetch the order
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $err = curl_errno($ch);
    curl_close($ch);

    // Handle errors or decode the response
    if ($err) {
        echo 'Error fetching order: ' . curl_error($ch);
        return;
    } else {
        $orderData = json_decode($response, true);

        // If the order exists and has line_items
        if (isset($orderData['line_items'])) {
            // Loop through line_items to find the product
            foreach ($orderData['line_items'] as &$product) {
                
                // Empty values
                if ($product['product_id'] === $product_id) {
                    if (!empty($product['meta_data']) && is_array($product['meta_data'])) {
                        foreach ($product['meta_data'] as &$product_meta_data) {

                            if (in_array($product_meta_data['key'], ['Playstation Email', 'Playstation Password', 'Xbox Email' , 'Xbox Password', 'Social Club Email', 'Social Club Password'])) {
                                // Empty the value
                                $product_meta_data['value'] = '';
                            }                            
                            // Check if meta_data contains 'fields'
                           if (isset($product_meta_data['value'][$section_id])) {
                                foreach($product_meta_data['display_value'][$section_id]['fields'] as &$sections){
                                    foreach ($sections as &$field) {
                                        // Check if type is 'email' or 'text'
                                        if (in_array($field['type'], ['email', 'text'])) {
                                            // Empty the value
                                            $field['value'] = '';
                                        }
                                    }
                                }
                                 $product_meta_data['display_value'] = json_encode($product_meta_data['display_value']);  // Convert to string
                                foreach($product_meta_data['value'][$section_id]['fields'] as &$sections){
                                    foreach ($sections as &$field) {
                                        // Check if type is 'email' or 'text'
                                        // Check if type is 'email' or 'text'
                                        if (in_array($field['type'], ['email', 'text'])) {
                                            // Empty the value
                                            $field['value'] = '';
                                        }
                                    }
                                }                                
                            }
                        }
                    }
                }
                
                if (!empty($product['meta_data']) && is_array($product['meta_data'])) {
                        foreach ($product['meta_data'] as &$product_meta_data) {
                            $product_meta_data['display_value'] = json_encode($product_meta_data['display_value']);
                        }
                }
                
            // Ensure 'parent_name' is a string or remove it
            foreach ($orderData['line_items'] as &$lineItem) {
                if (isset($lineItem['parent_name'])) {
                    if (!is_string($lineItem['parent_name'])) {
                        $lineItem['parent_name'] = (string) $lineItem['parent_name'];  // Ensure it's a string
                    }
                } else {
                    unset($lineItem['parent_name']);  // Remove if not needed
                }
            }
            }
            
        } else {
            echo "Order doesn't contain meta_data.";
            return;
        }
    }

    // Prepare the updated data to send to WooCommerce
    $updatedOrderData = [
        'line_items' => $orderData['line_items'],  // Updated line_items with empty values
    ];
    
    ////return $updatedOrderData;

    // WooCommerce API endpoint for updating the order
    $updateUrl = $this->store . "/wp-json/wc/v3/orders/$id?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";

    // Initialize cURL to update the order
    $ch = curl_init($updateUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatedOrderData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Execute the request
    $updateResponse = curl_exec($ch);
    $updateErr = curl_errno($ch);
    curl_close($ch);

    // Handle the response of the update
    if ($updateErr) {
        echo 'Error updating order: ' . curl_error($ch);
    } else {
        //echo $updateResponse;
        return true;
    }
}


public function generateCoupon($code, $percentage, $expireDate, $product_id)
{
    // WooCommerce API endpoint for creating a coupon
    $url = $this->store . "/wp-json/wc/v3/coupons?consumer_key={$this->consumerKey}&consumer_secret={$this->secreteKey}";
    
    // Data for coupon creation
    $data = [
        "code" => $code,
        "discount_type" => "percent",
        "amount" => $percentage,
        "date_expires" => $expireDate,
        "product_ids" => [$product_id],
        "usage_limit" => 1,
        "individual_use" => true,
        "exclude_sale_items" => false,
    ];
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options for POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ]);
    
    // Execute the request and get the response
    $response = curl_exec($ch);
    $err = curl_errno($ch);
    $errorMessage = $err ? curl_error($ch) : null;
    curl_close($ch);
    
    // Handle errors or return response
    if ($err) {
        return 'Error:' . $errorMessage;
    }
    
    $coupon = json_decode($response, true);
    
    // Check for API-level errors in the response
    if (isset($coupon['code']) && $coupon['code'] === 'rest_invalid_param') {
        return 'Error: ' . $coupon['message'];
    }
    
    return $coupon;
}


}
