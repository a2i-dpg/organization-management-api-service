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
> HumanResourceTemplate humanResourceTemplatesGet(page, limit, order, rowStatus, titleEn, titleBn)

get list

API endpoint to get the list of Human Resource templates.A successful request response will show 200 HTTP status code

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
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    String order = "order_example"; // String | 
    Integer rowStatus = 1; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      HumanResourceTemplate result = apiInstance.humanResourceTemplatesGet(page, limit, order, rowStatus, titleEn, titleBn);
      System.out.println(result);
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

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **Integer**|  | [optional]
 **limit** | **Integer**|  | [optional]
 **order** | **String**|  | [optional] [enum: asc, desc]
 **rowStatus** | **Integer**|  | [optional]
 **titleEn** | **String**|  | [optional]
 **titleBn** | **String**|  | [optional]

### Return type

[**HumanResourceTemplate**](HumanResourceTemplate.md)

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
> HumanResourceTemplate humanResourceTemplatesHumanResourceTemplatesIdDelete(humanResourceTemplatesId)

delete

 API endpoint to delete a Human Resource Template.A successful request response will show 200 HTTP status code

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
      HumanResourceTemplate result = apiInstance.humanResourceTemplatesHumanResourceTemplatesIdDelete(humanResourceTemplatesId);
      System.out.println(result);
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

[**HumanResourceTemplate**](HumanResourceTemplate.md)

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
> HumanResourceTemplate humanResourceTemplatesHumanResourceTemplatesIdGet(humanResourceTemplatesId)

get one

API endpoint to get a HumanResourceTemplate .A successful request response will show 200 HTTP status code

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
      HumanResourceTemplate result = apiInstance.humanResourceTemplatesHumanResourceTemplatesIdGet(humanResourceTemplatesId);
      System.out.println(result);
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

[**HumanResourceTemplate**](HumanResourceTemplate.md)

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
> HumanResourceTemplate humanResourceTemplatesHumanResourceTemplatesIdPut(humanResourceTemplatesId, organizationId, titleEn, titleBn, isDesignation, organizationUnitTypeId, parentId, rankId, displayOrder, status, rowStatus)

update

API endpoint to update an existing human resource template .A successful request response will show 201 HTTP status code

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
    Integer organizationId = 2; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer isDesignation = 1; // Integer | 
    Integer organizationUnitTypeId = 1; // Integer | 
    Integer parentId = 1; // Integer | 
    Integer rankId = 1; // Integer | 
    Integer displayOrder = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      HumanResourceTemplate result = apiInstance.humanResourceTemplatesHumanResourceTemplatesIdPut(humanResourceTemplatesId, organizationId, titleEn, titleBn, isDesignation, organizationUnitTypeId, parentId, rankId, displayOrder, status, rowStatus);
      System.out.println(result);
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
 **isDesignation** | **Integer**|  |
 **organizationUnitTypeId** | **Integer**|  |
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **status** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**HumanResourceTemplate**](HumanResourceTemplate.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | update |  -  |

<a name="humanResourceTemplatesPost"></a>
# **humanResourceTemplatesPost**
> HumanResourceTemplate humanResourceTemplatesPost(organizationId, titleEn, titleBn, isDesignation, organizationUnitTypeId, parentId, rankId, displayOrder, status, rowStatus)

create

 API endpoint to create a Human Resource template.A successful request response will show 201 HTTP status code

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
    Integer organizationId = 2; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer isDesignation = 1; // Integer | 
    Integer organizationUnitTypeId = 1; // Integer | 
    Integer parentId = 1; // Integer | 
    Integer rankId = 1; // Integer | 
    Integer displayOrder = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      HumanResourceTemplate result = apiInstance.humanResourceTemplatesPost(organizationId, titleEn, titleBn, isDesignation, organizationUnitTypeId, parentId, rankId, displayOrder, status, rowStatus);
      System.out.println(result);
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
 **isDesignation** | **Integer**|  |
 **organizationUnitTypeId** | **Integer**|  |
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **status** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**HumanResourceTemplate**](HumanResourceTemplate.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | create |  -  |

