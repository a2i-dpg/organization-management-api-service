# RankApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getOne1**](RankApi.md#getOne1) | **GET** /ranks/{rankId} | get_one
[**ranksGet**](RankApi.md#ranksGet) | **GET** /ranks | get_list
[**ranksPost**](RankApi.md#ranksPost) | **POST** /ranks | create
[**ranksRankIdDelete**](RankApi.md#ranksRankIdDelete) | **DELETE** /ranks/{rankId} | delete
[**ranksRankIdPut**](RankApi.md#ranksRankIdPut) | **PUT** /ranks/{rankId} | update


<a name="getOne1"></a>
# **getOne1**
> Rank getOne1(rankId)

get_one

API endpoint to get a rank.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankApi apiInstance = new RankApi(defaultClient);
    Integer rankId = 56; // Integer | 
    try {
      Rank result = apiInstance.getOne1(rankId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankApi#getOne1");
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
 **rankId** | **Integer**|  |

### Return type

[**Rank**](Rank.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

<a name="ranksGet"></a>
# **ranksGet**
> Rank ranksGet(page, order, titleEn, titleBn)

get_list

API endpoint to get the list of ranks .A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankApi apiInstance = new RankApi(defaultClient);
    Integer page = 56; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Rank result = apiInstance.ranksGet(page, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankApi#ranksGet");
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

[**Rank**](Rank.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="ranksPost"></a>
# **ranksPost**
> Rank ranksPost(organizationId, titleEn, titleBn, rankTypeId, grade, displayOrder, rowStatus)

create

API endpoint to get the list of ranks.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankApi apiInstance = new RankApi(defaultClient);
    Integer organizationId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer rankTypeId = 56; // Integer | 
    String grade = "grade_example"; // String | 
    Integer displayOrder = 56; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      Rank result = apiInstance.ranksPost(organizationId, titleEn, titleBn, rankTypeId, grade, displayOrder, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankApi#ranksPost");
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
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **rankTypeId** | **Integer**|  |
 **grade** | **String**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**Rank**](Rank.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

<a name="ranksRankIdDelete"></a>
# **ranksRankIdDelete**
> Rank ranksRankIdDelete(rankId)

delete

 API endpoint to get a rank.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankApi apiInstance = new RankApi(defaultClient);
    Integer rankId = 56; // Integer | 
    try {
      Rank result = apiInstance.ranksRankIdDelete(rankId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankApi#ranksRankIdDelete");
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
 **rankId** | **Integer**|  |

### Return type

[**Rank**](Rank.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="ranksRankIdPut"></a>
# **ranksRankIdPut**
> RankType ranksRankIdPut(organizationId, titleEn, titleBn, rankTypeId, grade, displayOrder, rowStatus)

update

API endpoint to get a rank. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.RankApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    RankApi apiInstance = new RankApi(defaultClient);
    Integer organizationId = 56; // Integer | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    Integer rankTypeId = 56; // Integer | 
    String grade = "grade_example"; // String | 
    Integer displayOrder = 56; // Integer | 
    Integer rowStatus = 56; // Integer | 
    try {
      RankType result = apiInstance.ranksRankIdPut(organizationId, titleEn, titleBn, rankTypeId, grade, displayOrder, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling RankApi#ranksRankIdPut");
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
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **rankTypeId** | **Integer**|  |
 **grade** | **String**|  | [optional]
 **displayOrder** | **Integer**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**RankType**](RankType.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

