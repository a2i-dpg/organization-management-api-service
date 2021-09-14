# HumanResourcesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**humanResourcesGet**](HumanResourcesApi.md#humanResourcesGet) | **GET** /human-resources | get list
[**humanResourcesHumanResourceIdDelete**](HumanResourcesApi.md#humanResourcesHumanResourceIdDelete) | **DELETE** /human-resources/{humanResourceId} | delete
[**humanResourcesHumanResourceIdGet**](HumanResourcesApi.md#humanResourcesHumanResourceIdGet) | **GET** /human-resources/{humanResourceId} | get one
[**humanResourcesHumanResourceIdPut**](HumanResourcesApi.md#humanResourcesHumanResourceIdPut) | **PUT** /human-resources/{humanResourceId} | update
[**humanResourcesPost**](HumanResourcesApi.md#humanResourcesPost) | **POST** /human-resources | create


<a name="humanResourcesGet"></a>
# **humanResourcesGet**
> HumanResource humanResourcesGet(page, limit, order, rowStatus, titleEn, titleBn)

get list

 API endpoint to get the list of Human Resources.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourcesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourcesApi apiInstance = new HumanResourcesApi(defaultClient);
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    String order = "order_example"; // String | 
    Integer rowStatus = 1; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      HumanResource result = apiInstance.humanResourcesGet(page, limit, order, rowStatus, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourcesApi#humanResourcesGet");
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

[**HumanResource**](HumanResource.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get list |  -  |

<a name="humanResourcesHumanResourceIdDelete"></a>
# **humanResourcesHumanResourceIdDelete**
> HumanResource humanResourcesHumanResourceIdDelete(humanResourceId)

delete

 API endpoint to delete the specified human Resource.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourcesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourcesApi apiInstance = new HumanResourcesApi(defaultClient);
    Integer humanResourceId = 2; // Integer | 
    try {
      HumanResource result = apiInstance.humanResourcesHumanResourceIdDelete(humanResourceId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourcesApi#humanResourcesHumanResourceIdDelete");
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
 **humanResourceId** | **Integer**|  |

### Return type

[**HumanResource**](HumanResource.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="humanResourcesHumanResourceIdGet"></a>
# **humanResourcesHumanResourceIdGet**
> HumanResource humanResourcesHumanResourceIdGet(humanResourceId)

get one

API endpoint to get a humanResource .A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourcesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourcesApi apiInstance = new HumanResourcesApi(defaultClient);
    Integer humanResourceId = 2; // Integer | 
    try {
      HumanResource result = apiInstance.humanResourcesHumanResourceIdGet(humanResourceId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourcesApi#humanResourcesHumanResourceIdGet");
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
 **humanResourceId** | **Integer**|  |

### Return type

[**HumanResource**](HumanResource.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="humanResourcesHumanResourceIdPut"></a>
# **humanResourcesHumanResourceIdPut**
> humanResourcesHumanResourceIdPut(humanResourceId, organizationId, organizationUnitId, titleEn, titleBn, isDesignation, humanResourceTemplateId, parentId, rankId, displayOrder, status, rowStatus)

update

API endpoint to update an existing organization unit types.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourcesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourcesApi apiInstance = new HumanResourcesApi(defaultClient);
    Integer humanResourceId = 2; // Integer | 
    Integer organizationId = 2; // Integer | 
    Integer organizationUnitId = 3; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer isDesignation = 1; // Integer | 
    Integer humanResourceTemplateId = 1; // Integer | 
    Integer parentId = 1; // Integer | 
    Integer rankId = 1; // Integer | 
    Integer displayOrder = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      apiInstance.humanResourcesHumanResourceIdPut(humanResourceId, organizationId, organizationUnitId, titleEn, titleBn, isDesignation, humanResourceTemplateId, parentId, rankId, displayOrder, status, rowStatus);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourcesApi#humanResourcesHumanResourceIdPut");
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
 **humanResourceId** | **Integer**|  |
 **organizationId** | **Integer**|  |
 **organizationUnitId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **isDesignation** | **Integer**|  |
 **humanResourceTemplateId** | **Integer**|  | [optional]
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **status** | **Integer**|  | [optional]
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
**201** | update |  -  |

<a name="humanResourcesPost"></a>
# **humanResourcesPost**
> HumanResource humanResourcesPost(organizationId, organizationUnitId, titleEn, titleBn, isDesignation, humanResourceTemplateId, parentId, rankId, displayOrder, status, rowStatus)

create

API endpoint to create a Human Resource.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.HumanResourcesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    HumanResourcesApi apiInstance = new HumanResourcesApi(defaultClient);
    Integer organizationId = 2; // Integer | 
    Integer organizationUnitId = 3; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer isDesignation = 1; // Integer | 
    Integer humanResourceTemplateId = 1; // Integer | 
    Integer parentId = 1; // Integer | 
    Integer rankId = 1; // Integer | 
    Integer displayOrder = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      HumanResource result = apiInstance.humanResourcesPost(organizationId, organizationUnitId, titleEn, titleBn, isDesignation, humanResourceTemplateId, parentId, rankId, displayOrder, status, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling HumanResourcesApi#humanResourcesPost");
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
 **organizationUnitId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **isDesignation** | **Integer**|  |
 **humanResourceTemplateId** | **Integer**|  | [optional]
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **status** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**HumanResource**](HumanResource.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | create |  -  |

