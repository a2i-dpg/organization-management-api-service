# RankTypesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**delete**](RankTypesApi.md#delete) | **DELETE** /rank-types/{rankTypeId} | delete
[**rankTypesGet**](RankTypesApi.md#rankTypesGet) | **GET** /rank-types | get_list
[**rankTypesPost**](RankTypesApi.md#rankTypesPost) | **POST** /rank-types | create
[**rankTypesRankTypeIdGet**](RankTypesApi.md#rankTypesRankTypeIdGet) | **GET** /rank-types/{rankTypeId} | get_one
[**rankTypesRankTypeIdPut**](RankTypesApi.md#rankTypesRankTypeIdPut) | **PUT** /rank-types/{rankTypeId} | update


<a name="delete"></a>
# **delete**
> delete(rankTypeId)

delete

 API endpoint to get the list of rank types  A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankTypesApi apiInstance = new RankTypesApi(defaultClient);
    Integer rankTypeId = 56; // Integer | 
    try {
      apiInstance.delete(rankTypeId);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankTypesApi#delete");
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
 **rankTypeId** | **Integer**|  |

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
**200** | get_list |  -  |

<a name="rankTypesGet"></a>
# **rankTypesGet**
> RankType rankTypesGet(page, order, titleEn, titleBn)

get_list

API endpoint to get the list of rank types.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankTypesApi apiInstance = new RankTypesApi(defaultClient);
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      RankType result = apiInstance.rankTypesGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankTypesApi#rankTypesGet");
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

[**RankType**](RankType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="rankTypesPost"></a>
# **rankTypesPost**
> RankType rankTypesPost(titleEn, titleBn, organizationId, description, rowStatus)

create

API endpoint to get the list of rank types.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankTypesApi apiInstance = new RankTypesApi(defaultClient);
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer organizationId = 56; // Integer | 
    String description = "description_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      RankType result = apiInstance.rankTypesPost(titleEn, titleBn, organizationId, description, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankTypesApi#rankTypesPost");
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
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **organizationId** | **Integer**|  | [optional]
 **description** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**RankType**](RankType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | create |  -  |

<a name="rankTypesRankTypeIdGet"></a>
# **rankTypesRankTypeIdGet**
> RankType rankTypesRankTypeIdGet(rankTypeId)

get_one

API endpoint to get a specified rank type.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankTypesApi apiInstance = new RankTypesApi(defaultClient);
    Integer rankTypeId = 56; // Integer | 
    try {
      RankType result = apiInstance.rankTypesRankTypeIdGet(rankTypeId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankTypesApi#rankTypesRankTypeIdGet");
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
 **rankTypeId** | **Integer**|  |

### Return type

[**RankType**](RankType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

<a name="rankTypesRankTypeIdPut"></a>
# **rankTypesRankTypeIdPut**
> RankType rankTypesRankTypeIdPut(titleEn, titleBn, organizationId, description, rowStatus)

update

API endpoint to get a specified rank type.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankTypesApi apiInstance = new RankTypesApi(defaultClient);
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer organizationId = 56; // Integer | 
    String description = "description_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      RankType result = apiInstance.rankTypesRankTypeIdPut(titleEn, titleBn, organizationId, description, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankTypesApi#rankTypesRankTypeIdPut");
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
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **organizationId** | **Integer**|  | [optional]
 **description** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**RankType**](RankType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

