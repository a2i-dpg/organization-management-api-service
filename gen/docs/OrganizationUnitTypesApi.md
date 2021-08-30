# OrganizationUnitTypesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationUnitTypesGet**](OrganizationUnitTypesApi.md#organizationUnitTypesGet) | **GET** /organization-unit-types | get list
[**organizationUnitTypesOrganizationUnitTypeIdDelete**](OrganizationUnitTypesApi.md#organizationUnitTypesOrganizationUnitTypeIdDelete) | **DELETE** /organization-unit-types/{organizationUnitTypeId} | delete
[**organizationUnitTypesOrganizationUnitTypeIdGet**](OrganizationUnitTypesApi.md#organizationUnitTypesOrganizationUnitTypeIdGet) | **GET** /organization-unit-types/{organizationUnitTypeId} | get one
[**organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet**](OrganizationUnitTypesApi.md#organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet) | **GET** /organization-unit-types/{organizationUnitTypeId}/get-hierarchy | get-hierarchy
[**organizationUnitTypesOrganizationUnitTypeIdPut**](OrganizationUnitTypesApi.md#organizationUnitTypesOrganizationUnitTypeIdPut) | **PUT** /organization-unit-types/{organizationUnitTypeId} | update
[**organizationUnitTypesPost**](OrganizationUnitTypesApi.md#organizationUnitTypesPost) | **POST** /organization-unit-types | create


<a name="organizationUnitTypesGet"></a>
# **organizationUnitTypesGet**
> OrganizationUnitType organizationUnitTypesGet(page, limit, rowStatus, order, titleEn, titleBn)

get list

API endpoint to get the list of organization unit types.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    Integer rowStatus = 1; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      OrganizationUnitType result = apiInstance.organizationUnitTypesGet(page, limit, rowStatus, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesGet");
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
 **rowStatus** | **Integer**|  | [optional]
 **order** | **String**|  | [optional] [enum: asc, desc]
 **titleEn** | **String**|  | [optional]
 **titleBn** | **String**|  | [optional]

### Return type

[**OrganizationUnitType**](OrganizationUnitType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get list |  -  |

<a name="organizationUnitTypesOrganizationUnitTypeIdDelete"></a>
# **organizationUnitTypesOrganizationUnitTypeIdDelete**
> OrganizationType organizationUnitTypesOrganizationUnitTypeIdDelete(organizationUnitTypeId)

delete

API endpoint to delete an organization unit type.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    Integer organizationUnitTypeId = 1; // Integer | 
    try {
      OrganizationType result = apiInstance.organizationUnitTypesOrganizationUnitTypeIdDelete(organizationUnitTypeId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesOrganizationUnitTypeIdDelete");
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
 **organizationUnitTypeId** | **Integer**|  |

### Return type

[**OrganizationType**](OrganizationType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="organizationUnitTypesOrganizationUnitTypeIdGet"></a>
# **organizationUnitTypesOrganizationUnitTypeIdGet**
> OrganizationUnitType organizationUnitTypesOrganizationUnitTypeIdGet(organizationUnitTypeId)

get one

 API endpoint to get a organization unit type. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    Integer organizationUnitTypeId = 1; // Integer | 
    try {
      OrganizationUnitType result = apiInstance.organizationUnitTypesOrganizationUnitTypeIdGet(organizationUnitTypeId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesOrganizationUnitTypeIdGet");
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
 **organizationUnitTypeId** | **Integer**|  |

### Return type

[**OrganizationUnitType**](OrganizationUnitType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet"></a>
# **organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet**
> OrganizationUnitType organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet(organizationUnitTypeId)

get-hierarchy

API endpoint to get organization unit type based hierarchy e.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    Integer organizationUnitTypeId = 1; // Integer | 
    try {
      OrganizationUnitType result = apiInstance.organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet(organizationUnitTypeId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesOrganizationUnitTypeIdGetHierarchyGet");
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
 **organizationUnitTypeId** | **Integer**|  |

### Return type

[**OrganizationUnitType**](OrganizationUnitType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get-hierarchy |  -  |

<a name="organizationUnitTypesOrganizationUnitTypeIdPut"></a>
# **organizationUnitTypesOrganizationUnitTypeIdPut**
> OrganizationUnitTypeId organizationUnitTypesOrganizationUnitTypeIdPut(organizationUnitTypeId, titleEn, titleBn, organizationId, rowStatus)

update

 API endpoint to update an existing organization unit types.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    Integer organizationUnitTypeId = 1; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer organizationId = 2; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnitTypeId result = apiInstance.organizationUnitTypesOrganizationUnitTypeIdPut(organizationUnitTypeId, titleEn, titleBn, organizationId, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesOrganizationUnitTypeIdPut");
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
 **organizationUnitTypeId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **organizationId** | **Integer**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**OrganizationUnitTypeId**](OrganizationUnitTypeId.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | update |  -  |

<a name="organizationUnitTypesPost"></a>
# **organizationUnitTypesPost**
> OrganizationUnitType organizationUnitTypesPost(titleEn, titleBn, organizationId, rowStatus)

create

API endpoint to create a organization unit types.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitTypesApi apiInstance = new OrganizationUnitTypesApi(defaultClient);
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer organizationId = 2; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnitType result = apiInstance.organizationUnitTypesPost(titleEn, titleBn, organizationId, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitTypesApi#organizationUnitTypesPost");
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
 **organizationId** | **Integer**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**OrganizationUnitType**](OrganizationUnitType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | create |  -  |

