# OccupationApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**occupationsGet**](OccupationApi.md#occupationsGet) | **GET** /occupations | get_list
[**occupationsOccupationIdDelete**](OccupationApi.md#occupationsOccupationIdDelete) | **DELETE** /occupations/{occupationId} | delete
[**occupationsOccupationIdGet**](OccupationApi.md#occupationsOccupationIdGet) | **GET** /occupations/{occupationId} | get_one
[**occupationsOccupationIdPut**](OccupationApi.md#occupationsOccupationIdPut) | **PUT** /occupations/{occupationId} | update
[**occupationsPost**](OccupationApi.md#occupationsPost) | **POST** /occupations | create


<a name="occupationsGet"></a>
# **occupationsGet**
> Occupation occupationsGet(page, limit, rowStatus, order, titleEn, titleBn)

get_list

API endpoint to get the list of occupations.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OccupationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OccupationApi apiInstance = new OccupationApi(defaultClient);
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    Integer rowStatus = 1; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Occupation result = apiInstance.occupationsGet(page, limit, rowStatus, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OccupationApi#occupationsGet");
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

[**Occupation**](Occupation.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="occupationsOccupationIdDelete"></a>
# **occupationsOccupationIdDelete**
> Occupation occupationsOccupationIdDelete(occupationId)

delete

 API endpoint to delete the specified occupation.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OccupationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OccupationApi apiInstance = new OccupationApi(defaultClient);
    Integer occupationId = 3; // Integer | 
    try {
      Occupation result = apiInstance.occupationsOccupationIdDelete(occupationId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OccupationApi#occupationsOccupationIdDelete");
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
 **occupationId** | **Integer**|  |

### Return type

[**Occupation**](Occupation.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="occupationsOccupationIdGet"></a>
# **occupationsOccupationIdGet**
> Occupation occupationsOccupationIdGet(occupationId)

get_one

API endpoint to get an occupations.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OccupationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OccupationApi apiInstance = new OccupationApi(defaultClient);
    Integer occupationId = 3; // Integer | 
    try {
      Occupation result = apiInstance.occupationsOccupationIdGet(occupationId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OccupationApi#occupationsOccupationIdGet");
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
 **occupationId** | **Integer**|  |

### Return type

[**Occupation**](Occupation.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

<a name="occupationsOccupationIdPut"></a>
# **occupationsOccupationIdPut**
> Occupation occupationsOccupationIdPut(occupationId, titleEn, titleBn, jobSectorId)

update

API endpoint to update the specified occupation.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OccupationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OccupationApi apiInstance = new OccupationApi(defaultClient);
    Integer occupationId = 3; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer jobSectorId = 1; // Integer | 
    try {
      Occupation result = apiInstance.occupationsOccupationIdPut(occupationId, titleEn, titleBn, jobSectorId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OccupationApi#occupationsOccupationIdPut");
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
 **occupationId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **jobSectorId** | **Integer**|  |

### Return type

[**Occupation**](Occupation.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

<a name="occupationsPost"></a>
# **occupationsPost**
> Occupation occupationsPost(titleEn, titleBn, jobSectorId, rowStatus)

create

API endpoint to get the list of occupations.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.OccupationApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    OccupationApi apiInstance = new OccupationApi(defaultClient);
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer jobSectorId = 1; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      Occupation result = apiInstance.occupationsPost(titleEn, titleBn, jobSectorId, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling OccupationApi#occupationsPost");
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
 **jobSectorId** | **Integer**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**Occupation**](Occupation.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

