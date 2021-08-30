# SkillsApi

All URIs are relative to *http://localhost:8000/api/v1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**skillsGet**](SkillsApi.md#skillsGet) | **GET** /skills | get_list
[**skillsPost**](SkillsApi.md#skillsPost) | **POST** /skills | create
[**skillsSkillIdDelete**](SkillsApi.md#skillsSkillIdDelete) | **DELETE** /skills/{skillId} | delete
[**skillsSkillIdGet**](SkillsApi.md#skillsSkillIdGet) | **GET** /skills/{skillId} | get_one
[**skillsSkillIdPut**](SkillsApi.md#skillsSkillIdPut) | **PUT** /skills/{skillId} | update


<a name="skillsGet"></a>
# **skillsGet**
> Skill skillsGet(page, limit, rowStatus, order, titleEn, titleBn)

get_list

API endpoint to get the list of Skills.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.SkillsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    SkillsApi apiInstance = new SkillsApi(defaultClient);
    Integer page = 1; // Integer | 
    Integer limit = 10; // Integer | 
    Integer rowStatus = 1; // Integer | 
    String order = "order_example"; // String | 
    String titleEn = "titleEn_example"; // String | 
    String titleBn = "titleBn_example"; // String | 
    try {
      Skill result = apiInstance.skillsGet(page, limit, rowStatus, order, titleEn, titleBn);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling SkillsApi#skillsGet");
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

[**Skill**](Skill.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_list |  -  |

<a name="skillsPost"></a>
# **skillsPost**
> Skill skillsPost(titleEn, titleBn, description, rowStatus)

create

API endpoint to create a Skill.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.SkillsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    SkillsApi apiInstance = new SkillsApi(defaultClient);
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    String description = "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      Skill result = apiInstance.skillsPost(titleEn, titleBn, description, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling SkillsApi#skillsPost");
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
 **description** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**Skill**](Skill.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | create |  -  |

<a name="skillsSkillIdDelete"></a>
# **skillsSkillIdDelete**
> Skill skillsSkillIdDelete(skillId)

delete

API endpoint to delete the specified Skill.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.SkillsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    SkillsApi apiInstance = new SkillsApi(defaultClient);
    Integer skillId = 5; // Integer | 
    try {
      Skill result = apiInstance.skillsSkillIdDelete(skillId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling SkillsApi#skillsSkillIdDelete");
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
 **skillId** | **Integer**|  |

### Return type

[**Skill**](Skill.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | delete |  -  |

<a name="skillsSkillIdGet"></a>
# **skillsSkillIdGet**
> Skill skillsSkillIdGet(skillId)

get_one

API endpoint to get a specified Skill.A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.SkillsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    SkillsApi apiInstance = new SkillsApi(defaultClient);
    Integer skillId = 5; // Integer | 
    try {
      Skill result = apiInstance.skillsSkillIdGet(skillId);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling SkillsApi#skillsSkillIdGet");
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
 **skillId** | **Integer**|  |

### Return type

[**Skill**](Skill.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | get_one |  -  |

<a name="skillsSkillIdPut"></a>
# **skillsSkillIdPut**
> Skill skillsSkillIdPut(skillId, organizationId, titleEn, titleBn, description, rowStatus)

update

API endpoint to update the specified Skill. A successful request response will show 200 HTTP status code

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.SkillsApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost:8000/api/v1");

    SkillsApi apiInstance = new SkillsApi(defaultClient);
    Integer skillId = 5; // Integer | 
    Integer organizationId = 2; // Integer | 
    String titleEn = "abc"; // String | 
    String titleBn = "abc"; // String | 
    String description = "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit"; // String | 
    Integer rowStatus = 56; // Integer | 
    try {
      Skill result = apiInstance.skillsSkillIdPut(skillId, organizationId, titleEn, titleBn, description, rowStatus);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling SkillsApi#skillsSkillIdPut");
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
 **skillId** | **Integer**|  |
 **organizationId** | **Integer**|  |
 **titleEn** | **String**|  |
 **titleBn** | **String**|  |
 **description** | **String**|  | [optional]
 **rowStatus** | **Integer**|  | [optional] [enum: 1, 0]

### Return type

[**Skill**](Skill.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | update |  -  |

