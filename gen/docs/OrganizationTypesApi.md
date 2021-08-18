# OrganizationTypesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationTypesGet**](OrganizationTypesApi.md#organizationTypesGet) | **GET** /organization-types | get_list
[**organizationTypesOrganizationTypeIdDelete**](OrganizationTypesApi.md#organizationTypesOrganizationTypeIdDelete) | **DELETE** /organization-types/{organizationTypeId} | delete
[**organizationTypesOrganizationTypeIdGet**](OrganizationTypesApi.md#organizationTypesOrganizationTypeIdGet) | **GET** /organization-types/{organizationTypeId} | get_one
[**organizationTypesOrganizationTypeIdPut**](OrganizationTypesApi.md#organizationTypesOrganizationTypeIdPut) | **PUT** /organization-types/{organizationTypeId} | update
[**organizationTypesPost**](OrganizationTypesApi.md#organizationTypesPost) | **POST** /organization-types | create


<a name="organizationTypesGet"></a>
# **organizationTypesGet**
> OrganizationType organizationTypesGet(page, order, titleEn, titleBn)

get_list

 API endpoint to get the list organization types.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationTypesApi apiInstance = new OrganizationTypesApi(defaultClient);
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      OrganizationType result = apiInstance.organizationTypesGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationTypesApi#organizationTypesGet");
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

[**OrganizationType**](OrganizationType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="organizationTypesOrganizationTypeIdDelete"></a>
# **organizationTypesOrganizationTypeIdDelete**
> Object organizationTypesOrganizationTypeIdDelete(organizationTypeId)

delete

API endpoint to delete an organization type.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationTypesApi apiInstance = new OrganizationTypesApi(defaultClient);
    Integer organizationTypeId = 56; // Integer | 
    try {
      Object result = apiInstance.organizationTypesOrganizationTypeIdDelete(organizationTypeId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationTypesApi#organizationTypesOrganizationTypeIdDelete");
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
 **organizationTypeId** | **Integer**|  |

### Return type

**Object**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="organizationTypesOrganizationTypeIdGet"></a>
# **organizationTypesOrganizationTypeIdGet**
> organizationTypesOrganizationTypeIdGet(organizationTypeId)

get_one

 API endpoint to get an organization type by organization id.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationTypesApi apiInstance = new OrganizationTypesApi(defaultClient);
    Integer organizationTypeId = 56; // Integer | 
    try {
      apiInstance.organizationTypesOrganizationTypeIdGet(organizationTypeId);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationTypesApi#organizationTypesOrganizationTypeIdGet");
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
 **organizationTypeId** | **Integer**|  |

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
**200** | get_one |  -  |

<a name="organizationTypesOrganizationTypeIdPut"></a>
# **organizationTypesOrganizationTypeIdPut**
> organizationTypesOrganizationTypeIdPut(organizationTypeId, titleEn, titleBn, isGovernment, rowStatus)

update

API endpoint to update an organization type.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationTypesApi apiInstance = new OrganizationTypesApi(defaultClient);
    Integer organizationTypeId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Boolean isGovernment = true; // Boolean | 
    Integer rowStatus = 56; // Integer | 
    try {
      apiInstance.organizationTypesOrganizationTypeIdPut(organizationTypeId, titleEn, titleBn, isGovernment, rowStatus);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationTypesApi#organizationTypesOrganizationTypeIdPut");
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
 **organizationTypeId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **isGovernment** | **Boolean**|  |
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
**200** |  |  -  |
**201** | update |  -  |

<a name="organizationTypesPost"></a>
# **organizationTypesPost**
> OrganizationType organizationTypesPost(titleEn, titleBn, isGovernment, rowStatus)

create

API endpoint to create an organization type.A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationTypesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationTypesApi apiInstance = new OrganizationTypesApi(defaultClient);
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Boolean isGovernment = true; // Boolean | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationType result = apiInstance.organizationTypesPost(titleEn, titleBn, isGovernment, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationTypesApi#organizationTypesPost");
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
 **isGovernment** | **Boolean**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

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
**201** | create |  -  |

