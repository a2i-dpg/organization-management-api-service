# ServicesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**servicesGet**](ServicesApi.md#servicesGet) | **GET** /services | get list
[**servicesPost**](ServicesApi.md#servicesPost) | **POST** /services | create
[**servicesServiceIdDelete**](ServicesApi.md#servicesServiceIdDelete) | **DELETE** /services/{serviceId} | delete
[**servicesServiceIdGet**](ServicesApi.md#servicesServiceIdGet) | **GET** /services/{serviceId} | get one
[**servicesServiceIdPut**](ServicesApi.md#servicesServiceIdPut) | **PUT** /services/{serviceId} | update


<a name="servicesGet"></a>
# **servicesGet**
> Service servicesGet(page, order, titleEn, titleBn)

get list

API endpoint to get the list of services.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.ServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    ServicesApi apiInstance = new ServicesApi(defaultClient);
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Service result = apiInstance.servicesGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling ServicesApi#servicesGet");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **Integer**|  | [optional]
 **order** | **String**|  | [optional] [enum: asc, desc]
 **titleEn** | **String**|  | [optional]
 **titleBn** | **String**|  | [optional]

### Return type

[**Service**](Service.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get list |  -  |

<a name="servicesPost"></a>
# **servicesPost**
> servicesPost(organizationId, titleEn, titleBn, rowStatus)

create

API endpoint to create a service.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.ServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    ServicesApi apiInstance = new ServicesApi(defaultClient);
    Integer organizationId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      apiInstance.servicesPost(organizationId, titleEn, titleBn, rowStatus);
    } catch (ApiException e) {
      System.err.println("Exception when calling ServicesApi#servicesPost");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **organizationId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

null (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

<a name="servicesServiceIdDelete"></a>
# **servicesServiceIdDelete**
> Service servicesServiceIdDelete(serviceId)

delete

API endpoint to get a specified service.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.ServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    ServicesApi apiInstance = new ServicesApi(defaultClient);
    Integer serviceId = 56; // Integer | 
    try {
      Service result = apiInstance.servicesServiceIdDelete(serviceId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling ServicesApi#servicesServiceIdDelete");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceId** | **Integer**|  |

### Return type

[**Service**](Service.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="servicesServiceIdGet"></a>
# **servicesServiceIdGet**
> Service servicesServiceIdGet(serviceId)

get one

API endpoint to get a specified service.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.ServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    ServicesApi apiInstance = new ServicesApi(defaultClient);
    Integer serviceId = 56; // Integer | 
    try {
      Service result = apiInstance.servicesServiceIdGet(serviceId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling ServicesApi#servicesServiceIdGet");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceId** | **Integer**|  |

### Return type

[**Service**](Service.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="servicesServiceIdPut"></a>
# **servicesServiceIdPut**
> Service servicesServiceIdPut(serviceId, organizationId, titleEn, titleBn, rowStatus)

update

###### API endpoint to update the specified service.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.ServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    ServicesApi apiInstance = new ServicesApi(defaultClient);
    Integer serviceId = 56; // Integer | 
    Integer organizationId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      Service result = apiInstance.servicesServiceIdPut(serviceId, organizationId, titleEn, titleBn, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling ServicesApi#servicesServiceIdPut");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceId** | **Integer**|  |
 **organizationId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**Service**](Service.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

