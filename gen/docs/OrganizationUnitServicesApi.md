# OrganizationUnitServicesApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationUnitServicesGet**](OrganizationUnitServicesApi.md#organizationUnitServicesGet) | **GET** /organization-unit-services | get list
[**organizationUnitServicesOrganizationUnitServiceIdDelete**](OrganizationUnitServicesApi.md#organizationUnitServicesOrganizationUnitServiceIdDelete) | **DELETE** /organization-unit-services/{organizationUnitServiceId} | delete
[**organizationUnitServicesOrganizationUnitServiceIdGet**](OrganizationUnitServicesApi.md#organizationUnitServicesOrganizationUnitServiceIdGet) | **GET** /organization-unit-services/{organizationUnitServiceId} | get one
[**organizationUnitServicesOrganizationUnitServiceIdPut**](OrganizationUnitServicesApi.md#organizationUnitServicesOrganizationUnitServiceIdPut) | **PUT** /organization-unit-services/{organizationUnitServiceId} | update
[**organizationUnitServicesPost**](OrganizationUnitServicesApi.md#organizationUnitServicesPost) | **POST** /organization-unit-services | create


<a name="organizationUnitServicesGet"></a>
# **organizationUnitServicesGet**
> organizationUnitServicesGet()

get list

###### API endpoint to get the list of organization unit types A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitServicesApi apiInstance = new OrganizationUnitServicesApi(defaultClient);
    try {
      apiInstance.organizationUnitServicesGet();
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitServicesApi#organizationUnitServicesGet");
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

<a name="organizationUnitServicesOrganizationUnitServiceIdDelete"></a>
# **organizationUnitServicesOrganizationUnitServiceIdDelete**
> organizationUnitServicesOrganizationUnitServiceIdDelete(organizationUnitServiceId)

delete

###### API endpoint to delete an organization unit type  A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitServicesApi apiInstance = new OrganizationUnitServicesApi(defaultClient);
    Integer organizationUnitServiceId = 1; // Integer | 
    try {
      apiInstance.organizationUnitServicesOrganizationUnitServiceIdDelete(organizationUnitServiceId);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitServicesApi#organizationUnitServicesOrganizationUnitServiceIdDelete");
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
 **organizationUnitServiceId** | **Integer**|  |

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

<a name="organizationUnitServicesOrganizationUnitServiceIdGet"></a>
# **organizationUnitServicesOrganizationUnitServiceIdGet**
> organizationUnitServicesOrganizationUnitServiceIdGet(organizationUnitServiceId)

get one

###### API endpoint to get a organization unit type  A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitServicesApi apiInstance = new OrganizationUnitServicesApi(defaultClient);
    Integer organizationUnitServiceId = 1; // Integer | 
    try {
      apiInstance.organizationUnitServicesOrganizationUnitServiceIdGet(organizationUnitServiceId);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitServicesApi#organizationUnitServicesOrganizationUnitServiceIdGet");
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
 **organizationUnitServiceId** | **Integer**|  |

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

<a name="organizationUnitServicesOrganizationUnitServiceIdPut"></a>
# **organizationUnitServicesOrganizationUnitServiceIdPut**
> organizationUnitServicesOrganizationUnitServiceIdPut(organizationUnitServiceId, organizationId, organizationUnitId, serviceId0)

update

###### API endpoint to update an existing organization unit types   A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitServicesApi apiInstance = new OrganizationUnitServicesApi(defaultClient);
    Integer organizationUnitServiceId = 1; // Integer | 
    Integer organizationId = 1; // Integer | 
    Integer organizationUnitId = 1; // Integer | 
    BigDecimal serviceId0 = new BigDecimal(78); // BigDecimal | 
    try {
      apiInstance.organizationUnitServicesOrganizationUnitServiceIdPut(organizationUnitServiceId, organizationId, organizationUnitId, serviceId0);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitServicesApi#organizationUnitServicesOrganizationUnitServiceIdPut");
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
 **organizationUnitServiceId** | **Integer**|  |
 **organizationId** | **Integer**|  |
 **organizationUnitId** | **Integer**|  |
 **serviceId0** | **BigDecimal**|  |

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

<a name="organizationUnitServicesPost"></a>
# **organizationUnitServicesPost**
> organizationUnitServicesPost(organizationId, organizationUnitId, serviceId0)

create

###### API endpoint to create a organization unit types  A successful request response will show 201 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitServicesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitServicesApi apiInstance = new OrganizationUnitServicesApi(defaultClient);
    Integer organizationId = 1; // Integer | 
    Integer organizationUnitId = 1; // Integer | 
    BigDecimal serviceId0 = new BigDecimal(78); // BigDecimal | 
    try {
      apiInstance.organizationUnitServicesPost(organizationId, organizationUnitId, serviceId0);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitServicesApi#organizationUnitServicesPost");
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
 **serviceId0** | **BigDecimal**|  |

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

