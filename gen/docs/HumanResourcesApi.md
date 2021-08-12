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
> humanResourcesGet()

get list

###### API endpoint to get the list of organization unit types A successful request response will show 200 HTTP status code

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
    try {
      apiInstance.humanResourcesGet();
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

<a name="humanResourcesHumanResourceIdDelete"></a>
# **humanResourcesHumanResourceIdDelete**
> humanResourcesHumanResourceIdDelete(humanResourceId)

delete

###### API endpoint to delete an organization unit type  A successful request response will show 200 HTTP status code

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
    Integer humanResourceId = 1; // Integer | 
    try {
      apiInstance.humanResourcesHumanResourceIdDelete(humanResourceId);
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

<a name="humanResourcesHumanResourceIdGet"></a>
# **humanResourcesHumanResourceIdGet**
> humanResourcesHumanResourceIdGet(humanResourceId)

get one

###### API endpoint to get a organization unit type  A successful request response will show 200 HTTP status code

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
    Integer humanResourceId = 1; // Integer | 
    try {
      apiInstance.humanResourcesHumanResourceIdGet(humanResourceId);
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

<a name="humanResourcesHumanResourceIdPut"></a>
# **humanResourcesHumanResourceIdPut**
> humanResourcesHumanResourceIdPut(humanResourceId, organizationId, organizationUnitId, titleEn, titleBn, displayOrder, isDesignation, status, humanResourceTemplateId, parentId, rankId)

update

###### API endpoint to update an existing organization unit types   A successful request response will show 201 HTTP status code

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
    Integer humanResourceId = 1; // Integer | 
    Integer organizationId = 1; // Integer | 
    Integer organizationUnitId = 1; // Integer | 
    String titleEn = "HRM_updated"; // String | 
    String titleBn = "আপডেটেড"; // String | 
    Integer displayOrder = 1; // Integer | 
    Integer isDesignation = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer humanResourceTemplateId = 1; // Integer | 
    Integer parentId = 56; // Integer | 
    Integer rankId = 56; // Integer | 
    try {
      apiInstance.humanResourcesHumanResourceIdPut(humanResourceId, organizationId, organizationUnitId, titleEn, titleBn, displayOrder, isDesignation, status, humanResourceTemplateId, parentId, rankId);
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
 **displayOrder** | **Integer**|  |
 **isDesignation** | **Integer**|  |
 **status** | **Integer**|  |
 **humanResourceTemplateId** | **Integer**|  | [optional]
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]

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

<a name="humanResourcesPost"></a>
# **humanResourcesPost**
> humanResourcesPost(organizationId, organizationUnitId, titleEn, titleBn, displayOrder, isDesignation, status, humanResourceTemplateId, parentId, rankId)

create

###### API endpoint to create a organization unit types  A successful request response will show 201 HTTP status code

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
    Integer organizationId = 1; // Integer | 
    Integer organizationUnitId = 1; // Integer | 
    String titleEn = "HRM"; // String | 
    String titleBn = "এইচআরএম"; // String | 
    Integer displayOrder = 1; // Integer | 
    Integer isDesignation = 1; // Integer | 
    Integer status = 1; // Integer | 
    Integer humanResourceTemplateId = 1; // Integer | 
    Integer parentId = 56; // Integer | 
    Integer rankId = 56; // Integer | 
    try {
      apiInstance.humanResourcesPost(organizationId, organizationUnitId, titleEn, titleBn, displayOrder, isDesignation, status, humanResourceTemplateId, parentId, rankId);
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
 **displayOrder** | **Integer**|  |
 **isDesignation** | **Integer**|  |
 **status** | **Integer**|  |
 **humanResourceTemplateId** | **Integer**|  | [optional]
 **parentId** | **Integer**|  | [optional]
 **rankId** | **Integer**|  | [optional]

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

