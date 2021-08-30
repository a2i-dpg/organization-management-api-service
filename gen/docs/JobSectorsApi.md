# JobSectorsApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**jobSectorsGet**](JobSectorsApi.md#jobSectorsGet) | **GET** /job-sectors | get_list
[**jobSectorsJobSectorIdDelete**](JobSectorsApi.md#jobSectorsJobSectorIdDelete) | **DELETE** /job-sectors/{jobSectorId} | delete
[**jobSectorsJobSectorIdGet**](JobSectorsApi.md#jobSectorsJobSectorIdGet) | **GET** /job-sectors/{jobSectorId} | get_one
[**jobSectorsJobSectorIdPut**](JobSectorsApi.md#jobSectorsJobSectorIdPut) | **PUT** /job-sectors/{jobSectorId} | update
[**jobSectorsPost**](JobSectorsApi.md#jobSectorsPost) | **POST** /job-sectors | create


<a name="jobSectorsGet"></a>
# **jobSectorsGet**
> JobSector jobSectorsGet(page, order, rowStatus, titleEn, titleBn)

get_list

API endpoint to get the list of job sectors.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.JobSectorsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    JobSectorsApi apiInstance = new JobSectorsApi(defaultClient);
    Integer page = 1; // Integer | 
    String order = "order_example"; // String | 
    Integer rowStatus = 1; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      JobSector result = apiInstance.jobSectorsGet(page, order, rowStatus, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling JobSectorsApi#jobSectorsGet");
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
 **rowStatus** | **Integer**|  | [optional]
 **titleEn** | **String**|  | [optional]
 **titleBn** | **String**|  | [optional]

### Return type

[**JobSector**](JobSector.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="jobSectorsJobSectorIdDelete"></a>
# **jobSectorsJobSectorIdDelete**
> JobSector jobSectorsJobSectorIdDelete(jobSectorId)

delete

 API endpoint to delete the specified job sector.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.JobSectorsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    JobSectorsApi apiInstance = new JobSectorsApi(defaultClient);
    Integer jobSectorId = 1; // Integer | 
    try {
      JobSector result = apiInstance.jobSectorsJobSectorIdDelete(jobSectorId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling JobSectorsApi#jobSectorsJobSectorIdDelete");
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
 **jobSectorId** | **Integer**|  |

### Return type

[**JobSector**](JobSector.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="jobSectorsJobSectorIdGet"></a>
# **jobSectorsJobSectorIdGet**
> JobSector jobSectorsJobSectorIdGet(jobSectorId)

get_one

API endpoint to get a specified job sector.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.JobSectorsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    JobSectorsApi apiInstance = new JobSectorsApi(defaultClient);
    Integer jobSectorId = 1; // Integer | 
    try {
      JobSector result = apiInstance.jobSectorsJobSectorIdGet(jobSectorId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling JobSectorsApi#jobSectorsJobSectorIdGet");
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
 **jobSectorId** | **Integer**|  |

### Return type

[**JobSector**](JobSector.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

<a name="jobSectorsJobSectorIdPut"></a>
# **jobSectorsJobSectorIdPut**
> JobSector jobSectorsJobSectorIdPut(jobSectorId, titleEn, titleBn, rowStatus)

update

API endpoint to get a specified job sector.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.JobSectorsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    JobSectorsApi apiInstance = new JobSectorsApi(defaultClient);
    Integer jobSectorId = 1; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      JobSector result = apiInstance.jobSectorsJobSectorIdPut(jobSectorId, titleEn, titleBn, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling JobSectorsApi#jobSectorsJobSectorIdPut");
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
 **jobSectorId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**JobSector**](JobSector.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

<a name="jobSectorsPost"></a>
# **jobSectorsPost**
> JobSector jobSectorsPost(titleEn, titleBn, limit, rowStatus)

create

API endpoint to create of job sectors.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.JobSectorsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    JobSectorsApi apiInstance = new JobSectorsApi(defaultClient);
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    Integer limit = 10; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      JobSector result = apiInstance.jobSectorsPost(titleEn, titleBn, limit, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling JobSectorsApi#jobSectorsPost");
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
 **limit** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**JobSector**](JobSector.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

