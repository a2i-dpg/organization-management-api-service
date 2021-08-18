# OrganizationApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationGet**](OrganizationApi.md#organizationGet) | **GET** /organization | get list
[**organizationPost**](OrganizationApi.md#organizationPost) | **POST** /organization | create
[**organizationsOrganizationIdDelete**](OrganizationApi.md#organizationsOrganizationIdDelete) | **DELETE** /organizations/{organizationId} | delete
[**organizationsOrganizationIdGet**](OrganizationApi.md#organizationsOrganizationIdGet) | **GET** /organizations/{organizationId} | get one
[**organizationsOrganizationIdPut**](OrganizationApi.md#organizationsOrganizationIdPut) | **PUT** /organizations/{organizationId} | update


<a name="organizationGet"></a>
# **organizationGet**
> Organization organizationGet(page, order, titleEn, titleBn)

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
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Organization result = apiInstance.organizationGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationGet");
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

<a name="organizationPost"></a>
# **organizationPost**
> Organization organizationPost(organizationTypeId, titleEn, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, logo, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description)

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
    Integer organizationTypeId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    String mobile = "mobile_example"; // String | 
    String email = "email_example"; // String | 
    String contactPersonName = "contactPersonName_example"; // String | 
    String contactPersonMobile = "contactPersonMobile_example"; // String | 
    String contactPersonEmail = "contactPersonEmail_example"; // String | 
    String contactPersonDesignation = "contactPersonDesignation_example"; // String | 
    String logo = "logo_example"; // String | 
    String domain = "domain_example"; // String | 
    Integer locDivisionId = 56; // Integer | 
    Integer locDistrictId = 56; // Integer | 
    Integer locUpazilaId = 56; // Integer | 
    String address = "address_example"; // String | 
    String faxNo = "faxNo_example"; // String | 
    String description = "description_example"; // String | 
    try {
      Organization result = apiInstance.organizationPost(organizationTypeId, titleEn, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, logo, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationApi#organizationPost");
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

<a name="organizationsOrganizationIdDelete"></a>
# **organizationsOrganizationIdDelete**
> Organization organizationsOrganizationIdDelete(organizationId)

delete

 API endpoint to delete a specified organization  A successful request response will show 200 HTTP status code

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
    Integer organizationId = 56; // Integer | 
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
> organizationsOrganizationIdGet(organizationId)

get one

API endpoint to get a specified organization.A successful request response will show 200 HTTP status code

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
    Integer organizationId = 56; // Integer | 
    try {
      apiInstance.organizationsOrganizationIdGet(organizationId);
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

<a name="organizationsOrganizationIdPut"></a>
# **organizationsOrganizationIdPut**
> Organization organizationsOrganizationIdPut(organizationId, organizationTypeId, titleEn, titleBn, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, domain, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, description, logo)

update

###### API endpoint to get a specified organization  A successful request response will show 200 HTTP status code

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
    Integer organizationId = 56; // Integer | 
    Integer organizationTypeId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    String mobile = "mobile_example"; // String | 
    String email = "email_example"; // String | 
    String contactPersonName = "contactPersonName_example"; // String | 
    String contactPersonMobile = "contactPersonMobile_example"; // String | 
    String contactPersonEmail = "contactPersonEmail_example"; // String | 
    String contactPersonDesignation = "contactPersonDesignation_example"; // String | 
    String domain = "domain_example"; // String | 
    Integer locDivisionId = 56; // Integer | 
    Integer locDistrictId = 56; // Integer | 
    Integer locUpazilaId = 56; // Integer | 
    String address = "address_example"; // String | 
    String faxNo = "faxNo_example"; // String | 
    String description = "description_example"; // String | 
    String logo = "logo_example"; // String | 
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

