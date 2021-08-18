# OrganizationUnitsApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationUnitsGet**](OrganizationUnitsApi.md#organizationUnitsGet) | **GET** /organization-units | get list
[**organizationUnitsOrganizationUnitIdDelete**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdDelete) | **DELETE** /organization-units/{organizationUnitId} | delete
[**organizationUnitsOrganizationUnitIdGet**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdGet) | **GET** /organization-units/{organizationUnitId} | get one
[**organizationUnitsOrganizationUnitIdPut**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdPut) | **PUT** /organization-units/{organizationUnitId} | get one
[**organizationUnitsPost**](OrganizationUnitsApi.md#organizationUnitsPost) | **POST** /organization-units | create


<a name="organizationUnitsGet"></a>
# **organizationUnitsGet**
> OrganizationUnit organizationUnitsGet(page, order, titleEn, titleBn)

get list

API endpoint to get the list of organization Units. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitsApi apiInstance = new OrganizationUnitsApi(defaultClient);
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsGet");
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

[**OrganizationUnit**](OrganizationUnit.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get list |  -  |

<a name="organizationUnitsOrganizationUnitIdDelete"></a>
# **organizationUnitsOrganizationUnitIdDelete**
> OrganizationUnitId organizationUnitsOrganizationUnitIdDelete(organizationUnitId)

delete

API endpoint to delete a specified Organization  Unit.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitsApi apiInstance = new OrganizationUnitsApi(defaultClient);
    Integer organizationUnitId = 56; // Integer | 
    try {
      OrganizationUnitId result = apiInstance.organizationUnitsOrganizationUnitIdDelete(organizationUnitId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsOrganizationUnitIdDelete");
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
 **organizationUnitId** | **Integer**|  |

### Return type

[**OrganizationUnitId**](OrganizationUnitId.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="organizationUnitsOrganizationUnitIdGet"></a>
# **organizationUnitsOrganizationUnitIdGet**
> OrganizationUnit organizationUnitsOrganizationUnitIdGet(organizationUnitId)

get one

 API endpoint to get a specified Organization  Unit.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitsApi apiInstance = new OrganizationUnitsApi(defaultClient);
    Integer organizationUnitId = 56; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsOrganizationUnitIdGet(organizationUnitId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsOrganizationUnitIdGet");
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
 **organizationUnitId** | **Integer**|  |

### Return type

[**OrganizationUnit**](OrganizationUnit.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="organizationUnitsOrganizationUnitIdPut"></a>
# **organizationUnitsOrganizationUnitIdPut**
> OrganizationUnit organizationUnitsOrganizationUnitIdPut(organizationUnitId, titleEn, titleBn, organizationId, organizationTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus)

get one

API endpoint to update a specified Organization  Unit. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitsApi apiInstance = new OrganizationUnitsApi(defaultClient);
    Integer organizationUnitId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer organizationId = 56; // Integer | 
    Integer organizationTypeId = 56; // Integer | 
    String mobile = "mobile_example"; // String | 
    String email = "email_example"; // String | 
    String contactPersonName = "contactPersonName_example"; // String | 
    String contactPersonMobile = "contactPersonMobile_example"; // String | 
    String contactPersonEmail = "contactPersonEmail_example"; // String | 
    String contactPersonDesignation = "contactPersonDesignation_example"; // String | 
    Integer employeeSize = 56; // Integer | 
    Integer locDivisionId = 56; // Integer | 
    Integer locDistrictId = 56; // Integer | 
    Integer locUpazilaId = 56; // Integer | 
    String address = "address_example"; // String | 
    String faxNo = "faxNo_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsOrganizationUnitIdPut(organizationUnitId, titleEn, titleBn, organizationId, organizationTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsOrganizationUnitIdPut");
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
 **organizationUnitId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **organizationId** | **Integer**|  |
 **organizationTypeId** | **Integer**|  |
 **mobile** | **String**|  |
 **email** | **String**|  |
 **contactPersonName** | **String**|  |
 **contactPersonMobile** | **String**|  |
 **contactPersonEmail** | **String**|  |
 **contactPersonDesignation** | **String**|  |
 **employeeSize** | **Integer**|  |
 **locDivisionId** | **Integer**|  | [optional]
 **locDistrictId** | **Integer**|  | [optional]
 **locUpazilaId** | **Integer**|  | [optional]
 **address** | **String**|  | [optional]
 **faxNo** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**OrganizationUnit**](OrganizationUnit.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get one |  -  |

<a name="organizationUnitsPost"></a>
# **organizationUnitsPost**
> OrganizationUnit organizationUnitsPost(titleEn, titleBn, organizationId, organizationTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus)

create

 API endpoint to create organization Unit.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OrganizationUnitsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OrganizationUnitsApi apiInstance = new OrganizationUnitsApi(defaultClient);
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer organizationId = 56; // Integer | 
    Integer organizationTypeId = 56; // Integer | 
    String mobile = "mobile_example"; // String | 
    String email = "email_example"; // String | 
    String contactPersonName = "contactPersonName_example"; // String | 
    String contactPersonMobile = "contactPersonMobile_example"; // String | 
    String contactPersonEmail = "contactPersonEmail_example"; // String | 
    String contactPersonDesignation = "contactPersonDesignation_example"; // String | 
    Integer employeeSize = 56; // Integer | 
    Integer locDivisionId = 56; // Integer | 
    Integer locDistrictId = 56; // Integer | 
    Integer locUpazilaId = 56; // Integer | 
    String address = "address_example"; // String | 
    String faxNo = "faxNo_example"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsPost(titleEn, titleBn, organizationId, organizationTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsPost");
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
 **organizationTypeId** | **Integer**|  |
 **mobile** | **String**|  |
 **email** | **String**|  |
 **contactPersonName** | **String**|  |
 **contactPersonMobile** | **String**|  |
 **contactPersonEmail** | **String**|  |
 **contactPersonDesignation** | **String**|  |
 **employeeSize** | **Integer**|  |
 **locDivisionId** | **Integer**|  | [optional]
 **locDistrictId** | **Integer**|  | [optional]
 **locUpazilaId** | **Integer**|  | [optional]
 **address** | **String**|  | [optional]
 **faxNo** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**OrganizationUnit**](OrganizationUnit.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

