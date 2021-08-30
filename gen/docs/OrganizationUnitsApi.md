# OrganizationUnitsApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**organizationUnitsGet**](OrganizationUnitsApi.md#organizationUnitsGet) | **GET** /organization-units | get list
[**organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost) | **POST** /organization-units/{organizationUnitId}/assign-service-to-organization-unit | Assign services to organizationUnit
[**organizationUnitsOrganizationUnitIdDelete**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdDelete) | **DELETE** /organization-units/{organizationUnitId} | delete
[**organizationUnitsOrganizationUnitIdGet**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdGet) | **GET** /organization-units/{organizationUnitId} | get one
[**organizationUnitsOrganizationUnitIdGetHierarchyGet**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdGetHierarchyGet) | **GET** /organization-units/{organizationUnitId}/get-hierarchy | get-hierarchy
[**organizationUnitsOrganizationUnitIdPut**](OrganizationUnitsApi.md#organizationUnitsOrganizationUnitIdPut) | **PUT** /organization-units/{organizationUnitId} | update
[**organizationUnitsPost**](OrganizationUnitsApi.md#organizationUnitsPost) | **POST** /organization-units | create


<a name="organizationUnitsGet"></a>
# **organizationUnitsGet**
> OrganizationUnit organizationUnitsGet(page, limit, order, rowStatus, titleEn, titleBn)

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
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    String order = "order_example"; // String | 
    Integer rowStatus = 1; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsGet(page, limit, order, rowStatus, titleEn, titleBn);
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
 **limit** | **Integer**|  | [optional]
 **order** | **String**|  | [optional] [enum: asc, desc]
 **rowStatus** | **Integer**|  | [optional]
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

<a name="organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost"></a>
# **organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost**
> OrganizationUnit organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost(organizationUnitId, serviceIds)

Assign services to organizationUnit

API endpoint to assign services to the specified organization unit.A successful request response will show 200 HTTP status code.

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
    Integer organizationUnitId = 2; // Integer | 
    List<Integer> serviceIds = Arrays.asList(); // List<Integer> | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost(organizationUnitId, serviceIds);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsOrganizationUnitIdAssignServiceToOrganizationUnitPost");
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
 **serviceIds** | [**List&lt;Integer&gt;**](Integer.md)|  | [optional]

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
**200** | Assign services to organizationUnit |  -  |

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
    Integer organizationUnitId = 2; // Integer | 
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

 API endpoint to get the specified Organization  Unit.A successful request response will show 200 HTTP status code

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
    Integer organizationUnitId = 2; // Integer | 
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

<a name="organizationUnitsOrganizationUnitIdGetHierarchyGet"></a>
# **organizationUnitsOrganizationUnitIdGetHierarchyGet**
> OrganizationUnit organizationUnitsOrganizationUnitIdGetHierarchyGet(organizationUnitId)

get-hierarchy

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
    Integer organizationUnitId = 2; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsOrganizationUnitIdGetHierarchyGet(organizationUnitId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OrganizationUnitsApi#organizationUnitsOrganizationUnitIdGetHierarchyGet");
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
**200** | get-hierarchy |  -  |

<a name="organizationUnitsOrganizationUnitIdPut"></a>
# **organizationUnitsOrganizationUnitIdPut**
> OrganizationUnit organizationUnitsOrganizationUnitIdPut(organizationUnitId, titleEn, titleBn, organizationId, organizationUnitTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus)

update

API endpoint to update the specified Organization  Unit. A successful request response will show 200 HTTP status code

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
    Integer organizationUnitId = 2; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer organizationId = 2; // Integer | 
    Integer organizationUnitTypeId = 1; // Integer | 
    String mobile = "01711223344"; // String | 
    String email = "company_name@gmail.com"; // String | 
    String contactPersonName = "Mr. X"; // String | 
    String contactPersonMobile = "01789277788"; // String | 
    String contactPersonEmail = "mr.x@gmail.com"; // String | 
    String contactPersonDesignation = "HR"; // String | 
    Integer employeeSize = 10; // Integer | 
    Integer locDivisionId = 1; // Integer | 
    Integer locDistrictId = 1; // Integer | 
    Integer locUpazilaId = 1; // Integer | 
    String address = "Dhaka-1208"; // String | 
    String faxNo = "+123456"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsOrganizationUnitIdPut(organizationUnitId, titleEn, titleBn, organizationId, organizationUnitTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus);
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
 **organizationUnitTypeId** | **Integer**|  |
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
> OrganizationUnit organizationUnitsPost(titleEn, titleBn, organizationId, organizationUnitTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus)

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
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer organizationId = 2; // Integer | 
    Integer organizationUnitTypeId = 1; // Integer | 
    String mobile = "01711223344"; // String | 
    String email = "company_name@gmail.com"; // String | 
    String contactPersonName = "Mr. X"; // String | 
    String contactPersonMobile = "01789277788"; // String | 
    String contactPersonEmail = "mr.x@gmail.com"; // String | 
    String contactPersonDesignation = "HR"; // String | 
    Integer employeeSize = 10; // Integer | 
    Integer locDivisionId = 1; // Integer | 
    Integer locDistrictId = 1; // Integer | 
    Integer locUpazilaId = 1; // Integer | 
    String address = "Dhaka-1208"; // String | 
    String faxNo = "+123456"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      OrganizationUnit result = apiInstance.organizationUnitsPost(titleEn, titleBn, organizationId, organizationUnitTypeId, mobile, email, contactPersonName, contactPersonMobile, contactPersonEmail, contactPersonDesignation, employeeSize, locDivisionId, locDistrictId, locUpazilaId, address, faxNo, rowStatus);
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
 **organizationUnitTypeId** | **Integer**|  |
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

