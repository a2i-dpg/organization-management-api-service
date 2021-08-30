# OrganizationApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationsGet**](OrganizationApi.md#organizationsGet) | **GET** /organizations | get list
[**organizationsOrganizationIdDelete**](OrganizationApi.md#organizationsOrganizationIdDelete) | **DELETE** /organizations/{organizationId} | delete
[**organizationsOrganizationIdGet**](OrganizationApi.md#organizationsOrganizationIdGet) | **GET** /organizations/{organizationId} | get one
[**organizationsOrganizationIdPut**](OrganizationApi.md#organizationsOrganizationIdPut) | **PUT** /organizations/{organizationId} | update
[**organizationsPost**](OrganizationApi.md#organizationsPost) | **POST** /organizations | create


<a name="organizationsGet"></a>
# **organizationsGet**
> Organization organizationsGet(page, limit, rowStatus, order, titleEn, titleBn)

get list

API endpoint to get the list of organizations.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationApi apiInstance = new OrganizationApi(defaultClient);
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    Integer rowStatus = 1; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Organization result = apiInstance.organizationsGet(page, limit, rowStatus, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationsGet");
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

[**Organization**](Organization.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get list |  -  |

<a name="organizationsOrganizationIdDelete"></a>
# **organizationsOrganizationIdDelete**
> Organization organizationsOrganizationIdDelete(organizationId)

delete

 API endpoint to delete the specified organization. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationApi apiInstance = new OrganizationApi(defaultClient);
    Integer organizationId = 2; // Integer | 
    try {
      Organization result = apiInstance.organizationsOrganizationIdDelete(organizationId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationsOrganizationIdDelete");
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

### Return type

[**Organization**](Organization.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="organizationsOrganizationIdGet"></a>
# **organizationsOrganizationIdGet**
> Organization organizationsOrganizationIdGet(organizationId)

get one

API endpoint to get the specified organization.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationApi apiInstance = new OrganizationApi(defaultClient);
    Integer organizationId = 2; // Integer | 
    try {
      Organization result = apiInstance.organizationsOrganizationIdGet(organizationId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationsOrganizationIdGet");
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

### Return type

[**Organization**](Organization.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="organizationsOrganizationIdPut"></a>
# **organizationsOrganizationIdPut**
> Organization organizationsOrganizationIdPut(organizationId, organizationTypeId, titleEn, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description, logo)

update

API endpoint to get the specified organization.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationApi apiInstance = new OrganizationApi(defaultClient);
    Integer organizationId = 2; // Integer | 
    Integer organizationTypeId = 1; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    String mobile = "01711223344"; // String | 
    String email = "company_name@gmail.com"; // String | 
    String contactPersonName = "Mr. X"; // String | 
    String contactPersonMobile = "01789277788"; // String | 
    String contactPersonEmail = "mr.x@gmail.com"; // String | 
    String contactPersonDesignation = "HR"; // String | 
    String domain = "https://www.companyname.com"; // String | 
    Integer locDivisionId = 1; // Integer | 
    Integer locDistrictId = 1; // Integer | 
    Integer locUpazilaId = 1; // Integer | 
    String address = "Dhaka-1208"; // String | 
    String faxNo = "+123456"; // String | 
    String description = "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit"; // String | 
    String logo = "logo.jpg"; // String | 
    try {
      Organization result = apiInstance.organizationsOrganizationIdPut(organizationId, organizationTypeId, titleEn, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description, logo);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationsOrganizationIdPut");
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
 **organizationTypeId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **mobile** | **String**|  |
 **email** | **String**|  |
 **contactPersonName** | **String**|  |
 **contactPersonMobile** | **String**|  |
 **contactPersonEmail** | **String**|  |
 **contactPersonDesignation** | **String**|  |
 **domain** | **String**|  |
 **locDivisionId** | **Integer**|  | [optional]
 **locDistrictId** | **Integer**|  | [optional]
 **locUpazilaId** | **Integer**|  | [optional]
 **address** | **String**|  | [optional]
 **faxNo** | **String**|  | [optional]
 **description** | **String**|  | [optional]
 **logo** | **String**|  | [optional]

### Return type

[**Organization**](Organization.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

<a name="organizationsPost"></a>
# **organizationsPost**
> Organization organizationsPost(titleEn, organizationTypeId, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, logo, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description)

create

API endpoint to create a organization.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationApi apiInstance = new OrganizationApi(defaultClient);
    String titleEn = "abc"; // String | 
    Integer organizationTypeId = 1; // Integer | 
    String titleBn = "abc"; // String | 
    String mobile = "01711223344"; // String | 
    String email = "company_name@gmail.com"; // String | 
    String contactPersonName = "Mr. X"; // String | 
    String contactPersonMobile = "01789277788"; // String | 
    String contactPersonEmail = "mr.x@gmail.com"; // String | 
    String contactPersonDesignation = "HR"; // String | 
    String logo = "logo.jpg"; // String | 
    String domain = "https://www.companyname.com"; // String | 
    Integer locDivisionId = 1; // Integer | 
    Integer locDistrictId = 1; // Integer | 
    Integer locUpazilaId = 1; // Integer | 
    String address = "Dhaka-1208"; // String | 
    String faxNo = "+123456"; // String | 
    String description = "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit"; // String | 
    try {
      Organization result = apiInstance.organizationsPost(titleEn, organizationTypeId, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, logo, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationsPost");
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
 **organizationTypeId** | **Integer**|  |
 **titleBn** | **String**|  |
 **mobile** | **String**|  |
 **email** | **String**|  |
 **contactPersonName** | **String**|  |
 **contactPersonMobile** | **String**|  |
 **contactPersonEmail** | **String**|  |
 **contactPersonDesignation** | **String**|  |
 **logo** | **String**|  |
 **domain** | **String**|  |
 **locDivisionId** | **Integer**|  | [optional]
 **locDistrictId** | **Integer**|  | [optional]
 **locUpazilaId** | **Integer**|  | [optional]
 **address** | **String**|  | [optional]
 **faxNo** | **String**|  | [optional]
 **description** | **String**|  | [optional]

### Return type

[**Organization**](Organization.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

