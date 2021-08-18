# HumanResourceTemplatesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**humanResourceTemplatesGet**](HumanResourceTemplatesApi.md#humanResourceTemplatesGet) | **GET** /human-resource-templates | get list
[**humanResourceTemplatesHumanResourceTemplatesIdDelete**](HumanResourceTemplatesApi.md#humanResourceTemplatesHumanResourceTemplatesIdDelete) | **DELETE** /human-resource-templates/{humanResourceTemplatesId} | delete
[**humanResourceTemplatesHumanResourceTemplatesIdGet**](HumanResourceTemplatesApi.md#humanResourceTemplatesHumanResourceTemplatesIdGet) | **GET** /human-resource-templates/{humanResourceTemplatesId} | get one
[**humanResourceTemplatesHumanResourceTemplatesIdPut**](HumanResourceTemplatesApi.md#humanResourceTemplatesHumanResourceTemplatesIdPut) | **PUT** /human-resource-templates/{humanResourceTemplatesId} | update
[**humanResourceTemplatesPost**](HumanResourceTemplatesApi.md#humanResourceTemplatesPost) | **POST** /human-resource-templates | create


<a name="humanResourceTemplatesGet"></a>
# **humanResourceTemplatesGet**
> humanResourceTemplatesGet()

get list

###### API endpoint to get the list of organization unit types A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourceTemplatesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourceTemplatesApi apiInstance = new HumanResourceTemplatesApi(defaultClient);
    try {
      apiInstance.humanResourceTemplatesGet();
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourceTemplatesApi#humanResourceTemplatesGet");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters
This endpoint does not need any parameter.

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
**200** | get list |  -  |

<a name="humanResourceTemplatesHumanResourceTemplatesIdDelete"></a>
# **humanResourceTemplatesHumanResourceTemplatesIdDelete**
> humanResourceTemplatesHumanResourceTemplatesIdDelete(humanResourceTemplatesId)

delete

###### API endpoint to delete an organization unit type  A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourceTemplatesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourceTemplatesApi apiInstance = new HumanResourceTemplatesApi(defaultClient);
    Integer humanResourceTemplatesId = 1; // Integer | 
    try {
      apiInstance.humanResourceTemplatesHumanResourceTemplatesIdDelete(humanResourceTemplatesId);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourceTemplatesApi#humanResourceTemplatesHumanResourceTemplatesIdDelete");
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
 **humanResourceTemplatesId** | **Integer**|  |

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
**200** | delete |  -  |

<a name="humanResourceTemplatesHumanResourceTemplatesIdGet"></a>
# **humanResourceTemplatesHumanResourceTemplatesIdGet**
> humanResourceTemplatesHumanResourceTemplatesIdGet(humanResourceTemplatesId)

get one

###### API endpoint to get a organization unit type  A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourceTemplatesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourceTemplatesApi apiInstance = new HumanResourceTemplatesApi(defaultClient);
    Integer humanResourceTemplatesId = 1; // Integer | 
    try {
      apiInstance.humanResourceTemplatesHumanResourceTemplatesIdGet(humanResourceTemplatesId);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourceTemplatesApi#humanResourceTemplatesHumanResourceTemplatesIdGet");
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
 **humanResourceTemplatesId** | **Integer**|  |

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
**200** | get one |  -  |

<a name="humanResourceTemplatesHumanResourceTemplatesIdPut"></a>
# **humanResourceTemplatesHumanResourceTemplatesIdPut**
> humanResourceTemplatesHumanResourceTemplatesIdPut(humanResourceTemplatesId, organizationId, titleEn, titleBn, displayOrder, isDesignation, organizationUnitTypeId, parentId, rankId, skillIds)

update

###### API endpoint to update an existing organization unit types   A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourceTemplatesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourceTemplatesApi apiInstance = new HumanResourceTemplatesApi(defaultClient);
    Integer humanResourceTemplatesId = 1; // Integer | 
    Integer organizationId = 1; // Integer | 
    String titleEn = "HRM Templete1_updated"; // String | 
    String titleBn = "এইচআরএম টেম্পলটেম এক_আপডেটেড"; // String | 
    BigDecimal displayOrder = new BigDecimal(78); // BigDecimal | 
    BigDecimal isDesignation = new BigDecimal(78); // BigDecimal | 
    BigDecimal organizationUnitTypeId = new BigDecimal(78); // BigDecimal | 
    BigDecimal parentId = new BigDecimal(78); // BigDecimal | 
    BigDecimal rankId = new BigDecimal(78); // BigDecimal | 
    BigDecimal skillIds = new BigDecimal(78); // BigDecimal | 
    try {
      apiInstance.humanResourceTemplatesHumanResourceTemplatesIdPut(humanResourceTemplatesId, organizationId, titleEn, titleBn, displayOrder, isDesignation, organizationUnitTypeId, parentId, rankId, skillIds);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourceTemplatesApi#humanResourceTemplatesHumanResourceTemplatesIdPut");
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
 **humanResourceTemplatesId** | **Integer**|  |
 **organizationId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **displayOrder** | **BigDecimal**|  |
 **isDesignation** | **BigDecimal**|  |
 **organizationUnitTypeId** | **BigDecimal**|  |
 **parentId** | **BigDecimal**|  | [optional]
 **rankId** | **BigDecimal**|  | [optional]
 **skillIds** | **BigDecimal**|  | [optional]

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
**200** |  |  -  |
**201** | update |  -  |

<a name="humanResourceTemplatesPost"></a>
# **humanResourceTemplatesPost**
> humanResourceTemplatesPost(organizationId, titleEn, titleBn, displayOrder, isDesignation, organizationUnitTypeId, parentId, rankId, skillIds)

create

###### API endpoint to create a organization unit types  A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourceTemplatesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourceTemplatesApi apiInstance = new HumanResourceTemplatesApi(defaultClient);
    Integer organizationId = 1; // Integer | 
    String titleEn = "HRM Templete1"; // String | 
    String titleBn = "এইচআরএম টেম্পলটেম এক"; // String | 
    BigDecimal displayOrder = new BigDecimal(78); // BigDecimal | 
    BigDecimal isDesignation = new BigDecimal(78); // BigDecimal | 
    BigDecimal organizationUnitTypeId = new BigDecimal(78); // BigDecimal | 
    BigDecimal parentId = new BigDecimal(78); // BigDecimal | 
    BigDecimal rankId = new BigDecimal(78); // BigDecimal | 
    BigDecimal skillIds = new BigDecimal(78); // BigDecimal | 
    try {
      apiInstance.humanResourceTemplatesPost(organizationId, titleEn, titleBn, displayOrder, isDesignation, organizationUnitTypeId, parentId, rankId, skillIds);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourceTemplatesApi#humanResourceTemplatesPost");
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
 **displayOrder** | **BigDecimal**|  |
 **isDesignation** | **BigDecimal**|  |
 **organizationUnitTypeId** | **BigDecimal**|  |
 **parentId** | **BigDecimal**|  | [optional]
 **rankId** | **BigDecimal**|  | [optional]
 **skillIds** | **BigDecimal**|  | [optional]

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
**200** |  |  -  |
**201** | create |  -  |

